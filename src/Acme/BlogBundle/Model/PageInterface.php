<?php

namespace Acme\BlogBundle\Model;

interface PageInterface
{
    public function setTitle($title);

    public function setBody($body);

    public function getBody();

    public function getTitle();
}