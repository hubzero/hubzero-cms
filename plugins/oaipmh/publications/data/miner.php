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

		if (is_null(self::$base))
		{
			self::$base = rtrim(\JURI::base(), '/');
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
		if (isset($filters['set']) && $filters['set'])
		{
			if (!preg_match('/^publications\:(.+)/i', $filters['set'], $matches))
			{
				return '';
			}

			$set = trim($matches[1]);
			$this->database->setQuery("SELECT t.id FROM `#__publication_master_types` AS t WHERE t.alias=" . $this->database->quote($set));
			$this->set('type', $this->database->loadResult());
		}

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
			"SELECT pv.*, pv.doi AS identifier, rt.alias AS type
			FROM `#__publication_versions` AS pv
			INNER JOIN `#__publications` AS p ON p.id = pv.publication_id
			INNER JOIN `#__publication_master_types` AS rt ON rt.id = p.master_type
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
			WHERE pa.publication_version_id = pv.id AND pa.role != 'submitter' AND pv.publication_id = p.id AND p.id=" . $this->database->quote($id) . "
			ORDER BY pa.name"
		);
		$record->creator = $this->database->loadColumn();

		$this->database->setQuery(
			"SELECT DISTINCT t.raw_tag
			FROM `#__tags` t, `#__tags_object` tos
			WHERE t.id = tos.tagid AND tos.objectid=" . $this->database->quote($id) . " AND tos.tbl='publications' AND t.admin=0
			ORDER BY t.raw_tag"
		);
		$record->subject = $this->database->loadColumn();

		// Relations
		$record->relation = array();

		$this->database->setQuery(
			"SELECT *
			FROM `#__citations` AS a
			LEFT JOIN `#__citations_assoc` AS n ON n.`cid`=a.`id`
			WHERE n.`tbl`='publication' AND n.`oid`=" . $this->database->quote($id) . " AND a.`published`=1
			ORDER BY `year` DESC"
		);
		$references = $this->database->loadObjectList();
		if (count($references) && file_exists(JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php'))
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php');

			$formatter = new \CitationFormat;
			$formatter->setTemplate('apa');

			foreach ($references as $reference)
			{
				$cite = strip_tags(html_entity_decode($reference->formatted ? $reference->formatted : \CitationFormat::formatReference($reference, '')));
				$cite = str_replace('&quot;', '"', $cite);

				$record->relation[] = array(
					'type'  => 'references',
					'value' => trim($cite)
				);
			}
		}

		return $record;
	}
}
