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


    public function testGet()
    {

        $page = new Page();
        $page->setTitle('testTitle');
        $page->setBody('testBody');
        $this->dm->persist($page);
        $this->dm->flush();

        $url = '/api/v1/pages/'. $page->getId().'.json';

        $this->client->request('GET', $url, [], [], ['accept' => 'application/json']);
        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals('testTitle', $content['title']);
        $this->assertEquals('testBody', $content['body']);

    }
}