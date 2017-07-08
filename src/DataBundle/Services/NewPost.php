<?php

namespace DataBundle\Services;

use Doctrine\ORM\EntityManager;
use DataBundle\Entity\NormalizedPost;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class NewPost
{
    private $entityManagerObject;
    private $kernel;
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManagerObject = $entityManager;
        $this->kernel = new \AppKernel('dev', false);
    }

    public function createNewPost($message)
    {
        $post = new NormalizedPost();
        $post->setContent('London This is extraordinary.');
        $post->setLang('es');
        $post->setTags('["london", "US"]');
        $post->setScore(4);
        $post->setCreatedAt(new \DateTime('2017-07-13 16:09:05'));
        $post->setSource('instagram');
        $this->entityManagerObject->persist($post);
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