<?php

namespace Acme\BlogBundle\Handler;

use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use JMS\DiExtraBundle\Annotation\Service;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Acme\BlogBundle\Form\PageType;
use Acme\BlogBundle\Model\PageInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\BlogBundle\Exception\InvalidFormException;

/**
 * Class PageHandler
 * @package Acme\BlogBundle\Handler
 *
 * @Service ("acme_blog.page.handler", public=true)
 */
class PageHandler implements PageHandlerInterface
{


    /**
     * @param DocumentRepository $repository
     * @param ObjectManager $om
     * @param FormFactoryInterface $formFactory
     * @param $entityClass
     *
     * @InjectParams({
     *      "repository"    =   @Inject("acme_blog.page.repository"),
     *      "om"            =   @Inject("doctrine.odm.mongodb.document_manager"),
     *      "formFactory"   =   @Inject("form.factory"),
     *      "entityClass"   =   @Inject("%acme_blog.document.page.class%")
     * })
     */
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
     * @param int $limit
     * @param int $offset
     * @param null $orderby
     * @return array
     */
    public function all($limit = 5, $offset = 0, $orderby = null)
    {
        return $this->repository->findBy(array(), $orderby, $limit, $offset);
    }

    /**
     * @param $id
     * @return object
     */
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

    /**
     * @param array $parameters
     * @return PageInterface|mixed
     */
    public function post(array $parameters)
    {
        $page = $this->createPage();

        return $this->processForm($page, $parameters, 'POST');
    }

    /**
     * @param PageInterface $page
     */
    public function delete(PageInterface $page)
    {
        $this->om->remove($page);
        $this->om->flush();
    }

    /**
     * @param PageInterface $page
     * @param array $parameters
     * @param string $method
     * @return PageInterface|mixed
     * @throws \Acme\BlogBundle\Exception\InvalidFormException
     */
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

    /**
     * @return mixed
     */
    private function createPage()
    {
        return new $this->entityClass();
    }
}