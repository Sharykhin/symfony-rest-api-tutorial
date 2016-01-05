<?php

namespace Acme\BlogBundle\Tests;

use Doctrine\Common\DataFixtures\Executor\MongoDBExecutor as Executor,
    Doctrine\Common\DataFixtures\Purger\MongoDBPurger as Purger,
    Doctrine\Common\DataFixtures\Loader,
    Doctrine\Common\DataFixtures\ReferenceRepository,
    Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


abstract class AbstractTest extends WebTestCase
{
    /**
     * Array of fixtures to load.
     */
    protected $fixtures = [];

    protected $dm;

    protected $container;

    protected $client;

    /**
     * Setup test environment
     */
    public function setUp()
    {

        $kernel = static::createKernel(['environment' => 'test', 'debug' => false]);
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->dm = $this->container->get('doctrine_mongodb')->getManager();

        if ($this->fixtures) {
            $this->loadFixtures($this->fixtures, false);
        }

        $this->client = static::createClient();
    }

    /**
     * Load fixtures
     *
     * @param array   $fixtures names of _fixtures to load
     * @param boolean $append   append data, or replace?
     */
    protected function loadFixtures($fixtures = array(), $append = true)
    {
        $defaultFixtures = false;

        $loader = new Loader();
        $refRepo = new ReferenceRepository($this->dm);

        foreach ((array) $fixtures as $name) {
            $fixture = new $name();
            $fixture->setReferenceRepository($refRepo);
            $loader->addFixture($fixture);
        }

        $purger = new Purger();
        $executor = new Executor($this->dm, $purger);
        $executor->execute($loader->getFixtures(), $append);
    }
}