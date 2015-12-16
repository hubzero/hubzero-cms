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
use \Components\Search\Models\IndexQueue;
use \Components\Search\Models\HubType;
use stdClass;

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
		Route::url('index.php?option=' . $this->_option . '&controller=solr&task=searchindex', false),
		'Successfully added to index queue.', 'success');
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
		Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller. '&task=manageBlacklist', false),
		'Successfully removed entry #' . $entryID, 'success');
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
