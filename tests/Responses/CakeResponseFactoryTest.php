<?php

namespace League\Glide\Responses;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class CakeResponseFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInstance()
    {
        $this->assertInstanceOf(
            'League\Glide\Responses\CakeResponseFactory',
            new CakeResponseFactory()
        );
    }

    public function testCreate()
    {
        $cache = new Filesystem(
            new Local(dirname(__DIR__))
        );

        $factory = new CakeResponseFactory();
        /** @var \Cake\Http\Response|\Cake\Network\Response $response */
        $response = $factory->create($cache, 'kayaks.jpg');

        $this->assertInstanceOf('Cake\Network\Response', $response);
        $this->assertEquals('image/jpeg', $response->type());
        $this->assertEquals('5175', $response->getHeaderLine('Content-Length'));
        $this->assertContains(gmdate('D, d M Y H:i', strtotime('+1 years')), $response->getHeaderLine('Expires'));
        $this->assertEquals('max-age=31536000, public', $response->getHeaderLine('Cache-Control'));
    }
}
