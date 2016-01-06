<?php

namespace Acme\BlogBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Acme\BlogBundle\Document\Page;
use Symfony\Component\HttpFoundation\Request;
use Acme\BlogBundle\Exception\InvalidFormException;

class PageController extends FOSRestController
{
    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Page for a given id",
     *   output = "Acme\BlogBundle\Document\Page",
     *   requirements={
     *     {
     *       "name"="id",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="The ObjectID of Page"
     *     },
     *     {
     *      "name"="_format",
     *      "dataType"="string",
     *      "requirement"="json|xml",
     *      "description"="Output format"
     *     }
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the page is not found"
     *   }
     * )
     *
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
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new page from the submitted data.",
     *   input = "Acme\BlogBundle\Form\PageType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
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
        $page = new Page();
        $page->setBody($request->request->get('body'));
        $page->setTitle($request->request->get('title'));
        $validator = $this->get('validator');
        $errors = $validator->validate($page);
        $response = [
            'data'=>null,
            'success'=>true,
            'errors'=>null
        ];
        if (count($errors) > 0) {
            $response['success'] = false;
            $response['errors'] = [];
            foreach($errors as $error) {
                $response['errors'][] = $error->getMessage();
            }

            return View::create($response, Codes::HTTP_BAD_REQUEST);
        }

        $dm = $this->container->get("doctrine_mongodb")->getManager();
        $dm->persist($page);
        $dm->flush();


        $response['data'] = $page;

        /*try {
            $page = $this->container->get('acme_blog.page.handler')->post($request->request->all());
        } catch (InvalidFormException $e) {

            return ['data'=>[], 'form'=>$e->getForm()];
        }*/

        return View::create($response, Codes::HTTP_OK);
    }
}