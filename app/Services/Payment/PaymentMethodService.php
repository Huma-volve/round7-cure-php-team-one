<?php

namespace App\Services\Payment;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Facades\Activity;

class PaymentMethodService
{
    public function __construct(private readonly DatabaseManager $db)
    {
    }

    /**
     * @throws \Throwable
     */
    public function create(User $user, array $attributes, ?User $actor = null): PaymentMethod
    {
        $data = $this->filterAttributes($attributes);
        $actor = $this->resolveActor($actor, $user);

        $method = $this->db->transaction(function () use ($user, $data) {
            if (Arr::get($data, 'is_default')) {
                $this->clearDefault($user);
            }

            $method = $user->paymentMethods()->create($data);

            // Ensure at least one default
            if (!$user->paymentMethods()->whereKey($method->id)->value('is_default')) {
                if (!$user->paymentMethods()->where('is_default', true)->exists()) {
                    $method->forceFill(['is_default' => true])->save();
                }
            }

            return $method->fresh();
        });

        $this->log($actor, $method, 'payment_method.created', [
            'provider' => $method->provider,
            'brand' => $method->brand,
            'last4' => $method->last4,
            'gateway' => $method->gateway,
            'is_default' => $method->is_default,
        ]);

        return $method;
    }

    /**
     * @throws \Throwable
     */
    public function setDefault(PaymentMethod $method, ?User $actor = null): PaymentMethod
    {
        $user = $method->user;
        $actor = $this->resolveActor($actor, $user);
        $previousDefaultIds = [];

        $method = $this->db->transaction(function () use ($user, $method, &$previousDefaultIds) {
            if ($method->trashed()) {
                throw ValidationException::withMessages([
                    'payment_method' => __('messages.payment_method.cannot_set_default_deleted'),
                ]);
            }

            $previousDefaultIds = $user->paymentMethods()
                ->where('is_default', true)
                ->pluck('id')
                ->toArray();

            $this->clearDefault($user, $method->id);

            $method->forceFill(['is_default' => true])->save();

            return $method->fresh();
        });

        $this->log($actor, $method, 'payment_method.set_default', [
            'previous_default_ids' => $previousDefaultIds,
            'payment_method_id' => $method->id,
        ]);

        return $method;
    }

    /**
     * Soft delete the payment method.
     */
    public function delete(PaymentMethod $method, ?User $actor = null): void
    {
        $user = $method->user;
        $wasDefault = (bool) $method->is_default;
        $actor = $this->resolveActor($actor, $user);

        $method->delete();

        $this->log($actor, $method, 'payment_method.deleted', [
            'was_default' => $wasDefault,
        ]);

        if ($wasDefault) {
            $nextDefault = $user->paymentMethods()
                ->whereNull('deleted_at')
                ->where('id', '!=', $method->id)
                ->orderByDesc('created_at')
                ->first();

            if ($nextDefault) {
                $this->setDefault($nextDefault, $actor);
            }
        }
    }

    public function restore(PaymentMethod $method, ?User $actor = null): PaymentMethod
    {
        $method->restore();
        $actor = $this->resolveActor($actor, $method->user);

        $method = $method->fresh();

        $this->log($actor, $method, 'payment_method.restored', [
            'payment_method_id' => $method->id,
        ]);

        return $method;
    }

    protected function clearDefault(User $user, ?int $excludeId = null): void
    {
        $query = $user->paymentMethods()->where('is_default', true);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $query->update(['is_default' => false]);
    }

    protected function filterAttributes(array $attributes): array
    {
        return Arr::only($attributes, [
            'provider',
            'brand',
            'last4',
            'exp_month',
            'exp_year',
            'gateway',
            'token',
            'is_default',
            'metadata',
        ]);
    }

    protected function resolveActor(?User $actor, User $fallback): ?User
    {
        return $actor ?: auth()->user() ?: $fallback;
    }

    protected function log(?User $actor, PaymentMethod $method, string $event, array $properties = []): void
    {
        if (!$actor) {
            return;
        }

        Activity::causedBy($actor)
            ->performedOn($method)
            ->withProperties($properties)
            ->log($event);
    }
}

