<?php

namespace App\Service; 

use Doctrine\ORM\Query; 
use Knp\Component\Pager\Pagination\PaginationInterface; 
use Knp\Component\Pager\PaginatorInterface; 
use Symfony\Component\HttpFoundation\RequestStack; 


class PaginationService { 
    private $paginator; 
    private $requestStack; 
    
    public function __construct(PaginatorInterface $paginator, RequestStack $requestStack) { 
        $this->paginator = $paginator; $this->requestStack = $requestStack; 
    } 
    
    public function paginate(Query $query, int $page, int $limit): PaginationInterface { 
        $request = $this->requestStack->getCurrentRequest(); 
        return $this->paginator->paginate($query, $request->query->getInt('page', $page), $limit); 
    } 
}