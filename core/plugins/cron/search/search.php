<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.	If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package	 hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license	 http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use \Components\Search\Models\IndexQueue;
use \Components\Search\Models\Hubtype;
use \Components\Search\Models\NoIndex;
use \Solarium;
use \stdClass;

class SolrEngine
{
	public function getConfig()
	{
		$config = array('endpoint' => array(
							'localhost' => array(
							'host' => '127.0.1.1',
							'port' =>8983 ,
							'path' => '/solr/hubsearch',
							'log_path' => '/opt/solr/server/logs/solr.log')));

		return $config;
	}

	public function start()
	{
		$cmd = "/opt/solr/bin/solr start -s /srv/example/solr/data/";
		shell_exec($cmd);
	}

	public function ping()
	{
		$config = $this->getConfig();
		$solr = new Solarium\Client($config);
		$ping = $solr->createPing();
		try {
			$ping = $solr->ping($ping);
			$ping = $ping->getData();
			$alive = false;
			if (isset($ping['status']) && $ping['status'] === "OK")
			{
				$alive = true;
			}
		} catch (\Solarium\Exception $e) {
			return false;
		}
		return $alive;
	}

	/** Query Operations **/
	// Create query object
	public function search($queryString = '')
	{
		$config = $this->getConfig();
		$this->solr = new Solarium\Client($config);
		$this->query = $this->solr->createSelect();
		$this->queryString = $queryString;
		$this->query->setQuery($queryString);
		return $this;
	}
	public function addFacet($label, $facetQueryString)
	{
		$this->facetSet = $this->query->getFacetSet();
		$this->facetSet->createFacetQuery($label)->setQuery($facetQueryString);
		return $this;
	}
	public function getFacetCount($label)
	{
		$count = $this->result->getFacetSet()->getFacet($label)->getValue();
		return $count;
	}
	public function setFields($fields)
	{
		if (!isset($this->fields))
		{
			$this->fields = array();
		}
		if (is_array($fields))
		{
			$this->fields = array_unique(array_merge($this->fields, $fields));
			$this->query->setFields($this->fields);
		}
		elseif (is_string($fields))
		{
			if (strpos("," , $fields) === FALSE)
			{
				$this->fields = array($fields);
			}
			else
			{
				$this->fields = explode("," , $fields);
			}
			$this->query->setFields($this->fields);
		}
	}
	public function getResult()
	{
		$this->result = $this->solr->select($this->query);
		return $this->result;
	}
	public function limit($number = 10)
	{
		$this->query->setRows($number);
		return $this;
	}
	public function orderBy($field, $direction)
	{
		$this->query->addSort($field, $direction);
		return $this;
	}
	public function lastInsert()
	{
		$this->search('*:*');
		$this->setFields('timestamp');
		$this->limit(1);
		$this->orderBy('timestamp', 'desc');
		$result = $this->getResult();
		if (isset($result->getDocuments()[0]))
		{
			return $result->getDocuments()[0]->getFields()['timestamp'];
		}
		else
		{
			return false;
		}
	}
	/* Update */
	public function delete($id = NULL)
	{
		// @FIXME Perhaps consider using addDeleteById(1234)?
		$config = $this->getConfig();
		$this->solr = new Solarium\Client($config);

		if ($id != NULL)
		{
			$update = $this->solr->createUpdate();
			$update->addDeleteQuery('id:'.$id);
			$update->addCommit();
			$response = $this->solr->update($update);

			// @FIXME: logical fallicy
			// Wild assumption that the update was successful
			return TRUE;
		}
		else
		{
			return Lang::txt('No record specified.');
		}
	}
	public function add($document, $docID = NULL)
	{
			$config = $this->getConfig();
			$this->solr = new Solarium\Client($config);

			$update = $this->solr->createUpdate();
			$solrDoc = $update->createDocument();
			foreach ($document as $key => $value)
			{
				$solrDoc->$key = $value;
			}
			if ($docID == NULL)
			{
				$solrDoc->id = hash('md5', time()*rand());
			}
			else
			{
				$solrDoc->id = $docID;
			}
			$update->addDocuments(array($solrDoc));
			$update->addCommit();
			$this->solr->update($update);

			return true;
	}

	/* Administrative & Mantainace */
	//public function cleanup($a
	public function getLog()
	{
		$config = $this->getConfig();
		$log = Filesystem::read($config['endpoint']['localhost']['log_path']);
		$levels = array();
		$this->logs = explode("\n",$log);

		return $this;
	}
}

/**
 * Cron plugin for support tickets
 */
class plgCronSearch extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return a list of events
	 *
	 * @return	array
	 */
	public function onCronEvents()
	{
		$this->loadLanguage();

		$obj = new stdClass();
		$obj->plugin = $this->_name;
		$obj->events = array(
			array(
				'name'	 => 'processQueue',
				'label'	=> Lang::txt('PLG_CRON_SEARCH_PROCESS_QUEUE'),
				'params' => ''
			)
		);

		return $obj;
	}

	/**
	 * Insert database records into the search Index 
	 *
	 * @param	 object	 $job	\Components\Cron\Models\Job
	 * @return	boolean
	 */
	public function processQueue(\Components\Cron\Models\Job $job)
	{
		// Get the relevant models 
		require_once(PATH_CORE . DS . 'components' . DS .'com_search' . DS . 'models' . DS . 'indexqueue.php');
		require_once(PATH_CORE . DS . 'components' . DS .'com_search' . DS . 'models' . DS . 'hubtype.php');
		require_once(PATH_CORE . DS . 'components' . DS .'com_search' . DS . 'models' . DS . 'noindex.php');

		// A counter for the number of documents indexed
		$indexedDocuments = 0;

		// Get the type needed to be indexed;
		$item = IndexQueue::all()->where('indexed', '=', 0)->limit(1)->row();
		$hubTypeID = $item->get('hubtype_id', 0);

		// @TODO relational
		$hubType = HubType::oneOrFail($hubTypeID);
		$noIndex = NoIndex::all()->select('hubid')->where('hubtype', '=', $hubType->get('type'))->rows()->toArray();
		$blacklist = array();
		foreach ($noIndex as $key => $value)
		{
			array_push($blacklist, $value['hubid']);
		}

		$mapping = unserialize($hubType->get('documentMatching'));

		// Get rows, limit 400
		$rows = $this->getRows($hubType, 100);

		$hubSearch = new SolrEngine;
		$queryString = 'hubtype:'.$hubType->get('type');
		$existingRecords = $hubSearch->search($queryString)->getResult();

		$existingTable = array();
		foreach ($existingRecords->getDocuments() as $document)
		{
			$existingTable[$document['hubid']] = $document['id'];
		}

		foreach ($rows as $row)
		{
			$id = is_array($row) ? $row['id'] : $row->id;
			if (in_array($id, $blacklist) === FALSE)
			{
				// Create blank object
				$document = new stdClass;

				foreach ($mapping as $field => $searchField)
				{
					// Ensure the searchField is not empty
					if ($searchField != '')
					{
						// Normalize the fieldname
						$searchField = strtolower($searchField);
						$document->$searchField = is_array($row) ? $row[$field] : $row->$field;
					}
				}

				// Add the unique hubtype
				$document->hubtype = $hubType->get('type');

				// Update or add
				if (in_array($id, array_keys($existingTable)))
				{
					$docID = $existingTable[$id];
				}
				else
				{
					$docID = null;
				}

				// Verify that it indexed properly
				if ($hubSearch->add($document, $docID))
				{
					$indexedDocuments++;
				}
			}

		}

		// Calculate offset
		$count = $this->getRows($hubType, null, TRUE);

		if ($count == ($indexedDocuments + count($blacklist)))
		{
			$item->set('indexed', 1);
			$item->set('indexed_on', Date::of()->toSql());
		}
		else
		{
			$item->set('offset', $indexedDocuments);
		}

		$item->save();

		return true;
	}
	private function getRows($hubType, $limit = 10, $count = FALSE)
	{
		require_once(PATH_ROOT . DS . $hubType->get('file_path'));

		$classpath = $hubType->get('class_path');

		if (strpos($classpath, 'Table') === FALSE)
		{
			$model = new $classpath;
		}
		else
		{
			$database = App::get('db');
			$model = new $classpath($database);
		}

		if (get_parent_class($model) == 'Hubzero\Database\Relational')
		{
			if ($count)
			{
				$row = $model->all()->rows()->count();
			}
			else
			{
				$row = $model->all()->limit($limit)->rows()->toArray();
			}
		}
		elseif (get_parent_class($model) == 'JTable')
		{
			$query = $database->getQuery(true);
			if ($count)
			{
				$query->select('count(*)');
			}
			else
			{
				$query->select('*');
				$query->limit($limit);
			}

			$query->from($model->getTableName());
			$database->setQuery($query);
			$row = $database->loadObjectList();
		}
			return $row;
	}
} //end plgCronSearch
