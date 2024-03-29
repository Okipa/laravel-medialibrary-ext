<?php

namespace Okipa\MediaLibraryExt\Tests\Unit\Captions;

use Okipa\MediaLibraryExt\Tests\Models\InteractsWithMediaModel;
use Okipa\MediaLibraryExt\Tests\TestCase;

class SizeCaptionTest extends TestCase
{
    /** @test */
    public function it_returns_none_when_max_file_size_is_not_defined(): void
    {
        config()->set('media-library.max_file_size', null);
        $sizeCaptionString = (new InteractsWithMediaModel())->getMediaSizeCaption();
        self::assertEquals('', $sizeCaptionString);
    }

    /** @test */
    public function it_returns_max_weight_caption(): void
    {
        config()->set('media-library.max_file_size', (1024 * 1024 * 10));
        $sizeCaptionString = (new InteractsWithMediaModel())->getMediaSizeCaption();
        self::assertEquals(__('Max. file size: :size Mb.', [
            'size' => 10,
        ]), $sizeCaptionString);
    }
}
