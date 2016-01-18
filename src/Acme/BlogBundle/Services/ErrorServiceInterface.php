<?php

namespace Acme\BlogBundle\Services;

use Symfony\Component\Form\FormInterface;

interface ErrorServiceInterface
{
    public function getFormErrorMessages(FormInterface $form);
}