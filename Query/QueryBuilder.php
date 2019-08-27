<?php


namespace VFou\Search\Query;


class QueryBuilder
{
    private $search;
    private $limit;
    private $offset;
    private $order;

    /**
     * QueryBuilder constructor.
     * @param string $query
     */
    public function __construct($query = "")
    {
        $this->search = $query;
        $this->limit = 10;
        $this->offset = 0;
        $this->order = [];
    }

    /**
     *
     * @param string $query
     */
    public function search($query = "")
    {
        $this->search = $query;
        return $this;
    }

    /**
     * @param $field
     * @param $terms
     */
    public function exactSearch($field, $terms)
    {
        $this->search = [
            $field => $terms
        ];
        return $this;
    }
    public function addExactSearch($field, $terms)
    {
        if(!is_array($this->search)){
            $this->search = [];
        }
        $this->search[$field] = $terms;
        return $this;
    }

    public function fieldSearch($field, $terms)
    {
        return $this->exactSearch($field.'%', $terms);
    }
    public function addFieldSearch($field, $terms)
    {
        return $this->addExactSearch($field.'%', $terms);
    }

    public function orderBy($field, $order = 'ASC'){
        $this->order = [
            $field => $order
        ];
        return $this;
    }

    public function getQuery()
    {
        return $this->search;
    }

    public function getFilters(){
        return [
            'limit' => $this->limit,
            'offset' => $this->offset,
            'order' => $this->order
        ];
    }
}