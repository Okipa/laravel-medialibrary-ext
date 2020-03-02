<?php

namespace Spatie\MediaLibrary\ValidationConstraintsGenerator;

trait validationConstraintsGeneratorTrait
{
    /** @inheritDoc */
    public function validationConstraints(string $collectionName): array
    {
        $mimesConstraints = $this->mimesValidationConstraints($collectionName);
        $mimeTypeConstraints = $this->mimeTypesValidationConstraints($collectionName);
        $dimensionConstraints = $this->dimensionValidationConstraints($collectionName);
        $sizeConstraint = $this->sizeValidationConstraint();

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
        $mediaConversions = $this->getMediaConversions($collectionName);
        if (empty($mediaConversions)) {
            return '';
        }
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
    public function sizeValidationConstraint(): string
    {
        $configMaxFileSize = config('medialibrary.max_file_size');

        return $configMaxFileSize ? 'max:' . $configMaxFileSize : '';
    }
}
