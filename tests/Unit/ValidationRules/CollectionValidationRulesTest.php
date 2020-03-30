<?php

namespace Okipa\MediaLibraryExt\Tests\Unit\Extension\UrlGenerator;

use Okipa\MediaLibraryExt\Tests\MediaLibraryExtTestCase;
use Okipa\MediaLibraryExt\Tests\Models\InteractsWithMediaModel;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CollectionValidationRulesTest extends MediaLibraryExtTestCase
{
    /** @test */
    public function it_returns_no_rule_when_non_existing_collection()
    {
        $testModel = new class extends InteractsWithMediaModel {
            public function registerMediaCollections(): void
            {
                $this->addMediaConversion('thumb')->crop(Manipulations::CROP_CENTER, 60, 20);
            }
        };
        $rules = $testModel->getMediaValidationRules('avatar');
        $this->assertEquals([], $rules);
    }

    /** @test */
    public function it_returns_only_size_rule_with_non_existent_conversions()
    {
        config()->set('medialibrary.max_file_size', 1000000);
        $rules = (new InteractsWithMediaModel)->getMediaValidationRules('avatar');
        $this->assertEquals(['max:1000'], $rules);
    }

    /** @test */
    public function it_can_return_no_rules()
    {
        config()->set('medialibrary.max_file_size', null);
        $rules = (new InteractsWithMediaModel)->getMediaValidationRules('avatar');
        $this->assertEquals([], $rules);
    }

    /** @test */
    public function it_can_return_only_dimension_rules()
    {
        config()->set('medialibrary.max_file_size', null);
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
        $rules = $testModel->getMediaValidationRules('avatar');
        $this->assertEquals(['dimensions:min_width=60,min_height=20'], $rules);
    }

    /** @test */
    public function it_can_return_only_mime_types_and_mimes_rules()
    {
        config()->set('medialibrary.max_file_size', null);
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
        $rules = $testModel->getMediaValidationRules('avatar');
        $this->assertEquals(['mimes:jpeg,jpg,jpe,png', 'mimetypes:image/jpeg,image/png'], $rules);
    }

    /** @test */
    public function it_can_return_only_size_rule()
    {
        config()->set('medialibrary.max_file_size', 1000000);
        $rules = (new InteractsWithMediaModel)->getMediaValidationRules('avatar');
        $this->assertEquals(['max:1000'], $rules);
    }

    /** @test */
    public function it_can_return_all_rules()
    {
        config()->set('medialibrary.max_file_size', 1000000);
        $testModel = new class extends InteractsWithMediaModel {
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
        $this->assertEquals([
            'mimes:jpeg,jpg,jpe,png',
            'mimetypes:image/jpeg,image/png',
            'dimensions:min_width=60,min_height=20',
            'max:1000',
        ], $rules);
    }
}
