##Introduction

Eloquent translatable allow you easy to translate your eloquent models in different languages. This package working strongly with https://github.com/parfumix/laravel-localization and install that package.

So before start working under eloquent translatable please study how to implement localization on your website.

### Instalation
You can use the `composer` package manager to install. From console run:

```
  $ php composer.phar require parfumix/eloquent-translatable "v1.0"
```

or add to your composer.json file

    "parfumix/eloquent-translatable": "v1.0"


##Basic usage

Before stary working with translatable package you have implement **Translatable** contract on your eloquent model and use **TranslatableTrait** .

```php
<?php

namespace App;

use Eloquent\Translatable\Translatable;
use Eloquent\Translatable\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

// Create page model .
class Page extends Model implements Translatable {

    use TranslatableTrait;
    
    // That attribute is not required, will try to detect class name. If you have one custom you can do it.
    protected $translationClass = PageTranslations::class;

}

// Create translation model for page .
class PageTranslations extends Model {

    public $timestamps = false;
    
    protected $fillable = ['page_id', 'language_id', 'title', 'description'];

}
```

and create translation model for you eloquent model.


##How to use

```php

// Wil create and page and their translations
Page::create([
    'slug'    => 'title-slug',
    'en'       => [ 'title' => 'English title', 'description' => 'English description' ],
    'ru'       => [ 'title' => 'Russian title', 'description' => 'Russian description' ] 
]);

// Will access Page translation instance
Page::first()->translate('en') 

// will access translate title in english 
Page::first()->translate('en')->title

// Will check if page have translation in english
Page::first()->hasTranslation('en')

// Will delete english translation for current page 
Page::first()->removeTranslation('en')

// Will return all translation for current page instance 
Page::first()->translations

```
