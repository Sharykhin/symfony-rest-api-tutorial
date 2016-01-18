<?php

namespace Acme\BlogBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Acme\BlogBundle\Model\PageInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;

/**
 * @MongoDB\Document (collection="pages")
 * @Unique("title")
 * @ExclusionPolicy("all")
 *
 */
class Page implements PageInterface
{
    /**
     * @MongoDB\Id
     * @Expose
     */
    protected $id;

    /**
     * @MongoDB\String
     * @Expose
     *
     * @Assert\NotBlank(
     *      message = "title could not be blank"
     * )
     * @Assert\NotNull()
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Your title must be at least {{ limit }} characters length",
     *      maxMessage = "Your title name cannot be longer than {{ limit }} characters length"
     * )
     */
    protected $title;

    /**
     * @MongoDB\String
     * @Expose
     *
     * @Assert\NotBlank(
     *      message = "body could not be blank"
     * )
     */
    protected $body;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return self
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Get body
     *
     * @return string $body
     */
    public function getBody()
    {
        return $this->body;
    }
}
