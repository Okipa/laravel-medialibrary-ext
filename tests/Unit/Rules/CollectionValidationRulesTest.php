<?php

namespace Spatie\MediaLibrary\Tests\Unit\Extension\UrlGenerator;

use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\Tests\Support\TestModels\TestModel;
use Spatie\MediaLibrary\Tests\TestCase;

class CollectionValidationRulesTest extends TestCase
{
    /** @test */
    public function it_returns_no_rule_when_non_existing_collection()
    {
        $testModel = new class extends TestModel
        {
            public function registerMediaCollections()
            {
                $this->addMediaConversion('thumb')->crop(Manipulations::CROP_CENTER, 60, 20);
            }
        };
        $rules = $testModel->validationRules('logo');
        $this->assertEquals([], $rules);
    }

    /** @test */
    public function it_returns_only_size_rule_with_non_existent_conversions()
    {
        config()->set('medialibrary.max_file_size', 1000);
        $rules = (new TestModel)->validationRules('avatar');
        $this->assertEquals(['max:1000'], $rules);
    }

    /** @test */
    public function it_can_return_no_rules()
    {
        config()->set('medialibrary.max_file_size', null);
        $rules = (new TestModel)->validationRules('avatar');
        $this->assertEquals([], $rules);
    }

    /** @test */
    public function it_can_return_only_dimension_rules()
    {
        config()->set('medialibrary.max_file_size', null);
        $testModel = new class extends TestModel
        {
            public function registerMediaCollections()
            {
                $this->addMediaCollection('logo');
            }

            public function registerMediaConversions(Media $media = null)
            {
                $this->addMediaConversion('thumb')->crop(Manipulations::CROP_CENTER, 60, 20);
            }
        };
        $rules = $testModel->validationRules('logo');
        $this->assertEquals(['dimensions:min_width=60,min_height=20'], $rules);
    }

    /** @test */
    public function it_can_return_only_mime_types_and_mimes_rules()
    {
        config()->set('medialibrary.max_file_size', null);
        $testModel = new class extends TestModel
        {
            public function registerMediaCollections()
            {
                $this->addMediaCollection('logo')->acceptsMimeTypes(['image/jpeg', 'image/png']);
            }

            public function registerMediaConversions(Media $media = null)
            {
                $this->addMediaConversion('thumb');
            }
        };
        $rules = $testModel->validationRules('logo');
        $this->assertEquals(['mimes:jpeg,jpg,jpe,png', 'mimetypes:image/jpeg,image/png'], $rules);
    }

    /** @test */
    public function it_can_return_only_size_rule()
    {
        config()->set('medialibrary.max_file_size', 1000);
        $rules = (new TestModel)->validationRules('avatar');
        $this->assertEquals(['max:1000'], $rules);
    }

    /** @test */
    public function it_can_return_all_rules()
    {
        config()->set('medialibrary.max_file_size', 1000);
        $testModel = new class extends TestModel
        {
            public function registerMediaCollections()
            {
                $this->addMediaCollection('logo')->acceptsMimeTypes(['image/jpeg', 'image/png']);
            }

            public function registerMediaConversions(Media $media = null)
            {
                $this->addMediaConversion('thumb')->crop(Manipulations::CROP_CENTER, 60, 20);
            }
        };
        $rules = $testModel->validationRules('logo');
        $this->assertEquals(
            ['mimes:jpeg,jpg,jpe,png', 'mimetypes:image/jpeg,image/png', 'dimensions:min_width=60,min_height=20', 'max:1000'],
            $rules
        );
    }
}
