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
class Solr extends AdminController
{
	/**
	 * Display the overview
	 */
	public function displayTask()
	{

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$hubSearch = new SolrEngine();
		$hubSearch->getLog();
		$this->view->logs = array_slice($hubSearch->logs, -10, 10, true);

		// Need to fix permissions, www-data must be able to invoke start
		//$hubSearch->start();

		$helper = new Helper;
		$dataTypes = $helper->fetchDataTypes($hubSearch->getConfig());
		$insertTime = Date::of($hubSearch->lastInsert())->format('relative');

		$this->view->status = $hubSearch->ping();
		$this->view->lastInsert = $insertTime;
		$this->view->setLayout('overview');

		// Display the view
		$this->view->display();
	}
	public function searchIndexTask()
	{
		// Display CMS errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Instantiate search engine
		$hubSearch = new SolrEngine();

		// Get all of the registered hubtypes
		$this->view->types = HubType::all()->rows();

		// Count indexed documents
		foreach ($this->view->types as $type)
		{
			// Search across everything
			$queryString = '*:*';

			// Perform an overall search
			$search = $hubSearch->search($queryString)->addFacet('count', 'hubtype:'.$type->type);

			// Get the result, but don't chain it back
			$result = $search->getResult();

			// Get facet count
			$type->docCount = $search->getFacetCount('count');

			// Fields description 
			$type->structure = $type->structure();
		}

		// Display the view
		$this->view->display();
	}
	public function documentByTypeTask()
	{
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$additionalQuery = Request::getVar('filter', '');
		$type = Request::getVar('type', '');

		if ($type != '')
		{
			// Instatiate new Engine Class
			$hubSearch = new SolrEngine();

			// Automatically apply type filter
			$queryString = 'hubtype:' . $type;

			// For filtering within results
			if ($additionalQuery != '')
			{
				$queryString .= ' AND ' . $additionalQuery;
				$this->view->filter = $additionalQuery;
			}
			else
			{
				$this->view->filter = '';
			}

			// @FIXME acertain from real document type.
			$fields = array('hubtype', 'hubid', 'scope', 'scope_id', 'id', 'title', 'author', 'created', 'timestamp', '_version_');

			// Apply the query and push the results to the view.
			$hubSearch->search($queryString);
			$hubSearch->setFields($fields);
			$this->view->documents = $hubSearch->getResult();
			$this->view->type = $type;

			// Display the document
			$this->view->setLayout('document');
			$this->view->display();
		}
		else
		{
			App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller. '&task=searchindex', false), 'Failed to locate HubType.', 'error');
		}
	}
	public function addTypeTask()
	{
		// New type
		$model = HubType::blank();
		$this->view->model = $model->getStructure()->getTableColumns($model->getTableName());

		// Display the view
		$this->view->setLayout('addHubType');
		$this->view->display();
	}
	public function editTypeTask()
	{

	}
	public function saveHubTypeTask()
	{
		// Get Variables
		$name = Request::getVar('type', '');
		$class_path = Request::getVar('class_path', '');
		$file_path = Request::getVar('file_path', '');
		$row = Request::getVar('id', 0);

		$model = HubType::oneOrNew($row);
		$model->set('class_path', $class_path);
		$model->set('file_path', $file_path);
		$model->set('type', $name);

		if (!$model->save())
		{
			// Fail
			App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller. '&task=searchIndex', false), 'Failed to add HubType.', 'error');
		}
			// Success
			App::redirect(
			Route::url('/administrator/index.php?option=' . $this->_option . '&controller=' . $this->_controller. '&task=searchIndex', false), 'Successfully added '. $name .   ' to the search index.', 'success');
	}
	public function deleteDocumentTask()
	{
		$id = Request::getVar('docID', 0);
		$hubid = Request::getVar('hubid', 0);
		$type = Request::getVar('type', '');
		$hubSearch = new SolrEngine();
		$noIndex = new Noindex;
		$noIndex = $noIndex->oneOrNew(0);
		$noIndex->set('hubtype', $type);
		$noIndex->set('hubid', $hubid);

		if ($hubSearch->delete($id) === TRUE && $noIndex->save())
		{
			App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller. '&task=documentByType&type='.$type, false), 'Successfully deleted Document ID: ' . $id . '.', 'success');
		}
		elseif ($type != '')
		{
			App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller. '&task=documentByType&type='. $type, false), 'Failed to Document ID: ' . $id . '.', 'error');
		}
	}
	public function addDocumentTask()
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
														//'hubtype' => '', // Not accessible
														'note' => '',
														'keywords' => '', // Could be tags if nothing, or differ
														'tags' => '',
														'badge' => '',
														'date' => '',
														'year' => '',
														'month' => '',
														'day' => '',
														'access-level' => '', //
														'access-group' => '', //
														'permission-level' => '', // All, registered, group-members, project members,
														'hub-assoc' => '', // Component, Module, or Plugin which inserts the data
														'organization' => '',
														'url' => '', // if the content is off-site
														'cms-ranking' => '',
														'cms-rating' => '',
														'params' => '',
														'hubID' => '', // the ID of the content within the HUB (differs from the Solr ID)
														'hubURL' => '', // how to view the record on the hub
														'cms-state' => '' // published, trashed, hidden, etc.
													);

			$this->view->type = $type;
			$this->view->hubDocument = $searchDocument;
			$this->view->setLayout('addDocuments');
			$this->view->display();
		}
	}
	public function saveSchemaTask()
	{
		/**
		 * This method depends on an array of pairings
		 * which match the Solr Schema to database fields.
		 * @TODO Save serialized input array to Hubtype params field
		 **/
		$pairings = Request::getVar('input', array());

		// The Hubtype
		$type = Request::getVar('type', '');

		// Instatiate helper
		$helper = new Helper;
		$rows = $helper->fetchHubTypeRows($type);

		// Some informative counters
		$docCount = 0;
		$updateCount = 0;
		$noIndexCount = 0;
		$removedCount = 0;

		// The blacklist
		$noIndex = new Noindex;
		$noIndex = $noIndex->all()->where('hubtype', '=', $type)->rows();
		$blacklist = $noIndex->fieldsByKey('hubid');

		foreach ($rows as $row)
		{
			$id = (get_class($row) != 'stdClass'? $row->get('id') : $row->id);

			if (!in_array($id, $blacklist))
			{
				$hubSearch = new SolrEngine();
				$document = new stdClass;

				foreach ($pairings as $solrField => $hubField)
				{
					if (get_class($row) != 'stdClass')
					{
						$document->$solrField = $row->get($hubField, '');
					}
					else
					{
						$document->$solrField = $row->$hubField;
					}
				}
				$document->hubtype = $type;

				if (get_class($row) == 'stdClass')
				{
					$document->hubid = $row->id;
				}
				else
				{
					$document->hubid = $row->get('id');
				}

				// Check for duplicates, updated 
				$query = 'hubtype:'.$type.' AND hubid:'.$document->hubid;
				$duplicates = $hubSearch->search($query)->getResult();

				if ($duplicates->count() == 0)
				{
					$solrID = NULL;
					$docCount++;
				}
				elseif ($duplicates->count() > 0)
				{
					foreach ($duplicates->getDocuments() as $dup)
					{
						$solrID = $dup->getFields()['id'];
						$updateCount++;
					}
				}
				else
				{
					$solrID = NULL;
				}
				$hubSearch->add($document, $solrID);
			} // end conditional 
			else
			{
				$noIndexCount++;
			}
		}
			App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller. '&task=documentByType&type='.$type, false), 'Successfully indexed ' . $docCount. ' documents; updated ' . $updateCount . ' documents; ignored '.$noIndexCount. ' documents.', 'success');
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
	public function viewLogsTask()
	{
		$hubSearch = new SolrEngine();
		$hubSearch->getLog();
		echo "<pre>";
		print_r($hubSearch->logs);
		exit();
	}
}
