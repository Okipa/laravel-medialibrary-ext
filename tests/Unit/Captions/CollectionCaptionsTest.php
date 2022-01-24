<?php

namespace Okipa\MediaLibraryExt\Tests\Unit\Captions;

use Okipa\MediaLibraryExt\Exceptions\CollectionNotFound;
use Okipa\MediaLibraryExt\Tests\TestCase;
use Okipa\MediaLibraryExt\Tests\Models\InteractsWithMediaModel;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CollectionCaptionsTest extends TestCase
{
    /** @test */
    public function it_throws_exception_when_it_is_called_with_non_existing_collection(): void
    {
        $this->expectException(CollectionNotFound::class);
        (new InteractsWithMediaModel())->getMediaCaption('test');
    }

    /** @test */
    public function it_returns_only_size_when_with_no_conversions(): void
    {
        config()->set('media-library.max_file_size', 1000000);
        $captionString = (new InteractsWithMediaModel())->getMediaCaption('avatar');
        self::assertEquals(__('Max. file size: :size Mb.', [
            'size' => 1,
        ]), $captionString);
    }

    /** @test */
    public function it_can_returns_dimensions_and_types_captions(): void
    {
        config()->set('media-library.max_file_size', null);
        $testModel = new class extends InteractsWithMediaModel {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')
                    ->acceptsMimeTypes(['image/jpeg', 'image/png'])
                    ->registerMediaConversions(function (Media $media = null) {
                        $this->addMediaConversion('admin-panel')
                            ->crop(Manipulations::CROP_CENTER, 150, 120);
                    });
            }

            public function registerMediaConversions(Media $media = null): void
            {
                $this->addMediaConversion('thumb')->crop(Manipulations::CROP_CENTER, 40, 40);
            }
        };
        $captionString = $testModel->getMediaCaption('avatar');
        self::assertEquals(
            __('Min. width: :width px.', ['width' => 150]) . ' '
            . __('Min. height: :height px.', ['height' => 120]) . ' '
            . trans_choice(
                '{1}Accepted type: :types.|[2,*]Accepted types: :types.',
                2,
                ['types' => 'jpg, jpeg, jpe, png']
            ),
            $captionString
        );
    }

    /** @test */
    public function it_can_returns_dimensions_and_size_captions(): void
    {
        config()->set('media-library.max_file_size', 1000000);
        $testModel = new class extends InteractsWithMediaModel {
            public function registerMediaConversions(Media $media = null): void
            {
                $this->addMediaConversion('card')->crop(Manipulations::CROP_CENTER, 100, 70);
            }
        };
        $captionString = $testModel->getMediaCaption('avatar');
        self::assertEquals(__('Min. width: :width px.', [
                'width' => 100,
            ]) . ' ' . __('Min. height: :height px.', [
                'height' => 70,
            ]) . ' ' . __('Max. file size: :size Mb.', [
                'size' => 1,
            ]), $captionString);
    }

    /** @test */
    public function it_can_returns_types_and_size_captions(): void
    {
        config()->set('media-library.max_file_size', 1000000);
        $testModel = new class extends InteractsWithMediaModel {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')->acceptsMimeTypes(['image/jpeg', 'image/png']);
            }
        };
        $captionString = $testModel->getMediaCaption('avatar');
        self::assertEquals(trans_choice(
            '{1}Accepted type: :types.|[2,*]Accepted types: :types.',
            2,
            [
                    'types' => 'jpg, jpeg, jpe, png',
                ]
        ) . ' ' . __('Max. file size: :size Mb.', [
                'size' => 1,
            ]), $captionString);
    }

    /** @test */
    public function it_can_return_all_captions(): void
    {
        config()->set('media-library.max_file_size', 1000000);
        $testModel = new class extends InteractsWithMediaModel {
            public function registerMediaCollections(): void
            {
                $this->addMediaCollection('avatar')->acceptsMimeTypes(['image/jpeg', 'image/png']);
            }

            public function registerMediaConversions(Media $media = null): void
            {
                $this->addMediaConversion('card')->crop(Manipulations::CROP_CENTER, 100, 70);
            }
        };
        $captionString = $testModel->getMediaCaption('avatar');
        self::assertEquals(__('Min. width: :width px.', [
                'width' => 100,
            ]) . ' ' . __('Min. height: :height px.', [
                'height' => 70,
            ]) . ' ' . trans_choice(
                '{1}Accepted type: :types.|[2,*]Accepted types: :types.',
                2,
                [
                    'types' => 'jpg, jpeg, jpe, png',
                ]
            ) . ' ' . __('Max. file size: :size Mb.', [
                'size' => 1,
            ]), $captionString);
    }
}
