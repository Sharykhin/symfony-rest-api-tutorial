<?php

namespace Acme\BlogBundle\Handler;

interface PageHandlerInterface
{
    public function get($id);

    public function post(array $parameters);
}