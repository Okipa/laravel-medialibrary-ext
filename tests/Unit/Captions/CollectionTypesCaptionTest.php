<?php

namespace Okipa\MediaLibraryExt\Tests\Unit\Captions;

use Okipa\MediaLibraryExt\Exceptions\CollectionNotFound;
use Okipa\MediaLibraryExt\Tests\MediaLibraryExtTestCase;
use Okipa\MediaLibraryExt\Tests\Models\InteractsWithMediaModel;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CollectionTypesCaptionTest extends MediaLibraryExtTestCase
{
    /** @test */
    public function it_throws_exception_when_it_is_called_with_non_existing_collection(): void
    {
        $this->expectException(CollectionNotFound::class);
        (new InteractsWithMediaModel())->getMediaMimeTypesCaption('test');
    }

    /** @test */
    public function it_returns_no_types_caption_when_none_declared(): void
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
        $dimensionsCaptionString = $testModel->getMediaMimeTypesCaption('avatar');
        self::assertEquals('', $dimensionsCaptionString);
    }

    /** @test */
    public function it_returns_types_caption_when_are_declared(): void
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
        $dimensionsCaptionString = $testModel->getMediaMimeTypesCaption('avatar');
        self::assertEquals(
            trans_choice(
                '{1}Accepted type: :types.|[2,*]Accepted types: :types.',
                3,
                ['types' => 'jpg, jpeg, jpe, png']
            ),
            $dimensionsCaptionString
        );
    }

    /** @test */
    public function it_removes_duplicated_types(): void
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
        $dimensionsCaptionString = $testModel->getMediaMimeTypesCaption('avatar');
        self::assertEquals(
            trans_choice(
                '{1}Accepted type: :types.|[2,*]Accepted types: :types.',
                1,
                ['types' => 'wav']
            ),
            $dimensionsCaptionString
        );
    }
}
