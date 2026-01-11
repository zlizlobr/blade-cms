<?php

namespace App\Support\Traits;

use Illuminate\Support\Facades\App;

trait HasTranslations
{
    /**
     * Get the list of translatable attributes.
     *
     * @return array
     */
    abstract protected function getTranslatableAttributes(): array;

    /**
     * Get a translation for a specific field and locale.
     *
     * @param  string  $field
     * @param  string|null  $locale
     * @return string|null
     */
    public function getTranslation(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?? App::getLocale();
        $fallbackLocale = config('i18n.fallback_locale', 'cs');

        // Get the raw value (should be JSON)
        $value = $this->attributes[$field] ?? null;

        if (is_null($value)) {
            return null;
        }

        // Decode JSON if needed
        $translations = is_string($value) ? json_decode($value, true) : $value;

        if (! is_array($translations)) {
            return $value;
        }

        // Return translation for requested locale or fallback
        return $translations[$locale] ?? $translations[$fallbackLocale] ?? null;
    }

    /**
     * Set a translation for a specific field and locale.
     *
     * @param  string  $field
     * @param  string  $locale
     * @param  string  $value
     * @return $this
     */
    public function setTranslation(string $field, string $locale, string $value): self
    {
        $translations = $this->getTranslations($field);
        $translations[$locale] = $value;

        $this->attributes[$field] = json_encode($translations);

        return $this;
    }

    /**
     * Get all translations for a specific field.
     *
     * @param  string  $field
     * @return array
     */
    public function getTranslations(string $field): array
    {
        $value = $this->attributes[$field] ?? null;

        if (is_null($value)) {
            return [];
        }

        $translations = is_string($value) ? json_decode($value, true) : $value;

        return is_array($translations) ? $translations : [];
    }

    /**
     * Check if a translation exists for a specific field and locale.
     *
     * @param  string  $field
     * @param  string  $locale
     * @return bool
     */
    public function hasTranslation(string $field, string $locale): bool
    {
        $translations = $this->getTranslations($field);

        return isset($translations[$locale]) && ! empty($translations[$locale]);
    }

    /**
     * Get attribute value with automatic translation.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        // Check if this is a translatable attribute
        if (in_array($key, $this->getTranslatableAttributes())) {
            return $this->getTranslation($key);
        }

        return parent::getAttribute($key);
    }

    /**
     * Set attribute value with automatic translation handling.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        // If it's a translatable attribute and value is an array, encode it
        if (in_array($key, $this->getTranslatableAttributes()) && is_array($value)) {
            $this->attributes[$key] = json_encode($value);

            return $this;
        }

        return parent::setAttribute($key, $value);
    }
}
