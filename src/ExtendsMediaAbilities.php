<?php

namespace Okipa\MediaLibraryExt;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Conversions\Conversion;
use Symfony\Component\Mime\MimeTypes;

trait ExtendsMediaAbilities
{
    public function getMediaCaption(string $collectionName): string
    {
        if (! $this->getMediaCollection($collectionName)) {
            return '';
        }
        $dimensionsCaption = $this->getMediaDimensionsCaption($collectionName);
        $mimeTypesCaption = $this->getMediaMimeTypesCaption($collectionName);
        $sizeCaption = $this->getMediaSizeCaption();

        return $dimensionsCaption . ($dimensionsCaption && ($mimeTypesCaption || $sizeCaption) ? ' ' : '')
            . $mimeTypesCaption . ($mimeTypesCaption && $sizeCaption ? ' ' : '') . $sizeCaption;
    }

    public function getMediaDimensionsCaption(string $collectionName): string
    {
        $maxDimensions = $this->getMediaMaxDimensions($collectionName);
        $width = Arr::get($maxDimensions, 'width');
        $height = Arr::get($maxDimensions, 'height');
        if ($width && $height) {
            return (string) __('Min. width: :width px.', ['width' => $width]) . ' '
                . __('Min. height: :height px.', ['height' => $height]);
        }
        if ($width && ! $height) {
            return (string) __('Min. width: :width px.', ['width' => $width]);
        }
        if (! $width && $height) {
            return (string) __('Min. height: :height px.', ['height' => $height]);
        }

        return '';
    }

    protected function getMediaMaxDimensions(string $collectionName): array
    {
        if (! $this->mediaHasDimensions($collectionName)) {
            return [];
        }
        $mediaConversions = $this->getAllMediaConversions($collectionName);
        $sizes = [];
        foreach ($mediaConversions as $key => $mediaConversion) {
            /** @var \Spatie\MediaLibrary\Conversions\Conversion $mediaConversion */
            $manipulations = head($mediaConversion->getManipulations()->toArray());
            $sizes[$key] = [
                'width' => Arr::get($manipulations, 'width'),
                'height' => Arr::get($manipulations, 'height'),
            ];
        }

        return $this->getMaxWidthAndMaxHeight($sizes);
    }

    protected function mediaHasDimensions(string $collectionName): bool
    {
        $mediaCollection = $this->getMediaCollection($collectionName);
        if (! $mediaCollection) {
            return false;
        }
        $mediaConversions = $this->getAllMediaConversions($collectionName);
        if ($mediaConversions->isEmpty()) {
            return false;
        }

        return ! count(array_filter($mediaCollection->acceptsMimeTypes, function ($mimeTypes) {
            return ! Str::startsWith($mimeTypes, 'image');
        }));
    }

    protected function getAllMediaConversions(string $collectionName): Collection
    {
        $this->registerAllMediaConversions();

        return collect(Arr::where($this->mediaConversions, function (Conversion $conversion) use ($collectionName) {
            return $conversion->shouldBePerformedOn($collectionName);
        }));
    }

    protected function getMaxWidthAndMaxHeight(array $sizes): array
    {
        $width = ! empty($sizes) ? max(Arr::pluck($sizes, 'width')) : null;
        $height = ! empty($sizes) ? max(Arr::pluck($sizes, 'height')) : null;

        return compact('width', 'height');
    }

    public function getMediaMimeTypesCaption(string $collectionName): string
    {
        $mediaCollection = $this->getMediaCollection($collectionName);
        if (! empty($mediaCollection->acceptsMimeTypes)) {
            $extensions = $this->getExtensionsFromMimeTypes($mediaCollection->acceptsMimeTypes);
            $extensionsString = implode(',', $extensions);
            $extensionsString = str_replace(',', ', ', $extensionsString);

            return trans_choice(
                '{1}Accepted type: :types.|[2,*]Accepted types: :types.',
                count($extensions),
                ['types' => $extensionsString]
            );
        }

        return '';
    }

    protected function getExtensionsFromMimeTypes(array $mimeTypes): array
    {
        $extensions = [];
        foreach ($mimeTypes as $mimeType) {
            $extensions[] = (new MimeTypes)->getExtensions($mimeType);
        }

        return array_unique(Arr::flatten($extensions));
    }

    public function getMediaSizeCaption(): string
    {
        $configMaxFileSize = config('medialibrary.max_file_size');

        return $configMaxFileSize
            ? (string) __('Max. file size: :size Mb.', [
                'size' => round($configMaxFileSize / 1000000, 1),
            ])
            : '';
    }

    public function getMediaValidationRules(string $collectionName): array
    {
        if (! $this->getMediaCollection($collectionName)) {
            return [];
        }
        $mimesConstraints = $this->getMediaMimesValidationRules($collectionName);
        $mimeTypeConstraints = $this->getMediaMimeTypesValidationRules($collectionName);
        $dimensionConstraints = $this->getMediaDimensionValidationRules($collectionName);
        $sizeConstraint = $this->getMediaSizeValidationRule();

        return array_values(array_filter([
            $mimesConstraints,
            $mimeTypeConstraints,
            $dimensionConstraints,
            $sizeConstraint,
        ]));
    }

    public function getMediaMimesValidationRules(string $collectionName): string
    {
        $mediaCollection = $this->getMediaCollection($collectionName);
        $validationString = '';
        if (! empty($mediaCollection->acceptsMimeTypes)) {
            $acceptedExtensions = $this->getExtensionsFromMimeTypes($mediaCollection->acceptsMimeTypes);
            if (! empty($acceptedExtensions)) {
                $validationString .= 'mimes:' . implode(',', $acceptedExtensions);
            }
        }

        return $validationString;
    }

    public function getMediaMimeTypesValidationRules(string $collectionName): string
    {
        $mediaConversions = $this->getAllMediaConversions($collectionName);
        if ($mediaConversions->isEmpty()) {
            return '';
        }
        $mediaCollection = $this->getMediaCollection($collectionName);
        $validationString = '';
        if (! empty($mediaCollection->acceptsMimeTypes)) {
            $validationString .= 'mimetypes:' . implode(',', $mediaCollection->acceptsMimeTypes);
        }

        return $validationString;
    }

    public function getMediaDimensionValidationRules(string $collectionName): string
    {
        $maxDimensions = $this->getMediaMaxDimensions($collectionName);
        if (empty($maxDimensions)) {
            return '';
        }
        $width = $maxDimensions['width'] ? 'min_width=' . $maxDimensions['width'] : '';
        $height = $maxDimensions['height'] ? 'min_height=' . $maxDimensions['height'] : '';
        $separator = $width && $height ? ',' : '';

        return $width || $height ? 'dimensions:' . $width . $separator . $height : '';
    }

    public function getMediaSizeValidationRule(): string
    {
        $configMaxFileSize = config('medialibrary.max_file_size');

        return $configMaxFileSize ? 'max:' . round($configMaxFileSize / 1000) : '';
    }
}
