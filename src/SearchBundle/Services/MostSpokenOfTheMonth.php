<?php
namespace SearchBundle\Services;

use Elastica\Query\BoolQuery;
use Elastica\Query\Filtered;
use Elastica\Query\Range;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;

class MostSpokenOfTheMonth
{
    const REPOSITORY = 'SearchBundle\Entity\NormalizedPost';
    const TAGS_PROPERTY = 'tags';
    const TOPIC_NAME = 'Topic';
    const BUCKETS = 'buckets';
    const CREATED_AT_PROPERTY = 'created_at';
    const FIRST_DAY_OF_THE_MONTH = '01';
    const LAST_DAT_OF_THE_MONTH = '30';
    const START_ARRAY_POSITION = 0;
    const MAXIMUM_ARRAY_LENGTH = 100;
    const KEEP_ARRAY_KEYS = true;
    private $repositoryManagerObject;
    private $matchAllQuery;
    private $themeAggregation;
    private $query;

    public function __construct(RepositoryManagerInterface $repositoryManager)
    {
        $this->repositoryManagerObject = $repositoryManager;
        $this->matchAllQuery = new \Elastica\Query\MatchAll();
        $this->query = new \Elastica\Query($this->matchAllQuery);
        $this->themeAggregation = new \Elastica\Aggregation\Terms(self::TOPIC_NAME);
    }

    /**
     * This function looks in the ElasticSearch database to get the top 5 categories that are
     * the most spoken in the current month.
     */
    public function searchMostSpokenOfTheMonth()
    {
        $repositoryPostTreated = $this->repositoryManagerObject->getRepository(self::REPOSITORY);
        $currentMonthFilter = $this->getTimeRangeFilter();
        $this->getTimeAndTagsFilter($currentMonthFilter);
        $aggregationPaginatorAdapter = $repositoryPostTreated->createPaginatorAdapter($this->query);
        $aggregationResult = $aggregationPaginatorAdapter->getAggregations();
        $bucketsResult = $aggregationResult[self::TOPIC_NAME][self::BUCKETS];
        return array_slice ($bucketsResult,
            self::START_ARRAY_POSITION,
            self::MAXIMUM_ARRAY_LENGTH,
            self::KEEP_ARRAY_KEYS
        );
    }

//TODO: Pending to improve how to calculate the date
//TODO: llibreria nesbot/carbon (per agafar el primer i l'últim dia del mes)
    private function getTimeRangeFilter(){
        $rangeLower = new Filtered(
            new BoolQuery(),
            new Range(self::CREATED_AT_PROPERTY, array(
                'gte' => date('Y').'-'.date('m').'-'. self::FIRST_DAY_OF_THE_MONTH
            ))
        );

        $timeRangeFilter = new Filtered(
            $rangeLower,
            new Range(self::CREATED_AT_PROPERTY, array(
                'lte' => date('Y').'-'.date('m').'-'. self::LAST_DAT_OF_THE_MONTH
            ))
        );
        return $timeRangeFilter;
    }

    private function getTimeAndTagsFilter($currentMonthFilter)
    {
        $this->themeAggregation->setField(self::TAGS_PROPERTY);
        $this->query->setQuery($currentMonthFilter);
        $this->query->addAggregation($this->themeAggregation);
        $this->query->setSize(0);
        return $this->query;
    }
}