<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait HasTranslations
{
    /**
     * Get a field's value in the currently active locale.
     * Falls back to English if translation is missing.
     *
     * Usage in blade: {{ $model->getLocalized('title') }}
     *
     * @param string $field  The base field name (e.g. 'title', 'content', 'description')
     * @return string
     */
    public function getLocalized(string $field): string
    {
        $locale = App::getLocale();

        // English → return original field
        if ($locale === 'en') {
            return (string) ($this->{$field} ?? '');
        }

        // Try locale-specific column first: title_hi, title_pa, etc.
        $localizedColumn = $field . '_' . $locale;

        if (isset($this->attributes[$localizedColumn]) || array_key_exists($localizedColumn, $this->attributes)) {
            $localizedValue = $this->{$localizedColumn};
            if (!empty($localizedValue)) {
                return (string) $localizedValue;
            }
        }

        // Fallback to English
        return (string) ($this->{$field} ?? '');
    }

    /**
     * Check if a translation exists for a given field and locale.
     */
    public function hasTranslation(string $field, string $locale): bool
    {
        if ($locale === 'en') {
            return !empty($this->{$field});
        }
        $col = $field . '_' . $locale;
        return !empty($this->{$col});
    }

    /**
     * Get translation status for all non-English locales.
     * Returns: 'complete', 'partial', 'missing'
     */
    public function getTranslationStatus(array $fields = ['title', 'description']): string
    {
        $locales = ['hi', 'pa'];
        $totalChecks = count($fields) * count($locales);
        $translated = 0;

        foreach ($locales as $locale) {
            foreach ($fields as $field) {
                $col = $field . '_' . $locale;
                if (!empty($this->{$col})) {
                    $translated++;
                }
            }
        }

        if ($translated === 0) return 'missing';
        if ($translated < $totalChecks) return 'partial';
        return 'complete';
    }
}
