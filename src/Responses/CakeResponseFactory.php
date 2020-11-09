<?php

namespace League\Glide\Responses;

use Cake\Http\Response;
use Laminas\Diactoros\CallbackStream;
use League\Flysystem\FilesystemInterface;

class CakeResponseFactory implements ResponseFactoryInterface
{
    /**
     * Create the response.
     *
     * @param  FilesystemInterface  $cache  The cache file system.
     * @param  string  $path  The cached file path.
     *
     * @return \Cake\Http\Response|\Cake\Network\Response            The response object.
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function create(FilesystemInterface $cache, $path)
    {
        $stream = $cache->readStream($path);

        $contentType = $cache->getMimetype($path);
        $contentLength = (string) $cache->getSize($path);
        $cacheControl = 'max-age=31536000, public';
        $expires = date_create('+1 years')->format('D, d M Y H:i:s').' GMT';

        $response = new Response();

        return $response->withType($contentType)
            ->withLength($contentLength)
            ->withAddedHeader('Cache-Control', $cacheControl)
            ->withExpires($expires)
            ->withBody(new CallbackStream(function () use ($stream) {
                rewind($stream);
                fpassthru($stream);
                fclose($stream);
            }));
    }
}
