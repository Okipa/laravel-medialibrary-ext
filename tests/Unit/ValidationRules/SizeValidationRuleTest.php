<?php

namespace Okipa\MediaLibraryExt\Tests\Unit\ValidationRules;

use Okipa\MediaLibraryExt\Tests\Models\InteractsWithMediaModel;
use Okipa\MediaLibraryExt\Tests\TestCase;

class SizeValidationRuleTest extends TestCase
{
    /** @test */
    public function it_return_none_when_it_is_not_defined(): void
    {
        config()->set('media-library.max_file_size', null);
        $validationConstraint = (new InteractsWithMediaModel())->getMediaSizeValidationRule();
        self::assertEquals('', $validationConstraint);
    }

    /** @test */
    public function it_can_return_size_constraint(): void
    {
        config()->set('media-library.max_file_size', (1024 * 1024 * 10));
        $captionString = (new InteractsWithMediaModel())->getMediaSizeValidationRule();
        self::assertEquals('max:10240', $captionString);
    }
}
