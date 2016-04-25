<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Oaipmh\Models;

use Components\Oaipmh\Models\Xml\Response;
use Hubzero\Base\Traits\Escapable;
use Hubzero\Base\Object;
use Hubzero\Database\Driver;
use Exception;
use Event;
use Lang;

require_once(__DIR__ . '/../models/xml/response.php');

/**
 * OAIPMH Provider for building responses
 */
class Service extends Object
{
	use Escapable;

	/**
	 * OAI-PMH error types
	 */
	const ERROR_BAD_VERB             = 'badVerb';
	const ERROR_BAD_ARGUMENT         = 'badArgument';
	const ERROR_BAD_ID               = 'idDoesNotExist';
	const ERROR_BAD_FORMAT           = 'cannotDisseminateFormat';
	const ERROR_RECORD_NOT_FOUND     = 'noRecordsMatch';
	const ERROR_BAD_RESUMPTION_TOKEN = 'badResumptionToken';
	const ERROR_NO_SET_HIERARCHY     = 'noSetHierarchy';

	/**
	 * Schema to use
	 * 
	 * @var  object
	 */
	protected $schema = null;

	/**
	 * List of schemas
	 * 
	 * @var  array
	 */
	protected $schemas = null;

	/**
	 * XML Response
	 * 
	 * @var  object
	 */
	protected $response = null;

	/**
	 * List of providers
	 * 
	 * @var  array
	 */
	protected $providers = array();

	/**
	 * Database connection
	 * 
	 * @var  object
	 */
	protected $database = null;

	/**
	 * Constructor
	 *
	 * @param   string  $stylesheet  Optional stylesheet to link to
	 * @param   object  $db          Optional database connection
	 * @param   string  $version     XML Version
	 * @param   string  $encoding    XML Encoding
	 * @return  void
	 */
	public function __construct($stylesheet=null, $db=null, $version = '1.0', $encoding = 'utf-8')
	{
		$this->response = new Response($version, $encoding);
		if ($stylesheet)
		{
			$this->response->stylesheet((string) $stylesheet);
		}
		$this->response
			->element('OAI-PMH')
				->attr('xmlns', 'http://www.openarchives.org/OAI/2.0/')
				->attr('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance')
				->attr('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd')
				->element('responseDate', gmdate('c', time()))->end();

		$this->loadSchemas();

		/*if ($schema)
		{
			$this->setSchema($schema);
		}*/

		$this->loadProviders();

		if (!$db)
		{
			$db = \App::get('db');
		}

		$this->setDbo($db);
	}

	/**
	 * Set the database connection
	 *
	 * @param   object  $db    Database connection
	 * @return  object  $this
	 */
	public function setDbo(Driver $db)
	{
		$this->database = $db;

		return $this;
	}

	/**
	 * Get the database connection
	 *
	 * @return  object
	 */
	public function getDbo()
	{
		return $this->database;
	}

	/**
	 * Load schemas
	 *
	 * @return  void
	 */
	private function loadSchemas()
	{
		foreach (glob(__DIR__ . DS . 'schemas' . DS . '*.php') as $schema)
		{
			require_once $schema;
		}

		$isSchemaClass = function($class)
		{
			return (in_array(__NAMESPACE__ . '\Schema', class_implements($class)));
		};

		$this->schemas = array_values(array_filter(get_declared_classes(), $isSchemaClass));
	}

	/**
	 * Get schema list
	 *
	 * @return  array
	 */
	public function getSchemas()
	{
		return $this->schemas;
	}

	/**
	 * Set schema
	 *
	 * @param   mixed   $type
	 * @return  object
	 * @throws  Exception
	 */
	public function setSchema($type)
	{
		if (!($type instanceof Schema))
		{
			$respondsTo = function($class) use ($type)
			{
				return $class::handles($type);
			};

			$responded = array_filter($this->schemas, $respondsTo);
			if ($schema = array_shift($responded))
			{
				$type = new $schema();
			}
		}

		$this->schema = $type;

		if (!($this->schema instanceof Schema))
		{
			return $this->error(self::ERROR_BAD_FORMAT, Lang::txt('Invalid or missing metadataPrefix'));
		}

		$this->schema->setService($this);
		$this->schema->setResponse($this->response);

		return $this;
	}

	/**
	 * Get schema
	 *
	 * @return  object
	 */
	public function getSchema()
	{
		return $this->schema;
	}

	/**
	 * Set the response object
	 *
	 * @param   object  $response
	 * @return  object  $this
	 */
	public function setResponse(Response $response)
	{
		$this->response = $response;

		return $this;
	}

	/**
	 * Get XML builder
	 *
	 * @return  object
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
	 * Load data providers
	 *
	 * Calls plugin event and passes self to
	 * the event method where plugins can
	 * register providers.
	 *
	 * @return  object
	 */
	private function loadProviders()
	{
		Event::trigger('oaipmh.onOaipmhProvider', array(&$this));

		return $this;
	}

	/**
	 * Register a provider
	 *
	 * @param   string  $key
	 * @param   object  $provider
	 * @return  object
	 */
	public function register($key, Provider $provider)
	{
		$this->providers[$key] = $provider;

		return $this;
	}

	/**
	 * Get the list of providers
	 *
	 * @return  array
	 */
	public function getProviders()
	{
		return $this->providers;
	}

	/**
	 * Set error
	 *
	 * @param   string  $error
	 * @param   string  $message
	 * @return  object
	 */
	public function error($error='', $message=null, $verb=null)
	{
		$this->setError($error ?: self::ERROR_BAD_ARGUMENT);

		$this->response
			->element('request', $this->get('baseURL') . '/oaipmh');

		if ($verb)
		{
			$this->response->attr('verb', $verb);
		}

		$this->response->end()
			->element('error', $message)
				->attr('code', ($error ?: self::ERROR_BAD_ARGUMENT))
			->end();

		return $this;
	}

	/**
	 * Identify the service
	 *
	 * @return  object
	 */
	public function identify()
	{
		$this->response
			->element('request', $this->get('baseURL') . '/oaipmh')
				->attr('verb', 'Identify')
			->end()
			->element('Identify');

		$fields = array(
			'repositoryName',
			'baseURL',
			'protocolVersion',
			'adminEmail',
			'earliestDatestamp',
			'deletedRecord',
			'granularity'
		);

		foreach ($fields as $key)
		{
			if ($val = $this->get($key))
			{
				$this->response->element($key, $val)->end();
			}
		}

		$this->response->end();

		return $this;
	}

	/**
	 * List formats
	 *
	 * @return  object
	 */
	public function formats()
	{
		$this->response
			->element('request', $this->get('repositoryName') . '/oaipmh')
				->attr('verb', 'ListMetadataFormats')
			->end()
			->element('ListMetadataFormats');

		foreach ($this->schemas as $schema)
		{
			$this->response
				->element('metadataFormat')
					->element('metadataPrefix', $schema::$prefix)->end()
					->element('schema', $schema::$schema)->end()
					->element('metadataNamespace', $schema::$ns)->end()
				->end();
		}

		$this->response->end();

		return $this;
	}

	/**
	 * List all sets
	 *
	 * @return  object
	 */
	public function sets()
	{
		// Set flow control vars
		$total   = 0;
		$limit   = $this->get('limit', 50);
		$start   = $this->get('start', 0);
		$records = array();

		foreach ($this->providers as $provider)
		{
			if (!($query = $provider->sets()))
			{
				continue;
			}

			$this->database->setQuery($query);
			$sets = $this->database->loadRowList();
			foreach ($sets as $set)
			{
				array_push($records, $set);
			}
		}

		$total = count($records);

		if (!$total)
		{
			return $this->error(self::ERROR_NO_SET_HIERARCHY, null, 'ListSets');
		}

		if ($total > $limit)
		{
			$records = array_slice($records, $start, $limit);
		}

		$this->response
			->element('request', $this->get('baseURL') . '/oaipmh')
				->attr('verb', 'ListSets')
			->end()
			->element('ListSets');

		foreach ($records as $index => $set)
		{
			// Make sure we have a record
			if ($set === null)
			{
				continue;
			}

			$spec = '';
			if (!empty($set[0]))
			{
				$spec = $set[0];
			}
			elseif (empty($set[0]) && !empty($set[1]))
			{
				$spec = strtolower($set[1]);
				$spec = str_replace(' ', '_', $spec);
			}
			if (isset($set[3]) && !empty($set[3]))
			{
				$spec = $set[3] . ':' . $spec;
			}

			$this->response
					->element('set')
						->element('setSpec', $spec)->end();

			if (!empty($set[1]))
			{
				$this->response->element('setName', $set[1])->end();
			}

			if (!empty($set[2]) && $this->schema)
			{
				$set[2] = html_entity_decode($set[2]);
				$set[2] = strip_tags($set[2]);

				$this->response
					->element('setDescription');

				$this->schema->set($set);

				$this->response
					->end();
			}

			$this->response
					->end();
		}

		// Write resumption token if needed
		if ($total > ($start + $limit))
		{
			$resumption = $this->encodeToken(
				$limit,
				$start,
				'',
				'',
				'',
				$this->get('metadataPrefix')
			);

			$this->response
				->element('resumptionToken', $resumption)
					->attr('completeListSize', $total)
					->attr('cursor', ($start + $limit))
				->end();
		}

		$this->response
			->end();

		return $this;
	}

	/**
	 * List only record headers
	 *
	 * @param   string  $from   Timestamp
	 * @param   string  $until  Timestamp
	 * @param   string  $set    Set to filter by
	 * @return  object  $this
	 */
	public function identifiers($from, $until, $set = null)
	{
		return $this->records($from, $until, $set, 'ListIdentifiers');
	}

	/**
	 * List records
	 *
	 * @param   string  $from   Timestamp
	 * @param   string  $until  Timestamp
	 * @param   string  $set    Set to filter by
	 * @param   string  $verb
	 * @return  object  $this
	 */
	public function records($from, $until, $set = null, $verb = 'ListRecords')
	{
		// Set flow control vars
		$limit = $this->get('limit', 50);
		$start = $this->get('start', 0);

		$queries = array();

		foreach ($this->providers as $provider)
		{
			if (!($query = $provider->records(array('from' => $from, 'until' => $until, 'set' => $set))))
			{
				continue;
			}

			$queries[] = $query;
		}

		if (!count($queries))
		{
			return $this->error(self::ERROR_RECORD_NOT_FOUND, null, $verb);
		}

		// Get Records
		$sql = "SELECT COUNT(*) FROM (" . implode(' UNION ', $queries) . ") AS m";
		$this->database->setQuery($sql);
		$total = $this->database->loadResult();

		$sql = "SELECT m.* FROM (" . implode(' UNION ', $queries) . ") AS m LIMIT " . $start . "," . $limit;
		$this->database->setQuery($sql);
		$records = $this->database->loadObjectList();

		if (!count($records))
		{
			return $this->error(self::ERROR_RECORD_NOT_FOUND, null, $verb);
		}

		// Hook for post processing
		foreach ($this->providers as $provider)
		{
			$records = $provider->postRecords($records);
		}

		// Write schema
		$this->response
			->element('request', $this->get('baseURL') . '/oaipmh')
				->attr('verb', $verb)
			->end()
			->element($verb);

		$this->schema->records($records, ($verb == 'ListIdentifiers' ? false : true));

		// Write resumption token if needed
		if ($total > ($start + $limit))
		{
			$resumption = $this->encodeToken(
				$limit,
				$start,
				$from,
				$until,
				$set,
				$this->get('metadataPrefix')
			);

			$this->response
				->element('resumptionToken', $resumption)
					->attr('completeListSize', $total)
					->attr('cursor', ($start + $limit))
				->end();
		}

		$this->response
			->end();

		return $this;
	}

	/**
	 * Create a resumption token
	 *
	 * @param   integer  $limit
	 * @param   integer  $start
	 * @param   string   $from
	 * @param   string   $until
	 * @param   string   $set
	 * @param   string   $prefix
	 * @return  string
	 */
	public function encodeToken($limit, $start, $from, $until, $set, $prefix)
	{
		$resumption = implode(',', array(
			$limit,
			$start,
			$from,
			$until,
			$set,
			$prefix
		));
		if (function_exists('gzcompress'))
		{
			$resumption = gzcompress($resumption);
		}
		$resumption = base64_encode($resumption);

		return rtrim(strtr($resumption, '+/', '-_'), '=');
	}

	/**
	 * Decode a resumption token
	 *
	 * @param   string  $resumption
	 * @return  array
	 */
	public function decodeToken($resumption)
	{
		$resumption = base64_decode(str_pad(strtr($resumption, '-_', '+/'), strlen($resumption) % 4, '=', STR_PAD_RIGHT));
		if (function_exists('gzuncompress'))
		{
			$resumption = gzuncompress($resumption);
		}

		list($limit, $start, $from, $until, $set, $prefix) = explode(',', $resumption);

		return array(
			'limit'  => $limit,
			'start'  => $start,
			'from'   => $from,
			'until'  => $until,
			'set'    => $set,
			'prefix' => $prefix
		);
	}

	/**
	 * Process a single record
	 *
	 * @param   string  $identifier
	 * @return  object  $this
	 */
	public function record($identifier)
	{
		$result = null;

		foreach ($this->providers as $provider)
		{
			if ($id = $provider->match($identifier))
			{
				$result = $provider->record($id);
				break;
			}
		}

		if (!$result)
		{
			return $this->error(self::ERROR_BAD_ID, null, 'GetRecord');
		}

		$this->response
			->element('request', $this->get('baseURL') . '/oaipmh')
				->attr('verb', 'GetRecord')
				->attr('identifier', $identifier)
				->attr('metadataPrefix', $this->get('metadataPrefix'))
			->end()
			->element('GetRecord');

		$this->schema->record($result);

		$this->response
			->end();

		return $this;
	}

	/**
	 * Get the final response as XML
	 *
	 * @return  string
	 */
	public function xml()
	{
		return $this->response->end()->getXml(true);
	}

	/**
	 * Return the XML response
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return $this->xml();
	}
}
