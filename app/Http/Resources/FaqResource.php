<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $this->normalizeLocale($request->header('Accept-Language', app()->getLocale()));

        return [
            'id' => $this->id,
            'question' => $this->getTranslatedQuestion($locale),
            'answer' => $this->getTranslatedAnswer($locale),
            'translations' => [
                'en' => [
                    'question' => $this->question_en,
                    'answer' => $this->answer_en,
                ],
                'ar' => [
                    'question' => $this->question_ar,
                    'answer' => $this->answer_ar,
                ],
            ],
            'locale' => $locale,
            'is_active' => (bool) $this->is_active,
            'display_order' => $this->display_order,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    protected function normalizeLocale(string $value): string
    {
        if (str_contains($value, ',')) {
            $value = explode(',', $value)[0];
        }

        return in_array($value, ['ar', 'en'], true) ? $value : 'en';
    }
}

