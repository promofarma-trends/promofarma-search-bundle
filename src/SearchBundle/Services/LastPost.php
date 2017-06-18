<?php

namespace SearchBundle\Services;

use Elastica\Query;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;

class LastPost
{
    const REPOSITORY = 'SearchBundle\Entity\NormalizedPost';
    const CREATED_AT_PROPERTY = 'created_at';
    private $query;
    private $repositoryManagerObject;
    public function __construct(RepositoryManagerInterface $repositoryManager)
    {
        $this->repositoryManagerObject = $repositoryManager;
        $this->query = new Query();
    }
    public function getLatestPost(){
        $repositoryPostTreated = $this->repositoryManagerObject->getRepository(self::REPOSITORY);
        $this->query->addSort(array(self::CREATED_AT_PROPERTY => array('order' => 'desc')));
        $allPostsTopic = $repositoryPostTreated->find($this->query);
        return reset($allPostsTopic);
    }
}