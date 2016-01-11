<?php

namespace Acme\BlogBundle\Controller;

use Acme\BlogBundle\Model\PageInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\InjectParams;
use JMS\DiExtraBundle\Annotation\Inject;
use Acme\BlogBundle\Document\Page;
use Acme\BlogBundle\Handler\PageHandlerInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Acme\BlogBundle\Handler\PageHandler;
use Symfony\Component\HttpFoundation\Request;
use Acme\BlogBundle\Exception\InvalidFormException;

/**
 * Class PageController
 * @package Acme\BlogBundle\Controller
 * @Service("acme_blog.page.controller")
 */
class PageController extends FOSRestController
{

    /**
     * @InjectParams({
     *     "pager" = @Inject("acme_blog.page.handler")
     * })
     */
    public function __construct(PageHandlerInterface $pager)
    {
        $this->pager = $pager;
    }

    /**
     * List all pages.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing pages.")
     * @Rest\QueryParam(name="limit", requirements="\d+", default="5", description="How many pages to return.")
     *
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getPagesAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        $data = $this->container->get('acme_blog.page.handler')->all($limit, $offset);
        return ['data' => $data,'success' => true, 'errors' => []];


    }

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
     * @param $id
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getPageAction($id)
    {


        $page = $this->pager->get($id);

        if (!$page instanceof PageInterface) {
            throw $this->createNotFoundException('No page found for id '. $id);
        }
        return $this->view(['data' => $page,'success' => true, 'errors' => []], Codes::HTTP_OK);
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
            if (!$page instanceof PageInterface) {
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