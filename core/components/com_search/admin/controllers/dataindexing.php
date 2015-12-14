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

namespace Components\Search\Admin\Controllers;

use Hubzero\Component\AdminController;
use \Solarium;
use \Hubzero\Utility\Sanitize;
use Filesystem;
use \Components\Search\Helpers\SearchHelper as Helper;
use \Components\Search\Models\Noindex;
use \Components\Search\Models\IndexQueue;
use \Components\Search\Models\HubType;
use stdClass;

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
 *
 */
class DataIndexing extends AdminController
{
	public function matchDocumentSchemaTask()
	{
		// @TODO Verify the HubType is not new, use serialized params input to load last settings.
		$type = Request::getVar('type', '');

		if ($type != '')
		{
			//$hubSearch = new SolrEngine();
			$helper = new Helper;
			$this->view->typeStructure = $helper->fetchDataTypes(null, $type);
			$searchDocument = array(
														//'id' => '', // Not accessible / managed via user
														'title' => '',
														'doi' => '',
														'isbn' => '',
														'author' => '',
														'created' => '', // According to the DB
														'modified' => '', // According to the DB
														'scope' => '', // From the DB
														'scope_id' => '',  // From the DB
														'fulltext' => '',
														'description' => '',
														'abstract' => '',
														'location' => '',
														'uid' => '',
														'gid' => '',
														'created_by' => '',
														'child_id' => '',
														'parent_id' => '',
														'state' => '',
														'hits' => '',
														'publish_up' => '',
														'publish_down' => '',
														'type' => '',
														//'hubtype' => '', // Not accessible 
														'note' => '',
														'keywords' => '', // Could be tags if nothing, or differ
														'language' => '',
														'tags' => '',
														'badge' => '',
														'date' => '',
														'year' => '',
														'month' => '',
														'day' => '',
														'address' => '',
														'organization' => '',
														'name' => '',
														'access-level' => '', //
														'access-group' => '', //
														'permission-level' => '', // All, registered, group-members, project members,
														'hub-assoc' => '', // Component, Module, or Plugin which inserts the data
														'organization' => '',
														'url' => '', // if the content is off-site
														'cms-ranking' => '',
														'cms-rating' => '',
														'params' => '',
														'meta' => '',
														'hubID' => '', // the ID of the content within the HUB (differs from the Solr ID)
														'hubURL' => '', // how to view the record on the hub
														'cms-state' => '' // published, trashed, hidden, etc.
													);

			$this->view->type = $type;
			$this->view->hubDocument = $searchDocument;
			$this->view->previousSettings = (isset($type->documentMatching) && $type->documentMatching != '' ? unserialize($type->documentMatching) : NULL);
			$this->view->setLayout('addDocuments');
			$this->view->display();
		}
	}
	public function saveSchemaTask()
	{
		$pairings = Request::getVar('input', array());
		$pairings = serialize($pairings);

		// The Hubtype
		$type = Request::getVar('type', '');

		$hubType = HubType::all()->where('type', '=', $type)->row();

		$hubType->set('documentMatching', $pairings);

		if (!$hubType->save())
		{
			// error
		}

		// Add hubType to search index table
		$queue = IndexQueue::oneOrNew(0);
		$queue->set('hubtype_id', $hubType->get('id'));
		if (!$queue->save())
		{
			// error
		}

		App::redirect(
		Route::url('index.php?option=' . $this->_option . '&controller=solr&task=searchindex', false), 'Successfully added to index queue.', 'success');
	}
	public function setPermissionsTask()
	{
		$typeID = Request::getInt('typeID', 0);
		$hubType = HubType::one($typeID)->row();
		$this->view->fields = array_keys(unserialize($hubType->get('documentMatching')));

		$this->view->display();
	}
	public function setupSearchListingTask()
	{
		$typeID = Request::getInt('typeID', 0);

		$hubType = HubType::one($typeID)->row();
		$classpath = $hubType->get('class_path');
		$filepath = $hubType->get('file_path');
		$type = $hubType->get('type');

		require_once(PATH_ROOT . DS . $filepath);

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
			$row = $model->all()->limit(1)->rows();
		}
		elseif (get_parent_class($model) == 'JTable')
		{
			$query = $database->getQuery(true);
			$query->select('*');
			$query->limit(1);
			$query->from($model->getTableName());
			$database->setQuery($query);
			$row = $database->loadObjectList();
		}

		// Get the helper for now
		$helper = new Helper;

		// Push data to the the view
		$this->view->row = $row;
		$this->view->type = $type;
		$this->view->dynamicFields = array('cms_link', 'location-aware', 'member');
		$this->view->placeholders = array('title', 'description', 'fulltext', 'createdby', 'created_date', 'tags');
		$this->view->display();
	}
	public function sampleRowTask()
	{
		$type = Request::getVar('type','');
		$rowID = Request::getInt('rowID', 0);

		$hubType = Hubtype::all()->where('type', '=', $type)->row();
		$classpath = $hubType->get('class_path');
		$filepath = $hubType->get('file_path');
		$type = $hubType->get('type');

		require_once(PATH_ROOT . DS . $filepath);

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
			$row = $model->all()->where('id', '=', $rowID)->row()->toArray();
		}
		elseif (get_parent_class($model) == 'JTable')
		{
			$query = $database->getQuery(true);
			$query->select('*');
			$query->limit(1);
			$query->where('id', '=', $rowID);
			$query->from($model->getTableName());
			$database->setQuery($query);
			$row = $database->loadAssoc();
		}

		// Maybe some sanitiation?
		echo json_encode($row);
		exit();
	}

	public function manageBlacklistTask()
	{
		$this->view->blacklist = NoIndex::all()->rows();
		$this->view->display();
	}
	public function removeBlacklistEntryTask()
	{
		$entryID = Request::getInt('entryID', 0);
		$entry = NoIndex::one($entryID);
		$entry->destroy();
		App::redirect(
		Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller. '&task=manageBlacklist', false), 'Successfully removed entry #' . $entryID, 'success');
	}

	private function getRows($hubType, $limit = 10)
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
			$row = $model->all()->limit($limit)->rows()->toArray();
		}
		elseif (get_parent_class($model) == 'JTable')
		{
			$query = $database->getQuery(true);
			$query->select('*');
			$query->limit($limit);
			$query->from($model->getTableName());
			$database->setQuery($query);
			$row = $database->loadAssoc();
		}
			return $row;
	}

}
