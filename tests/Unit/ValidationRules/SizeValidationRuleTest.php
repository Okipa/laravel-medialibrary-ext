<?php

namespace Okipa\MediaLibraryExt\Tests\Unit\Extension\UrlGenerator;

use Okipa\MediaLibraryExt\Tests\MediaLibraryExtTestCase;
use Okipa\MediaLibraryExt\Tests\Models\InteractsWithMediaModel;

class SizeValidationRuleTest extends MediaLibraryExtTestCase
{
    /** @test */
    public function it_return_none_when_it_is_not_defined()
    {
        config()->set('medialibrary.max_file_size', null);
        $validationConstraint = (new InteractsWithMediaModel)->getMediaSizeValidationRule();
        $this->assertEquals('', $validationConstraint);
    }

    /** @test */
    public function it_can_return_size_constraint()
    {
        config()->set('medialibrary.max_file_size', 1000000);
        $captionString = (new InteractsWithMediaModel)->getMediaSizeValidationRule();
        $this->assertEquals('max:1000', $captionString);
    }
}
