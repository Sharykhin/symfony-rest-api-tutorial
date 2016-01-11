<?php

namespace Acme\BlogBundle\Handler;

use JMS\DiExtraBundle\Annotation\Service;

/**
 * Class TestHandler
 * @Service("acme.blog.test", public=true)
 */
class TestHandler
{

    public function test()
    {
        echo "it works";
    }
}