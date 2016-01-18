<?php

namespace Acme\BlogBundle\Controller;

use Acme\BlogBundle\Model\PageInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Acme\BlogBundle\Handler\PageHandlerInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Request;
use Acme\BlogBundle\Exception\InvalidFormException;
use Acme\BlogBundle\Helper\ResponseTrait;

/**
 * Class PageController
 * @package Acme\BlogBundle\Controller
 */
class PageController extends FOSRestController
{
    use ResponseTrait;

    /** @DI\Inject("acme_blog.page.handler") */
    private $pageHandler;

    /**
     * @DI\Inject("acme_blog.error.service")
     * @var
     */
    private $errorService;


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
        $data = $this->pageHandler->all($limit, $offset);
        return $this->successResponse($data);
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
        $page = $this->pageHandler->get($id);
        if (!$page instanceof PageInterface) {
            return $this->notFoundResponse('No page found for id'. $id);
        }
        return $this->successResponse($page);
    }

    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new page from the submitted data.",
     *   input = "Acme\BlogBundle\Form\PageType",
     *
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

        try {
            $page = $this->pageHandler->post($request->request->all());
        } catch (InvalidFormException $e) {
            $errors = $this->errorService->getFormErrorMessages($e->getForm());
            return $this->badRequestResponse($errors);
        }
        return $this->successCreatedResponse($page);
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
            $page = $this->pageHandler->get($id);
            if (!$page instanceof PageInterface) {
                $page = $this->pageHandler->post($request->request->all());
                return $this->successCreatedResponse($page);
            }
            $page = $this->pageHandler->put($page, $request->request->all());
            return $this->successUpdatedResponse($page);

        } catch (InvalidFormException $e) {
            $errors = $this->errorService->getFormErrorMessages($e->getForm());
            return $this->badRequestResponse($errors);
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
            $page = $this->pageHandler->get($id);
            if (!$page instanceof PageInterface) {
                return $this->notFoundResponse('No page found with id '. $id);
            }
            $page = $this->pageHandler->patch(
                $page,
                $request->request->all()
            );
            return $this->successUpdatedResponse($page);
        } catch (InvalidFormException $e) {
            $errors = $this->errorService->getFormErrorMessages($e->getForm());
            return $this->badRequestResponse($errors);
        }
    }

    /**
     * Remove page by id
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when an existing Page was successfully removed",
     *      404 = "Returned when the Page was not found"
     *  }
     * )
     *
     * @param $id
     * @return \FOS\RestBundle\View\View
     */
    public function deletePageAction($id)
    {
        $page = $this->pageHandler->get($id);
        if (!$page instanceof PageInterface) {
            return $this->notFoundResponse('No page found with id '. $id);
        }
        $this->pageHandler->delete($page);
        return $this->successRemoveResponse();
    }
}