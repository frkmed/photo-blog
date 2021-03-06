<?php

namespace Core\DataProviders\Photo\Subscribers;

use Core\DataProviders\Photo\PhotoDataProvider;
use Core\Models\Photo;
use Core\Models\Tag;
use Core\Models\Thumbnail;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class PhotoDataProviderSubscriber.
 *
 * @property ConnectionInterface dbConnection
 * @package Core\DataProviders\Photo\Subscribers
 */
class PhotoDataProviderSubscriber
{
    /**
     * PhotoDataProviderSubscriber constructor.
     *
     * @param ConnectionInterface $dbConnection
     */
    public function __construct(ConnectionInterface $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            PhotoDataProvider::class . '@beforeGetById',
            static::class . '@onBeforeGet'
        );

        $events->listen(
            PhotoDataProvider::class . '@beforeGetFirst',
            static::class . '@onBeforeGet'
        );

        $events->listen(
            PhotoDataProvider::class . '@beforeGet',
            static::class . '@onBeforeGet'
        );

        $events->listen(
            PhotoDataProvider::class . '@beforePaginate',
            static::class . '@onBeforeGet'
        );

        $events->listen(
            PhotoDataProvider::class . '@afterSave',
            static::class . '@onAfterSave'
        );
    }

    /**
     * Handle before get event.
     *
     * @param $query
     * @param array $options
     * @return void
     */
    public function onBeforeGet(Builder $query, array $options)
    {
        $query->with('exif', 'thumbnails', 'tags');
    }

    /**
     * Handle after save event.
     *
     * @param Photo $photo
     * @param array $attributes
     * @param array $options
     * @return void
     */
    public function onAfterSave(Photo $photo, array $attributes = [], array $options = [])
    {
        if (!array_key_exists('save', $options)) {
            return;
        }

        if (in_array('exif', $options['save']) && array_key_exists('exif', $attributes)) {
            $this->savePhotoExif($photo, $attributes['exif']);
        }

        if (in_array('thumbnails', $options['save']) && array_key_exists('thumbnails', $attributes)) {
            $this->savePhotoThumbnails($photo, $attributes['thumbnails']);
        }

        if (in_array('tags', $options['save']) && array_key_exists('tags', $attributes)) {
            $this->savePhotoTags($photo, $attributes['tags']);
        }
    }

    /**
     * Save photo exif.
     *
     * @param Photo $photo
     * @param array $attributes
     */
    private function savePhotoExif(Photo $photo, array $attributes)
    {
        $photo->exif()->delete();

        $exif = $photo->exif()->create($attributes);

        $photo->exif = $exif;
    }

    /**
     * Save photo thumbnails.
     *
     * @param Photo $photo
     * @param array $records
     */
    private function savePhotoThumbnails(Photo $photo, array $records)
    {
        $photo->thumbnails()->detach();

        $thumbnails = $photo->thumbnails()->createMany($records);

        $photo->thumbnails = new Collection($thumbnails);

        Thumbnail::deleteAllWithoutRelations();
    }

    /**
     * Save photo tags.
     *
     * @param Photo $photo
     * @param array $records
     */
    private function savePhotoTags(Photo $photo, array $records)
    {
        $photo->tags()->detach();

        // Attach existing tags.
        foreach ($records as $key => $attributes) {
            $tag = Tag::whereValue($attributes['value'])->first();
            if (!is_null($tag)) {
                $existingTags[] = $tag;
                $photo->tags()->attach($tag->id);
                unset($records[$key]);
            }
        }

        $newTags = $photo->tags()->createMany($records);

        $photo->tags = (new Collection($newTags))->merge($existingTags ?? []);

        Tag::deleteAllWithoutRelations();
    }
}
