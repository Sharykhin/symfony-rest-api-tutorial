<?php

namespace Acme\BlogBundle\Tests\Handler;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageHandlerTest extends WebTestCase
{

    /**
     * @dataProvider urlProvider
     */
    public function testGet($url)
    {
        $client = self::createClient();
        $client->request('GET', $url, [], [] ,$server = ['CONTENT_TYPE'=>'application/json']);
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }

    public function urlProvider()
    {
        return array(
            array('/api/v1/pages/568b68a33dfd592f2b8b4567')
        );
    }
}