<?php

namespace Acme\BlogBundle\Handler;

use Acme\BlogBundle\Model\PageInterface;
use Doctrine\ODM\MongoDB\DocumentRepository;

class PageHandler implements PageInterface
{

    private $repository;

    public function __construct(DocumentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }
}