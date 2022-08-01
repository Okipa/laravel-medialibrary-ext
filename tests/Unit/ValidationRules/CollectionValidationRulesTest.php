<?php

namespace Okipa\MediaLibraryExt\Tests\Unit\ValidationRules;

use Okipa\MediaLibraryExt\Exceptions\CollectionNotFound;
use Okipa\MediaLibraryExt\Tests\Models\InteractsWithMediaModel;
use Okipa\MediaLibraryExt\Tests\TestCase;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CollectionValidationRulesTest extends TestCase
{
    /** @test */
    public function it_throws_exception_when_it_is_called_with_non_existing_collection(): void
    {
        $this->expectException(CollectionNotFound::class);
        (new InteractsWithMediaModel())->getMediaValidationRules('test');
    }

    /** @test */
    public function it_returns_only_size_rule_with_non_existent_conversions(): void
    {
        config()->set('media-library.max_file_size', (1024 * 1024 * 10));
        $rules = (new InteractsWithMediaModel())->getMediaValidationRules('avatar');
        self::assertEquals(['max:10240'], $rules);
    }

    /** @test */
    public function it_can_return_no_rules(): void
    {
        config()->set('media-library.max_file_size', null);
        $rules = (new InteractsWithMediaModel())->getMediaValidationRules('avatar');
        self::assertEquals([], $rules);
    }

    /** @test */
    public function it_can_return_only_dimension_rules(): void
    {
        config()->set('media-library.max_file_size', null);
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
        $rules = $testModel->getMediaValidationRules('avatar');
        self::assertEquals(['dimensions:min_width=60,min_height=20'], $rules);
    }

    /** @test */
    public function it_can_return_only_mime_types_and_mimes_rules(): void
    {
        config()->set('media-library.max_file_size', null);
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
        $rules = $testModel->getMediaValidationRules('avatar');
        self::assertEquals(['mimes:jpg,jpeg,jpe,png', 'mimetypes:image/jpeg,image/png'], $rules);
    }

    /** @test */
    public function it_can_return_only_size_rule(): void
    {
        config()->set('media-library.max_file_size', (1024 * 1024 * 10));
        $rules = (new InteractsWithMediaModel())->getMediaValidationRules('avatar');
        self::assertEquals(['max:10240'], $rules);
    }

    /** @test */
    public function it_can_return_all_rules(): void
    {
        config()->set('media-library.max_file_size', (1024 * 1024 * 10));
        $testModel = new class extends InteractsWithMediaModel
        {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')->acceptsMimeTypes(['image/jpeg', 'image/png']);
            }

            public function registerMediaConversions(Media $media = null): void
            {
                $this->addMediaConversion('thumb')->crop(Manipulations::CROP_CENTER, 60, 20);
            }
        };
        $rules = $testModel->getMediaValidationRules('avatar');
        self::assertEquals([
            'mimes:jpg,jpeg,jpe,png',
            'mimetypes:image/jpeg,image/png',
            'dimensions:min_width=60,min_height=20',
            'max:10240',
        ], $rules);
    }
}
