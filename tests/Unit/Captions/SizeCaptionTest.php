<?php

namespace Okipa\MediaLibraryExt\Tests\Unit\Extension\UrlGenerator;

use Okipa\MediaLibraryExt\Tests\MediaLibraryExtTestCase;
use Okipa\MediaLibraryExt\Tests\Models\InteractsWithMediaModel;

class SizeCaptionTest extends MediaLibraryExtTestCase
{
    /** @test */
    public function it_returns_none_when_max_file_size_is_not_defined()
    {
        config()->set('medialibrary.max_file_size', null);
        $sizeCaptionString = (new InteractsWithMediaModel)->getMediaSizeCaption();
        $this->assertEquals('', $sizeCaptionString);
    }

    /** @test */
    public function it_returns_max_weight_caption()
    {
        config()->set('medialibrary.max_file_size', 1000000);
        $sizeCaptionString = (new InteractsWithMediaModel)->getMediaSizeCaption();
        $this->assertEquals(__('Max. file size: :size Mb.', [
            'size' => 1,
        ]), $sizeCaptionString);
    }
}
