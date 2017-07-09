<?php
namespace SearchBundle\Services;

use SearchBundle\Services\Domain\MostOfTheMonth;

class MostSpokenTopicsOfTheMonth extends MostOfTheMonth
{
    public function search()
    {
        $currentMonthFilter = $this->getTimeRangeFilter();
        $this->getTimeAndTagsFilter($currentMonthFilter);
        $aggregationPaginatorAdapter = $this->repositoryManagerObject->createPaginatorAdapter($this->query);
        $aggregationResult = $aggregationPaginatorAdapter->getAggregations();
        $bucketsResult = $aggregationResult[self::TOPIC_NAME][self::BUCKETS];
        return  array_slice ($bucketsResult,
            self::START_ARRAY_POSITION,
            self::MAXIMUM_ARRAY_LENGTH,
            self::KEEP_ARRAY_KEYS
        );
    }

    private function getTimeAndTagsFilter($currentMonthFilter)
    {
        $this->topicAggregation->setField(self::TAGS_PROPERTY);
        $this->query->setQuery($currentMonthFilter);
        $this->query->addAggregation($this->topicAggregation);
        $this->query->setSize(0);
    }
}