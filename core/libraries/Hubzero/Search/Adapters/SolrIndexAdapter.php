<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Search\Adapters;

use Hubzero\Search\IndexInterface;
use Solarium;

/**
 * SolrIndexAdapter - Index adapter for Solr using the Solarium library
 *
 * @uses IndexInterface
 * @uses Solarium
 */
class SolrIndexAdapter implements IndexInterface
{
	var $logPath = null;

	var $connection = null;

	var $query = null;

	var $bufferAdd = null;

	var $commitWithin = null;

	var $overwrite = null;

	/**
	 * __construct - Constructor for adapter, sets config and established connection
	 *
	 * @param mixed $config - Configuration object
	 * @access public
	 * @return void
	 */
	public function __construct($config)
	{
		// Some setup information
		$core = $config->get('solr_core','hubzero-solr-core');
		$port = $config->get('solr_port', '2090');
		$host = $config->get('solr_host','localhost');
		$path = $config->get('solr_path','/');
		$context = $config->get('solr_context','solr');

		$this->logPath = $config->get('solr_log_path');

		// Build the Solr config object
		$solrConfig = array( 'endpoint' =>
			array( $core =>
				array(	'host' => $host,
					'port' => $port,
					'path' => $path,
					'context' => $context,
					'core' => $core
				)
			)
		);

		// Create the client
		$adapter = new Solarium\Core\Client\Adapter\Curl();
		$eventDispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();

		$this->connection = new Solarium\Client($adapter, $eventDispatcher, $solrConfig);

		// Create the Solr Query object
		$this->query = $this->connection->createSelect();
	}

	/**
	 * getLogs - Returns an array of search engine query log entries
	 *
	 * @access public
	 * @return void
	 */
	public function getLogs()
	{
		if (file_exists($this->logPath))
		{
			$log = Filesystem::read($this->logPath);
			$levels = array();
			$this->logs = explode("\n", $log);
		}
		else
		{
			return array();
		}

		return $this->logs;
	}

	/**
	 * lastInsert - Returns the timestamp of the last document indexed
	 *
	 * @access public
	 * @return void
	 */
	public function lastInsert()
	{
		$query = $this->connection->createSelect();
		$query->setQuery('*:*');
		$query->setFields(array('timestamp'));
		$query->addSort('timestamp', 'DESC');
		$query->setRows(1);
		$query->setStart(0);

		$results = $this->connection->execute($query);
		foreach ($results as $document)
		{
			foreach ($document as $field => $value)
			{
				$result = $value;
				return $result;
			}
		}
	}

	/**
	 * status - Checks whether or not the search engine is responding
	 *
	 * @access public
	 * @return void
	 */
	public function status()
	{
		try
		{
			$pingRequest = $this->connection->createPing();
			$ping = $this->connection->ping($pingRequest);
			$pong = $ping->getData();
			$alive = false;

			if (isset($pong['status']) && $pong['status'] === "OK")
			{
				return true;
			}
		}
		catch (\Solarium\Exception $e)
		{
			return false;
		}
	}


	/**
	 * index - Stores a document within an index
	 *
	 * @param mixed $document
	 * @access public
	 * @return void
	 */
	public function index($document, $overwrite = null, $commitWithin = null, $buffer = 1500)
	{
		$this->initBufferAdd($overwrite, $commitWithin, $buffer);
		$this->addDocument($document);
	}

	/**
	 * optimize - Defragment the index
	 *
	 * @access public
	 * @return Solarium\QueryType\Update\Result
	 */
	public function optimize()
	{
		$update = $this->connection->createUpdate();
		$update->addOptimize();
		return $this->connection->update($update);
	}

	/**
	 * Initialize Solarium bufferedAdd plugin
	 * @param boolean $overwrite if true, overwrites existing entries with the same docId
	 * @param int $commitWithin time in milliseconds that a commit should happen
	 * @param int $buffer max number of documents to add before flushing
	 * @return Solarium\Plugin\BufferedAdd\BufferedAdd
	 */
	public function initBufferAdd($overwrite = null, $commitWithin = null, $buffer = null)
	{
		if (!isset($this->bufferAdd))
		{
			$this->bufferAdd = $this->connection->getPlugin('bufferedadd');

                        if ($commitWithin !== null) {
                                $this->bufferAdd->setCommitWithin($commitWithin); 
                                $this->commitWithin = $commitWithin;
                        }
                        if ($overwrite !== null) {
                                $this->bufferAdd->setOverwrite($overwrite);
                                $this->overwrite = $overwrite;
                        }
                        if ($buffer !== null) {
                                $this->bufferAdd->setBufferSize($buffer);
                        }
		}
		return $this->bufferAdd;
	}

	public function addDocument($document)
	{
		$update = $this->connection->createUpdate();
		$newDoc = $update->createDocument();
		foreach ($document as $field => $value)
		{
			if (!is_string($value))
			{
				$newDoc->$field = json_decode(json_encode($value), true);
			}
			else
			{
				$newDoc->setField($field, $value);
			}
		}
		$this->initBufferAdd()->addDocument($newDoc);
	}

	/**
	 * deleteById - Removes a single document from the search index
	 *
	 * @param string $id
	 * @access public
	 * @return mixed string if error caught
	 */
	public function delete($query)
	{
		$deleteQuery = $this->parseQuery($query);

		if (!empty($deleteQuery))
		{
			try
			{
				$update = $this->connection->createUpdate();
				$update->addDeleteQuery($deleteQuery);
				$update->addCommit();
				$response = $this->connection->update($update);
				return null;
			}
			catch (\Solarium\Exception\HttpException $e)
			{
				$body = json_decode($e->getBody());
				$message = isset($body->error->msg) ? $body->error->msg : $e->getStatusMessage();
				return $message;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * updateIndex - Updates a document existing in the search index
	 *
	 * @param mixed $document
	 * @param mixed $id
	 * @access public
	 * @return void
	 */
	public function updateIndex($document, $commitWithin = 3000)
	{
		$this->index($document, true, $commitWithin);
		return $this->finalize();
	}

	/**
	 * parseQuery - Translates symbols from query string
	 *
	 * @param  array $query - Passes in the query array
	 * @access private
	 * @return void
	 */
	private function parseQuery($query)
	{
		$string = '';
		if (is_array($query))
		{
			foreach ($query as $index => $value)
			{
				$string .= !empty($string) ? ' AND ' : '';
				$string .= $index . ':' . $value;
			}
		}
		else
		{
			$string = 'id:' . $query;
		}
		return $string;
	}

	/**
	 * Automatically flushes any remaining documents in the buffer
	 *
	 * @return mixed string if error caught/ null if successful
	 */
	public function finalize()
	{
		try
		{
			if (isset($this->bufferAdd))
			{
				$this->bufferAdd->flush($this->overwrite, $this->commitWithin);
			}
			return null;
		}
		catch (\Solarium\Exception\HttpException $e)
		{
			$jbody = $e->getBody();
			$body = json_decode($jbody == null ? '' : $jbody);
			$message = isset($body->error->msg) ? $body->error->msg : $e->getStatusMessage();
			return $message;
		}
	}
}
