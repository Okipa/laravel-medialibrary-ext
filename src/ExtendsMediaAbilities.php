<?php

namespace Okipa\MediaLibraryExt;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Okipa\MediaLibraryExt\Exceptions\CollectionNotFound;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\Mime\MimeTypes;

trait ExtendsMediaAbilities
{
    abstract public function getMediaCollection(string $collectionName = 'default'): ?MediaCollection;

    abstract public function registerAllMediaConversions(Media $media = null): void;

    /**
     * @param string $collectionName
     * @return string
     *
     * @throws \Okipa\MediaLibraryExt\Exceptions\CollectionNotFound
     */
    public function getMediaCaption(string $collectionName): string
    {
        $this->checkIfMediaCollectionExist($collectionName);
        $dimensionsCaption = $this->getMediaDimensionsCaption($collectionName);
        $mimeTypesCaption = $this->getMediaMimeTypesCaption($collectionName);
        $sizeCaption = $this->getMediaSizeCaption();

        return $dimensionsCaption
            . ($dimensionsCaption && ($mimeTypesCaption || $sizeCaption) ? ' ' : '')
            . $mimeTypesCaption
            . ($mimeTypesCaption && $sizeCaption ? ' ' : '')
            . $sizeCaption;
    }

    /**
     * @param string $collectionName
     *
     * @throws \Okipa\MediaLibraryExt\Exceptions\CollectionNotFound
     */
    protected function checkIfMediaCollectionExist(string $collectionName): void
    {
        $mediaCollection = $this->getMediaCollection($collectionName);
        if (! $mediaCollection) {
            throw new CollectionNotFound("The collection {$mediaCollection} as not been found.");
        }
    }

    /**
     * @param string $collectionName
     * @return string
     *
     * @throws \Okipa\MediaLibraryExt\Exceptions\CollectionNotFound
     */
    public function getMediaDimensionsCaption(string $collectionName): string
    {
        $maxDimensions = $this->getMediaMaxDimensions($collectionName);
        $width = data_get($maxDimensions, 'width');
        $height = data_get($maxDimensions, 'height');
        $caption = '';
        if ($width) {
            $caption .= __('Min. width: :width px.', ['width' => $width]);
        }
        if ($height) {
            $caption .= ($caption ? ' ' : '') . __('Min. height: :height px.', ['height' => $height]);
        }

        return $caption;
    }

    /**
     * @param string $collectionName
     * @return array
     *
     * @throws \Okipa\MediaLibraryExt\Exceptions\CollectionNotFound
     */
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
                'width' => data_get($manipulations, 'width'),
                'height' => data_get($manipulations, 'height'),
            ];
        }

        return $this->getMaxWidthAndMaxHeight($sizes);
    }

    /**
     * @param string $collectionName
     * @return bool
     *
     * @throws \Okipa\MediaLibraryExt\Exceptions\CollectionNotFound
     */
    protected function mediaHasDimensions(string $collectionName): bool
    {
        $mediaCollection = $this->getMediaCollectionOrFail($collectionName);
        $mediaConversions = $this->getAllMediaConversions($collectionName);
        if ($mediaConversions->isEmpty()) {
            return false;
        }

        return ! count(array_filter(
            $mediaCollection->acceptsMimeTypes,
            static fn ($mimeTypes) => ! Str::startsWith($mimeTypes, 'image')
        ));
    }

    /**
     * @param string $collectionName
     * @return \Spatie\MediaLibrary\MediaCollections\MediaCollection
     *
     * @throws \Okipa\MediaLibraryExt\Exceptions\CollectionNotFound
     */
    protected function getMediaCollectionOrFail(string $collectionName = 'default'): MediaCollection
    {
        $mediaCollection = $this->getMediaCollection($collectionName);
        if (! $mediaCollection) {
            throw new CollectionNotFound("The collection {$mediaCollection} as not been found.");
        }

        return $mediaCollection;
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

    /**
     * @param string $collectionName
     * @return string
     *
     * @throws \Okipa\MediaLibraryExt\Exceptions\CollectionNotFound
     */
    public function getMediaMimeTypesCaption(string $collectionName): string
    {
        $mediaCollection = $this->getMediaCollectionOrFail($collectionName);
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
            $extensions[] = (new MimeTypes())->getExtensions($mimeType);
        }

        return array_unique(Arr::flatten($extensions));
    }

    public function getMediaSizeCaption(): string
    {
        $configMaxFileSize = config('media-library.max_file_size');

        return $configMaxFileSize
            ? (string) __('Max. file size: :size Mb.', [
                // 1 Mb = 1 048 576 bytes.
                'size' => round($configMaxFileSize / 1048576, 1),
            ])
            : '';
    }

    /**
     * @param string $collectionName
     * @return array
     *
     * @throws \Okipa\MediaLibraryExt\Exceptions\CollectionNotFound
     */
    public function getMediaValidationRules(string $collectionName): array
    {
        $this->checkIfMediaCollectionExist($collectionName);
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

    /**
     * @param string $collectionName
     * @return string
     *
     * @throws \Okipa\MediaLibraryExt\Exceptions\CollectionNotFound
     */
    public function getMediaMimesValidationRules(string $collectionName): string
    {
        $mediaCollection = $this->getMediaCollectionOrFail($collectionName);
        $validationString = '';
        if (! empty($mediaCollection->acceptsMimeTypes)) {
            $acceptedExtensions = $this->getExtensionsFromMimeTypes($mediaCollection->acceptsMimeTypes);
            if (! empty($acceptedExtensions)) {
                $validationString .= 'mimes:' . implode(',', $acceptedExtensions);
            }
        }

        return $validationString;
    }

    /**
     * @param string $collectionName
     * @return string
     *
     * @throws \Okipa\MediaLibraryExt\Exceptions\CollectionNotFound
     */
    public function getMediaMimeTypesValidationRules(string $collectionName): string
    {
        $mediaCollection = $this->getMediaCollectionOrFail($collectionName);
        $mediaConversions = $this->getAllMediaConversions($collectionName);
        if ($mediaConversions->isEmpty()) {
            return '';
        }
        $validationString = '';
        if (! empty($mediaCollection->acceptsMimeTypes)) {
            $validationString .= 'mimetypes:' . implode(',', $mediaCollection->acceptsMimeTypes);
        }

        return $validationString;
    }

    /**
     * @param string $collectionName
     * @return string
     *
     * @throws \Okipa\MediaLibraryExt\Exceptions\CollectionNotFound
     */
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
        $configMaxFileSize = config('media-library.max_file_size');

        // 1 kilobyte = 1 024 bytes.
        return $configMaxFileSize ? 'max:' . round($configMaxFileSize / 1024) : '';
    }
}
