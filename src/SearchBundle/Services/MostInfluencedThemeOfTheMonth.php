<?php

namespace SearchBundle\Services;

use Elastica\Filter\Range;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use Elastica\Query\BoolQuery;
use Elastica\Query\Filtered;
use Elastica\Query\Match;

class MostInfluencedThemeOfTheMonth
{
    const REPOSITORY = 'SearchBundle\Entity\NormalizedPost';
    const START_ARRAY_POSITION = 0;
    const MAXIMUM_ARRAY_LENGTH = 100;
    const KEEP_ARRAY_KEYS = true;
    const SCORE_PROPERTY = 'score';
    const TAGS_PROPERTY = 'tags';
    const CREATED_AT_PROPERTY = 'created_at';
    const TOPIC_NAME = 'Topic';
    const BUCKETS = 'buckets';
    const FIRST_DAY_OF_THE_MONTH = '01';
    const LAST_DAT_OF_THE_MONTH = '30';
    private $repositoryManagerObject;
    private $boolQuery;
    private $fieldQuery;
    private $scores = ['10','9', '8', '7', '6' ,'5','4', '3', '2', '1'];
    private $amountOfEachScore = [];
    private $transformedArray;

    public function __construct(RepositoryManagerInterface $repositoryManager)
    {
        $this->repositoryManagerObject = $repositoryManager;
        $this->boolQuery = new BoolQuery();
        $this->fieldQuery = new Match();
        $this->matchAllQuery = new \Elastica\Query\MatchAll();
        $this->query = new \Elastica\Query($this->matchAllQuery);
        $this->topicAggregation = new \Elastica\Aggregation\Terms(self::TOPIC_NAME);
        $this->transformedArray = new ArrayKeysTransformer();
    }

    /**
     * This function looks in the ElasticSearch database to get the top 5 categories that are
     * the most influenced in the current month.
     */
    public function searchMostInfluencedOfTheMonth()
    {
        $repositoryPostTreated = $this->repositoryManagerObject->getRepository(self::REPOSITORY);
        $currentMonthFilter = $this->getTimeRangeFilter();
        $this->boolQuery->addMust($currentMonthFilter);
        $this->amountOfEachScore = $this->getAmountOfEachScore($repositoryPostTreated);
        $amountOfEachScoreTransformed = $this->transformedArray->getArrayTransformed($this->amountOfEachScore);
        return array_slice ($amountOfEachScoreTransformed,
            self::START_ARRAY_POSITION,
            self::MAXIMUM_ARRAY_LENGTH,
            self::KEEP_ARRAY_KEYS
        );
    }

//TODO: Pending to improve how to calculate the date
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
    private function getAmountOfEachScore($repositoryPostTreated){
        foreach($this->scores as $score ){
            $this->fieldQuery->setFieldQuery(self::SCORE_PROPERTY, $score);
            $this->boolQuery->addMust($this->fieldQuery);
            $this->topicAggregation->setField(self::TAGS_PROPERTY);
            $this->query->setQuery($this->boolQuery);
            $this->query->addAggregation($this->topicAggregation);
            $this->query->setSize(0);
            $aggregation = $repositoryPostTreated->createPaginatorAdapter($this->query);
            $aggregationResult = $aggregation->getAggregations();
            $this->amountOfEachScore[$score] = $aggregationResult[self::TOPIC_NAME][self::BUCKETS];
        }
        return $this->amountOfEachScore;
    }
}