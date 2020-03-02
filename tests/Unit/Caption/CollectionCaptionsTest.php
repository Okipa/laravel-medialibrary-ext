<?php

namespace Spatie\MediaLibrary\Tests\Unit\Extension\UrlGenerator;

use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\Tests\Support\TestModels\TestModel;
use Spatie\MediaLibrary\Tests\TestCase;

class CollectionCaptionsTest extends TestCase
{
    /** @test */
    public function it_returns_none_with_non_existing_collection()
    {
        config()->set('medialibrary.max_file_size', 1000000);
        $captionString = (new TestModel)->constraintsCaption('logo');
        $this->assertEquals('', $captionString);
    }

    /** @test */
    public function it_returns_only_size_when_with_no_conversions()
    {
        config()->set('medialibrary.max_file_size', 1000000);
        $captionString = (new TestModel)->constraintsCaption('avatar');
        $this->assertEquals(__('medialibrary::medialibrary.constraints.size.max', [
            'size' => 1,
        ]), $captionString);
    }

    /** @test */
    public function it_can_returns_dimensions_and_types_captions()
    {
        config()->set('medialibrary.max_file_size', null);
        $testModel = new class extends TestModel {
            public function registerMediaCollections()
            {
                $this->addMediaCollection('logo')
                    ->acceptsMimeTypes(['image/jpeg', 'image/png'])
                    ->registerMediaConversions(function (Media $media = null) {
                        $this->addMediaConversion('admin-panel')
                            ->crop(Manipulations::CROP_CENTER, 150, 120);
                    });
            }

            public function registerMediaConversions(Media $media = null)
            {
                $this->addMediaConversion('thumb')->crop(Manipulations::CROP_CENTER, 40, 40);
            }
        };
        $captionString = $testModel->constraintsCaption('logo');
        $this->assertEquals(__('medialibrary::medialibrary.constraints.dimensions.width.min', [
                'width' => 150,
            ]) . ' ' . __('medialibrary::medialibrary.constraints.dimensions.height.min', [
                'height' => 120,
            ]) . ' ' . trans_choice('medialibrary::medialibrary.constraints.types', 2, [
                'types' => 'jpeg, jpg, jpe, png',
            ]), $captionString);
    }

    /** @test */
    public function it_can_returns_dimensions_and_size_captions()
    {
        config()->set('medialibrary.max_file_size', 1000000);
        $testModel = new class extends TestModel {
            public function registerMediaConversions(Media $media = null)
            {
                $this->addMediaConversion('card')->crop(Manipulations::CROP_CENTER, 100, 70);
            }
        };
        $captionString = $testModel->constraintsCaption('avatar');

        $this->assertEquals(__('medialibrary::medialibrary.constraints.dimensions.width.min', [
                'width' => 100,
            ]) . ' ' . __('medialibrary::medialibrary.constraints.dimensions.height.min', [
                'height' => 70,
            ]) . ' ' . __('medialibrary::medialibrary.constraints.size.max', [
                'size' => 1,
            ]), $captionString);
    }

    /** @test */
    public function it_can_returns_types_and_size_captions()
    {
        config()->set('medialibrary.max_file_size', 1000000);
        $testModel = new class extends TestModel {
            public function registerMediaCollections()
            {
                $this->addMediaCollection('logo')->acceptsMimeTypes(['image/jpeg', 'image/png']);
            }
        };
        $captionString = $testModel->constraintsCaption('logo');
        $this->assertEquals(trans_choice('medialibrary::medialibrary.constraints.types', 2, [
                'types' => 'jpeg, jpg, jpe, png',
            ]) . ' ' . __('medialibrary::medialibrary.constraints.size.max', [
                'size' => 1,
            ]), $captionString);
    }

    /** @test */
    public function it_can_return_all_captions()
    {
        config()->set('medialibrary.max_file_size', 1000000);
        $testModel = new class extends TestModel {
            public function registerMediaCollections()
            {
                $this->addMediaCollection('logo')->acceptsMimeTypes(['image/jpeg', 'image/png']);
            }

            public function registerMediaConversions(Media $media = null)
            {
                $this->addMediaConversion('card')->crop(Manipulations::CROP_CENTER, 100, 70);
            }
        };
        $captionString = $testModel->constraintsCaption('logo');
        $this->assertEquals(__('medialibrary::medialibrary.constraints.dimensions.width.min', [
                'width' => 100,
            ]) . ' ' . __('medialibrary::medialibrary.constraints.dimensions.height.min', [
                'height' => 70,
            ]) . ' ' . trans_choice('medialibrary::medialibrary.constraints.types', 2, [
                'types' => 'jpeg, jpg, jpe, png',
            ]) . ' ' . __('medialibrary::medialibrary.constraints.size.max', [
                'size' => 1,
            ]), $captionString);
    }
}
