<?php

namespace Acme\BlogBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;

class PageController extends FOSRestController
{
    public function getPageAction($id)
    {
        $page = $this->container->get('doctrine_mongodb')->getRepository('AcmeBlogBundle:Page')->find($id);
        if (!$page) {
            throw $this->createNotFoundException('No page found for id '. $id);
        }
        return $page;
    }
}