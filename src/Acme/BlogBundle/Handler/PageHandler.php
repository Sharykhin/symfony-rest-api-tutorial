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

    /**
     * Get a list of Pages.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0, $orderby = null)
    {
        return $this->repository->findBy(array(), $orderby, $limit, $offset);
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Edit a Page.
     *
     * @param PageInterface $page
     * @param array         $parameters
     *
     * @return PageInterface
     */
    public function put(PageInterface $page, array $parameters)
    {
        return $this->processForm($page, $parameters, 'PUT');
    }
    /**
     * Partially update a Page.
     *
     * @param PageInterface $page
     * @param array         $parameters
     *
     * @return PageInterface
     */
    public function patch(PageInterface $page, array $parameters)
    {
        return $this->processForm($page, $parameters, 'PATCH');
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