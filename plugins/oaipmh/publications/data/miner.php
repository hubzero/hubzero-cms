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

namespace Plugins\Oaipmh\Publications\Data;

use Hubzero\Base\Object;
use Components\Oaipmh\Models\Provider;

require_once(JPATH_ROOT . '/components/com_oaipmh/models/provider.php');

/**
 * Data miner for publications to be used by OAI-PMH
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
	protected $name = 'publications';

	/**
	 * Data source aliases
	 * 
	 * @var  array
	 */
	protected $provides = array(
		'publications',
		'publication'
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
			throw new Exception(\JText::_('Database must be of type JDatabase'), 500);
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
				FROM `#__publication_master_types`";
		if ($type = $this->get('type'))
		{
			$query .= " WHERE id=" . $this->database->quote($type);
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
		$query = "SELECT p.id, " . $this->database->quote($this->name()) . " AS `base`
				FROM `#__publications` p, `#__publication_versions` pv
				WHERE p.id = pv.publication_id
				AND pv.state=1
				AND pv.published_up";

		if ($type = $this->get('type'))
		{
			$query .= " AND p.master_type=" . $this->database->quote($type);
		}

		if (isset($filters['from']) && $filters['from'])
		{
			$query .= " AND p.`created` > " . $filters['from'];
		}
		if (isset($filters['until']) && $filters['until'])
		{
			$query .= " AND p.`created` < " . $filters['until'];
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
		if (preg_match('/(.*?)\/publications\/(\d+)/i', $identifier, $matches))
		{
			return $matches[2];
		}

		$this->database->setQuery(
			"SELECT pv.`id`
			FROM `#__publication_versions` AS pv
			WHERE pv.`doi`=" . $this->database->quote($identifier) . "
			LIMIT 1"
		);
		if ($id = $this->database->loadResult())
		{
			return $id;
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
		$this->database->setQuery(
			"SELECT pv.*, pv.doi AS identifier, t.alias AS type
			FROM `#__publication_versions` AS pv
			INNER JOIN `#__publications` AS p ON p.id = pv.publication_id
			INNER JOIN `#__publications_master_types` AS rt ON rt.id = p.master_type
			WHERE p.id = " . $this->database->quote($id)
		);
		$record = $this->database->loadObject();
		$record->version_id = $record->id;
		$record->id = $id;

		$record->base = $this->name();
		$record->type = $record->base . ':' . $record->type;

		$record->description = strip_tags($record->description);
		$record->description = trim($record->description);

		$this->database->setQuery(
			"SELECT pv.submitted
			FROM `#__publication_versions` pv, `#__publications` p
			WHERE p.id = pv.publication_id AND p.id = " . $this->database->quote($id) . "
			ORDER BY pv.submitted DESC LIMIT 1"
		);
		$record->date = $this->database->loadResult();

		$this->database->setQuery(
			"SELECT pa.name
			FROM `#__publication_authors` pa, `#__publication_versions` pv, `#__publications` p
			WHERE pa.publication_version_id = pv.id AND pv.publication_id = p.id AND p.id=" . $this->database->quote($id) . "
			ORDER BY pa.name"
		);
		$record->creator = $this->database->loadResultArray();

		$this->database->setQuery(
			"SELECT DISTINCT t.raw_tag
			FROM `#__tags` t, `#__tags_object` tos
			WHERE t.id = tos.tagid AND tos.objectid=" . $this->database->quote($id) . " AND tos.tbl='publications' AND t.admin=0
			ORDER BY t.raw_tag"
		);
		$record->subject = $this->database->loadResultArray();

		return $record;
	}
}
