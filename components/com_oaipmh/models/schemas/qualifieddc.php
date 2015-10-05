<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Oaipmh\Models\Schemas;

use Components\Oaipmh\Models\Xml\Response;
use Components\Oaipmh\Models\Service;
use Components\Oaipmh\Models\Schema;

require_once(__DIR__ . '/dublincore.php');

/**
 * Qualified Dublin Core schema handler
 */
class QualifiedDC extends DublinCore
{
	/**
	 * Schema prefix
	 * 
	 * @var  string
	 */
	public static $prefix = 'oai_qdc';

	/**
	 * Schema description
	 * 
	 * @var  string http://www.bepress.com/assets/xsd/oai_qualified_dc.xsd
	 */
	public static $schema = 'http://worldcat.org/xmlschemas/qdc/1.0/qdc-1.0.xsd';

	/**
	 * Schema namespace
	 * 
	 * @var  string http://www.bepress.com/OAI/2.0/qualified-dublin-core/
	 */
	public static $ns = 'http://worldcat.org/xmlschemas/qdc-1.0/';

	/**
	 * Get the schema name
	 *
	 * @return  string
	 */
	public function name()
	{
		return 'Qualified Dublin Core';
	}

	/**
	 * Does this adapter respond to a mime type
	 *
	 * @param   string   $type  Schema type
	 * @return  boolean
	 */
	public static function handles($type)
	{
		return in_array($type, array(
			'qdc',
			'dcq',
			'oai_qdc',
			'qualified-dublin-core',
			'qualifieddublincore',
			'qualifiedddc',
			__CLASS__
		));
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
					->element('oai_qdc:qualifieddc')
						->attr('xmlns:' . self::$prefix, self::$ns)
						->attr('xmlns:dcterms', 'http://purl.org/dc/terms/')
						->attr('xmlns:dc', 'http://purl.org/dc/elements/1.1/')
						->attr('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance')
						->attr('xsi:schemaLocation', self::$ns . ' ' . self::$schema . ' http://purl.org/net/oclcterms http://worldcat.org/xmlschemas/oclcterms/1.4/oclcterms-1.4.xsd');

			$dcs = array(
				'title'   => array(
					'alternative'
				),
				'creator' => null,
				'subject' => null,
				'date'    => array(
					'created',
					'valid',
					'available',
					'issued',
					'modified',
					'dateAccepted',
					'dateCopyrighted',
					'dateSubmitted'
				),
				'identifier'  => null,
				'description' => array(
					'tableOfContents',
					'abstract'
				),
				'type'        => null,
				'publisher'   => null,
				'rights'      => null,
				'contributor' => null,
				'relation'    => array(
					'isVersionOf',
					'hasVersion',
					'isReplacedBy',
					'replaces',
					'isRequiredBy',
					'requires',
					'isPartOf',
					'hasPart',
					'isReferencedBy',
					'references',
					'isFormatof',
					'hasFormat',
					'conformsTo'
				),
				'format'   => array(
					'medium',
					'extent'
				),
				'coverage' => array(
					'spatial',
					'temporal'
				),
				'language' => null,
				'source'   => null
			);

			// Loop through DC elements
			foreach ($dcs as $dc => $attrs)
			{
				if (!isset($result->$dc))
				{
					continue;
				}

				if (is_array($result->$dc))
				{
					foreach ($result->$dc as $val)
					{
						$term = '';

						if (is_array($val))
						{
							$res  = $val['value'];
							$term = $val['type'];
							// Make sure it's a valid modifier
							if (!in_array($term, $attrs))
							{
								$term = '';
							}
						}
						else
						{
							$res = $val;
						}

						$this->response->element(($term ? 'dcterms:' . $term : 'dc:' . $dc), $this->prepare($res))->end();
					}
				}
				elseif (!empty($result->$dc))
				{
					if ($dc == 'date')
					{
						$this->response->element('dc:' . $dc, \JFactory::getDate($result->date)->format($gran))->end();
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
