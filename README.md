![Laravel Medialibrary Extension](/docs/laravel-medialibrary-ext.png)
<p align="center">
    <a href="https://github.com/Okipa/laravel-medialibrary-ext/releases" title="Latest Stable Version">
        <img src="https://img.shields.io/github/release/Okipa/laravel-medialibrary-ext.svg?style=flat-square" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/Okipa/laravel-medialibrary-ext" title="Total Downloads">
        <img src="https://img.shields.io/packagist/dt/okipa/laravel-medialibrary-ext.svg?style=flat-square" alt="Total Downloads">
    </a>
    <a href="https://github.com/Okipa/laravel-medialibrary-ext/actions" title="Build Status">
        <img src="https://github.com/Okipa/laravel-medialibrary-ext/workflows/CI/badge.svg" alt="Build Status">
    </a>
    <a href="https://coveralls.io/github/Okipa/laravel-medialibrary-ext?branch=master" title="Coverage Status">
        <img src="https://coveralls.io/repos/github/Okipa/laravel-medialibrary-ext/badge.svg?branch=master" alt="Coverage Status">
    </a>
    <a href="/LICENSE.md" title="License: MIT">
        <img src="https://img.shields.io/badge/License-MIT-blue.svg" alt="License: MIT">
    </a>
</p>

This package extension provides extra features for the [spatie/laravel-medialibrary](https://github.com/spatie/laravel-medialibrary) package.

Found this package helpful? Please consider supporting my work!

[![Donate](https://img.shields.io/badge/Buy_me_a-Ko--fi-ff5f5f.svg)](https://ko-fi.com/arthurlorent)
[![Donate](https://img.shields.io/badge/Donate_on-PayPal-green.svg)](https://paypal.me/arthurlorent)

## Compatibility

This package extension will follow the [base package](https://github.com/spatie/laravel-medialibrary) major versions and compatibility constraints.

## Upgrade guide

* [From V9 to V10](/docs/upgrade-guides/from-v9-to-v10.md)
* [From V8 to V9](/docs/upgrade-guides/from-v8-to-v9.md)
* [From V7 to V8](/docs/upgrade-guides/from-v7-to-v8.md)

## Table of contents

* [Installation](#installation)
* [Documentation](#documentation)
* [Translations](#translations)
* [Extension features](#extension-features)
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

Finally, implement the `ExtendsMediaAbilities` trait to be able to use the extension features in addition of the base package ones.

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

## Extension features

### Media validation rules

Declaring your media validation rules like this:

```php
// In your user storing form request for example
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
    // Example
    ['mimetypes:image/jpeg,image/png', 'mimes:jpg,jpeg,jpe,png', 'dimensions:min_width=60,min_height=20', 'max:5000'];
```

#### Available public methods:

* `->getMediaValidationRules(string $collectionName): array`: returns all the validation rules for the given collection.
* `->getMediaMimesValidationRules(string $collectionName): string`: returns only the mimes validation rules for the given collection.
* `->getMediaMimeTypesValidationRules(string $collectionName): string`: returns only the mime types validation rules for the given collection.
* `->getMediaDimensionValidationRules(string $collectionName): string`: returns only the dimension validation rules for the given collection.
* `->getMediaSizeValidationRule(): string`: returns only the max file size validation rule set from the base package `media-library.max_file_size` configuration value.

### Media caption

Adding a constraint caption under a file input:

```html
<!-- In your HTML form -->
<label for="avatar">Choose a profile picture:</label>
<input type="file" id="avatar" name="avatar" value="{{ $user->getFirstMedia('avatar')->name }}">
<small>{{ $user->getMediaCaption('avatar') }}</small>
```

Will generate:

```html
    <!-- Example -->
    Min. width: 150 px. Min. height: 70 px. Accepted types: jpg, jpeg, jpe, png. Max file size: 5Mb.
```

#### Available public methods:

* `getMediaCaption(string $collectionName): string`: returns a complete caption for the given collection.
* `getMediaDimensionsCaption(string $collectionName): string`: returns only the dimensions caption for the given collection.
* `getMediaMimeTypesCaption(string $collectionName): string`: returns only the mime types caption for the given collection.
* `getMediaSizeCaption(): string`: returns only the config max file size caption only.

### Exceptions

In order to avoid careless mistakes when using public methods that are requiring a `string $collectionName` argument provided by this extension, an `Okipa\MediaLibraryExt\Exceptions\CollectionNotFound` exception will be thrown when the given collection name is not found in the targeted model. 

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information about what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email arthur.lorent@gmail.com instead of using the issue tracker.

## Credits

- [Arthur LORENT](https://github.com/okipa)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
