<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'question_en',
        'answer_en',
        'question_ar',
        'answer_ar',
        'locale',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope the query to only include active FAQs ordered properly.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('id');
    }

    public function getTranslatedQuestion(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return match ($locale) {
            'ar' => $this->question_ar ?? $this->question,
            default => $this->question_en ?? $this->question,
        };
    }

    public function getTranslatedAnswer(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return match ($locale) {
            'ar' => $this->answer_ar ?? $this->answer,
            default => $this->answer_en ?? $this->answer,
        };
    }
}

