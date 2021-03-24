<?php

namespace Responses;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Glide\Responses\CakeResponseFactory;
use PHPUnit\Framework\TestCase;

class CakeResponseFactoryTest extends TestCase
{
    public function testCreateInstance()
    {
        self::assertInstanceOf(
            'League\Glide\Responses\CakeResponseFactory',
            new CakeResponseFactory()
        );
    }

    public function testCreate()
    {
        $cache = new Filesystem(
            new LocalFilesystemAdapter(dirname(__DIR__))
        );

        $factory = new CakeResponseFactory();
        $response = $factory->create($cache, 'kayaks.jpg');

        $headers = $response->getHeaders();

        self::assertInstanceOf('Cake\Network\Response', $response);
        self::assertEquals('image/jpeg', $response->getType());
        self::assertEquals('5175', $headers['Content-Length'][0]);
        self::assertStringContainsString(gmdate('D, d M Y H:i', strtotime('+1 years')), $headers['Expires'][0]);
        self::assertEquals('max-age=31536000, public', $headers['Cache-Control'][0]);
    }
}
