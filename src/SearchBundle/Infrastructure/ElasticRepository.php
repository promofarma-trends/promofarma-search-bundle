<?php

namespace SearchBundle\Infrastructure;


use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;

interface ElasticRepository
{
    const REPOSITORY = 'SearchBundle\Entity\NormalizedPost';
    public function __construct (RepositoryManagerInterface $repositoryManager);

}