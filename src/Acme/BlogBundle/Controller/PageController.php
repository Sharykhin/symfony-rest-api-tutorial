<?php

namespace Acme\BlogBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

class PageController extends FOSRestController
{
    /**
     * @Rest\View(templateVar="page")
     *
     * @param $id
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getPageAction($id)
    {
        $page = $this->container->get('acme_blog.page.handler')->get($id);

        if (!$page) {
            throw $this->createNotFoundException('No page found for id '. $id);
        }

        return $page;
    }

    /**
     * @Rest\View(templateVar="page")
     *
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function postPageAction(Request $request)
    {
        try {
            $page = $this->container->get('acme_blog.page.handler')->post($request->request->all());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return $page;
    }
}