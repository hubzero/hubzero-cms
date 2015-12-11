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

namespace Components\Oaipmh\Models\Schemas;

use Components\Oaipmh\Models\Xml\Response;
use Components\Oaipmh\Models\Service;
use Components\Oaipmh\Models\Schema;
use Hubzero\Base\Traits\Escapable;
use Date;

require_once(__DIR__ . '/../schema.php');

/**
 * Dublin Core schema handler
 */
class DublinCore implements Schema
{
	use Escapable;

	/**
	 * Schema prefix
	 * 
	 * @var  string
	 */
	public static $prefix = 'oai_dc';

	/**
	 * Schema description
	 * 
	 * @var  string
	 */
	public static $schema = 'http://www.openarchives.org/OAI/2.0/oai_dc.xsd';

	/**
	 * Schema namespace
	 * 
	 * @var  string
	 */
	public static $ns = 'http://www.openarchives.org/OAI/2.0/oai_dc/';

	/**
	 * Parent service
	 * 
	 * @var  object
	 */
	protected $service = null;

	/**
	 * XML Response
	 * 
	 * @var  object
	 */
	protected $response = null;

	/**
	 * Does this adapter respond to a mime type
	 *
	 * @param   string   $type  Schema type
	 * @return  boolean
	 */
	public static function handles($type)
	{
		return in_array($type, array(
			'dc',
			'oai_dc',
			'oai_pmh:dc',
			'simple-dublin-core',
			'dublincore',
			__CLASS__
		));
	}

	/**
	 * Constructor
	 *
	 * @param   object  $service
	 * @param   object  $response
	 * @return  void
	 */
	public function __construct($service=null, $response=null)
	{
		if ($service)
		{
			$this->setService($service);
		}

		if ($response)
		{
			$this->setResponse($response);
		}
	}

	/**
	 * Get the schema name
	 *
	 * @return  string
	 */
	public function name()
	{
		return 'Dublin Core';
	}

	/**
	 * Get the schema prefix
	 *
	 * @return  string
	 */
	public function prefix()
	{
		return self::$prefix;
	}

	/**
	 * Prepare a value for XML output
	 *
	 * @param   string  $var  The output to escape.
	 * @return  string  The prepared value.
	 */
	public function prepare($var)
	{
		$var = html_entity_decode(stripslashes($var));
		return $this->escape($var);
	}

	/**
	 * Get XML builder
	 *
	 * @return  object
	 */
	public function getService()
	{
		return $this->service;
	}

	/**
	 * Set service
	 *
	 * @param   object  $service
	 * @return  object
	 */
	public function setService(Service &$service)
	{
		$this->service = $service;

		return $this;
	}

	/**
	 * Get response
	 *
	 * @return  object
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
	 * Set response
	 *
	 * @param   object  $response
	 * @return  object
	 */
	public function setResponse(Response &$response)
	{
		$this->response = $response;

		return $this;
	}

	/**
	 * Process a list of sets
	 *
	 * @param   array   $iterator
	 * @return  object  $this
	 */
	public function set($set) //$iterator)
	{
		/*foreach ($iterator as $index => $set)
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

			if (!empty($set[2]))
			{
				$set[2] = html_entity_decode($set[2]);
				$set[2] = strip_tags($set[2]);

				$this->response
					->element('setDescription')
						->element('oai_dc:dc')
							->attr('xmlns:' . self::$prefix, self::$ns)
							->attr('xmlns:dc', 'http://purl.org/dc/elements/1.1/')
							->attr('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance')
							->attr('xsi:schemaLocation', self::$ns . ' ' . self::$schema)
							->element('dc:description', $this->escape($set[2]))->end()
						->end()
					->end();
			}

			$this->response
					->end();
		}*/
		$this->response
				->element('oai_dc:dc')
					->attr('xmlns:' . self::$prefix, self::$ns)
					->attr('xmlns:dc', 'http://purl.org/dc/elements/1.1/')
					->attr('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance')
					->attr('xsi:schemaLocation', self::$ns . ' ' . self::$schema)
					->element('dc:description', $this->escape($set[2]))->end()
				->end();

		return $this;
	}

	/**
	 * Process a list of records
	 *
	 * @param   array    $iterator
	 * @param   boolean  $metadata
	 * @return  object   $this
	 */
	public function records($iterator, $metadata=true)
	{
		// Loop through each item
		foreach ($iterator as $index => $record)
		{
			// Make sure we have a record
			if ($record === null)
			{
				continue;
			}

			$this->record($record, $metadata);
		}

		return $this;
	}

	/**
	 * Process a single record
	 *
	 * @param   object   $result
	 * @param   boolean  $metadata
	 * @return  object   $this
	 */
	public function record($result, $metadata=true)
	{
		if ($metadata)
		{
			$this->response
				->element('record')
					->element('header');
		}
		else
		{
			$this->response
				->element('header');
		}

		if (!empty($result->identifier))
		{
			$this->response->element('identifier', $result->identifier)->end();
		}

		// We want the "T" & "Z" strings in the output NOT the UTC offset (-400)
		$gran = $this->service->get('gran', 'c');
		if ($gran == 'c')
		{
			$gran = 'Y-m-d\Th:i:s\Z';
		}

		$datestamp = strtotime($result->date);
		$datestamp = gmdate($gran, $datestamp);
		if (!empty($datestamp))
		{
			$this->response->element('datestamp', $datestamp)->end();
		}
		if (!empty($result->type))
		{
			$this->response->element('setSpec', $result->type)->end();
		}

		$this->response->end(); // End header

		if ($metadata)
		{
			$this->response
				->element('metadata')
					->element('oai_dc:dc')
						->attr('xmlns:' . self::$prefix, self::$ns)
						->attr('xmlns:dc', 'http://purl.org/dc/elements/1.1/')
						->attr('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance')
						->attr('xsi:schemaLocation', self::$ns . ' ' . self::$schema);

			$dcs = array(
				'title',
				'creator',
				'subject',
				'date',
				'identifier',
				'description',
				'type',
				'publisher',
				'rights',
				'contributor',
				'relation',
				'format',
				'coverage',
				'language',
				'source'
			);

			// Loop through DC elements
			foreach ($dcs as $dc)
			{
				if (!isset($result->$dc))
				{
					continue;
				}

				if (is_array($result->$dc))
				{
					foreach ($result->$dc as $val)
					{
						if (is_array($val))
						{
							$res  = $val['value'];
						}
						else
						{
							$res = $val;
						}

						$this->response->element('dc:' . $dc, $this->prepare($res))->end();
					}
				}
				elseif (!empty($result->$dc))
				{
					if ($dc == 'date')
					{
						$this->response->element('dc:' . $dc, $datestamp)->end();
					}
					else
					{
						$this->response->element('dc:' . $dc, $this->prepare($result->$dc))->end();
					}
				}
			}

			$this->response->end() // End oai_dc:dc
						->end(); // End metadata

			$this->response->end(); // End record
		}

		return $this;
	}
}
