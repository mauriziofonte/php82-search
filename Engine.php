<?php

namespace VFou\Search;

use DateTime;
use Exception;
use VFou\Search\Query\QueryBuilder;
use VFou\Search\Services\Index;
use VFou\Search\Tokenizers\DateFormatTokenizer;
use VFou\Search\Tokenizers\DateSplitTokenizer;
use VFou\Search\Tokenizers\LowerCaseTokenizer;
use VFou\Search\Tokenizers\singleQuoteTokenizer;
use VFou\Search\Tokenizers\TrimPunctuationTokenizer;
use VFou\Search\Tokenizers\WhiteSpaceTokenizer;

class Engine
{
    /**
     * @var Index $index
     */
    private $index;

    /**
     * @var array $config
     */
    private $config;


    /**
     * Engine constructor.
     * @param array $config
     * @throws Exception
     */
    public function __construct($config = [])
    {
        $defaultConfig = $this->getDefaultConfig();
        $this->config = array_replace_recursive($defaultConfig, $config);
        $this->index = new Index($this->config['config'], $this->config['schemas'], $this->config['types']);
    }

    /**
     * Get the Engine's index. Used to perform modifications to the index,
     * such as clearing the cache or rebuilding the index
     * @return Index
     */
    public function getIndex(){
        return $this->index;
    }

    /**
     * Insert or update a given document to the index
     * @param $document
     * @return bool
     * @throws Exception
     */
    public function update($document){
        return $this->index->update($document);
    }

    /**
     * Insert or update multiple documents to the index
     * @param array $document
     * @return bool
     * @throws Exception
     */
    public function updateMultiple(array $document){
        return $this->index->updateMultiple($document);
    }

    /**
     * perform a search
     * @param string|array|QueryBuilder $query
     * @param array $filters
     * @return array
     * @throws Exception
     */
    public function search($query, $filters = []){
        if(is_a($query, QueryBuilder::class)){
            return $this->index->search($query->getQuery(), $query->getFilters());
        }
        return $this->index->search($query, $filters);
    }

    /**
     * Suggest last word for a search
     * @param $query
     * @return array
     * @throws Exception
     */
    public function suggest($query){
        $terms = explode(' ', $query);
        $search = array_pop($terms);
        $tokens = $this->index->tokenizeQuery($search);
        $suggestions = [];
        foreach($tokens as $token) {
            $suggestions = array_replace($suggestions, $this->index->suggest($token));
        }
        $before = implode(' ',$terms);
        foreach($suggestions as &$suggest){
            $suggest = $before.' '.$suggest;
        }
        return array_chunk($suggestions, 10)[0];
    }

    /**
     * delete the given document ID into the index
     * @param $id
     * @return bool
     * @throws Exception
     */
    public function delete($id){
        return $this->index->delete($id);
    }

    /**
     * @return array
     */
    private function getDefaultConfig(){
        return [
            'config' => [
                'var_dir' => $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'var',
                'index_dir' => DIRECTORY_SEPARATOR.'engine'.DIRECTORY_SEPARATOR.'index',
                'documents_dir' => DIRECTORY_SEPARATOR.'engine'.DIRECTORY_SEPARATOR.'documents',
                'cache_dir' => DIRECTORY_SEPARATOR.'engine'.DIRECTORY_SEPARATOR.'cache',
                'fuzzy_cost' => 1,
                'serializableObjects' => [
                    DateTime::class => function($datetime) { return $datetime->format(DATE_ATOM); }
                ]
            ],
            'schemas' => [
                'example-post' => [
                    'title' => [
                        '_type' => 'string',
                        '_indexed' => true,
                        '_boost' => 10
                    ],
                    'content' => [
                        '_type' => 'text',
                        '_indexed' => true,
                        '_boost' => 0.5
                    ],
                    'date' => [
                        '_type' => 'datetime',
                        '_indexed' => true,
                        '_boost' => 2
                    ],
                    'categories' => [
                        '_type' => 'list',
                        '_type.' => 'string',
                        '_indexed' => true,
                        '_filterable' => true,
                        '_boost' => 6
                    ],
                    'comments' => [
                        '_type' => 'list',
                        '_type.' => 'array',
                        '_array' => [
                            'author' => [
                                '_type' => 'string',
                                '_indexed' => true,
                                '_filterable' => true,
                                '_boost' => 1
                            ],
                            'date' => [
                                '_type' => 'datetime',
                                '_indexed' => true,
                                '_boost' => 0
                            ],
                            'message' => [
                                '_type' => 'text',
                                '_indexed' => true,
                                '_boost' => 0.1
                            ]
                        ]
                    ]
                ]
            ],
            'types' => [
                'datetime' => [
                    DateFormatTokenizer::class,
                    DateSplitTokenizer::class
                ],
                '_default' => [
                    LowerCaseTokenizer::class,
                    WhiteSpaceTokenizer::class,
                    singleQuoteTokenizer::class,
                    TrimPunctuationTokenizer::class
                ]
            ]
        ];
    }

}
