<?php

namespace Spatie\MediaLibrary\ValidationRulesGenerator;

trait ValidationRulesGeneratorTrait
{
    /** @inheritDoc */
    public function validationConstraints(string $collectionName): array
    {
        return $this->validationRules($collectionName);
    }

    /** @inheritDoc */
    public function validationRules(string $collectionName): array
    {
        if (! $this->getMediaCollection($collectionName)) {
            return [];
        }
        $mimesConstraints = $this->mimesValidationRules($collectionName);
        $mimeTypeConstraints = $this->mimeTypesValidationRules($collectionName);
        $dimensionConstraints = $this->dimensionValidationRules($collectionName);
        $sizeConstraint = $this->sizeValidationRule();

        return array_values(array_filter([
            $mimesConstraints,
            $mimeTypeConstraints,
            $dimensionConstraints,
            $sizeConstraint,
        ]));
    }

    /** @inheritDoc */
    public function mimesValidationConstraints(string $collectionName): string
    {
        return $this->mimesValidationRules($collectionName);
    }

    /** @inheritDoc */
    public function mimesValidationRules(string $collectionName): string
    {
        $mediaCollection = $this->getMediaCollection($collectionName);
        $validationString = '';
        if (! empty($mediaCollection->acceptsMimeTypes)) {
            $acceptedExtensions = $this->extensionsFromMimeTypes($mediaCollection->acceptsMimeTypes);
            if (! empty($acceptedExtensions)) {
                $validationString .= 'mimes:' . implode(',', $acceptedExtensions);
            }
        }

        return $validationString;
    }

    /** @inheritDoc */
    public function mimeTypesValidationConstraints(string $collectionName): string
    {
        return $this->mimeTypesValidationRules($collectionName);
    }

    /** @inheritDoc */
    public function mimeTypesValidationRules(string $collectionName): string
    {
        $mediaConversions = $this->getMediaConversions($collectionName);
        if (empty($mediaConversions)) {
            return '';
        }
        $mediaCollection = $this->getMediaCollection($collectionName);
        $validationString = '';
        if (! empty($mediaCollection->acceptsMimeTypes)) {
            $validationString .= 'mimetypes:' . implode(',', $mediaCollection->acceptsMimeTypes);
        }

        return $validationString;
    }

    /** @inheritDoc */
    public function dimensionValidationConstraints(string $collectionName): string
    {
        return $this->dimensionValidationRules($collectionName);
    }

    /** @inheritDoc */
    public function dimensionValidationRules(string $collectionName): string
    {
        /** @var \Spatie\MediaLibrary\HasMedia\HasMediaTrait $this */
        $maxSizes = $this->collectionMaxSizes($collectionName);
        if (empty($maxSizes)) {
            return '';
        }
        $width = $maxSizes['width'] ? 'min_width=' . $maxSizes['width'] : '';
        $height = $maxSizes['height'] ? 'min_height=' . $maxSizes['height'] : '';
        $separator = $width && $height ? ',' : '';

        return $width || $height ? 'dimensions:' . $width . $separator . $height : '';
    }

    /** @inheritDoc */
    public function sizeValidationRule(): string
    {
        $configMaxFileSize = config('medialibrary.max_file_size');

        return $configMaxFileSize ? 'max:' . $configMaxFileSize : '';
    }
}
