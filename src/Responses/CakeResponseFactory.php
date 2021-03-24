<?php

namespace League\Glide\Responses;

use Cake\Http\Response;
use GuzzleHttp\Psr7\Utils;
use League\Flysystem\FilesystemOperator;
use function GuzzleHttp\Psr7\stream_for;

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
        $response = $response
            ->withType($contentType)
            ->withHeader('Content-Length', $contentLength)
            ->withHeader('Cache-Control', $cacheControl)
            ->withHeader('Expires', $expires)
            ->withBody(Utils::streamFor(
                function () use ($stream) {
                    rewind($stream);
                    fpassthru($stream);
                    fclose($stream);
                }
            ));

        return $response;
    }
}
