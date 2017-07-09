<?php

namespace SearchBundle\Services\Domain;

class ArrayKeysTransformer
{
    const SCORE_NAME = 'score';
    private $finalArrayTransformed;

    public function getArrayTransformed(array $givenArray){
        foreach ($givenArray as $key => $arrayPerScore){
            $this->finalArrayTransformed = $this->insertScoreInEachTagArray($arrayPerScore, $key);
        }
        return $this->finalArrayTransformed;
    }

    private function insertScoreInEachTagArray(array $arrayPerScore, $scoreNumber){
        foreach ($arrayPerScore as $tagPerScore){
            $tagPerScore[self::SCORE_NAME] = $scoreNumber;
            $this->finalArrayTransformed[]=$tagPerScore;
        }
        return $this->finalArrayTransformed;
    }
}