<?php

namespace Acme\BlogBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\BlogBundle\Document\Page;

class LoadPageData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $page = new Page();
        $page->setTitle('testing page title');
        $page->setBody('testing page body');

        $manager->persist($page);
        $manager->flush();
    }
}