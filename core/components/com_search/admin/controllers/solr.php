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
use \Components\Search\Helpers\SolrEngine as SearchEngine;
use \Components\Search\Models\Noindex;
use \Components\Search\Models\HubType;
use stdClass;

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

		$hubSearch = new SearchEngine();
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
		$hubSearch = new SearchEngine();

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
			$hubSearch = new SearchEngine();

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
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller. '&task=searchIndex', false),
			 'Failed to add HubType.', 'error');
		}
			// Success
			App::redirect(
			Route::url('/administrator/index.php?option=' . $this->_option . '&controller=' . $this->_controller. '&task=searchIndex', false),
			'Successfully added '. $name .   ' to the search index.', 'success');
	}
	public function deleteDocumentTask()
	{
		$id = Request::getVar('docID', 0);
		$hubid = Request::getVar('hubid', 0);
		$type = Request::getVar('type', '');
		$hubSearch = new SearchEngine();
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
			//$hubSearch = new SearchEngine();
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
				$hubSearch = new SearchEngine();
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
		$hubSearch = new SearchEngine();
		$hubSearch->getLog();
		echo "<pre>";
		print_r($hubSearch->logs);
		exit();
	}
}
