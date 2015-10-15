<?php

namespace Eloquent\Translatable;

use Localization as Locale;

trait TranslatableTrait {

    /**
     * @var
     */
    protected $translations = [];

    /**
     * Remove translation by locale .
     *
     * @param null $locale
     * @return $this
     * @throws TranslatableException
     */
    public function removeTranslation($locale = null) {
        $translation = $this->translate($locale);

        if(! is_null($translation))
            $translation->delete();

        return $this;
    }

    /**
     * Check if model has translation for specific locale .
     *
     * @param null $locale
     * @return bool
     * @throws TranslatableException
     */
    public function hasTranslations($locale = null) {
        return !is_null($this->translate($locale));
    }

    /**
     * Translate attribute by locale .
     *
     * @param null $locale
     * @return mixed
     * @throws TranslatableException
     */
    public function translate($locale = null) {
        $locale = isset($locale) ? $locale : Locale\get_active_locale();

        if( in_array( $locale, Locale\get_locales() ) )
            throw new TranslatableException(
                _('Invalid locale')
            );

        $language = $this->getByLocale($locale);

        return $this->translations()
            ->where('language_id', $language->id)
            ->first();
    }


    /**
     * Save eloquent model .
     *
     * @param array $options
     * @return mixed
     */
    public function save(array $options = []) {
        $saved = parent::save($options);

        array_walk($this->translations, function($translation, $locale)  {
            if(! array_filter($translation))
                return false;

            $this->newTranslation($locale, $translation)
                ->save();
        });

        return $saved;
    }

    /**
     * Fill attributes and save locales if exists .
     *
     * @param array $attributes
     * @return mixed
     */
    public function fill(array $attributes) {
        $locales = Locale\get_locales();

        foreach($locales as $locale => $options)
            if( in_array($locale, array_keys($attributes)) )
                $this->translations[$locale] = array_pull($attributes, $locale);

        return parent::fill($attributes);
    }


    /**
     * Get new translation instance .
     *
     * @param null $locale
     * @param array $attributes
     * @return mixed
     */
    protected function newTranslation($locale = null, array $attributes) {
        $locale = isset($locale) ? $locale : Locale\get_active_locale();

        $language = $this->getByLocale($locale);

        $class = $this->classTranslation();

        $update = [
            'language_id' => $language->id,
             isset($this->translation_id) ? $this->translation_id : str_singular($this->getModel()->getTable()) . '_id' => $this->id,
        ];

        $attributes = array_merge($update, $attributes);

       return $class::updateOrCreate($update, $attributes);
    }

    /**
     * Get all translations ..
     *
     * @return mixed
     */
    public function translations() {
        return $this->hasMany(
            $this->classTranslation()
        );
    }


    /**
     * Get Class Translations .
     *
     * @return string
     */
    protected function classTranslation() {
        if(! $classTranslation = $this['translationClass'])
            $classTranslation = sprintf('App\\%s%s', ucfirst(str_singular($this->getModel()->getTable())), 'Translations');

        return $classTranslation;
    }

    /**
     * Get by locale .
     *
     * @param $locale
     * @return mixed
     */
    protected function getByLocale($locale) {
        $languageRepository = app('lang-db-repo');

        return $languageRepository
            ->getBySlug($locale);
    }

    /**
     * Get translatedAttributes .
     *
     * @return mixed
     */
    public function translatedAttributes() {
        if(! $attributes = isset($this['translatedAttributes']) ? $this['translatedAttributes'] : null) {
            $class = $this->classTranslation();

            $attributes = (new $class)
                ->getFillable();

            $attributes = array_except(array_flip($attributes),  [
                'language_id' ,
                str_singular($this->getModel()->getTable()) . '_id'
            ]);

            $attributes = array_flip($attributes);
        }

        return $attributes;
    }
}
