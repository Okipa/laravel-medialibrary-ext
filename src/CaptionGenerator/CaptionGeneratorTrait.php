<?php

namespace Spatie\MediaLibrary\CaptionGenerator;

use Illuminate\Support\Arr;

trait CaptionGeneratorTrait
{
    /** @inheritDoc */
    public function constraintsLegend(string $collectionName): string
    {
        return $this->constraintsCaption($collectionName);
    }

    /** @inheritDoc */
    public function constraintsCaption(string $collectionName): string
    {
        if (! $this->getMediaCollection($collectionName)) {
            return '';
        }
        $dimensionsCaption = $this->dimensionsCaption($collectionName);
        $mimeTypesCaption = $this->mimeTypesCaption($collectionName);
        $sizeCaption = $this->sizeCaption();

        return $dimensionsCaption
            . ($dimensionsCaption && ($mimeTypesCaption || $sizeCaption) ? ' ' : '')
            . $mimeTypesCaption
            . ($mimeTypesCaption && $sizeCaption ? ' ' : '') . $sizeCaption;
    }

    /** @inheritDoc */
    public function dimensionsCaption(string $collectionName): string
    {
        /** @var \Spatie\MediaLibrary\HasMedia\HasMediaTrait $this */
        $sizes = $this->collectionMaxSizes($collectionName);
        $width = Arr::get($sizes, 'width');
        $height = Arr::get($sizes, 'height');
        $legend = '';
        if ($width && $height) {
            $legend = (string) __('medialibrary::medialibrary.constraints.dimensions.width.min', [
                    'width' => $width,
                ]) . ' ' . __('medialibrary::medialibrary.constraints.dimensions.height.min', [
                    'height' => $height,
                ]);
        } elseif ($width && ! $height) {
            $legend = (string) __('medialibrary::medialibrary.constraints.dimensions.width.min', [
                'width' => $width,
            ]);
        } elseif (! $width && $height) {
            $legend = (string) __('medialibrary::medialibrary.constraints.dimensions.height.min', [
                'height' => $height,
            ]);
        }

        return $legend;
    }

    /** @inheritDoc */
    public function mimeTypesCaption(string $collectionName): string
    {
        $mediaCollection = $this->getMediaCollection($collectionName);
        $legendString = '';
        if (! empty($mediaCollection->acceptsMimeTypes)) {
            $extensions = $this->extensionsFromMimeTypes($mediaCollection->acceptsMimeTypes);
            $extensionsString = implode(',', $extensions);
            $extensionsString = str_replace(',', ', ', $extensionsString);
            $legendString .= trans_choice('medialibrary::medialibrary.constraints.types', count($extensions), [
                'types' => $extensionsString,
            ]);
        }

        return $legendString;
    }

    /** @inheritDoc */
    public function sizeCaption(): string
    {
        $configMaxFileSize = config('medialibrary.max_file_size');

        return $configMaxFileSize
            ? (string) __('medialibrary::medialibrary.constraints.size.max', ['size' => $configMaxFileSize / 1000])
            : '';
    }

    /** @inheritDoc */
    public function dimensionsLegend(string $collectionName): string
    {
        return $this->dimensionsCaption($collectionName);
    }

    /** @inheritDoc */
    public function mimeTypesLegend(string $collectionName): string
    {
        return $this->mimeTypesCaption($collectionName);
    }
}
