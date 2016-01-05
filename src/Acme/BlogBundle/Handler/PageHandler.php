<?php

namespace Acme\BlogBundle\Handler;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Acme\BlogBundle\Model\PageInterface;
use Doctrine\Common\Persistence\ObjectManager;


class PageHandler implements PageHandlerInterface
{

    private $repository;

    private $entityClass;

    private $om;

    public function __construct(DocumentRepository $repository, ObjectManager $om, $entityClass)
    {
        $this->repository = $repository;
        $this->om = $om;
        $this->entityClass = $entityClass;
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function post(array $parameters)
    {
        return $this->createPage(new $this->entityClass(), $parameters);
    }

    private function createPage(PageInterface $page, array $parameters)
    {
        $page->setTitle($parameters['title']);
        $page->setBody($parameters['body']);
        $this->om->persist($page);
        $this->om->flush();
        return $page;
    }
}