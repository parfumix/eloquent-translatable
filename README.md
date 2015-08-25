##Introduction

Eloquent translatable allow you easy to translate your eloquent models in different languages. This package working strongly with https://github.com/parfumix/laravel-localization. 

So before start working under eloquent translatable please study how to implement localization on your website.

### Instalation
You can use the `composer` package manager to install. From console run:

```
  $ php composer.phar require parfumix/eloquent-translatable "v1.0"
```

or add to your composer.json file

    "parfumix/parfumix/eloquent-translatable": "v1.0"


##Basic usage

Before stary working with translatable package you have implement **Translatable** contract on your eloquent model and use **TranslatableTrait** .

```php
<?php

namespace App;

use Eloquent\Translatable\Translatable;
use Eloquent\Translatable\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements Translatable {

    use TranslatableTrait;

}
```

and create translation model for you eloquent model.

