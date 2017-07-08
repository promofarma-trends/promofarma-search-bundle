<?php
namespace DataBundle\Infrastructure;

use DataBundle\Services\NewPost;
use Doctrine\ORM\EntityManager;

class IndexPostMessageHandler
{
    private $manager;
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }
    public function processMessages($message)
    {
        $newPost = new NewPost($this->manager);
        $newPost->createNewPost($message);
    }
}