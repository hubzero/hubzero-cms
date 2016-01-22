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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Oaipmh\Resources\Data;

use Hubzero\Base\Object;
use Components\Oaipmh\Models\Provider;

require_once(PATH_CORE . '/components/com_oaipmh/models/provider.php');

/**
 * Data miner for resources to be used by OAI-PMH
 */
class Miner extends Object implements Provider
{
	/**
	 * Base URL
	 * 
	 * @var  string
	 */
	protected static $base = null;

	/**
	 * Database connection
	 * 
	 * @var  object
	 */
	protected $database = null;

	/**
	 * Data source name
	 * 
	 * @var  string
	 */
	protected $name = 'resources';

	/**
	 * Data source aliases
	 * 
	 * @var  object
	 */
	protected $provides = array(
		'resources',
		'resource'
	);

	/**
	 * Constructor
	 *
	 * @param   object  $db
	 * @return  void
	 * @throws  Exception
	 */
	public function __construct($db=null)
	{
		if (!$db)
		{
			$db = \App::get('db');
		}

		if (!($db instanceof \Hubzero\Database\Driver) && !($db instanceof \JDatabase))
		{
			throw new \Exception(\Lang::txt('Database must be of type JDatabase'), 500);
		}

		$this->database = $db;

		if (is_null(self::$base))
		{
			self::$base = rtrim(\Request::getSchemeAndHttpHost(), '/');
		}
	}

	/**
	 * Get Data source name
	 *
	 * @return  string
	 */
	public function name()
	{
		return $this->name;
	}

	/**
	 * Does this provider handle the specified type?
	 *
	 * @param   string   $type
	 * @return  boolean
	 */
	public function provides($type)
	{
		return in_array($type, $this->provides);
	}

	/**
	 * Get query for returning sets
	 *
	 * @return  string
	 */
	public function sets()
	{
		$query = "SELECT alias, type, description, " . $this->database->quote($this->name()) . " as base
				FROM `#__resource_types`";
		if ($type = $this->get('type'))
		{
			$query .= " WHERE id=" . $this->database->quote($type);
		}
		else
		{
			$query .= " WHERE category=" . $this->database->quote(27);
		}

		return $query;
	}

	/**
	 * Build query for retrieving records
	 *
	 * @param   array   $filters
	 * @return  string
	 */
	public function records($filters = array())
	{
		if (isset($filters['set']) && $filters['set'])
		{
			if (!preg_match('/^resources\:(.+)/i', $filters['set'], $matches))
			{
				return '';
			}

			$set = trim($matches[1]);
			$this->database->setQuery("SELECT t.id FROM `#__resource_types` AS t WHERE t.alias=" . $this->database->quote($set));
			$this->set('type', $this->database->loadResult());
			if (!$this->get('type'))
			{
				return '';
			}
		}

		$query = "SELECT CASE WHEN v.revision THEN CONCAT(r.id, ':', v.revision) ELSE r.id END AS id, " . $this->database->quote($this->name()) . " AS `base`
				FROM `#__resources` AS r
				LEFT JOIN `#__tool_version` AS v ON v.`toolname`=r.`alias`
				WHERE r.`standalone`=1 AND r.`published`=1";
		if ($type = $this->get('type'))
		{
			$query .= " AND r.`type`=" . $this->database->quote($type);
		}

		if (isset($filters['from']) && $filters['from'])
		{
			$d = explode('-', $filters['from']);
			$filters['from'] = $d[0];
			$filters['from'] .= '-' . (isset($d[1]) ? $d[1] : '00');
			$filters['from'] .= '-' . (isset($d[2]) ? $d[2] : '00');

			if (!preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $filters['from']))
			{
				$filters['from'] .= ' 00:00:00';
			}
			$query .= " AND (
				(r.`publish_up` != '0000-00-00 00:00:00' AND r.`publish_up` >= " . $this->database->quote($filters['from']) . ") OR r.`created` >= " . $this->database->quote($filters['from']) . "
			)";
		}
		if (isset($filters['until']) && $filters['until'])
		{
			$d = explode('-', $filters['until']);
			$filters['until'] = $d[0];
			$filters['until'] .= '-' . (isset($d[1]) ? $d[1] : '00');
			$filters['until'] .= '-' . (isset($d[2]) ? $d[2] : '00');

			if (!preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $filters['until']))
			{
				$filters['until'] .= ' 00:00:00';
			}
			$query .= " AND (
				(r.`publish_up` != '0000-00-00 00:00:00' AND r.`publish_up` < " . $this->database->quote($filters['until']) . ") OR r.`created` < " . $this->database->quote($filters['until']) . "
			)";
		}

		return $query;
	}

	/**
	 * Process list of records
	 *
	 * @param   array  $records
	 * @return  array
	 */
	public function postRecords($records)
	{
		foreach ($records as $i => $record)
		{
			if (!$this->provides($record->base))
			{
				continue;
			}

			$records[$i] = $this->record($record->id);
		}

		return $records;
	}

	/**
	 * Try to match a given ID as being an
	 * ID of the data type.
	 *
	 * @param   string   $identifier
	 * @return  integer
	 */
	public function match($identifier)
	{
		if (preg_match('/(.*?)\/resources\/(\d+)(?:\?rev=(\d+))?/i', $identifier, $matches))
		{
			return $matches[2] . (isset($matches[3]) && is_numeric($matches[3]) ? ':' . $matches[3] : '');
		}

		$resolver = $this->doiResolver();
		if (substr($identifier, 0, strlen($resolver)) == $resolver)
		{
			$identifier = substr($identifier, strlen($resolver));
		}

		$this->database->setQuery(
			"SELECT a.`rid`, v.`revision`
			FROM `#__doi_mapping` AS a
			LEFT JOIN `#__tool_version` AS v ON v.`id`=a.`versionid`
			WHERE a.`doi`=" . $this->database->quote($identifier) . "
			LIMIT 1"
		);
		$doi = $this->database->loadObject();
		if ($doi && $doi->rid)
		{
			return $doi->rid . ($doi->revision ? ':' . $doi->revision : '');
		}

		return 0;
	}

	/**
	 * Process a single record
	 *
	 * @param   integer  $id
	 * @return  object
	 */
	public function record($id)
	{
		if (strstr($id, ':'))
		{
			list($id, $revision) = explode(':', $id);
		}
		$id = intval($id);
		if (!isset($revision))
		{
			$revision = 0;
		}

		$this->database->setQuery(
			"SELECT r.id, r.id AS identifier, r.title, r.introtext AS description, r.fulltxt, r.created, r.publish_up, r.alias, rt.alias AS type
			FROM `#__resources` AS r
			INNER JOIN `#__resource_types` AS rt ON r.type = rt.id
			WHERE r.id = " . $this->database->quote($id)
		);
		$record = $this->database->loadObject();

		$record->base = $this->name();
		$record->type = $record->base . ':' . $record->type;

		if ($revision)
		{
			$this->database->setQuery(
				"SELECT *
				FROM `#__tool_version`
				WHERE toolname=" . $this->database->quote($record->alias) . " AND revision=" . $this->database->quote($revision) . "
				LIMIT 1"
			);
			$tool = $this->database->loadObject();
			if ($tool->id)
			{
				$record->title .= ' [version ' . $tool->version . ']';
				$record->fulltxt = $tool->fulltxt;
				$record->publish_up = $tool->released;
			}
		}

		$record->date = $record->created;
		if ($record->publish_up && $record->publish_up != $this->database->getNullDate())
		{
			$record->date = $record->publish_up;
		}

		if (!$record->description)
		{
			$record->description = $record->fulltxt;
			$record->description = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $record->description);
		}
		$record->description = strip_tags($record->description);
		$record->description = trim($record->description);

		unset($record->publish_up);
		unset($record->created);
		unset($record->fulltxt);

		$isTool = 0;

		if ($record->alias)
		{
			$this->database->setQuery(
				"SELECT id
				FROM `#__tool`
				WHERE toolname=" . $this->database->quote($record->alias) . "
				LIMIT 1"
			);
			$isTool = $this->database->loadResult();
		}

		if ($revision)
		{
			/*"SELECT a.`doi`
				FROM `#__doi_mapping` AS a
				LEFT JOIN `#__tool_version` AS v ON v.id=a.versionid
				WHERE a.rid=" . $this->database->quote($id) . " AND v.revision=" . $this->database->quote($revision) . "
				LIMIT 1"*/
			$this->database->setQuery(
				"SELECT a.`doi`
				FROM `#__doi_mapping` AS a
				WHERE a.rid=" . $this->database->quote($id) . " AND a.local_revision=" . $this->database->quote($revision) . "
				LIMIT 1"
			);
		}
		else
		{
			$this->database->setQuery(
				"SELECT a.`doi`
				FROM `#__doi_mapping` AS a
				WHERE a.rid=" . $this->database->quote($id) . "
				ORDER BY `versionid` DESC LIMIT 1"
			);
		}
		$record->identifier = $this->identifier($id, $this->database->loadResult(), $revision);

		$this->database->setQuery(
			"SELECT DISTINCT t.raw_tag
			FROM `#__tags` t, `#__tags_object` tos
			WHERE t.id = tos.tagid AND tos.objectid=" . $this->database->quote($id) . " AND tos.tbl='resources' AND t.admin=0
			ORDER BY t.raw_tag"
		);
		$record->subject = $this->database->loadColumn();

		$record->relation = array();

		$this->database->setQuery(
			"SELECT r.id, r.title, r.type, r.logical_type AS logicaltype, r.created, r.created_by,
			r.published, r.publish_up, r.path, r.access, t.type AS logicaltitle, rt.type AS typetitle, r.standalone
			FROM `#__resources` AS r
			INNER JOIN `#__resource_types` AS rt ON r.type=rt.id
			INNER JOIN `#__resource_assoc` AS a ON r.id=a.child_id
			LEFT JOIN `#__resource_types` AS t ON r.logical_type=t.id
			WHERE r.published=1 AND a.parent_id=" . $this->database->quote($id) . "
			ORDER BY a.ordering, a.grouping"
		);
		if ($children = $this->database->loadObjectList())
		{
			require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');
			require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php');

			foreach ($children as $child)
			{
				$uri = \Components\Resources\Helpers\Html::processPath('com_resources', $child, $id, 3);
				if (substr($uri, 0, 4) != 'http')
				{
					$uri = self::$base . '/' . ltrim($uri, '/');
				}

				$record->relation[] = array(
					'type'  => 'hasPart',
					'value' => $uri
				);
			}
		}

		$this->database->setQuery(
			"SELECT DISTINCT r.id
			FROM `#__resources` AS r
			INNER JOIN `#__resource_assoc` AS a ON r.id=a.parent_id
			WHERE r.published=1 AND a.child_id=" . $this->database->quote($id) . "
			ORDER BY a.ordering, a.grouping"
		);
		if ($parents = $this->database->loadObjectList())
		{
			foreach ($parents as $parent)
			{
				$record->relation[] = array(
					'type'  => 'isPartOf',
					'value' => $this->identifier($parent->id, 0)
				);
			}
		}

		if ($isTool)
		{
			if ($revision)
			{
				$this->database->setQuery(
					"SELECT n.uidNumber AS id,
						CASE WHEN t.name!='' AND t.name IS NOT NULL THEN t.name
						ELSE n.name
						END AS `name`
					FROM `#__tool_authors` AS t, `#__xprofiles` AS n, `#__tool_version` AS v
					WHERE n.uidNumber=t.uid AND t.toolname=" . $this->database->quote($record->alias) . " AND v.id=t.version_id and v.state<>3
					AND t.revision=" . $this->database->quote($revision) . "
					ORDER BY t.ordering"
				);
				$record->creator = $this->database->loadColumn();

				/*$record->relation[] = array(
					'type'  => 'isVersionOf',
					'value' => $this->identifier($id, '', 0)
				);*/
			}

			$this->database->setQuery(
				"SELECT v.id, v.revision, d.*
				FROM `#__tool_version` as v
				LEFT JOIN `#__doi_mapping` as d
				ON d.alias = v.toolname
				AND d.local_revision=v.revision
				WHERE v.toolname = " . $this->database->quote($record->alias) . "
				AND v.state!=3
				ORDER BY v.state DESC, v.revision DESC"
			);
			$versions = $this->database->loadObjectList();
			foreach ($versions as $i => $v)
			{
				if (!$v->revision || $v->revision == $revision)
				{
					continue;
				}

				$record->relation[] = array(
					'type'  => 'hasVersion',
					'value' => $this->identifier($id, $v->doi, $v->revision)
				);
			}
		}

		if (!isset($record->creator))
		{
			$this->database->setQuery(
				"SELECT 
					CASE WHEN a.name!='' AND a.name IS NOT NULL THEN a.name
					ELSE n.name
					END AS `name`
				FROM `#__author_assoc` AS a
				LEFT JOIN `#__xprofiles` AS n ON n.uidNumber=a.authorid
				WHERE a.subtable='resources' AND a.subid=" . $this->database->quote($id) . " AND a.role!='submitter'
				ORDER BY a.ordering, a.name"
			);
			$record->creator = $this->database->loadColumn();
		}

		$this->database->setQuery(
			"SELECT *
			FROM `#__citations` AS a
			INNER JOIN `#__citations_assoc` AS n ON n.`cid`=a.`id`
			WHERE n.`tbl`='resource' AND n.`oid`=" . $this->database->quote($id) . " AND a.`published`=1
			ORDER BY `year` DESC"
		);
		$references = $this->database->loadObjectList();
		if (count($references) && file_exists(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php'))
		{
			include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php');

			$formatter = new \Components\Citations\Helpers\Format;
			$formatter->setTemplate('apa');

			foreach ($references as $reference)
			{
				//<dcterms:isReferencedBy>uytruytry</dcterms:isReferencedBy>
				//<dcterms:isVersionOf>jgkhfjf</dcterms:isVersionOf>
				$cite = strip_tags(html_entity_decode($reference->formatted ? $reference->formatted : \Components\Citations\Helpers\Format::formatReference($reference, '')));
				$cite = str_replace('&quot;', '"', $cite);

				$record->relation[] = array(
					'type'  => 'references',
					'value' => trim($cite)
				);
			}
		}

		return $record;
	}

	/**
	 * Build the identifier URI for a resource
	 *
	 * @param   integer  $id
	 * @param   string   $doi
	 * @param   integer  $rev
	 * @return  string
	 */
	protected function identifier($id, $doi, $rev=0)
	{
		if ($doi)
		{
			$identifier = $this->doiResolver() . $doi;
		}
		else
		{
			$identifier = self::$base . '/' . ltrim(\Route::url('index.php?option=com_resources&id=' . $id . ($rev ? '&rev=' . $rev : '')), '/');
		}

		return $identifier;
	}

	/**
	 * Get the DOI resolver
	 *
	 * @return  string
	 */
	protected function doiResolver()
	{
		static $resolver;

		if (!$resolver)
		{
			$resolver = \Component::params('com_tools')->get('doi_resolve', 'http://dx.doi.org/');
			$resolver = rtrim($resolver, '/') . '/';
			if ($shoulder = \Component::params('com_tools')->get('doi_shoulder'))
			{
				$resolver .= $shoulder . '/';
			}
		}

		return $resolver;
	}
}
