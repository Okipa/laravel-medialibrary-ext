<?php

namespace Okipa\MediaLibraryExt\Tests\Unit;

use Okipa\MediaLibraryExt\Tests\MediaLibraryExtTestCase;
use Okipa\MediaLibraryExt\Tests\Models\InteractsWithMediaModel;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\MediaCollections\File;

class CollectionMaxSizeTest extends MediaLibraryExtTestCase
{
    /** @test */
    public function it_returns_none_when_it_is_called_with_non_existing_collection()
    {
        $testModel = new class extends InteractsWithMediaModel
        {
            public function registerMediaCollections(): void
            {
                $this->addMediaConversion('thumb')->crop(Manipulations::CROP_CENTER, 60, 20);
            }
        };
        $maxDimensions = $testModel->getMediaMaxDimensions('test');
        $this->assertEquals([], $maxDimensions);
    }

    /** @test */
    public function it_returns_none_when_it_is_called_with_non_existing_conversions()
    {
        $testModel = new class extends InteractsWithMediaModel
        {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')->acceptsMimeTypes(['image/jpeg', 'image/png']);
            }
        };
        $maxDimensions = $testModel->getMediaMaxDimensions('avatar');
        $this->assertEquals([], $maxDimensions);
    }

    /** @test */
    public function it_returns_global_conversion_max_sizes_when_no_collection_conversions_declared()
    {
        $testModel = new class extends InteractsWithMediaModel
        {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar');
            }

            public function registerMediaConversions(Media $media = null): void
            {
                $this->addMediaConversion('thumb')->crop(Manipulations::CROP_CENTER, 60, 20);
            }
        };
        $maxDimensions = $testModel->getMediaMaxDimensions('avatar');
        $this->assertEquals(60, $maxDimensions['width']);
        $this->assertEquals(20, $maxDimensions['height']);
    }

    /** @test */
    public function it_returns_only_width_when_only_width_is_declared()
    {
        $testModel = new class extends InteractsWithMediaModel
        {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')->acceptsMimeTypes(['image/jpeg', 'image/png']);
            }

            public function registerMediaConversions(Media $media = null): void
            {
                $this->addMediaConversion('thumb')->width(120);
            }
        };
        $maxDimensions = $testModel->getMediaMaxDimensions('avatar');
        $this->assertEquals(120, $maxDimensions['width']);
        $this->assertNull($maxDimensions['height']);
    }

    /** @test */
    public function it_returns_only_height_when_only_height_is_declared()
    {
        $testModel = new class extends InteractsWithMediaModel
        {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')->acceptsMimeTypes(['image/jpeg', 'image/png']);
            }

            public function registerMediaConversions(Media $media = null): void
            {
                $this->addMediaConversion('thumb')->height(30);
            }
        };
        $maxDimensions = $testModel->getMediaMaxDimensions('avatar');
        $this->assertNull($maxDimensions['width']);
        $this->assertEquals(30, $maxDimensions['height']);
    }

    /** @test */
    public function it_returns_no_size_when_none_is_declared()
    {
        $testModel = new class extends InteractsWithMediaModel
        {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')->acceptsMimeTypes(['image/jpeg', 'image/png']);
            }

            public function registerMediaConversions(Media $media = null): void
            {
                $this->addMediaConversion('thumb');
            }
        };
        $maxDimensions = $testModel->getMediaMaxDimensions('avatar');
        $this->assertNull($maxDimensions['width']);
        $this->assertNull($maxDimensions['height']);
    }

    /** @test */
    public function it_returns_collection_conversions_max_sizes_when_no_global_conversions_declared()
    {
        $testModel = new class extends InteractsWithMediaModel
        {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')
                    ->acceptsMimeTypes(['image/jpeg', 'image/png'])
                    ->registerMediaConversions(function (Media $media = null) {
                        $this->addMediaConversion('admin-panel')
                            ->crop(Manipulations::CROP_CENTER, 100, 140);
                        $this->addMediaConversion('mail')
                            ->crop(Manipulations::CROP_CENTER, 120, 100);
                    });
            }

            public function registerMediaConversions(Media $media = null): void
            {
                $this->addMediaConversion('thumb')->crop(Manipulations::CROP_CENTER, 40, 40);
            }
        };
        $maxDimensions = $testModel->getMediaMaxDimensions('avatar');
        $this->assertEquals(120, $maxDimensions['width']);
        $this->assertEquals(140, $maxDimensions['height']);
    }

    /** @test */
    public function it_returns_global_and_collection_conversions_max_sizes_when_both_are_declared()
    {
        $testModel = new class extends InteractsWithMediaModel
        {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')
                    ->acceptsFile(function (File $file) {
                        return true;
                    })
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
        $maxDimensions = $testModel->getMediaMaxDimensions('avatar');
        $this->assertEquals(100, $maxDimensions['width']);
        $this->assertEquals(80, $maxDimensions['height']);
    }

    /** @test */
    public function it_returns_empty_array_when_no_image_declared()
    {
        $testModel = new class extends InteractsWithMediaModel
        {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')
                    ->acceptsFile(function (File $file) {
                        return true;
                    })
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
        $maxDimensions = $testModel->getMediaMaxDimensions('avatar');
        $this->assertEquals([], $maxDimensions);
    }

    /** @test */
    public function it_returns_empty_array_when_mime_type_different_from_image_is_declared()
    {
        $testModel = new class extends InteractsWithMediaModel
        {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')
                    ->acceptsFile(function (File $file) {
                        return true;
                    })
                    ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf'])
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
        $maxDimensions = $testModel->getMediaMaxDimensions('avatar');
        $this->assertEquals([], $maxDimensions);
    }
}
