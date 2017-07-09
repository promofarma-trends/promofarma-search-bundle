<?php

namespace SearchBundle\Services;

use Elastica\Query;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use SearchBundle\Infrastructure\ElasticRepository;

class LastPost implements ElasticRepository
{
    const CREATED_AT_PROPERTY = 'created_at';
    private $query;
    private $repositoryManagerObject;

    public function __construct(RepositoryManagerInterface $repositoryManager)
    {
        $this->repositoryManagerObject = $repositoryManager->getRepository(self::REPOSITORY);
        $this->query = new Query();
    }

    public function getLatestPost(){
        $this->query->addSort(array(self::CREATED_AT_PROPERTY => array('order' => 'desc')));
        $allPostsTopic = $this->repositoryManagerObject->find($this->query);
        return reset($allPostsTopic);
    }
}