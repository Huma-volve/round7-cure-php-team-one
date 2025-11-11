<?php

namespace Tests\Feature\Api;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PatientPaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('patient', 'api');
    }

    public function test_patient_can_list_payment_methods(): void
    {
        $user = $this->createPatientUser();

        PaymentMethod::factory()->count(2)->for($user)->create();
        PaymentMethod::factory()->for($user)->create()->delete();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/patient/payment-methods');

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonCount(2, 'data');
    }

    public function test_patient_can_create_payment_method(): void
    {
        $user = $this->createPatientUser();

        $payload = [
            'provider' => 'card',
            'brand' => 'visa',
            'last4' => '1234',
            'exp_month' => 12,
            'exp_year' => now()->year + 2,
            'gateway' => 'mock',
            'token' => 'tok_' . uniqid(),
            'is_default' => true,
            'metadata' => ['source' => 'mobile'],
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/patient/payment-methods', $payload);

        $response->assertCreated()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.is_default', true);

        $this->assertDatabaseHas('payment_methods', [
            'user_id' => $user->id,
            'token' => $payload['token'],
            'is_default' => true,
        ]);
    }

    public function test_patient_can_set_default_payment_method(): void
    {
        $user = $this->createPatientUser();

        $primary = PaymentMethod::factory()->for($user)->asDefault()->create();
        $secondary = PaymentMethod::factory()->for($user)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/patient/payment-methods/{$secondary->id}/default");

        $response->assertOk()
            ->assertJsonPath('data.id', $secondary->id)
            ->assertJsonPath('data.is_default', true);

        $this->assertFalse($primary->fresh()->is_default);
        $this->assertTrue($secondary->fresh()->is_default);
    }

    public function test_patient_cannot_manage_other_users_methods(): void
    {
        $user = $this->createPatientUser();
        $otherUser = $this->createPatientUser();
        $foreignMethod = PaymentMethod::factory()->for($otherUser)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/patient/payment-methods/{$foreignMethod->id}");

        $response->assertForbidden()
            ->assertJson(['message' => 'This action is unauthorized.']);

        $this->assertNotNull($foreignMethod->fresh());
    }

    public function test_deleting_default_method_promotes_next_available(): void
    {
        $user = $this->createPatientUser();

        $default = PaymentMethod::factory()->for($user)->asDefault()->create();
        $backup = PaymentMethod::factory()->for($user)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/patient/payment-methods/{$default->id}");

        $response->assertOk()
            ->assertJsonPath('message', __('messages.payment_method.deleted'));

        $this->assertSoftDeleted('payment_methods', ['id' => $default->id]);
        $this->assertTrue($backup->fresh()->is_default);
    }

    public function test_card_requires_expiry_fields(): void
    {
        $user = $this->createPatientUser();

        $payload = [
            'provider' => 'card',
            'brand' => 'visa',
            'last4' => '1234',
            'gateway' => 'mock',
            'token' => 'tok_' . uniqid(),
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/patient/payment-methods', $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['expiry']);
    }

    protected function createPatientUser(): User
    {
        $user = User::factory()->create();
        $role = Role::findByName('patient', 'api');
        $user->assignRole($role);

        return $user;
    }
}

