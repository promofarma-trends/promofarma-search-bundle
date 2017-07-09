<?php

namespace SearchBundle\Services\Domain;

use Elastica\Query\BoolQuery;
use Elastica\Query\Filtered;
use Elastica\Query\Range;

class CreateDateFilter
{
    const CREATED_AT_PROPERTY = 'created_at';

    public function getTimeRangeFilter($fromDate, $toDate){
        $rangeLower = new Filtered(
            new BoolQuery(),
            new Range(self::CREATED_AT_PROPERTY, array(
                'gte' => $fromDate
            ))
        );

        $timeRangeFilter = new Filtered(
            $rangeLower,
            new Range(self::CREATED_AT_PROPERTY, array(
                'lte' => $toDate
            ))
        );
        return $timeRangeFilter;
    }
}