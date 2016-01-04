<?php

namespace Acme\BlogBundle\Tests\Handler;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Acme\BlogBundle\Handler\PageHandler;
use Acme\BlogBundle\Model\PageInterface;

class PageHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var PageHandler */
    protected $pageHandler;

    public function testGet()
    {
        $id = "568a0bcec7a80ff4ee9cc70b";
        $page = $this->getPage();
        $this->repository->expects($this->once())
            ->method('find')
            ->with($this->equalTo($id))
            ->will($this->returnValue($page));

        $this->pageHandler->get($id);
    }
}