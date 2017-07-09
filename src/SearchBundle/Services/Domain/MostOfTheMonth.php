<?php
namespace SearchBundle\Services\Domain;

use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use SearchBundle\Infrastructure\ElasticRepository;

abstract class MostOfTheMonth implements ElasticRepository
{
    const KEEP_ARRAY_KEYS = true;
    const START_ARRAY_POSITION = 0;
    const MAXIMUM_ARRAY_LENGTH = 100;
    const TOPIC_NAME = 'Topic';
    const TAGS_PROPERTY = 'tags';
    const BUCKETS = 'buckets';
    const ONE_MONTH_AGO = "1 month ago";
    const NOW = "now";
    const CREATED_AT_PROPERTY = 'created_at';
    private $dateFormat = 'Y-m-d';
    protected $repositoryManagerObject;
    protected $query;
    protected $topicAggregation;
    protected $currentMonthFilter;
    protected $dateFilter;

    public function __construct(RepositoryManagerInterface $repositoryManager)
    {
        $this->repositoryManagerObject = $repositoryManager->getRepository(self::REPOSITORY);
        $this->matchAllQuery = new \Elastica\Query\MatchAll();
        $this->query = new \Elastica\Query($this->matchAllQuery);
        $this->topicAggregation = new \Elastica\Aggregation\Terms(self::TOPIC_NAME);
        $this->dateFilter = new CreateDateFilter();
    }

    public function search(){
    }

    protected function getTimeRangeFilter(){
        $timeRangeFilter = $this->dateFilter->getTimeRangeFilter(
            date($this->dateFormat, strtotime(self::ONE_MONTH_AGO)),
            date($this->dateFormat, strtotime(self::NOW))
        );
        return $timeRangeFilter;
    }

}