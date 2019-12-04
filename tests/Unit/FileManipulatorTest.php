<?php

namespace Spatie\MediaLibrary\Tests\Unit;

use Spatie\MediaLibrary\Conversion\Conversion;
use Spatie\MediaLibrary\FileManipulator;
use Spatie\MediaLibrary\Tests\TestCase;

class FileManipulatorTest extends TestCase
{
    protected $conversionName = 'test';

    /** @var \Spatie\MediaLibrary\Conversion\Conversion */
    protected $conversion;

    public function setUp(): void
    {
        parent::setUp();

        $this->conversion = new Conversion($this->conversionName);
    }

    /** @test */
    public function it_does_not_perform_manipulations_if_not_necessary()
    {
        $imageFile = $this->getTestJpg();
        $media = $this->testModelWithoutMediaConversions->addMedia($this->getTestJpg())->toMediaCollection();

        $conversionTempFile = (new FileManipulator)->performManipulations(
            $media,
            $this->conversion->withoutManipulations(),
            $imageFile
        );

        $this->assertEquals($imageFile, $conversionTempFile);
    }
}
