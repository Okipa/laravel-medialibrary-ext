# Upgrade from v7 to v8

First, make sure to follow the [base package upgrade guide](https://github.com/spatie/laravel-medialibrary/blob/master/UPGRADING.md).

Then, follow the steps below to upgrade the package extension.

## New trait to implement in models

The extension does live separately from the base package.

As so, you now have to add the `ExtendsMediaAbilities` trait use in each model that implement the `InteractsWithMedia` trait to allow them to use the extension extra features.

## Config changes

The `config('media-library.queued_conversions')` extension is not used anymore.

Make sure to remove the line from the config file.

To avoid conversions to be queued, you'll have to use the `->nonQueued()` method, as specified in the [base package documentation](https://docs.spatie.be/laravel-medialibrary/v7/converting-images/defining-conversions/#queuing-conversions).

## Method signature changes

The following methods name have been changed. Make sure to change them in your code.

* Search and replace all `->validationRules(` calls by `->getMediaValidationRules(`.
* Search and replace all `->mimesValidationRules(` calls by `->getMediaMimesValidationRules(`.
* Search and replace all `->mimeTypesValidationRules(` calls by `->getMediaMimeTypesValidationRules(`.
* Search and replace all `->dimensionValidationRules(` calls by `->getMediaDimensionValidationRules(`.
* Search and replace all `->sizeValidationRule(` calls by `->getMediaSizeValidationRule(`.
* Search and replace all `->constraintsCaption(` calls by `->getMediaCaption(`.
* Search and replace all `->dimensionsCaption(` calls by `->getMediaDimensionsCaption(`.
* Search and replace all `->mimeTypesCaption(` calls by `->getMediaMimeTypesCaption(`.
* Search and replace all `->sizeCaption(` calls by `->getMediaSizeCaption(`.

## Deprecated methods removed

Some deprecated methods have been remove. Make to change them in your code.

* Search and replace all `->constraintsLegend(` calls by `->getMediaValidationRules(`.
* Search and replace all `->dimensionsLegend(` calls by `->getMediaDimensionsCaption(`.
* Search and replace all `->mimeTypesLegend(` calls by `->getMediaMimeTypesCaption(`.
* Search and replace all `->validationConstraints(` calls by `->getMediaValidationRules(`.
* Search and replace all `->mimesValidationConstraints(` calls by `->getMediaMimesValidationRules(`.
* Search and replace all `->mimeTypesValidationConstraints(` calls by `->getMediaMimeTypesValidationRules(`.
* Search and replace all `->dimensionValidationConstraints(` calls by `->getMediaDimensionValidationRules(`.

## Translations

The translations files have been removed and will not be used anymore.

Translations have now to be handled [this way](../../README.md#translations).

See the extension documentation the see [which sentences are available for translation](../README.md#translations).

## See all changes

See all change with the [comparison tool](https://github.com/Okipa/laravel-medialibrary-ext/compare/7.19.3...8.0.0).

## Undocumented changes

If you see any forgotten and undocumented change, please submit a PR to add them to this upgrade guide.
