<?php

namespace SearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="NormalizedPost")
 */
class NormalizedPost
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\Column(type="text")
     */
    public $content;

    /**
     * @ORM\Column(type="string")
     */
    public $lang;

    /**
     * @ORM\Column(type="text", nullable=TRUE)
     */
    public $tags;

    /**
     * @ORM\Column(type="integer", nullable=TRUE)
     */
    public $score;

    /**
     * @ORM\Column(type="datetime")
     */
    public $created_at;

    /**
     * @ORM\Column(type="string", nullable=TRUE)
     */
    public $source;
}