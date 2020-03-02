# Extra features for [spatie/laravel-medialibrary](https://github.com/spatie/laravel-medialibrary) package

[![Source Code](https://img.shields.io/badge/source-okipa/laravel--medialibrary--ext-blue.svg)](https://github.com/Okipa/laravel-medialibrary-ext)
[![Latest Version](https://img.shields.io/packagist/v/okipa/laravel-medialibrary-ext.svg?style=flat-square)](https://packagist.org/packages/okipa/laravel-medialibrary-ext)
[![Total Downloads](https://img.shields.io/packagist/dt/okipa/laravel-medialibrary-ext.svg?style=flat-square)](https://packagist.org/packages/okipa/laravel-medialibrary-ext)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/Okipa/laravel-medialibrary-ext/run-tests?label=tests)
[![Quality Score](https://img.shields.io/scrutinizer/g/Okipa/laravel-medialibrary-ext.svg?style=flat-square)](https://scrutinizer-ci.com/g/Okipa/laravel-medialibrary-ext)

This package provides extra features built on top of the [spatie/laravel-medialibrary](https://github.com/spatie/laravel-medialibrary) package.

## Compatibility

The extension package will follow the original package compatibilities and upgrades.

However, the minor and patch version numbers may differ, according to the feature additions or bug fixes required by this package.  

## Documentation

Find the complete package documentation here: https://docs.spatie.be/laravel-medialibrary/v7.

## Installation

This extension package is a fork from the original [spatie/laravel-medialibrary](https://github.com/spatie/laravel-medialibrary) one.

As so, you should uninstall the original package if you installed it to avoid conflicts:

```bash
composer remove spatie/laravel-medialibrary
```

Then, install the extension package via composer:

```bash
composer require "okipa/laravel-medialibrary-ext:^7.0"
```

Follow the original package installation instructions:

* https://github.com/spatie/laravel-medialibrary#installation
* https://docs.spatie.be/laravel-medialibrary/v7/installation-setup

Finally, you can publish the extension translation files if needed with:

```bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="translations"
```

## Extra features

* [Validation rules](#validation-rules)
* [Constraints caption](#constraints-caption)
* [Global conversions queued status](#global-conversions-queued-status)



### Validation rules

Declaring your media validation rules like this:

```php
// in your user storing form request for example
public function rules()
{
    return [
        'avatar' => (new User)->validationRules('avatar'),
        // your other validation rules
    ];
}
```

Will generate:

```php
    // example
    ['mimetypes:image/jpeg,image/png', 'mimes:jpeg,jpg,jpe,png', 'dimensions:min_width=60,min_height=20', 'max:5000'];
```

### Constraints caption

Adding a constraint caption under a file input:

```html
<!-- in your HTML form -->
<label for="avatar">Choose a profile picture:</label>
<input type="file" id="avatar" name="avatar" value="{{ $avatarFileName }}">
<small>{{ (new User)->constraintsCaption('avatar') }}</small>
```

Will generate:

```html
    <!-- example -->
    Min. width: 150 px. Min. height: 70 px. Accepted types: jpeg, jpg, jpe, png. Max file size: 5Mb.
```

### Global conversions queued status

Manage the global conversions queued status by setting a boolean value to `MEDIALIBRARY_QUEUED_CONVERSIONS` in your`.env` file, or directly to `config('medialibrary.queued_conversions')` if you published the package config file.
  
This will set the default queued status for all your defined conversions.

You still will be able to manually define a [specific queued status for a conversion](https://docs.spatie.be/laravel-medialibrary/v7/converting-images/defining-conversions/#queuing-conversions). 

## Testing

```bash
vendor/bin/phpunit
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information about what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

* [Arthur LORENT](https://github.com/okipa) (Extension maintainer)
* [Freek Van der Herten](https://github.com/freekmurze) (Package creator and maintainer)
* [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
