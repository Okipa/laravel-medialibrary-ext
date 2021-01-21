<?php

namespace Okipa\MediaLibraryExt\Tests\Unit\Captions;

use Okipa\MediaLibraryExt\Exceptions\CollectionNotFound;
use Okipa\MediaLibraryExt\Tests\MediaLibraryExtTestCase;
use Okipa\MediaLibraryExt\Tests\Models\InteractsWithMediaModel;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CollectionDimensionsCaptionTest extends MediaLibraryExtTestCase
{
    /** @test */
    public function it_throws_exception_when_it_is_called_with_non_existing_collection(): void
    {
        $this->expectException(CollectionNotFound::class);
        (new InteractsWithMediaModel())->getMediaDimensionsCaption('test');
    }

    /** @test */
    public function it_returns_none_when_it_is_called_with_non_existent_conversions(): void
    {
        $testModel = new class extends InteractsWithMediaModel {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')->acceptsMimeTypes(['image/jpeg', 'image/png']);
            }
        };
        $dimensionsCaptionString = $testModel->getMediaDimensionsCaption('avatar');
        self::assertEquals('', $dimensionsCaptionString);
    }

    /** @test */
    public function it_returns_only_width_dimension_caption_when_only_width_is_declared(): void
    {
        $testModel = new class extends InteractsWithMediaModel {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')->acceptsMimeTypes(['image/jpeg', 'image/png']);
            }

            public function registerMediaConversions(Media $media = null): void
            {
                $this->addMediaConversion('thumb')->width(120);
            }
        };
        $dimensionsCaptionString = $testModel->getMediaDimensionsCaption('avatar');
        self::assertEquals(__('Min. width: :width px.', ['width' => 120]), $dimensionsCaptionString);
    }

    /** @test */
    public function it_returns_only_height_dimension_caption_when_only_height_is_declared(): void
    {
        $testModel = new class extends InteractsWithMediaModel {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')->acceptsMimeTypes(['image/jpeg', 'image/png']);
            }

            public function registerMediaConversions(Media $media = null): void
            {
                $this->addMediaConversion('thumb')->height(30);
            }
        };
        $dimensionsCaptionString = $testModel->getMediaDimensionsCaption('avatar');
        self::assertEquals(__('Min. height: :height px.', ['height' => 30]), $dimensionsCaptionString);
    }

    /** @test */
    public function it_returns_no_dimension_caption_when_no_size_is_declared(): void
    {
        $testModel = new class extends InteractsWithMediaModel {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')->acceptsMimeTypes(['image/jpeg', 'image/png']);
            }

            public function registerMediaConversions(Media $media = null): void
            {
                $this->addMediaConversion('thumb');
            }
        };
        $dimensionsCaptionString = $testModel->getMediaDimensionsCaption('avatar');
        self::assertEquals('', $dimensionsCaptionString);
    }

    /** @test */
    public function it_returns_width_and_height_dimension_caption_when_both_are_declared(): void
    {
        $testModel = new class extends InteractsWithMediaModel {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')
                    ->acceptsFile(fn(File $file) => true)
                    ->acceptsMimeTypes(['image/jpeg', 'image/png'])
                    ->registerMediaConversions(function (Media $media = null) {
                        $this->addMediaConversion('admin-panel')
                            ->crop(Manipulations::CROP_CENTER, 20, 80);
                    });
            }

            public function registerMediaConversions(Media $media = null): void
            {
                $this->addMediaConversion('thumb')->crop(Manipulations::CROP_CENTER, 100, 70);
            }
        };
        $dimensionsCaptionString = $testModel->getMediaDimensionsCaption('avatar');
        self::assertEquals(__('Min. width: :width px.', [
                'width' => 100,
            ]) . ' ' . __('Min. height: :height px.', [
                'height' => 80,
            ]), $dimensionsCaptionString);
    }

    /** @test */
    public function it_does_not_returns_dimensions_caption_when_no_image_declared(): void
    {
        $testModel = new class extends InteractsWithMediaModel {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')
                    ->acceptsFile(fn(File $file) => true)
                    ->acceptsMimeTypes(['application/pdf'])
                    ->registerMediaConversions(function (Media $media = null) {
                        $this->addMediaConversion('admin-panel')
                            ->crop(Manipulations::CROP_CENTER, 20, 80);
                    });
            }

            public function registerMediaConversions(Media $media = null): void
            {
                $this->addMediaConversion('thumb')->crop(Manipulations::CROP_CENTER, 100, 70);
            }
        };
        $dimensionsCaptionString = $testModel->getMediaDimensionsCaption('avatar');
        self::assertEquals('', $dimensionsCaptionString);
    }
}
