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

namespace Plugins\Oaipmh\Resources\Data;

use Hubzero\Base\Object;
use Components\Oaipmh\Models\Provider;

require_once(JPATH_ROOT . '/components/com_oaipmh/models/provider.php');

/**
 * Data miner for resources to be used by OAI-PMH
 */
class Miner extends Object implements Provider
{
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
			$db = \JFactory::getDBO();
		}

		if (!($db instanceof \JDatabase))
		{
			throw new \Exception(\Lang::txt('Database must be of type JDatabase'), 500);
		}

		$this->database = $db;
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
		}

		$query = "SELECT r.id, " . $this->database->quote($this->name()) . " AS `base` FROM `#__resources` AS r WHERE r.`standalone`=1 AND r.`published`=1";
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
			$query .= " AND r.`created` > " . $this->database->quote($filters['from']);
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
			$query .= " AND r.`created` < " . $this->database->quote($filters['until']);
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
			$this->database->setQuery(
				"SELECT a.`doi`
				FROM `#__doi_mapping` AS a
				LEFT JOIN `#__tool_version` AS v ON v.id=a.versionid
				WHERE a.rid=" . $this->database->quote($id) . " AND v.revision=" . $this->database->quote($revision) . "
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
		$record->subject = $this->database->loadResultArray();

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
				$record->creator = $this->database->loadResultArray();
			}

			$record->relation = array();

			if ($revision)
			{
				$record->relation[] = array(
					'type'  => 'isVersionOf',
					'value' => $this->identifier($id, '', 0)
				);
			}

			$this->database->setQuery(
				"SELECT v.id, v.revision, d.*
				FROM `#__tool_version` as v
				LEFT JOIN `#__doi_mapping` as d
				ON d.alias = v.toolname
				AND d.local_revision=v.revision
				WHERE v.toolname = " . $this->database->quote($record->alias) . "
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
			$record->creator = $this->database->loadResultArray();
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
			$identifier = 'http://dx.doi.org/' . $doi;
		}
		else
		{
			$identifier = rtrim(\Request::base(), '/') . '/' . ltrim(\Route::url('index.php?option=com_resources&id=' . $id . ($rev ? '&rev=' . $rev : '')), '/');
		}

		return $identifier;
	}
}
