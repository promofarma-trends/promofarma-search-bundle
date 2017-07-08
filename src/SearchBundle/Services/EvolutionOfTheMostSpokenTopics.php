<?php

namespace SearchBundle\Services;

use Elastica\Filter\Range;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Filtered;
use Elastica\Filter\Term;
use Elastica\Query\Match;

class EvolutionOfTheMostSpokenTopics
{
    const REPOSITORY = 'SearchBundle\Entity\NormalizedPost';
    const CHART_DATA_KEY = 'data_chart';
    const CHART_DATA_VALUES_KEY = 'values';
    const CHART_DATA_DATES_KEY = 'dates';
    const TOPIC_KEY = 'tag_name';
    const INIT_AND_FINAL_DATES_KEY = 'init_and_final_dates';
    const SCORE_PROPERTY = 'score';
    const TAGS_PROPERTY = 'tags';
    const CREATED_AT_PROPERTY = 'created_at';
    const DEFAULT_DATE_FORMAT = 'Y-m-d';
    const INCREASE_ONE_DATE = '+1 day';
    private $fieldQuery;
    private $currentDate;
    private $currentYear;
    private $currentMonth;
    private $currentDay;
    private $amountOfEachCategory = [];
    private $query;
    private $filter;
    private $eachDayArray;
    //TODO: remove starting date and get the date from the repo
    private $startingDate = "2017-06-01 00.00.00";

    public function __construct(RepositoryManagerInterface $repositoryManager)
    {
        $this->repository = $repositoryManager->getRepository(self::REPOSITORY);
        $this->fieldQuery = new Match();
        $this->termQuery = new Term();
        $this->currentDate = getdate();
        $this->currentYear = date('Y');
        $this->currentMonth = date('m');
        $this->currentDay = date('d');
        $this->query = new Query();
        $this->filter = new Filtered();
    }

    /**
     * This function looks in the ElasticSearch database to get the top 5 categories that are
     * the most spoken in the current month.
     */
    public function setEvolutionChartOfTheMostSpoken($topic)
    {
        $startDate = strtotime($this->startingDate);
        //TODO: use DateTime class
        $endDate = strtotime($this->currentYear.'-'.$this->currentMonth.'-'.$this->currentDay, $startDate);
        //TODO: new method
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
        //TODO: pending to remove topic
        $finalEvolutionChartArray[self::TOPIC_KEY] = $topic;
        //TODO: change key-> date value->amount ???
        $finalEvolutionChartArray[self::CHART_DATA_KEY][self::CHART_DATA_VALUES_KEY] = $this->amountOfEachCategory;
        $finalEvolutionChartArray[self::CHART_DATA_KEY][self::CHART_DATA_DATES_KEY] = $this->eachDayArray;
        return $finalEvolutionChartArray;
    }

    private function setTimeRangeFilter(string $startDate){
        $rangeLower = new Filtered(
            new BoolQuery(),
            new Range(self::CREATED_AT_PROPERTY, array(
                'gte' => $startDate
            ))
        );

        $timeRangeFilter = new Filtered(
            $rangeLower,
            new Range(self::CREATED_AT_PROPERTY, array(
                'lte' => $startDate
            ))
        );
        return $timeRangeFilter;
    }
}