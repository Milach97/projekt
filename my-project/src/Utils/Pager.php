<?php

namespace App\Utils;

use Doctrine\ORM\Query;

class Pager
{
    private $maxPerPage = 20;
    private $page = 20;
    private $qb = null;
    private $count = null;
    private $lastPage = null;
    
    public function __construct($maxPerPage = 20) {
        $this->maxPerPage = $maxPerPage;
    }


    function setPage($page) {
        $pTmp = intval($page);
        
        $this->page = $pTmp ? $pTmp : 1;
    }
    function setQueryBuilder($prefix, $qb) {
        $this->qb = $qb;
        $qb_c = clone($this->qb);
        $this->count = $qb_c->select('count('.$prefix.')')->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    
    function getResult() {
        
        $this->qb->setMaxResults($this->maxPerPage);
        $this->qb->setFirstResult($this->getFrom());
        
        return $this->qb->getQuery()->getResult();
    }
    function getPager() {

        $this->lastPage = ceil($this->count / $this->maxPerPage);
                
        return array(
            'haveToPaginate' => $this->count > $this->maxPerPage,
            
            'from' => $this->getFrom() +1,
            'to' => $this->getTo(),
            
            'page' => $this->page,
            'count' => $this->count,
            'isFirstPage' => ($this->page == 1),
            'lastPage' => $this->lastPage,
            'isLastPage' => ($this->getTo() == $this->count),
            'links' => $this->getLinks()
            );
    }
    
    
    

    
    public function getFrom(){
        
        if ($this->count == 0) {
            return 0;
        }
        
        $maxPage = ceil($this->count / $this->maxPerPage);
        if($this->page > $maxPage) 
            $this->page = $maxPage;
        
        $from = (intval($this->page) - 1) * intval($this->maxPerPage);

        return $from;
    }
    public function getTo(){
        
        $to = $this->getFrom() + $this->maxPerPage;
        
        if($to > $this->count)
            $to = $this->count;
        
        return $to;
    }
    public function getLinks() {
        
        $links = array(); // array of page numbers to show

        if($this->lastPage <= 5)
          $links = range(1,$this->lastPage);
        else if ($this->page == $this->lastPage)
          $links = range($this->lastPage-4,$this->lastPage);
        else if ($this->page == ($this->lastPage-1) )
          $links = range($this->lastPage-4,$this->lastPage);
        else if ($this->page == 1)
          $links = range(1,5);
        else if ($this->page == 2)
          $links = range(1,5);
        else
          $links = range($this->page -2,$this->page +2);
    
        return $links;
    }
    
}
