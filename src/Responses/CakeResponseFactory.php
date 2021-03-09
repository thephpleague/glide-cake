<?php

namespace League\Glide\Responses;

use Cake\Network\Response;
use League\Flysystem\FilesystemOperator;

class CakeResponseFactory implements ResponseFactoryInterface
{
    /**
     * Create the response.
     * @param  FilesystemOperator $cache The cache file system.
     * @param  string              $path  The cached file path.
     * @return Response            The response object.
     */
    public function create(FilesystemOperator $cache, $path)
    {
        $stream = $cache->readStream($path);

        $contentType = $cache->mimeType($path);
        $contentLength = (string) $cache->fileSize($path);
        $cacheControl = 'max-age=31536000, public';
        $expires = date_create('+1 years')->format('D, d M Y H:i:s').' GMT';

        $response = new Response();
        $response->type($contentType);
        $response->header('Content-Length', $contentLength);
        $response->header('Cache-Control', $cacheControl);
        $response->header('Expires', $expires);
        $response->body(function () use ($stream) {
            rewind($stream);
            fpassthru($stream);
            fclose($stream);
        });

        return $response;
    }
}
