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
    private $repositoryManagerObject;
    private $boolQuery;
    private $fieldQuery;
    private $currentDate;
    private $currentYear;
    private $currentMonth;
    private $currentDay;
    private $amountOfEachCategory = [];
    private $query;
    private $filter;
    private $eachDayArray;
    private $dateFormat = 'Y-m-d';
    private $startingDate = "2017-06-01 00.00.00";
    private $increaseOneDay = "+1 day";

    public function __construct(RepositoryManagerInterface $repositoryManager)
    {
        $this->repositoryManagerObject = $repositoryManager;
        $this->boolQuery = new BoolQuery();
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
        $repositoryPostTreated = $this->repositoryManagerObject->getRepository(self::REPOSITORY);
        $startDate = strtotime($this->startingDate);
        $endDate = strtotime($this->currentYear.'-'.$this->currentMonth.'-'.$this->currentDay, $startDate);
        while ($startDate <= $endDate) {
            $boolQuery = new BoolQuery();
            $this->fieldQuery->setFieldQuery(self::TAGS_PROPERTY, array($topic));
            $boolQuery->addMust($this->fieldQuery);
            $transformedDate = date($this->dateFormat, $startDate);
            $timeRange = $this->setTimeRangeFilter($transformedDate);
            $boolQuery->addMust($timeRange);
            $adapter = $repositoryPostTreated->createPaginatorAdapter($boolQuery);
            $amountOfACategory = $adapter->getTotalHits();
            $this->amountOfEachCategory[] = $amountOfACategory;
            $this->eachDayArray[] = $transformedDate;
            $startDate = strtotime($this->increaseOneDay, $startDate);
        }
        $finalEvolutionChartArray[self::TOPIC_KEY] = $topic;
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