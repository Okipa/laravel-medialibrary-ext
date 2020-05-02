# Extra features for spatie/laravel-medialibrary package

[![Source Code](https://img.shields.io/badge/source-okipa/laravel--medialibrary--ext-blue.svg)](https://github.com/Okipa/laravel-medialibrary-ext)
[![Latest Version](https://img.shields.io/github/release/okipa/laravel-medialibrary-ext.svg?style=flat-square)](https://github.com/Okipa/laravel-medialibrary-ext/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/okipa/laravel-medialibrary-ext.svg?style=flat-square)](https://packagist.org/packages/okipa/laravel-medialibrary-ext)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Build status](https://github.com/Okipa/laravel-medialibrary-ext/workflows/CI/badge.svg)](https://github.com/Okipa/laravel-medialibrary-ext/actions)
[![Coverage Status](https://coveralls.io/repos/github/Okipa/laravel-medialibrary-ext/badge.svg?branch=master)](https://coveralls.io/github/Okipa/laravel-medialibrary-ext?branch=master)
[![Quality Score](https://img.shields.io/scrutinizer/g/Okipa/laravel-medialibrary-ext.svg?style=flat-square)](https://scrutinizer-ci.com/g/Okipa/laravel-medialibrary-ext/?branch=master)

This package extension provides extra features for the [spatie/laravel-medialibrary](https://github.com/spatie/laravel-medialibrary) package.

## Compatibility

This package extension will follow the [base package](https://github.com/spatie/laravel-medialibrary) major versions and compatibility constraints.

## Upgrade guide

* [From V7 to V8](/docs/upgrade-guides/from-v7-to-v8.md)

## Table of contents

* [Installation](#installation)
* [Documentation](#documentation)
* [Translations](#translations)
* [Extra features](#extra-features)
  * [Validation rules](#media-validation-rules)
  * [Media caption](#media-caption)
* [Testing](#testing)
* [Changelog](#changelog)
* [Contributing](#contributing)
* [Security](#security)
* [Credits](#credits)
* [Licence](#license)

## Installation

First, be sure to follow the base package installation instructions:

* https://github.com/spatie/laravel-medialibrary#installation
* https://docs.spatie.be/laravel-medialibrary/v8/installation-setup

Then, install the extension via composer:

```bash
composer require okipa/laravel-medialibrary-ext
```

Finally, implement the `ExtendsMediaAbilities` trait to be able to use the extension features with the base package ones.

```php

use Illuminate\Database\Eloquent\Model;
use Okipa\MediaLibraryExt\ExtendsMediaAbilities;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Page extends Model implements HasMedia
{
    use InteractsWithMedia;
	use ExtendsMediaAbilities;

	// ...
}
```

## Documentation

Find the complete documentation of the base package here: https://docs.spatie.be/laravel-medialibrary/v8/introduction.

## Translations

All captions are translatable.

See how to translate them on the Laravel official documentation: https://laravel.com/docs/localization#using-translation-strings-as-keys.

Here is the list of the sentences available for translation:

* `Min. width: :width px.`
* `Min. height: :height px.`
* `{1}Accepted type: :types.|[2,*]Accepted types: :types.`
* `Max. file size: :size Mb.`

## Extra features

### Media validation rules

Declaring your media validation rules like this:

```php
// in your user storing form request for example
public function rules()
{
    return [
        'avatar' => (new User)->getMediaValidationRules('avatar'),
        // your other validation rules
    ];
}
```

Will generate:

```php
    // example
    ['mimetypes:image/jpeg,image/png', 'mimes:jpeg,jpg,jpe,png', 'dimensions:min_width=60,min_height=20', 'max:5000'];
```

#### Available methods:

* `->getMediaValidationRules(string $collectionName): array`: returns all the validation rules for the given collection.
* `->getMediaMimesValidationRules(string $collectionName): string`: returns only the mimes validation rule for the given collection.
* `->getMediaMimeTypesValidationRules(string $collectionName): string`: returns only the mime types validation rule for the given collection.
* `->getMediaDimensionValidationRules(string $collectionName): string`: returns only the dimensions validation rule for the given collection.
* `->getMediaSizeValidationRule(): string`: returns only the config max file size validation rule.

### Media caption

Adding a constraint caption under a file input:

```html
<!-- in your HTML form -->
<label for="avatar">Choose a profile picture:</label>
<input type="file" id="avatar" name="avatar" value="{{ $user->getFirstMedia('avatar')->name }}">
<small>{{ $user->getMediaCaption('avatar') }}</small>
```

Will generate:

```html
    <!-- example -->
    Min. width: 150 px. Min. height: 70 px. Accepted types: jpeg, jpg, jpe, png. Max file size: 5Mb.
```

#### Available methods:

* `getMediaCaption(string $collectionName): string`: returns a complete caption for the given collection.
* `getMediaDimensionsCaption(string $collectionName): string`: returns only the dimensions caption for the given collection.
* `getMediaMimeTypesCaption(string $collectionName): string`: returns only the mime types caption for the given collection.
* `getMediaSizeCaption(): string`: returns only the config max file size caption only.

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information about what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email arthur.lorent@gmail.com instead of using the issue tracker.

## Credits

- [Arthur LORENT](https://github.com/okipa)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
