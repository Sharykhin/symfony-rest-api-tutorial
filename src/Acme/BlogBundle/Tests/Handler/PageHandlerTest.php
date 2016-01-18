<?php

namespace Acme\BlogBundle\Tests\Handler;

use Acme\BlogBundle\Tests\AbstractTest;
use Acme\BlogBundle\Document\Page;

class PageHandlerTest extends AbstractTest
{

    /**
     * fixtures to load before each test
     */
    protected $fixtures = array(
        'Acme\BlogBundle\DataFixtures\MongoDB\LoadPageData'
    );

    public function testPostPageAction()
    {
        $this->client->request(
            'POST',
            '/api/v1/pages.json',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"title":"title1","body":"body1"}'
        );
        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $$response->getContent();
    }
}