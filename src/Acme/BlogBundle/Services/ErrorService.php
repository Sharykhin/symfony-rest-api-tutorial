<?php

namespace Acme\BlogBundle\Services;

use Symfony\Component\Form\FormInterface;
use JMS\DiExtraBundle\Annotation\Service;

/**
 * Class ErrorService
 * @package AppBundle\Services
 *
 * @Service("app.error.service", public=true)
 */
class ErrorService implements ErrorServiceInterface
{
    /**
     * Return errors of form
     *
     * @param FormInterface $form
     * @return array
     */
    public function getFormErrorMessages(FormInterface $form)
    {
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
                $errors[$child->getName()] = $this->getFormErrorMessages($child);
            }
        }

        return $errors;
    }
}