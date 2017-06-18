<?php
namespace SearchBundle\Services;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;

class FirstAndLastDatesTopic
{
    const REPOSITORY = 'SearchBundle\Entity\NormalizedPost';
    const TAGS_PROPERTY = 'tags';
    const CREATED_AT_PROPERTY = 'created_at';
    const DATE_PROPERTY = 'date';

    public function __construct(RepositoryManagerInterface $repositoryManager)
    {
        $this->repositoryManagerObject = $repositoryManager;
        $this->boolQuery = new BoolQuery();
        $this->fieldQuery = new Match();
        $this->query = new Query();
    }
    public function getFirstAndLastDateTopic($topic){
        $repoTreated = $this->repositoryManagerObject->getRepository(self::REPOSITORY);
        $this->fieldQuery->setFieldQuery(self::TAGS_PROPERTY, $topic);
        $this->boolQuery->addMust($this->fieldQuery);
        $this->query->setQuery($this->boolQuery);
        $this->query->addSort(array(self::CREATED_AT_PROPERTY => array('order' => 'asc')));
        $allPostsTopic = $repoTreated->find($this->query);
        $initialAndFinalDate['initial']= $this->getFirstPostDate($allPostsTopic);
        $initialAndFinalDate['final'] = $this->getLastPostDate($allPostsTopic);
        return $initialAndFinalDate;
    }
    private function getFirstPostDate($allPostsTopic){
        $fistPostObject = reset($allPostsTopic);
        $firstPostArray = $this->getArrayFromAnObject($fistPostObject);
        $dateFirstPostArray = $this->getArrayFromAnObject($firstPostArray[self::CREATED_AT_PROPERTY]);
        return $dateFirstPostArray[self::DATE_PROPERTY];
    }
    private function getLastPostDate($allPostsTopic){
        $lastPostObject = end ($allPostsTopic);
        $lastPostArray = $this->getArrayFromAnObject($lastPostObject);
        $dateLastPostArray = $this->getArrayFromAnObject($lastPostArray[self::CREATED_AT_PROPERTY]);
        return $dateLastPostArray[self::DATE_PROPERTY];
    }
    private function getArrayFromAnObject($object){
        return get_object_vars ($object);
    }
}