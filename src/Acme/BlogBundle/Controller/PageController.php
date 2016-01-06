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
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function postPageAction(Request $request)
    {
        /*$page = new Page();
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


        $response['data'] = $page;*/

        $response=[
            'success'=>true,
            'data'=>null,
            'errors'=>null
        ];

        try {
            $page = $this->container->get('acme_blog.page.handler')->post($request->request->all());
        } catch (InvalidFormException $e) {
            $response=[];
            $errors = $this->getErrorMessages($e->getForm());
            $response['data'] = null;
            $response['success'] = false;
            $response['errors'] = $errors;


            return View::create($response, Codes::HTTP_BAD_REQUEST);
        }
        $response['data'] = $page;
        return View::create($response, Codes::HTTP_OK);
    }

    /**
     * Update existing page from the submitted data or create a new page at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Acme\DemoBundle\Form\PageType",
     *   statusCodes = {
     *     201 = "Returned when the Page is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @param Request $request the request object
     * @param int     $id      the page id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when page not exist
     */
    public function putPageAction(Request $request, $id)
    {
        try {
            $page = $this->container->get('acme_blog.page.handler')->get($id);
            if (!($page)) {
                $statusCode = Codes::HTTP_CREATED;
                $page = $this->container->get('acme_blog.page.handler')->post(
                    $request->request->all()
                );
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $page = $this->container->get('acme_blog.page.handler')->put(
                    $page,
                    $request->request->all()
                );
            }
            return View::create(['page'=>$page], $statusCode);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }
    /**
     * Update existing page from the submitted data or create a new page at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Acme\DemoBundle\Form\PageType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the page id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when page not exist
     */
    public function patchPageAction(Request $request, $id)
    {
        try {
            $page = $this->container->get('acme_blog.page.handler')->get($id);
            if (!$page) {
                $this->createNotFoundException('Page not found');
            }
            $page = $this->container->get('acme_blog.page.handler')->patch(
                $page,
                $request->request->all()
            );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        return View::create(['page'=>$page], Codes::HTTP_OK);
    }


    private function getErrorMessages(\Symfony\Component\Form\Form $form) {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }
}