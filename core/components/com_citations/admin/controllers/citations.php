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
 * @author	Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Admin\Controllers;

require_once Component::path('com_citations') . DS . 'models' . DS . 'citation.php';

use Components\Citations\Models\Citation;
use Components\Citations\Models\Association;
use Components\Citations\Models\Type;
use Components\Citations\Models\Sponsor;
use Components\Citations\Helpers\Format;
use Hubzero\Component\AdminController;
use Hubzero\Config\Registry;
use Exception;
use Request;
use Config;
use Route;
use Lang;
use App;

/**
 * Controller class for citations
 */
class Citations extends AdminController
{
	/**
	 * List citations
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		$this->view->filters = array(
			// Get paging variables
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			// Get filters
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'sort',
				'created DESC'
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'published' => array(0, 1),
			'scope' => Request::getVar('scope', 'all')
		);


		// Get a record count
		$this->view->total = Citation::getFilteredRecords($this->view->filters)->count();

		// Get records
		$this->view->rows = Citation::getFilteredRecords($this->view->filters)->paginated('limitstart');

		$this->_displayMessages();
		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new citation
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a citation
	 *
	 * @return	void
	 */
	public function editTask()
	{
		//stop menu from working?
		Request::setVar('hidemainmenu', 1);

		//get request vars - expecting an array id[]=4232
		$id = Request::getInt('id', 0);
		if (!isset($this->row) || !($this->row instanceof Citation))
		{
			$this->row = Citation::oneOrNew($id);
		}
		//get all citations sponsors
		$this->view->sponsors = Sponsor::all();

		//get all citation types
		$this->view->types = Type::all();

		//empty citation object
		$this->view->row = $this->row;

		//if we have an id load that citation data
		if (!$this->view->row->isNew())
		{

			$assocArray = array();
			foreach ($this->view->row->associations as $assoc)
			{
				$assocArray[] = $assoc;
			}

			$this->view->assocs = $assocArray;

			//get sponsors for citation
			$this->view->row_sponsors = $this->view->row->sponsors->fieldsByKey('id');

			//get the citations tags
			$this->view->tags = Format::citationTags($this->view->row, false);

			//get the badges
			$this->view->badges = Format::citationBadges($this->view->row, false);

			//parse citation params
			$this->view->params = new Registry($this->view->row->params);
		}
		else
		{
			//set the creator
			$this->view->row->set('uid', User::get('id'));

			// It's new - no associations to get
			$this->view->assocs = array();

			//array of sponsors - empty
			$this->view->row_sponsors = array();

			//empty tags and badges arrays
			$this->view->tags = array();
			$this->view->badges = array();

			//empty params object
			$this->view->params = new Registry('');
		}
		// Set any errors
		if ($this->getError())
		{
			Notify::error($this->getError(), 'com.citations');
		}

		//set vars for view
		$this->view->config = $this->config;

		$this->_displayMessages();
		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Publish a citation
	 *
	 * @return	void
	 */
	public function publishTask()
	{
		//get request vars - expecting an array id[]=4232
		$id = Request::getInt('id', 0);

		$row = Citation::oneOrFail($id);

		// mark published and save
		$row->set('published', 1);
		if (!$row->save())
		{
			Notify::error($row->getError(), 'com.citations');
			$this->displayTask();
			return;
		}

		Notify::success(Lang::txt('CITATION_PUBLISHED'), 'com.citations');
		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Unpublish a citation
	 *
	 * @return	void
	 */
	public function unpublishTask()
	{
		$id = Request::getInt('id', 0);

		$row = Citation::oneOrFail($id);

		// mark unpublished and save
		$row->set('published', 0);
		if (!$row->save())
		{
			Notify::error($row->getError(), 'com.citations');
			$this->displayTask();
			return;
		}
		Notify::success(Lang::txt('CITATION_UNPUBLISHED'), 'com.citations');
		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/*
	* Toggle affliliation 
	*
	* @return void
	*/
	public function affiliateTask()
	{
		$id = Request::getInt('id', 0);
		$row = Citation::oneOrFail($id);
		$affiliatedId = ($row->affiliated == 1) ? 0 : 1;
		$row->set('affiliated', $affiliatedId);

		if (!$row->save())
		{
				Notify::error($row->getError(), 'com.citations');
				$this->displayTask();
				return;
		}
		Notify::success(Lang::txt('CITATION_AFFILIATED'), 'com.citations');
		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/*
	* Toggle fundedby 
	*
	* @return void
	*/
	public function fundTask()
	{
		$id = Request::getInt('id', 0);
		$row = Citation::oneOrFail($id);
		$fundedbyId = ($row->fundedby == 1) ? 0 : 1;
		$row->set('fundedby', $fundedbyId);

		if (!$row->save())
		{
				Notify::error($row->getError(), 'com.citations');
				$this->displayTask();
				return;
		}
		Notify::success(Lang::txt('CITATION_FUNDED'), 'com.citations');

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Display stats for citations
	 *
	 * @return	void
	 */
	public function statsTask()
	{
		$filters = array('scope' => 'all', 'published' => array(0,1));
		$this->view->stats = Citation::getYearlyStats($filters);

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save a citation
	 *
	 * @return	void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$citation = array_map('trim', Request::getVar('citation', array(), 'post'));
		$exclude  = Request::getVar('exclude', '', 'post');
		$rollover = Request::getInt("rollover", 0);
		$this->tags	 = Request::getVar('tags', '');
		$this->badges	= Request::getVar('badges', '');
		$this->sponsors = array_filter(Request::getVar('sponsors', array(), 'post'));
		$citationId = !empty($citation['id']) ? $citation['id'] : null;
		unset($citation['id']);

		// toggle the affiliation
		if (!isset($citation['affiliated']) || $citation['affiliated'] == null)
		{
				$citation['affiliated'] = 0;
		}

		// toggle fundeby
		if (!isset($citation['fundedby']) || $citation['fundedby'] == null)
		{
				$citation['fundedby'] = 0;
		}
		// Bind incoming data to object
		$row = Citation::oneOrNew($citationId);
		$row->set($citation);

		//set params
		$cparams = new Registry($this->_getParams($row->id));
		$cparams->set('exclude', $exclude);
		$cparams->set('rollover', $rollover);
		$row->set('params', $cparams->toString());


		// Incoming associations
		$assocParams = Request::getVar('assocs', array(), 'post');
		$associations = array();
		foreach ($assocParams as $assoc)
		{
			$assoc = array_map('trim', $assoc);
			$assocId = !empty($assoc['id']) ? $assoc['id'] : null;
			unset($assoc['id']);
			$newAssociation = Association::oneOrNew($assocId)->set($assoc);
			if (!$newAssociation->isNew() && (empty($assoc['tbl']) || empty($assoc['oid'])))
			{
				$newAssociation->destroy();
			}
			else
			{
				if (!empty($assoc['tbl']) && !empty($assoc['oid']))
				{
					$associations[] = $newAssociation;
				}
			}
		}

		$row->attach('associations', $associations);

		// Store new content
		if (!$row->saveAndPropagate())
		{
			$this->row = $row;
			Notify::error($row->getError(), 'com.citations');
			$this->editTask();
			return;
		}
		$row->sponsors()->sync($this->sponsors);

		//add tags & badges
		$row->updateTags($this->tags);
		$row->updateTags($this->badges, 'badge');

		Notify::success(Lang::txt('CITATION_SAVED', $row->id), 'com.citations');

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Check if an array has any values set other than $ignored values
	 *
	 * @param	array	$b		Array to check
	 * @param	array	$ignored  Values to ignore
	 * @return	boolean  True if empty
	 */
	private function _isEmpty($b, $ignored=array())
	{
		foreach ($ignored as $ignore)
		{
			if (array_key_exists($ignore, $b))
			{
				$b[$ignore] = null;
			}
		}
		if (array_key_exists('id', $b))
		{
			$b['id'] = null;
		}
		$values = array_values($b);
		$e = true;
		foreach ($values as $v)
		{
			if ($v)
			{
				$e = false;
			}
		}
		return $e;
	}

	/**
	 * Remove one or more citations
	 *
	 * @return	void
	 */
	public function removeTask()
	{
		// Incoming (we're expecting an array)
		$ids = Request::getVar('id', array());
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

        if (count($ids) > 0)
		{
			// Loop through the IDs and delete the citation
			$citations = Citation::whereIn('id', $ids)->rows();
			$citationsRemoved = array();
			foreach ($citations as $citation)
			{
				$citationId = $citation->get('id');
				if (!$citation->destroy())
				{
					foreach ($citation->getErrors() as $error)
					{
						Notify::error($citation->getError(), 'com.citations');
					}
					App::redirect(Route::url('index.php?option=com_citations&task=browse'));
				}
				else
				{
					Notify::success(Lang::txt('CITATION_REMOVED', $citationId), 'com.citations');
				}
			}
		}
		else
		{
			Notify::error($Lang::txt('NO_SELECTION'), 'com_citations');
		}
		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, true)
		);
	}

	private function _displayMessages($domain = 'com.citations')
	{
		foreach (Notify::messages($domain) as $message)
		{
			Notify::message($message['message'], $message['type']);
		}
	}

	/**
	 * Get the params for a citation
	 *
	 * @param	integer  $citation	Citation ID
	 * @return	integer
	 */
	private function _getParams($citation = 0)
	{
		$this->database->setQuery("SELECT c.params from `#__citations` c WHERE id=" . $this->database->quote($citation));
		return $this->database->loadResult();
	}
}
