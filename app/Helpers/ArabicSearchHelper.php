<?php

namespace App\Helpers;

class ArabicSearchHelper
{
    /**
     * Normalize Arabic text for flexible search
     * Converts all variations of Arabic characters to a unified form
     * 
     * @param string $text
     * @return string
     */
    public static function normalizeArabicText(string $text): string
    {
        // Remove diacritics (tashkeel/harakat)
        $text = self::removeDiacritics($text);
        
        // Normalize Alef variations (أ, إ, ا, آ) to ا
        $text = str_replace(['أ', 'إ', 'آ'], 'ا', $text);
        
        // Normalize Teh Marbuta (ة) to ه
        $text = str_replace('ة', 'ه', $text);
        
        // Normalize Yeh variations (ي, ى) to ي
        $text = str_replace('ى', 'ي', $text);
        
        // Remove Tatweel (ـ)
        $text = str_replace('ـ', '', $text);
        
        return trim($text);
    }
    
    /**
     * Remove Arabic diacritics (tashkeel/harakat)
     * 
     * @param string $text
     * @return string
     */
    private static function removeDiacritics(string $text): string
    {
        $diacritics = [
            // Fatha variations
            "\u{064B}", // Fathatan
            "\u{064C}", // Dammatan
            "\u{064D}", // Kasratan
            "\u{064E}", // Fatha
            "\u{064F}", // Damma
            "\u{0650}", // Kasra
            "\u{0651}", // Shadda
            "\u{0652}", // Sukun
            "\u{0653}", // Maddah
            "\u{0654}", // Hamza Above
            "\u{0655}", // Hamza Below
            "\u{0656}", // Subscript Alef
            "\u{0657}", // Inverted Damma
            "\u{0658}", // Mark Noon Ghunna
            "\u{0659}", // Zwarakay
            "\u{065A}", // Vowel Sign Small V
            "\u{065B}", // Vowel Sign Inverted Small V
            "\u{065C}", // Vowel Sign Dot Below
            "\u{065D}", // Reversed Damma
            "\u{065E}", // Fatha With Two Dots
            "\u{065F}", // Wavy Hamza Below
            "\u{0670}", // Superscript Alef
        ];
        
        return str_replace($diacritics, '', $text);
    }
    
    /**
     * Build search query with Arabic normalization
     * Returns an array of search terms to match against
     * 
     * @param string $searchTerm
     * @return array
     */
    public static function buildArabicSearchTerms(string $searchTerm): array
    {
        $normalized = self::normalizeArabicText($searchTerm);
        $original = trim($searchTerm);
        
        // Return both original and normalized for maximum flexibility
        return array_filter([
            $original,
            $normalized,
        ], function($term) {
            return !empty(trim($term));
        });
    }
}

