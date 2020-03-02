<?php

namespace Spatie\MediaLibrary\Tests\Unit\Extension\UrlGenerator;

use Spatie\MediaLibrary\Tests\Support\TestModels\TestModel;
use Spatie\MediaLibrary\Tests\TestCase;

class SizeCaptionTest extends TestCase
{
    /** @test */
    public function it_returns_none_when_max_file_size_is_not_defined()
    {
        config()->set('medialibrary.max_file_size', null);
        $sizeCaptionString = (new TestModel)->sizeCaption();
        $this->assertEquals('', $sizeCaptionString);
    }

    /** @test */
    public function it_returns_max_weight_caption()
    {
        config()->set('medialibrary.max_file_size', 1000);
        $sizeCaptionString = (new TestModel)->sizeCaption();
        $this->assertEquals(__('medialibrary::medialibrary.constraints.size.max', [
            'size' => 1000 / 1000000,
        ]), $sizeCaptionString);
    }
}
