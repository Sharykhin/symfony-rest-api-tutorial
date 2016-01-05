<?php

namespace Acme\BlogBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Acme\BlogBundle\Exception\InvalidFormException;

class PageController extends FOSRestController
{
    /**
     * @Rest\View
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

        return ['data' => $page,'success' => true, 'errors' => []];
    }

    /**
     * @Rest\View(
     *   statusCode = Codes::HTTP_BAD_REQUEST
     * )
     *
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function postPageAction(Request $request)
    {
        try {
            $page = $this->container->get('acme_blog.page.handler')->post($request->request->all());
        } catch (InvalidFormException $e) {

            return ['data'=>[], 'form'=>$e->getForm()];
        }

        return ['data' => $page];
    }
}