<?php

namespace Acme\BlogBundle\Handler;

use Acme\BlogBundle\Model\PageInterface;

interface PageHandlerInterface
{
    public function all($limit = 5, $offset = 0, $orderby = null);

    public function get($id);

    public function post(array $parameters);

    public function put(PageInterface $page, array $parameters);

    public function patch(PageInterface $page, array $parameters);

    public function delete(PageInterface $page);
}