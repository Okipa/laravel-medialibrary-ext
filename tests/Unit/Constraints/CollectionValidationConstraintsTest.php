<?php

namespace Spatie\MediaLibrary\Tests\Unit\Extension\UrlGenerator;

use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\Tests\Support\TestModels\TestModel;
use Spatie\MediaLibrary\Tests\TestCase;

class CollectionValidationConstraintsTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_no_constraint_when_non_existing_collection()
    {
        $testModel = new class extends TestModel
        {
            public function registerMediaCollections()
            {
                $this->addMediaConversion('thumb')->crop(Manipulations::CROP_CENTER, 60, 20);
            }
        };
        $validationConstraints = $testModel->validationConstraints('logo');
        $this->assertEquals([], $validationConstraints);
    }

    /**
     * @test
     */
    public function it_returns_only_size_constraint_with_non_existent_conversions()
    {
        config()->set('medialibrary.max_file_size', 1000);
        $validationConstraints = (new TestModel)->validationConstraints('avatar');
        $this->assertEquals(['max:1000'], $validationConstraints);
    }

    /**
     * @test
     */
    public function it_can_return_no_constraint()
    {
        config()->set('medialibrary.max_file_size', null);
        $validationConstraints = (new TestModel)->validationConstraints('avatar');
        $this->assertEquals([], $validationConstraints);
    }

    /**
     * @test
     */
    public function it_can_return_only_dimension_constraints()
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
        $validationConstraints = $testModel->validationConstraints('logo');
        $this->assertEquals(['dimensions:min_width=60,min_height=20'], $validationConstraints);
    }

    /**
     * @test
     */
    public function it_can_return_only_mime_types_and_mimes_constraints()
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
        $validationConstraints = $testModel->validationConstraints('logo');
        $this->assertEquals(['mimes:jpeg,jpg,jpe,png', 'mimetypes:image/jpeg,image/png'], $validationConstraints);
    }

    /**
     * @test
     */
    public function it_can_return_only_size_constraint()
    {
        config()->set('medialibrary.max_file_size', 1000);
        $validationConstraints = (new TestModel)->validationConstraints('avatar');
        $this->assertEquals(['max:1000'], $validationConstraints);
    }

    /**
     * @test
     */
    public function it_can_return_all_constraints()
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
        $validationConstraints = $testModel->validationConstraints('logo');
        $this->assertEquals(
            ['mimes:jpeg,jpg,jpe,png', 'mimetypes:image/jpeg,image/png', 'dimensions:min_width=60,min_height=20', 'max:1000'],
            $validationConstraints
        );
    }
}
