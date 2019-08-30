<?php

namespace Spatie\MediaLibrary\Tests\Feature\Models\Media;

use Carbon\Carbon;
use Spatie\MediaLibrary\Tests\TestCase;
use Spatie\MediaLibrary\Exceptions\InvalidConversion;
use Spatie\MediaLibrary\Exceptions\UrlCannotBeDetermined;

class GetUrlTest extends TestCase
{
    /** @test */
    public function it_can_get_an_url_of_an_original_item()
    {
        $media = $this->testModel->addMedia($this->getTestJpg())->toMediaCollection();

        $this->assertEquals($media->getUrl(), "/media/{$media->id}/test.jpg");
    }

    /** @test */
    public function it_can_get_a_versioned_url_of_an_original_item()
    {
        config()->set('medialibrary.image_name_versioning', true);

        $media = $this->testModel->addMedia($this->getTestJpg())->toMediaCollection();

        $this->assertEquals($media->getUrl(), "/media/{$media->id}/test.jpg?id=" . $media->updated_at->timestamp);
    }

    /** @test */
    public function it_can_get_an_url_of_a_derived_image()
    {
        $media = $this->testModelWithConversion->addMedia($this->getTestJpg())->toMediaCollection();

        $conversionName = 'thumb';

        $this->assertEquals("/media/{$media->id}/conversions/test-{$conversionName}.jpg", $media->getUrl($conversionName));
    }

    /** @test */
    public function it_returns_an_exception_when_getting_an_url_for_an_unknown_conversion()
    {
        $media = $this->testModel->addMedia($this->getTestJpg())->toMediaCollection();

        $this->expectException(InvalidConversion::class);

        $media->getUrl('unknownConversionName');
    }

    /** @test */
    public function it_can_get_the_full_url_of_an_original_item()
    {
        $media = $this->testModel->addMedia($this->getTestJpg())->toMediaCollection();

        $this->assertEquals($media->getFullUrl(), "http://localhost/media/{$media->id}/test.jpg");
    }

    /** @test */
    public function it_can_get_the_full_url_of_a_derived_image()
    {
        $media = $this->testModelWithConversion->addMedia($this->getTestJpg())->toMediaCollection();

        $conversionName = 'thumb';

        $this->assertEquals("http://localhost/media/{$media->id}/conversions/test-{$conversionName}.jpg", $media->getFullUrl($conversionName));
    }

    /** @test */
    public function it_throws_an_exception_when_trying_to_get_a_temporary_url_on_local_disk()
    {
        $media = $this->testModelWithConversion->addMedia($this->getTestJpg())->toMediaCollection();

        $this->expectException(UrlCannotBeDetermined::class);

        $media->getTemporaryUrl(Carbon::now()->addMinutes(5));
    }
}
