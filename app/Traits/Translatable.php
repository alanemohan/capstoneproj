<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait Translatable
{
    /**
     * Get a translated attribute.
     * Logic: If locale is 'en', return the original field.
     * If locale is 'hi' or 'pa', try field_hi or field_pa.
     * Fallback to original field (en) if translated version is empty.
     */
    public function getTranslated($field)
    {
        $locale = App::getLocale();
        
        if ($locale === 'en') {
            return $this->{$field};
        }

        $translatedField = $field . '_' . $locale;
        
        if (!empty($this->{$translatedField})) {
            return $this->{$translatedField};
        }

        return $this->{$field};
    }

    /**
     * Optional: Override __get to handle $model->title automatically.
     * For simplicity, we will use getTranslated() explicitly in views
     * or add individual accessors in models if needed.
     */
}
