<?php

namespace Acme\BlogBundle\Helper;

use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;

/**
 * Class ResponseTrait
 * @package AppBundle\Helper
 */
trait ResponseTrait
{

    /**
     * @param $data
     * @param int $statusCode
     * @return View
     */
    public function successResponse($data, $statusCode = Codes::HTTP_OK)
    {
        return View::create(['data'=>$data, 'success'=>true, 'errors'=>null], $statusCode);
    }

    /**
     * @param $data
     * @param int $statusCode
     * @return View
     */
    public function successCreatedResponse($data, $statusCode = Codes::HTTP_CREATED)
    {
        return View::create(['data'=>$data, 'success'=>true, 'errors'=>null], $statusCode);
    }

    /**
     * @param $errors
     * @param int $statusCode
     * @return View
     */
    public function badRequestResponse($errors, $statusCode = Codes::HTTP_BAD_REQUEST)
    {
        return View::create(['data'=>null, 'success'=>false, 'errors'=>$errors], $statusCode);
    }

    /**
     * @param $data
     * @param int $statusCode
     * @return View
     */
    public function successUpdatedResponse($data, $statusCode = Codes::HTTP_NO_CONTENT)
    {
        return View::create(['data'=>$data, 'success'=>true, 'errors'=>null], $statusCode);
    }

    /**
     * @param int $statusCode
     * @return View
     */
    public function successRemoveResponse($statusCode = Codes::HTTP_NO_CONTENT)
    {
        return View::create(null, $statusCode);
    }

    /**
     * @param string $message
     * @param int $statusCode
     * @return View
     */
    public function notFoundResponse($message = 'Not Found', $statusCode = Codes::HTTP_NOT_FOUND)
    {
        return View::create(['data'=>null, 'success'=>false, 'errors'=>$message], $statusCode);
    }

}