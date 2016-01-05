<?php

namespace Acme\BlogBundle\Handler;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Acme\BlogBundle\Form\PageType;
use Acme\BlogBundle\Model\PageInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\BlogBundle\Exception\InvalidFormException;


class PageHandler implements PageHandlerInterface
{

    private $repository;

    private $entityClass;

    private $om;

    private $formFactory;

    public function __construct(
        DocumentRepository $repository,
        ObjectManager $om,
        FormFactoryInterface $formFactory,
        $entityClass
    )
    {
        $this->repository = $repository;
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->formFactory = $formFactory;
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function post(array $parameters)
    {
        $page = $this->createPage();

        return $this->processForm($page, $parameters, 'POST');
    }

    private function processForm(PageInterface $page, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new PageType(), $page, ['method' => $method]);
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $page = $form->getData();
            $this->om->persist($page);
            $this->om->flush($page);
            return $page;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    private function createPage()
    {
        return new $this->entityClass();
    }
}