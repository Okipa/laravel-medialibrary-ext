<?php

namespace Spatie\MediaLibrary\HasMedia;

use Spatie\MediaLibrary\Conversion\Conversion;
use Spatie\MediaLibrary\Models\Media;

interface HasMedia
{
    /**
     * Set the polymorphic relation.
     *
     * @return mixed
     */
    public function media();

    /**
     * Move a file to the medialibrary.
     *
     * @param string|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return \Spatie\MediaLibrary\FileAdder\FileAdder
     */
    public function addMedia($file);

    /**
     * Copy a file to the medialibrary.
     *
     * @param string|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return \Spatie\MediaLibrary\FileAdder\FileAdder
     */
    public function copyMedia($file);

    /**
     * Determine if there is media in the given collection.
     *
     * @param $collectionMedia
     *
     * @return bool
     */
    public function hasMedia(string $collectionMedia = ''): bool;

    /**
     * Get media collection by its collectionName.
     *
     * @param string $collectionName
     * @param array|callable $filters
     *
     * @return \Illuminate\Support\Collection
     */
    public function getMedia(string $collectionName = 'default', $filters = []);

    /**
     * Remove all media in the given collection.
     *
     * @param string $collectionName
     */
    public function clearMediaCollection(string $collectionName = 'default');

    /**
     * Remove all media in the given collection except some.
     *
     * @param string $collectionName
     * @param \Spatie\MediaLibrary\Media[]|\Illuminate\Support\Collection $excludedMedia
     *
     * @return string $collectionName
     */
    public function clearMediaCollectionExcept(string $collectionName = 'default', $excludedMedia = []);

    /**
     * Determines if the media files should be preserved when the media object gets deleted.
     *
     * @return bool
     */
    public function shouldDeletePreservingMedia();

    /**
     * Cache the media on the object.
     *
     * @param string $collectionName
     *
     * @return mixed
     */
    public function loadMedia(string $collectionName);

    /*
     * Add a conversion.
     */
    public function addMediaConversion(string $name): Conversion;

    /*
     * Register the media conversions.
     */
    public function registerMediaConversions(Media $media = null);

    /*
     * Register the media collections.
     */
    public function registerMediaCollections();

    /*
     * Register the media conversions and conversions set in media collections.
     */
    public function registerAllMediaConversions();

    /**
     * Get a collection mime types validation rules.
     *
     * @param string $collectionName
     *
     * @return string
     * @deprecated Use the mimeTypesValidationRules($collectionName) method instead.
     */
    public function mimeTypesValidationConstraints(string $collectionName): string;

    /**
     * Get a collection mime types validation rules.
     *
     * @param string $collectionName
     *
     * @return string
     */
    public function mimeTypesValidationRules(string $collectionName): string;

    /**
     * Get a collection mimes validation rules.
     *
     * @param string $collectionName
     *
     * @return string
     * @deprecated Use the mimesValidationRules($collectionName) method instead.
     */
    public function mimesValidationConstraints(string $collectionName): string;

    /**
     * Get a collection mimes validation rules.
     *
     * @param string $collectionName
     *
     * @return string
     */
    public function mimesValidationRules(string $collectionName): string;

    /**
     * Get a collection dimensions validation rules.
     *
     * @param string $collectionName
     *
     * @return string
     * @deprecated Use the dimensionValidationRules($collectionName) method instead.
     */
    public function dimensionValidationConstraints(string $collectionName): string;

    /**
     * Get a collection dimensions validation rules.
     *
     * @param string $collectionName
     *
     * @return string
     */
    public function dimensionValidationRules(string $collectionName): string;

    /**
     * Get the config max file size validation rule.
     *
     * @return string
     */
    public function sizeValidationRule(): string;

    /**
     * Get the collection declared max width and max height.
     *
     * @param string $collectionName
     *
     * @return array
     */
    public function collectionMaxSizes(string $collectionName): array;

    /**
     * Get a collection validation rules.
     *
     * @param string $collectionName
     *
     * @return array
     * @deprecated Use the validationRules($collectionName) method instead.
     */
    public function validationConstraints(string $collectionName): array;

    /**
     * Get a collection validation rules.
     *
     * @param string $collectionName
     *
     * @return array
     */
    public function validationRules(string $collectionName): array;

    /**
     * Get a collection constraints caption.
     *
     * @param string $collectionName
     *
     * @return string
     * @deprecated Use the constraintsCaption($collectionName) method instead.
     */
    public function constraintsLegend(string $collectionName): string;

    /**
     * Get a collection constraints caption.
     *
     * @param string $collectionName
     *
     * @return string
     */
    public function constraintsCaption(string $collectionName): string;

    /**
     * Get a collection dimensions constraints caption.
     *
     * @param string $collectionName
     *
     * @return string
     * @deprecated Use the dimensionsCaption($collectionName) method instead.
     */
    public function dimensionsLegend(string $collectionName): string;

    /**
     * Get a collection dimensions constraints caption.
     *
     * @param string $collectionName
     *
     * @return string
     */
    public function dimensionsCaption(string $collectionName): string;

    /**
     * Get a collection mime types constraints caption.
     *
     * @param string $collectionName
     *
     * @return string
     * @deprecated Use the mimeTypesCaption($collectionName) method instead.
     */
    public function mimeTypesLegend(string $collectionName): string;

    /**
     * Get a collection mime types constraints caption.
     *
     * @param string $collectionName
     *
     * @return string
     */
    public function mimeTypesCaption(string $collectionName): string;

    /**
     * Get the config max file size constraint caption.
     *
     * @return string
     */
    public function sizeCaption(): string;

    /**
     * Get declared conversions from a media collection name.
     *
     * @param string $collectionName
     *
     * @return array
     */
    public function getMediaConversions(string $collectionName): array;

    /**
     * Check if the given media collection should handle dimensions, according to its declared accepted mime types.
     *
     * @param string $collectionName
     *
     * @return bool
     */
    public function shouldHandleDimensions(string $collectionName): bool;
}
