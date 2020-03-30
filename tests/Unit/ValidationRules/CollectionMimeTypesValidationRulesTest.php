<?php

namespace Okipa\MediaLibraryExt\Tests\Unit\Extension\UrlGenerator;

use Okipa\MediaLibraryExt\Tests\MediaLibraryExtTestCase;
use Okipa\MediaLibraryExt\Tests\Models\InteractsWithMediaModel;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CollectionMimeTypesValidationRulesTest extends MediaLibraryExtTestCase
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
        $rules = $testModel->getMediaMimeTypesValidationRules('test');
        $this->assertEquals('', $rules);
        ;
    }

    /** @test */
    public function it_returns_mime_types_rules_when_declared_in_collection()
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
        $rules = $testModel->getMediaMimeTypesValidationRules('avatar');
        $this->assertEquals('mimetypes:image/jpeg,image/png', $rules);
    }

    /** @test */
    public function it_returns_no_collection_mime_types_rules_when_none_declared()
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
        $this->assertEquals('', $rules);
    }
}
