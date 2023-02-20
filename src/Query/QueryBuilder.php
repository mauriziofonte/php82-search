<?php

namespace MFonte\Search\Query;

class QueryBuilder
{
    /** @var string|array|QuerySegment */
    private $search;
    private $limit;
    private $offset;
    private $order;
    private $facets;
    private $connex = false;

    /**
     * QueryBuilder constructor.
     *
     * @param string|QuerySegment $query
     * @param QuerySegment        $querySegment
     */
    public function __construct($query = '', QuerySegment $querySegment = null)
    {
        if (\is_string($query)) {
            $this->search = QuerySegment::search($query, $querySegment);
        } else {
            $this->search = $query;
        }
        $this->limit = 10;
        $this->offset = 0;
        $this->order = [];
        $this->facets = [];
    }

    public function setQuerySegment(QuerySegment $query) : void
    {
        $this->search = $query;
    }

    /**
     * @param string $query
     *
     * @throws \Exception
     */
    public function search($query = '')
    {
        throw new \Exception('Old QueryBuilder queries are not supported anymore. Please use QuerySegment::search($query, *other segments*) instead.');
    }

    /**
     * @throws \Exception
     */
    public function exactSearch($field, $terms)
    {
        throw new \Exception('Old QueryBuilder queries are not supported anymore. Please use QuerySegment::exactSearch($field, $terms) instead.');
    }

    /**
     * @throws \Exception
     */
    public function addExactSearch($field, $terms)
    {
        throw new \Exception('Old QueryBuilder queries are not supported anymore. Please use QuerySegment::exactSearch($field, $terms) instead.');
    }

    /**
     * @throws \Exception
     */
    public function fieldSearch($field, $terms)
    {
        throw new \Exception('Old QueryBuilder queries are not supported anymore. Please use QuerySegment::fieldSearch($field, $terms) instead.');
    }

    /**
     * @throws \Exception
     */
    public function addFieldSearch($field, $terms)
    {
        throw new \Exception('Old QueryBuilder queries are not supported anymore. Please use QuerySegment::fieldSearch($field, $terms) instead.');
    }

    /**
     * @throws \Exception
     */
    public function lesserSearch($field, $terms)
    {
        throw new \Exception('Old QueryBuilder queries are not supported anymore. Please use QuerySegment::lesserSearch($field, $terms) instead.');
    }

    /**
     * @throws \Exception
     */
    public function lesserEqualSearch($field, $terms)
    {
        throw new \Exception('Old QueryBuilder queries are not supported anymore. Please use QuerySegment::lesserEqualSearch($field, $terms) instead.');
    }

    /**
     * @throws \Exception
     */
    public function greaterSearch($field, $terms)
    {
        throw new \Exception('Old QueryBuilder queries are not supported anymore. Please use QuerySegment::greaterSearch($field, $terms) instead.');
    }

    /**
     * @throws \Exception
     */
    public function greaterEqualSearch($field, $terms)
    {
        throw new \Exception('Old QueryBuilder queries are not supported anymore. Please use QuerySegment::greaterEqualSearch($field, $terms) instead.');
    }

    /**
     * @throws \Exception
     */
    public function notEqualSearch($field, $terms)
    {
        throw new \Exception('Old QueryBuilder queries are not supported anymore. Please use QuerySegment::notEqualSearch($field, $terms) instead.');
    }

    /**
     * @throws \Exception
     */
    public function notSearch($field, $terms)
    {
        throw new \Exception('Old QueryBuilder queries are not supported anymore. Please use QuerySegment::not(QuerySegment::fieldSearch($field, $terms)) instead.');
    }

    /**
     * Set the ordering to a specific $field with the provided $order (ASC/DESC)
     * By default the ordering is based on the document's score.
     *
     * @param string $order
     *
     * @return $this
     */
    public function orderBy($field, $order = 'ASC') : self
    {
        $this->order = [
            $field => $order,
        ];

        return $this;
    }

    /**
     * Ask for $field's facet to be provided in the result array.
     */
    public function addFacet($field) : void
    {
        $this->facets[$field] = $field;
    }

    /**
     * Set the number of documents you want to retrieve.
     */
    public function setLimit(int $limit) : void
    {
        $this->limit = $limit;
    }

    /**
     * Set the offset of documents, useful for pagination.
     */
    public function setOffset(int $offset) : void
    {
        $this->offset = $offset;
    }

    /**
     * Enables the connex feature.
     */
    public function enableConnex() : void
    {
        $this->connex = true;
    }

    /**
     * Disables the connex feature.
     */
    public function disableConnex() : void
    {
        $this->connex = false;
    }

    /**
     * Returns the user's query or QuerySegment.
     *
     * @return array|string|QuerySegment
     */
    public function getQuery()
    {
        return $this->search;
    }

    /**
     * Compiles the filters into an array.
     *
     * @return array
     */
    public function getFilters() : array
    {
        return [
            'limit' => $this->limit,
            'offset' => $this->offset,
            'order' => $this->order,
            'facets' => $this->facets,
            'connex' => $this->connex,
        ];
    }
}
