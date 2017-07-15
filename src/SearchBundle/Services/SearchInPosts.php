<?php
namespace SearchBundle\Services;

use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use SearchBundle\Infrastructure\ElasticRepository;

class SearchInPosts implements ElasticRepository
{
    const TAGS_PROPERTY = 'tags';
    const CONTENT_PROPERTY = 'content';
    const SEARCHED_WORD_KEY = 'searched_word';
    const SEARCH_NUMBER_KEY = 'searched_word_occurrences';
    const SEARCH_EVOLUTION_KEY = 'evolution_searched_word';
    private $repositoryManagerObject;
    private $tagsQuery;
    private $contentQuery;
    private $boolQuery;
    public function __construct(RepositoryManagerInterface $repositoryManager, EvolutionOfTheMostSpokenTopics $evolutionChart = null){
        $this->repositoryManagerObject = $repositoryManager->getRepository(self::REPOSITORY);
        $this->tagsQuery = new Match();
        $this->contentQuery = new Match();
        $this->boolQuery = new BoolQuery();
        $this->evolutionChart = $evolutionChart;
    }
    public function getResultSearch(string $searchedWord){
        $amountInTags = $this->getResultsFromTags($searchedWord);
        $amountInTagsAndContent = $this->getResultsFromContent($searchedWord);
        $resultSearch[self::SEARCHED_WORD_KEY] = $searchedWord;
        $resultSearch[self::SEARCH_NUMBER_KEY] = $amountInTagsAndContent;
        if ($amountInTags === 0){
            return $resultSearch;
        }else{
            $resultSearch[self::SEARCH_EVOLUTION_KEY] =
                $this->evolutionChart->setEvolutionChartOfTheMostSpoken($searchedWord);
            return $resultSearch;
        }
    }

    private function getResultsFromContent($searchedWord){
        $this->contentQuery->setFieldQuery(self::CONTENT_PROPERTY, $searchedWord);
        $this->boolQuery->addShould($this->contentQuery);
        $amountInContent = $this->getNumberOfQueryResults($this->boolQuery);
        return $amountInContent;
    }

    private function getResultsFromTags($searchedWord){
        $this->tagsQuery->setFieldQuery(self::TAGS_PROPERTY, $searchedWord);
        $this->boolQuery->addShould($this->tagsQuery);
        $amountInTags = $this->getNumberOfQueryResults($this->boolQuery);
        return $amountInTags;
    }

    private function getNumberOfQueryResults($query){
        $adapter = $this->repositoryManagerObject->createPaginatorAdapter($query);
        $amountOfAWord = $adapter->getTotalHits();
        return $amountOfAWord;
    }
}