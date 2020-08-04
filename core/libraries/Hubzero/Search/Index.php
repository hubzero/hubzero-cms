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
	 * __construct
	 *
	 * @param mixed $config - Configuration object
	 * @access public
	 * @return void
	 */
	public function __construct($config)
	{
		$engine = $config->get('engine');
		if ($engine != 'hubgraph')
		{
			$adapter = "\\Hubzero\\Search\\Adapters\\" . ucfirst($engine) . 'IndexAdapter';
			$this->adapter = new $adapter($config);
		}
		return $this;
	}

	/**
	 * getLogs - Returns an array of search engine query log entries
	 *
	 * @access public
	 * @return void
	 */
	public function getLogs()
	{
		$logs = $this->adapter->getLogs();
		return $logs;
	}

	/**
	 * defragment search index
	 *
	 * @return void
	 */
	public function optimize()
	{
		return $this->adapter->optimize();
	}

	/**
	 * lastInsert - Returns the timestamp of the last document indexed
	 *
	 * @access public
	 * @return void
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
	 * @return void
	 */
	public function status()
	{
		return $this->adapter->status();
	}

	/**
	 * index - Stores a document within an index
	 *
	 * @param mixed $document
	 * @access public
	 * @return void
	 */
	public function index($document, $overwrite = null, $commitWithin = 3000, $buffer = 1500)
	{
		return $this->adapter->index($document, $overwrite, $commitWithin, $buffer);
	}

	/**
	 * updateIndex - Update existing index item
	 *
	 * @param mixed $document
	 * @access public
	 * @return void
	 */
	public function updateIndex($document, $commitWithin = 3000)
	{
		return $this->adapter->updateIndex($document, $commitWithin);
	}

	/**
	 * delete - Deletes a document from the index
	 *
	 * @param string $id
	 * @access public
	 * @return void
	 */
	public function delete($id)
	{
		return $this->adapter->delete($id);
	}
}
