<?php

namespace League\Glide\Responses;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;

class CakeResponseFactoryTest extends TestCase
{
    public function testCreateInstance()
    {
        $cakeResponseFactory = new CakeResponseFactory();
        $this->assertInstanceOf(
            'League\Glide\Responses\CakeResponseFactory',
            $cakeResponseFactory
        );
    }

    /**
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function testCreate()
    {
        $cache = new Filesystem(
            new Local(dirname(__DIR__))
        );

        $factory = new CakeResponseFactory();
        /** @var \Cake\Http\Response|\Cake\Network\Response $response */
        $response = $factory->create($cache, 'kayaks.jpg');

        $headers = $response->getHeaders();

        $this->assertInstanceOf('Cake\Http\Response', $response);
        $this->assertEquals('image/jpeg', $headers['Content-Type'][0]);
        $this->assertEquals('5175', $headers['Content-Length'][0]);
        $this->assertStringContainsString(gmdate('D, j M Y H:i:s', strtotime('+1 years')).' GMT', $headers['Expires'][0]);
        $this->assertEquals('max-age=31536000, public', $headers['Cache-Control'][0]);
    }
}
