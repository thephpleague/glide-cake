<?php

namespace League\Glide\Responses;

use Cake\Network\Request;
use Cake\Network\Response;
use League\Flysystem\FilesystemInterface;

class CakeResponseFactory implements ResponseFactoryInterface
{
    protected $request;

    public function __construct(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * Create the response.
     * @param  FilesystemInterface $cache The cache file system.
     * @param  string              $path  The cached file path.
     * @return Response            The response object.
     */
    public function create(FilesystemInterface $cache, $path)
    {
        $stream = $cache->readStream($path);

        $contentType = $cache->getMimetype($path);
        $contentLength = (string) $cache->getSize($path);
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

        if ($this->request) {
            $response = $response->withModified((new \DateTime())->setTimestamp($cache->getTimestamp($path)));
            $response->checkNotModified($this->request);
        }
        return $response;
    }
}
