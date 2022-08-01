<?php

namespace Okipa\MediaLibraryExt\Tests\Unit\ValidationRules;

use Okipa\MediaLibraryExt\Exceptions\CollectionNotFound;
use Okipa\MediaLibraryExt\Tests\Models\InteractsWithMediaModel;
use Okipa\MediaLibraryExt\Tests\TestCase;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CollectionMimeTypesValidationRulesTest extends TestCase
{
    /** @test */
    public function it_throws_exception_when_it_is_called_with_non_existing_collection(): void
    {
        $this->expectException(CollectionNotFound::class);
        (new InteractsWithMediaModel())->getMediaMimeTypesValidationRules('test');
    }

    /** @test */
    public function it_returns_mime_types_rules_when_declared_in_collection(): void
    {
        $testModel = new class extends InteractsWithMediaModel
        {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')
                    ->acceptsFile(fn (File $file) => true)
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
        $rules = $testModel->getMediaMimeTypesValidationRules('avatar');
        self::assertEquals('mimetypes:image/jpeg,image/png', $rules);
    }

    /** @test */
    public function it_returns_no_collection_mime_types_rules_when_none_declared(): void
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
        $rules = $testModel->getMediaMimeTypesValidationRules('avatar');
        self::assertEquals('', $rules);
    }
}
