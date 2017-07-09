<?php

namespace SearchBundle\Services;

use Elastica\Filter\Range;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Filtered;
use Elastica\Filter\Term;
use Elastica\Query\Match;
use SearchBundle\Infrastructure\ElasticRepository;
use SearchBundle\Services\Domain\CreateDateFilter;

class EvolutionOfTheMostSpokenTopics implements ElasticRepository
{
    const CHART_DATA_KEY = 'data_chart';
    const CHART_DATA_VALUES_KEY = 'values';
    const CHART_DATA_DATES_KEY = 'dates';
    const TOPIC_KEY = 'tag_name';
    const TAGS_PROPERTY = 'tags';
    const CREATED_AT_PROPERTY = 'created_at';
    const DEFAULT_DATE_FORMAT = 'Y-m-d';
    const INCREASE_ONE_DATE = '+1 day';
    private $repository;
    private $fieldQuery;
    private $amountOfEachCategory = [];
    private $query;
    private $filter;
    private $eachDayArray;
    private $dateFilter;
    private $dateTime;

    public function __construct(RepositoryManagerInterface $repositoryManager)
    {
        $this->repository = $repositoryManager->getRepository(self::REPOSITORY);
        $this->fieldQuery = new Match();
        $this->termQuery = new Term();
        $this->query = new Query();
        $this->filter = new Filtered();
        $this->dateFilter = new CreateDateFilter();
        $this->dateTime = new \DateTime();
    }

    public function setEvolutionChartOfTheMostSpoken($topic)
    {
        $startingDate = $this->getFirstPostDate();
        $endDate = strtotime($this->dateTime->format(self::DEFAULT_DATE_FORMAT), $startingDate);
        $this->evolutionArrayBuilder($topic, $startingDate, $endDate);
        $finalEvolutionChartArray[self::TOPIC_KEY] = $topic;
        $finalEvolutionChartArray[self::CHART_DATA_KEY][self::CHART_DATA_VALUES_KEY] = $this->amountOfEachCategory;
        $finalEvolutionChartArray[self::CHART_DATA_KEY][self::CHART_DATA_DATES_KEY] = $this->eachDayArray;
        return $finalEvolutionChartArray;
    }

    public function getFirstPostDate(){
        $this->query->addSort(array(self::CREATED_AT_PROPERTY => array('order' => 'asc')));
        $allPostsTopic = $this->repository->find($this->query);
        $firstPost = reset($allPostsTopic);
        $dateOfTheFirstPost = $firstPost->getCreatedAt();
        return $dateOfTheFirstPost->getTimeStamp();
    }
    
    private function evolutionArrayBuilder($topic, $startDate, $endDate)
    {
        while ($startDate <= $endDate) {
            $boolQuery = new BoolQuery();
            $this->fieldQuery->setFieldQuery(self::TAGS_PROPERTY, array($topic));
            $boolQuery->addMust($this->fieldQuery);
            $transformedDate = date(self::DEFAULT_DATE_FORMAT, $startDate);
            $timeRange = $this->setTimeRangeFilter($transformedDate);
            $boolQuery->addMust($timeRange);
            $adapter = $this->repository->createPaginatorAdapter($boolQuery);
            $amountOfACategory = $adapter->getTotalHits();
            $this->amountOfEachCategory[] = $amountOfACategory;
            $this->eachDayArray[] = $transformedDate;
            $startDate = strtotime(self::INCREASE_ONE_DATE, $startDate);
        }
    }

    private function setTimeRangeFilter(string $startDate){
        $timeRangeFilter = $this->dateFilter->getTimeRangeFilter(
            $startDate,
            $startDate
        );
        return $timeRangeFilter;
    }
}