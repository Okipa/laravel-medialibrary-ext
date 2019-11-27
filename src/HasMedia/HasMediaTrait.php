<?php

namespace Spatie\MediaLibrary\HasMedia;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Conversion\Conversion;
use Spatie\MediaLibrary\Events\CollectionHasBeenCleared;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\InvalidBase64Data;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\MimeTypeNotAllowed;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\UnreachableUrl;
use Spatie\MediaLibrary\Exceptions\MediaCannotBeDeleted;
use Spatie\MediaLibrary\Exceptions\MediaCannotBeUpdated;
use Spatie\MediaLibrary\FileAdder\FileAdder;
use Spatie\MediaLibrary\FileAdder\FileAdderFactory;
use Spatie\MediaLibrary\LegendGenerator\LegendGeneratorTrait;
use Spatie\MediaLibrary\MediaCollection\MediaCollection;
use Spatie\MediaLibrary\MediaRepository;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\ValidationConstraintsGenerator\validationConstraintsGeneratorTrait;
use Symfony\Component\Mime\MimeTypes;

trait HasMediaTrait
{
    use LegendGeneratorTrait;
    use validationConstraintsGeneratorTrait;
    /** @var Conversion[] */
    public $mediaConversions = [];
    /** @var MediaCollection[] */
    public $mediaCollections = [];
    /** @var bool */
    protected $deletePreservingMedia = false;
    /** @var array */
    protected $unAttachedMediaLibraryItems = [];

    public static function bootHasMediaTrait()
    {
        static::deleting(function (HasMedia $entity) {
            if ($entity->shouldDeletePreservingMedia()) {
                return;
            }

            if (in_array(SoftDeletes::class, class_uses_recursive($entity))) {
                if (! $entity->forceDeleting) {
                    return;
                }
            }

            $entity->media()->get()->each->delete();
        });
    }

    /**
     * Set the polymorphic relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function media()
    {
        return $this->morphMany(config('medialibrary.media_model'), 'model');
    }

    /**
     * Add a file from a request.
     *
     * @param string $key
     *
     * @return \Spatie\MediaLibrary\FileAdder\FileAdder
     */
    public function addMediaFromRequest(string $key)
    {
        return app(FileAdderFactory::class)->createFromRequest($this, $key);
    }

    /**
     * Add multiple files from a request by keys.
     *
     * @param string[] $keys
     *
     * @return \Spatie\MediaLibrary\FileAdder\FileAdder[]
     */
    public function addMultipleMediaFromRequest(array $keys)
    {
        return app(FileAdderFactory::class)->createMultipleFromRequest($this, $keys);
    }

    /**
     * Add all files from a request.
     *
     * @return \Spatie\MediaLibrary\FileAdder\FileAdder[]
     */
    public function addAllMediaFromRequest()
    {
        return app(FileAdderFactory::class)->createAllFromRequest($this);
    }

    /**
     * Add a remote file to the medialibrary.
     *
     * @param string $url
     * @param string|array ...$allowedMimeTypes
     *
     * @return \Spatie\MediaLibrary\FileAdder\FileAdder
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded
     */
    public function addMediaFromUrl(string $url, ...$allowedMimeTypes)
    {
        if (! $stream = @fopen($url, 'r')) {
            throw UnreachableUrl::create($url);
        }

        $temporaryFile = tempnam(sys_get_temp_dir(), 'media-library');
        file_put_contents($temporaryFile, $stream);

        $this->guardAgainstInvalidMimeType($temporaryFile, $allowedMimeTypes);

        $filename = basename(parse_url($url, PHP_URL_PATH));
        $filename = str_replace('%20', ' ', $filename);

        if ($filename === '') {
            $filename = 'file';
        }

        $mediaExtension = explode('/', mime_content_type($temporaryFile));

        if (! Str::contains($filename, '.')) {
            $filename = "{$filename}.{$mediaExtension[1]}";
        }

        return app(FileAdderFactory::class)
            ->create($this, $temporaryFile)
            ->usingName(pathinfo($filename, PATHINFO_FILENAME))
            ->usingFileName($filename);
    }

    protected function guardAgainstInvalidMimeType(string $file, ...$allowedMimeTypes)
    {
        $allowedMimeTypes = Arr::flatten($allowedMimeTypes);

        if (empty($allowedMimeTypes)) {
            return;
        }

        $validation = Validator::make(
            ['file' => new File($file)],
            ['file' => 'mimetypes:' . implode(',', $allowedMimeTypes)]
        );

        if ($validation->fails()) {
            throw MimeTypeNotAllowed::create($file, $allowedMimeTypes);
        }
    }

    /**
     * Add a base64 encoded file to the medialibrary.
     *
     * @param string $base64data
     * @param string|array ...$allowedMimeTypes
     *
     * @return \Spatie\MediaLibrary\FileAdder\FileAdder
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded
     * @throws InvalidBase64Data
     */
    public function addMediaFromBase64(string $base64data, ...$allowedMimeTypes): FileAdder
    {
        // strip out data uri scheme information (see RFC 2397)
        if (strpos($base64data, ';base64') !== false) {
            [$_, $base64data] = explode(';', $base64data);
            [$_, $base64data] = explode(',', $base64data);
        }

        // strict mode filters for non-base64 alphabet characters
        if (base64_decode($base64data, true) === false) {
            throw InvalidBase64Data::create();
        }

        // decoding and then reencoding should not change the data
        if (base64_encode(base64_decode($base64data)) !== $base64data) {
            throw InvalidBase64Data::create();
        }

        $binaryData = base64_decode($base64data);

        // temporarily store the decoded data on the filesystem to be able to pass it to the fileAdder
        $tmpFile = tempnam(sys_get_temp_dir(), 'medialibrary');
        file_put_contents($tmpFile, $binaryData);

        $this->guardAgainstInvalidMimeType($tmpFile, $allowedMimeTypes);

        $file = app(FileAdderFactory::class)->create($this, $tmpFile);

        return $file;
    }

    /**
     * Copy a file to the medialibrary.
     *
     * @param string|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return \Spatie\MediaLibrary\FileAdder\FileAdder
     */
    public function copyMedia($file)
    {
        return $this->addMedia($file)->preservingOriginal();
    }

    /*
     * Determine if there is media in the given collection.
     */

    /**
     * Add a file to the medialibrary.
     *
     * @param string|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return \Spatie\MediaLibrary\FileAdder\FileAdder
     */
    public function addMedia($file)
    {
        return app(FileAdderFactory::class)->create($this, $file);
    }

    public function hasMedia(string $collectionName = 'default'): bool
    {
        return count($this->getMedia($collectionName)) ? true : false;
    }

    /**
     * Get media collection by its collectionName.
     *
     * @param string $collectionName
     * @param array|callable $filters
     *
     * @return \Illuminate\Support\Collection
     */
    public function getMedia(string $collectionName = 'default', $filters = []): Collection
    {
        return app(MediaRepository::class)->getCollection($this, $collectionName, $filters);
    }

    /*
     * Get the url of the image for the given conversionName
     * for first media for the given collectionName.
     * If no profile is given, return the source's url.
     */

    public function getFirstMediaUrl(string $collectionName = 'default', string $conversionName = ''): string
    {
        $media = $this->getFirstMedia($collectionName);

        if (! $media) {
            return $this->getFallbackMediaUrl($collectionName) ?: '';
        }

        return $media->getUrl($conversionName);
    }

    /*
     * Get the url of the image for the given conversionName
     * for first media for the given collectionName.
     *
     * If no profile is given, return the source's url.
     */

    public function getFirstMedia(string $collectionName = 'default', array $filters = []): ?Media
    {
        $media = $this->getMedia($collectionName, $filters);

        return $media->first();
    }

    public function getFallbackMediaUrl(string $collectionName = 'default'): string
    {
        return optional($this->getMediaCollection($collectionName))->fallbackUrl ?? '';
    }

    public function getMediaCollection(string $collectionName = 'default'): ?MediaCollection
    {
        $this->registerMediaCollections();

        return collect($this->mediaCollections)
            ->first(function (MediaCollection $collection) use ($collectionName) {
                return $collection->name === $collectionName;
            });
    }

    public function registerMediaCollections()
    {
    }

    /*
     * Get the url of the image for the given conversionName
     * for first media for the given collectionName.
     * If no profile is given, return the source's url.
     */

    public function getFirstTemporaryUrl(
        DateTimeInterface $expiration,
        string $collectionName = 'default',
        string $conversionName = ''
    ): string {
        $media = $this->getFirstMedia($collectionName);

        if (! $media) {
            return $this->getFallbackMediaUrl($collectionName) ?: '';
        }

        return $media->getTemporaryUrl($expiration, $conversionName);
    }

    public function getFirstMediaPath(string $collectionName = 'default', string $conversionName = ''): string
    {
        $media = $this->getFirstMedia($collectionName);

        if (! $media) {
            return $this->getFallbackMediaPath($collectionName) ?: '';
        }

        return $media->getPath($conversionName);
    }

    public function getFallbackMediaPath(string $collectionName = 'default'): string
    {
        return optional($this->getMediaCollection($collectionName))->fallbackPath ?? '';
    }

    /**
     * Update a media collection by deleting and inserting again with new values.
     *
     * @param array $newMediaArray
     * @param string $collectionName
     *
     * @return \Illuminate\Support\Collection
     * @throws \Spatie\MediaLibrary\Exceptions\MediaCannotBeUpdated
     */
    public function updateMedia(array $newMediaArray, string $collectionName = 'default'): Collection
    {
        $this->removeMediaItemsNotPresentInArray($newMediaArray, $collectionName);

        $mediaClass = config('medialibrary.media_model');
        $mediaInstance = new $mediaClass();
        $keyName = $mediaInstance->getKeyName();

        return collect($newMediaArray)
            ->map(function (array $newMediaItem) use ($collectionName, $mediaClass, $keyName) {
                static $orderColumn = 1;

                $currentMedia = $mediaClass::findOrFail($newMediaItem[$keyName]);

                if ($currentMedia->collection_name !== $collectionName) {
                    throw MediaCannotBeUpdated::doesNotBelongToCollection($collectionName, $currentMedia);
                }

                if (array_key_exists('name', $newMediaItem)) {
                    $currentMedia->name = $newMediaItem['name'];
                }

                if (array_key_exists('custom_properties', $newMediaItem)) {
                    $currentMedia->custom_properties = $newMediaItem['custom_properties'];
                }

                $currentMedia->order_column = $orderColumn++;

                $currentMedia->save();

                return $currentMedia;
            });
    }

    protected function removeMediaItemsNotPresentInArray(array $newMediaArray, string $collectionName = 'default')
    {
        $this->getMedia($collectionName)
            ->reject(function (Media $currentMediaItem) use ($newMediaArray) {
                return in_array($currentMediaItem->getKey(),
                    array_column($newMediaArray, $currentMediaItem->getKeyName()));
            })
            ->each->delete();
    }

    /**
     * Remove all media in the given collection except some.
     *
     * @param string $collectionName
     * @param \Spatie\MediaLibrary\Models\Media[]|\Illuminate\Support\Collection $excludedMedia
     *
     * @return $this
     */
    public function clearMediaCollectionExcept(string $collectionName = 'default', $excludedMedia = [])
    {
        if ($excludedMedia instanceof Media) {
            $excludedMedia = collect()->push($excludedMedia);
        }

        $excludedMedia = collect($excludedMedia);

        if ($excludedMedia->isEmpty()) {
            return $this->clearMediaCollection($collectionName);
        }

        $this->getMedia($collectionName)
            ->reject(function (Media $media) use ($excludedMedia) {
                return $excludedMedia->where($media->getKeyName(), $media->getKey())->count();
            })
            ->each->delete();

        if ($this->mediaIsPreloaded()) {
            unset($this->media);
        }

        if ($this->getMedia($collectionName)->isEmpty()) {
            event(new CollectionHasBeenCleared($this, $collectionName));
        }

        return $this;
    }

    /*
     * Add a conversion.
     */

    /**
     * Remove all media in the given collection.
     *
     * @param string $collectionName
     *
     * @return $this
     */
    public function clearMediaCollection(string $collectionName = 'default'): self
    {
        $this->getMedia($collectionName)
            ->each->delete();

        event(new CollectionHasBeenCleared($this, $collectionName));

        if ($this->mediaIsPreloaded()) {
            unset($this->media);
        }

        return $this;
    }

    protected function mediaIsPreloaded(): bool
    {
        return $this->relationLoaded('media');
    }

    /**
     * Delete the associated media with the given id.
     * You may also pass a media object.
     *
     * @param int|\Spatie\MediaLibrary\Models\Media $mediaId
     *
     * @throws \Spatie\MediaLibrary\Exceptions\MediaCannotBeDeleted
     */
    public function deleteMedia($mediaId)
    {
        if ($mediaId instanceof Media) {
            $mediaId = $mediaId->getKey();
        }

        $media = $this->media->find($mediaId);

        if (! $media) {
            throw MediaCannotBeDeleted::doesNotBelongToModel($mediaId, $this);
        }

        $media->delete();
    }

    public function addMediaConversion(string $name): Conversion
    {
        $conversion = Conversion::create($name);

        $this->mediaConversions[] = $conversion;

        return $conversion;
    }

    public function addMediaCollection(string $name): MediaCollection
    {
        $mediaCollection = MediaCollection::create($name);

        $this->mediaCollections[] = $mediaCollection;

        return $mediaCollection;
    }

    /**
     * Delete the model, but preserve all the associated media.
     *
     * @return bool
     */
    public function deletePreservingMedia(): bool
    {
        $this->deletePreservingMedia = true;

        return $this->delete();
    }

    /**
     * Determines if the media files should be preserved when the media object gets deleted.
     *
     * @return bool
     */
    public function shouldDeletePreservingMedia()
    {
        return $this->deletePreservingMedia ?? false;
    }

    /**
     * Cache the media on the object.
     *
     * @param string $collectionName
     *
     * @return mixed
     */
    public function loadMedia(string $collectionName)
    {
        $collection = $this->exists
            ? $this->media
            : collect($this->unAttachedMediaLibraryItems)->pluck('media');

        return $collection
            ->filter(function (Media $mediaItem) use ($collectionName) {
                if ($collectionName == '') {
                    return true;
                }

                return $mediaItem->collection_name === $collectionName;
            })
            ->sortBy('order_column')
            ->values();
    }

    public function prepareToAttachMedia(Media $media, FileAdder $fileAdder)
    {
        $this->unAttachedMediaLibraryItems[] = compact('media', 'fileAdder');
    }

    public function processUnattachedMedia(callable $callable)
    {
        foreach ($this->unAttachedMediaLibraryItems as $item) {
            $callable($item['media'], $item['fileAdder']);
        }

        $this->unAttachedMediaLibraryItems = [];
    }

    public function registerAllMediaConversions(Media $media = null)
    {
        $this->registerMediaCollections();

        collect($this->mediaCollections)->each(function (MediaCollection $mediaCollection) use ($media) {
            $actualMediaConversions = $this->mediaConversions;

            $this->mediaConversions = [];

            ($mediaCollection->mediaConversionRegistrations)($media);

            $preparedMediaConversions = collect($this->mediaConversions)
                ->each(function (Conversion $conversion) use ($mediaCollection) {
                    $conversion->performOnCollections($mediaCollection->name);
                })
                ->values()
                ->toArray();

            $this->mediaConversions = array_merge($actualMediaConversions, $preparedMediaConversions);
        });

        $this->registerMediaConversions($media);
    }

    public function registerMediaConversions(Media $media = null)
    {
    }

    /**
     * Get registered collection max width and max height from its name.
     *
     * @param string $collectionName
     *
     * @return array
     */
    public function collectionMaxSizes(string $collectionName): array
    {
        if (! $this->shouldHandleDimensions($collectionName)) {
            return [];
        }
        $mediaConversions = $this->getMediaConversions($collectionName);
        $sizes = [];
        foreach ($mediaConversions as $key => $mediaConversion) {
            $manipulations = head($mediaConversion->getManipulations()->toArray());
            $sizes[$key] = [
                'width'  => Arr::get($manipulations, 'width'),
                'height' => Arr::get($manipulations, 'height'),
            ];
        }

        return $this->getMaxWidthAndMaxHeight($sizes);
    }

    /**
     * Check if the given media collection should handle dimensions, according to its declared accepted mime types.
     *
     * @param string $collectionName
     *
     * @return bool
     */
    public function shouldHandleDimensions(string $collectionName): bool
    {
        $mediaCollection = $this->getMediaCollection($collectionName);
        if (! $mediaCollection) {
            return false;
        }
        $mediaConversions = $this->getMediaConversions($collectionName);
        if (empty($mediaConversions)) {
            return false;
        }

        return ! count(array_filter($mediaCollection->acceptsMimeTypes, function ($mimeTypes) {
            return ! Str::startsWith($mimeTypes, 'image');
        }));
    }

    /**
     * Get declared conversions from a media collection name.
     *
     * @param string $collectionName
     *
     * @return array
     */
    public function getMediaConversions(string $collectionName): array
    {
        $this->registerAllMediaConversions();
        return Arr::where($this->mediaConversions, function (Conversion $conversion) use ($collectionName) {
            return $conversion->shouldBePerformedOn($collectionName);
        });
    }

    /**
     * Calculate max width and max height from sizes array.
     *
     * @param array $sizes
     *
     * @return array
     */
    protected function getMaxWidthAndMaxHeight(array $sizes): array
    {
        $width = ! empty($sizes) ? max(Arr::pluck($sizes, 'width')) : null;
        $height = ! empty($sizes) ? max(Arr::pluck($sizes, 'height')) : null;

        return compact('width', 'height');
    }

    /**
     * Get related extensions from a mime types list.
     *
     * @param array $mimeTypes
     *
     * @return array
     */
    protected function extensionsFromMimeTypes(array $mimeTypes): array
    {
        $extensions = [];
        foreach ($mimeTypes as $mimeType) {
            $extensions[] = (new MimeTypes)->getExtensions($mimeType);
        }

        return array_unique(Arr::flatten($extensions));
    }
}
