<?php

namespace Eloquent\Translatable;

interface Translatable {

    /**
     * Translate eloquent .
     *
     * @param null $locale
     * @return mixed
     */
    public function translate($locale = null);
}