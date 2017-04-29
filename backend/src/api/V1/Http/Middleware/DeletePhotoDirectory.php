<?php

namespace Api\V1\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use Illuminate\Contracts\Filesystem\Factory as Storage;

/**
 * Class DeletePhotoDirectory.
 *
 * @property Storage storage
 * @package Api\V1\Http\Middleware
 */
class DeletePhotoDirectory
{
    /**
     * DeletePhotoDirectory constructor.
     *
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $response = $next($request);

        if ($response->isSuccessful()) {
            $directoryPath = $request->photo->directory_path ?? $request->published_photo->directory_path ?? null;
            if (!is_null($directoryPath)) {
                $this->storage->disk('public')->deleteDirectory($directoryPath);
            }
        }

        return $response;
    }
}
