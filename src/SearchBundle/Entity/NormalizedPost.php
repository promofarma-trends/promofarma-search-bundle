<?php

namespace SearchBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="NormalizedPost",
 *     options={
 *     "collate":"utf8mb4_general_ci",
 *     "charset":"utf8mb4"}
 * )
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
     * @ORM\Column(type="text", nullable=TRUE)
     */
    public $content;

    /**
     * @ORM\Column(type="string", nullable=TRUE)
     */
    public $lang;

    /**
     * @ORM\Column(type="json_array", nullable=TRUE)
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

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return mixed
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @param mixed $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @param mixed $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

}