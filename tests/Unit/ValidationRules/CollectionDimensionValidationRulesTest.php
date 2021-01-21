<?php

namespace Okipa\MediaLibraryExt\Tests\Unit\Extension\UrlGenerator;

use Okipa\MediaLibraryExt\Exceptions\CollectionNotFound;
use Okipa\MediaLibraryExt\Tests\MediaLibraryExtTestCase;
use Okipa\MediaLibraryExt\Tests\Models\InteractsWithMediaModel;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CollectionDimensionValidationRulesTest extends MediaLibraryExtTestCase
{
    /** @test */
    public function it_throws_exception_when_it_is_called_with_non_existing_collection(): void
    {
        $this->expectException(CollectionNotFound::class);
        $testModel = new class extends InteractsWithMediaModel
        {
            public function registerMediaCollections(): void
            {
                $this->addMediaConversion('thumb')->crop(Manipulations::CROP_CENTER, 60, 20);
            }
        };
        $rules = $testModel->getMediaDimensionValidationRules('test');
    }

    /** @test */
    public function it_returns_none_when_it_is_called_with_non_existent_conversions(): void
    {
        $testModel = new class extends InteractsWithMediaModel
        {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')->acceptsMimeTypes(['image/jpeg', 'image/png']);
            }
        };
        $rules = $testModel->getMediaDimensionValidationRules('avatar');
        self::assertEquals('', $rules);
    }

    /** @test */
    public function it_returns_global_conversion_dimension_rules_when_no_collection_conversions_declared(): void
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
        $rules = $testModel->getMediaDimensionValidationRules('avatar');
        self::assertEquals('dimensions:min_width=60,min_height=20', $rules);
    }

    /** @test */
    public function it_returns_only_width_dimension_rule_when_only_width_is_declared(): void
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
        $rules = $testModel->getMediaDimensionValidationRules('avatar');
        self::assertEquals('dimensions:min_width=120', $rules);
    }

    /** @test */
    public function it_returns_only_height_dimension_rule_when_only_height_is_declared(): void
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
        $rules = $testModel->getMediaDimensionValidationRules('avatar');
        self::assertEquals('dimensions:min_height=30', $rules);
    }

    /** @test */
    public function it_returns_no_dimension_rule_when_no_size_is_declared(): void
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
        $rules = $testModel->getMediaDimensionValidationRules('avatar');
        self::assertEquals('', $rules);
    }

    /** @test */
    public function it_returns_collection_dimension_rules_when_no_global_conversions_declared(): void
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
        $rules = $testModel->getMediaDimensionValidationRules('avatar');
        self::assertEquals('dimensions:min_width=120,min_height=140', $rules);
    }

    /** @test */
    public function it_returns_global_and_collection_dimension_rules_when_both_are_declared(): void
    {
        $testModel = new class extends InteractsWithMediaModel
        {
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
        $rules = $testModel->getMediaDimensionValidationRules('avatar');
        self::assertEquals('dimensions:min_width=100,min_height=80', $rules);
    }

    /** @test */
    public function it_does_not_returns_dimension_rules_when_no_image_declared(): void
    {
        $testModel = new class extends InteractsWithMediaModel
        {
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
        $rules = $testModel->getMediaDimensionValidationRules('avatar');
        self::assertEquals('', $rules);
    }
}
