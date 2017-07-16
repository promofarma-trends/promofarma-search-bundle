<?php

namespace SearchBundle\Services;

use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use SearchBundle\Services\Domain\MostOfTheMonth;
use SearchBundle\Services\Domain\ArrayKeysTransformer;

class MostRatedTopicsOfTheMonth extends MostOfTheMonth
{
    const SCORE_PROPERTY = 'score';
    const SCORES = ['10', '9', '8', '7', '6' ,'5','4', '3', '2', '1', '0'];
    private $boolQuery;
    private $fieldQuery;
    private $amountOfEachScore = [];
    private $transformedArray;

   public function __construct(RepositoryManagerInterface $repositoryManager)
    {
        $this->boolQuery = new BoolQuery();
        $this->fieldQuery = new Match();
        $this->transformedArray = new ArrayKeysTransformer();
        parent::__construct($repositoryManager);
    }

    public function search()
    {
        $currentMonthFilter = $this->getTimeRangeFilter();
        $this->boolQuery->addMust($currentMonthFilter);
        $this->amountOfEachScore = $this->getAmountOfEachScore($this->repositoryManagerObject);
        $amountOfEachScoreTransformed = $this->checkIfThereIsData();
        $amountOfEachScoreTransformed = call_user_func_array('array_merge', $amountOfEachScoreTransformed);
        return array_slice ($amountOfEachScoreTransformed,
            self::START_ARRAY_POSITION,
            self::MAXIMUM_ARRAY_LENGTH,
            self::KEEP_ARRAY_KEYS
        );
    }

    private function getAmountOfEachScore($repositoryPostTreated){
        foreach(self::SCORES as $score ) {
            $this->fieldQuery->setFieldQuery(self::SCORE_PROPERTY, $score);
            $this->boolQuery->addMust($this->fieldQuery);
            $this->topicAggregation->setField(self::TAGS_PROPERTY);
            $this->query->setQuery($this->boolQuery);
            $this->query->addAggregation($this->topicAggregation);
            $this->topicAggregation->setSize(0);
            $aggregation = $repositoryPostTreated->createPaginatorAdapter($this->query);
            $aggregationResult = $aggregation->getAggregations();
            $this->amountOfEachScore[] = $aggregationResult[self::TOPIC_NAME][self::BUCKETS];
        }
        return $this->amountOfEachScore;
    }

    private function checkIfThereIsData()
    {
        foreach ($this->amountOfEachScore as $eachScore) {
            if (!empty($eachScore)) {
                $amountOfEachScoreTransformed = $this->transformedArray->getArrayTransformed($this->amountOfEachScore);
                break;
            } else {
                $amountOfEachScoreTransformed = $this->amountOfEachScore;
                break;
            }
        }
        return $amountOfEachScoreTransformed;
    }
}