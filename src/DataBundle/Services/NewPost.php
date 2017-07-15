<?php

namespace DataBundle\Services;

use Doctrine\ORM\EntityManager;
use SearchBundle\Entity\NormalizedPost;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class NewPost
{
    private $entityManagerObject;
    private $kernel;
    private $post;
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManagerObject = $entityManager;
        $this->kernel = new \AppKernel('dev', false);
        $this->post = new NormalizedPost();
    }

    public function createNewPost($message)
    {
        $date = new \DateTime($message['created_at']['date']);
        $date->format('Y-m-d');
        $this->post->setContent(json_encode($message['content']));
        $this->post->setLang($message['lang']);
        $this->post->setTags($message['tags']);
        $this->post->setScore($message['score']);
        $this->post->setCreatedAt($date);
        $this->post->setSource($message['source']);
        sleep(5);
        $this->entityManagerObject->persist($this->post);
        $this->entityManagerObject->flush();
        $this->populateCommand();
    }

    private function populateCommand()
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput(array(
            'command' => 'fos:elastica:populate'
        ));
        // Use the NullOutput class instead of BufferedOutput.
        $output = new NullOutput();
        $application->run($input, $output);
    }
}