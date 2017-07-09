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
    private $boolQuery;
    private $fieldQuery;
    private $scores = ['10', '9', '8', '7', '6' ,'5','4', '3', '2', '1'];
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
        $amountOfEachScoreTransformed = $this->transformedArray->getArrayTransformed($this->amountOfEachScore);
        return array_slice ($amountOfEachScoreTransformed,
            self::START_ARRAY_POSITION,
            self::MAXIMUM_ARRAY_LENGTH,
            self::KEEP_ARRAY_KEYS
        );
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