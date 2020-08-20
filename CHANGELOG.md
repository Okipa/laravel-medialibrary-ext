# Changelog

## [8.1.0](https://github.com/Okipa/laravel-medialibrary-ext/compare/8.0.0...Okipa:8.1.0)

2020-08-20

* Added Laravel 8 support.
* Dropped Laravel 6 support.

## [8.0.0](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.19.2...Okipa:8.0.0)

2020-03-30

* Followed spatie/media-library upgrade to v8.
* Removed fork link from spatie/laravel-medialibrary repository.
* Pulled out the extension from the original package. The extension does now live separately.
* Removed configuration use.
* Removed deprecated methods.
* Renamed methods.
* Removed translation files.

:point_right: [See the upgrade guide](/docs/upgrade-guides/from-v7-to-v8.md)

## [7.19.2](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.19.1...Okipa:7.19.2)

2020-03-30

* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.19.3 release.

## [7.19.1](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.19.0...Okipa:7.19.1)

2020-03-06

* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.19.2 release.

## [7.19.0](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.18.0...Okipa:7.19.0)

2020-03-03

* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.19.0 release.

## [7.18.0](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.17.3...Okipa:7.18.0)

2020-03-02

* Refactored the translations, you should update them if you use custom translations.
* Deprecated the `constraintsLegend` method. Use `constraintsCaption` instead.
* Deprecated the `dimensionsLegend` method. Use `dimensionsCaption` instead.
* Deprecated the `mimeTypesLegend`method . Use `mimeTypesCaption` instead.
* Deprecated the `mimeTypesValidationConstraints`method. Use `mimeTypesValidationRules` instead.
* Deprecated the `mimesValidationConstraints`method. Use `mimesValidationRules` instead.
* Deprecated the `dimensionValidationConstraints`method. Use `dimensionValidationRules` instead.
* Added a `->sizeCaption()` method to generate the config max file size constraint caption.
* The max file size caption is now returned by the `->constraintsCaption()` method with the other constraint captions.
* Added a `->sizeValidationRule()` method to generate the the config max file size validation rule.
* The max file size validation rule is now returned by the `->validationRules()` method with the other validation rules.

## [7.17.3](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.17.2...Okipa:7.17.3)

2020-02-19

* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.18.3 release.

## [7.17.2](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.17.1...Okipa:7.17.2)

2020-02-14

* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.18.2 release.

## [7.17.1](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.17.0...Okipa:7.17.1)

2020-01-22

* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.18.1 release.

## [7.17.0](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.16.0...Okipa:7.17.0)

2020-01-06

* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.18.0 release.

## [7.16.0](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.15.1...Okipa:7.16.0)

2019-12-16

* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.17.1 release.
* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.17.0 release.
* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.16.2 release.

## [7.15.1](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.15.0...Okipa:7.15.1)

2019-12-11

* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.16.1 release.

## [7.15.0](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.14.5...Okipa:7.15.0)

2019-12-04

* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.16.0 release.

## [7.14.5](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.14.4...Okipa:7.14.5)

2019-11-27

* The extensions detection from mime types from the `Spatie\MediaLibrary\HasMedia\HasMediaTrait::extensionsFromMimeTypes()` method is now executed by `Symfony\Component\Mime\MimeTypes::getExtensions()` (which is far more reliable).
* The `mimes` validation is now executed before the `mimetypes` validation, in order to return a more comprehensible error for end user in case of wrong uploaded file type.

## [7.14.4](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.14.3...Okipa:7.14.4)

2019-11-25

* Fixed mimes extraction from mimes types, in order to remove the duplicated mimes during the constraints and legend generation.

## [7.14.3](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.14.2...Okipa:7.14.3)

2019-10-17

* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.14.2 release.

## [7.14.2](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.14.1...Okipa:7.14.2)

2019-10-15

* Fixed the translations publication and overriding as specified on the Laravel documentation: https://laravel.com/docs/packages#translations.

## [7.14.1](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.14.0...Okipa:7.14.1)

2019-09-27

* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.14.1 release.

## [7.14.0](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.13.4...Okipa:7.14.0)

2019-09-26

* Added mimes validation generation: https://laravel.com/docs/validation#rule-mimes
* Updated validation process order: mime types and mimes validations now happens before dimensions validation.
* :warning: The `->validationConstraints()` method does now return an array, rather than a string before.
* :warning: Removed the `CollectionNotFound` exception in order to follow the base package behaviour.
* :warning: Removed the `ConversionsNotFound` exception in order to follow the base package behaviour.
* :warning: Replaced the `__('medialibrary.constraint.mimeTypes')` translation by `trans_choice('medialibrary.constraint.types')` translation, in order to provide clearer legends.

## [7.13.4](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.13.3...Okipa:7.13.4)

2019-09-25

* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.14.0 release.
  * you now have to set `version_urls` to `true` in the config file in order to have your image urls versioned.

## [7.13.3](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.13.2...Okipa:7.13.3)

2019-09-25

* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.13.0 release.
* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.12.4 release.
* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.12.3 release.

## [7.13.2](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.13.1...Okipa:7.13.2)

2019-09-24

* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.12.2 release.

## [7.13.1](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.13.0...Okipa:7.13.1)

2019-09-13

* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.12.1 release.

## [7.13.0](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.12.0...Okipa:7.13.0)

2019-09-04

* Implemented https://github.com/spatie/laravel-medialibrary/releases/tag/7.12.0 release.

## [7.12.0](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.11.0...Okipa:7.12.0)

2019-08-27

* Added automatic image file name version for cache busting when `config('medialibrary.image_name_versioning')` is set to true.
* Fixed missing translations loading in service provider.
* Implemented `spatie/laravel-medialibrary:7.10.1` release.

## [7.11.0](https://github.com/Okipa/laravel-medialibrary-ext/releases/tag/7.11.0)

2019-08-27

* First extension release.
