<?php

namespace App\Service;

use Pagerfanta\Pagerfanta;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;

class Paginator
{    
    public function paginate(QueryBuilder $qb, $offset = 1, $limit = 10)
    {
        if($offset === 0 || $limit === 0){
            throw new \Exception('$offset and $limit must be greater than 0.');
        }

        $pager = new Pagerfanta(new DoctrineORMAdapter($qb));
        $currentPage = ceil(($offset + 1) / $limit);
        $pager->setCurrentPage($currentPage);
        $pager->setMaxPerPage(intval($limit));

        return $pager;
    }
}