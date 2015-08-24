<?php

namespace Eloquent\Translatable;

use Localization as Locale;

trait TranslatableTrait {

    /**
     * 1. locale is not required .
     * 2. if locale is not sent as argument than get default detected locale.
     * 3. check locale if exists in array locales .
     * 4. get current
     */
    public function translate($locale = null) {
        $locale = isset($locale) ?: Locale\get_active_locale();

        if( in_array( $locale, Locale\get_locales() ) )
            throw new TranslatableException(
                _('Invalid locale')
            );

        return $this->translations()
            ->where('locale', $locale);
    }

    /**
     * Create new eloquent instance and save locales if exists .
     *
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes = []) {
        $locales = Locale\get_locales();

        foreach($locales as $locale)
            if( in_array($locale, $attributes) )
                $translations = array_shift($attributes, $locale);

        $eloquent = parent::create($attributes);

        array_walk($translations, function($translation) use($eloquent) {
            $this->newTranslation($translation)
                ->save();
        });

        return $eloquent;
    }

    /**
     * Fill attributes and save locales if exists .
     *
     * @param array $attributes
     * @return mixed
     */
    public function fill(array $attributes) {
        $locales = Locale\get_locales();

        foreach($locales as $locale)
            if( in_array($locale, $attributes) )
                $translations = array_shift($attributes, $locale);

        $eloquent = parent::create($attributes);

        array_walk($translations, function($translation) use($eloquent) {
            $this->newTranslation($translation)
                ->save();
        });

        return $eloquent;
    }

    /**
     * Get new translation instance .
     *
     * @param null $locale
     * @return mixed
     */
    protected function newTranslation($locale = null) {
        $locale = isset($locale) ?: Locale\get_active_locale();

        $class = $this->classTranslation();

        return (new $class(
            [ 'language_id' => $locale ]
        ));
    }

    /**
     * Get all translations ..
     *
     * @return mixed
     */
    public function translations() {
        return $this->hasMany(
            $this->getClassTranslations()
        );
    }

    /**
     * Get Class Translations .
     *
     * @return string
     */
    protected function classTranslation() {
        if(! $classTranslation = $this->getAttribute('translationClass'))
            $classTranslation = sprintf('%s%s', $this->getModel()->getTable(), 'Translations');

        return $classTranslation;
    }
}
