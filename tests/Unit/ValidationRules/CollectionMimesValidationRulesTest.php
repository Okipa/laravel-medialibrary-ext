<?php

namespace Okipa\MediaLibraryExt\Tests\Unit\ValidationRules;

use Okipa\MediaLibraryExt\Exceptions\CollectionNotFound;
use Okipa\MediaLibraryExt\Tests\MediaLibraryExtTestCase;
use Okipa\MediaLibraryExt\Tests\Models\InteractsWithMediaModel;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CollectionMimesValidationRulesTest extends MediaLibraryExtTestCase
{
    /** @test */
    public function it_throws_exception_when_it_is_called_with_non_existing_collection(): void
    {
        $this->expectException(CollectionNotFound::class);
        (new InteractsWithMediaModel())->getMediaMimesValidationRules('test');
    }

    /** @test */
    public function it_returns_mimes_rules_when_declared_in_collection(): void
    {
        $testModel = new class extends InteractsWithMediaModel {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')
                    ->acceptsFile(fn(File $file) => true)
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
        $rules = $testModel->getMediaMimesValidationRules('avatar');
        self::assertEquals('mimes:jpg,jpeg,jpe,png,pdf', $rules);
    }

    /** @test */
    public function it_returns_no_collection_mimes_rules_when_none_declared(): void
    {
        $testModel = new class extends InteractsWithMediaModel {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar');
            }

            public function registerMediaConversions(Media $media = null): void
            {
                $this->addMediaConversion('thumb')->crop(Manipulations::CROP_CENTER, 60, 20);
            }
        };
        $rules = $testModel->getMediaMimesValidationRules('avatar');
        self::assertEquals('', $rules);
    }

    /** @test */
    public function it_removes_duplicated_mimes(): void
    {
        $testModel = new class extends InteractsWithMediaModel {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')
                    ->acceptsFile(fn(File $file) => true)
                    ->acceptsMimeTypes(['audio/wav', 'audio/wave', 'audio/x-wav'])
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
        $rules = $testModel->getMediaMimesValidationRules('avatar');
        self::assertEquals('mimes:wav', $rules);
    }
}
