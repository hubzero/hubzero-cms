<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Search;

use Hubzero\Search\Adapters;

/**
 * Index - For indexing operations
 *
 */
class Index
{
    /**
     * @var  \Hubzero\Search\Adapters\SolrIndexAdapter|null  Solr is the only engine there is
     */
    public $adapter = null;

    /**
     * __construct
     *
     * @param mixed $config - Configuration object
     * @access public
     * @return static
     */
    public function __construct($config)
    {
        $engine = $config->get('engine');
        if ($engine != 'hubgraph') {
            $adapter = "\\Hubzero\\Search\\Adapters\\" . ucfirst($engine) . 'IndexAdapter';

            if (!is_a($adapter, Adapters\SolrIndexAdapter::class, true)) {
                throw new \RuntimeException(sprintf('No index adapter for the "%s" search engine', $engine));
            }

            $this->adapter = new $adapter($config);
        }
        return;
    }

    /**
     * getLogs - Returns an array of search engine query log entries
     *
     * @access public
     * @return mixed
     */
    public function getLogs()
    {
        $logs = $this->adapter->getLogs();
        return $logs;
    }

    /**
     * defragment search index
     *
     * @return mixed
     */
    public function optimize()
    {
        return $this->adapter->optimize();
    }

    /**
     * lastInsert - Returns the timestamp of the last document indexed
     *
     * @access public
     * @return mixed
     */
    public function lastInsert()
    {
        $lastInsert = $this->adapter->lastInsert();
        return $lastInsert;
    }

    /**
     * status - Checks whether or not the search engine is responding
     *
     * @access public
     * @return mixed
     */
    public function status()
    {
        return $this->adapter->status();
    }

    /**
     * index - Stores a document within an index
     *
     * @param mixed $document
     * @param   mixed  $overwrite
     * @param   int  $commitWithin
     * @param   int  $buffer
     * @access public
     * @return void  The adapter returns nothing
     */
    public function index($document, $overwrite = null, $commitWithin = 3000, $buffer = 1500)
    {
        $this->adapter->index($document, $overwrite, $commitWithin, $buffer);
    }

    /**
     * updateIndex - Update existing index item
     *
     * @param mixed $document
     * @param   mixed  $commitWithin
     * @access public
     * @return mixed
     */
    public function updateIndex($document, $commitWithin = 3000)
    {
        return $this->adapter->updateIndex($document, $commitWithin);
    }

    /**
     * delete - Deletes a document from the index
     *
     * @param string|array<mixed> $id  A document ID, or the fields of the documents to delete
     * @access public
     * @return mixed
     */
    public function delete($id)
    {
        return $this->adapter->delete($id);
    }
}
