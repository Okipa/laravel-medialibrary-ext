<?php

namespace Spatie\MediaLibrary\Tests\Unit\Extension\UrlGenerator;

use Spatie\MediaLibrary\Tests\Support\TestModels\TestModel;
use Spatie\MediaLibrary\Tests\TestCase;

class SizeValidationRuleTest extends TestCase
{
    /** @test */
    public function it_return_none_when_it_is_not_defined()
    {
        config()->set('medialibrary.max_file_size', null);
        $validationConstraint = (new TestModel)->sizeValidationRule();
        $this->assertEquals('', $validationConstraint);
    }

    /** @test */
    public function it_can_return_size_constraint()
    {
        config()->set('medialibrary.max_file_size', 1000000);
        $captionString = (new TestModel)->sizeValidationRule();
        $this->assertEquals('max:1000', $captionString);
    }
}
