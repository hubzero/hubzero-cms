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

namespace Components\Citations\Admin\Controllers;

use Components\Citations\Tables\Citation;
use Components\Citations\Tables\Association;
use Components\Citations\Tables\Type;
use Components\Citations\Tables\Sponsor;
use Components\Citations\Tables\Author;
use Components\Citations\Tables\Tags;
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
	 * @return  void
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
			'published' => array(0, 1)
		);

		$obj = new Citation($this->database);

		// Get a record count
		$this->view->total = $obj->getCount($this->view->filters);

		// Get records
		$this->view->rows = $obj->getRecords($this->view->filters);

		//get the dynamic citation types
		$ct = new Type($this->database);
		$this->view->types = $ct->getType();

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new citation
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a citation
	 *
	 * @return  void
	 */
	public function editTask()
	{
		//stop menu from working?
		Request::setVar('hidemainmenu', 1);

		//get request vars - expecting an array id[]=4232
		$id = Request::getVar('id', array());
		if (is_array($id))
		{
			$id = (!empty($id)) ? $id[0] : 0;
		}

		//get all citations sponsors
		$cs = new Sponsor($this->database);
		$this->view->sponsors = $cs->getSponsor();

		//get all citation types
		$ct = new Type($this->database);
		$this->view->types = $ct->getType();

		//empty citation object
		$this->view->row = new Citation($this->database);

		//if we have an id load that citation data
		if (isset($id) && $id != '' && $id != 0)
		{
			// Load the citation object
			$this->view->row->load( $id );

			// Get the associations
			$assoc = new Association($this->database);
			$this->view->assocs = $assoc->getRecords(array('cid' => $id));

			//get sponsors for citation
			$this->view->row_sponsors = $cs->getCitationSponsor($this->view->row->id);

			//get the citations tags
			$this->view->tags = Format::citationTags($this->view->row, \JFactory::getDBO(), false);

			//get the badges
			$this->view->badges = Format::citationBadges($this->view->row, \JFactory::getDBO(), false);

			//parse citation params
			$this->view->params = new Registry($this->view->row->params);
		}
		else
		{
			//set the creator
			$this->view->row->uid = User::get('id');

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

		//are we padding back the citation data
		if (isset($this->row))
		{
			$this->view->row = $this->row;
		}

		//are we passing back the tags from edit
		if ($this->tags != '')
		{
			$this->tags = explode(',', $this->tags);
			foreach ($this->tags as $tag)
			{
				$this->view->tags[]['raw_tag'] = $tag;
			}
		}

		//are we passing back the tags from edit
		if ($this->badges != '')
		{
			$this->badges = explode(',', $this->badges);
			foreach ($this->badges as $badge)
			{
				$this->view->badges[]['raw_tag'] = $badge;
			}
		}

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		//set vars for view
		$this->view->config = $this->config;

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Publish a citation
	 * 
	 * @return  void
	 */
	public function publishTask()
	{
		//get request vars - expecting an array id[]=4232
		$id = Request::getVar('id', array());
		if (is_array($id))
		{
			$id = (!empty($id)) ? $id[0] : 0;
		}

		//empty citation object
		$row = new Citation($this->database);
		$row->load($id);

		// mark published and save
		$row->published = 1;
		if (!$row->save($row))
		{
			$this->setError($row->getError());
			$this->displayTask();
			return;
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('CITATION_PUBLISHED')
		);
	}

	/**
	 * Unpublish a citation
	 * 
	 * @return  void
	 */
	public function unpublishTask()
	{
		//get request vars - expecting an array id[]=4232
		$id = Request::getVar('id', array());
		if (is_array($id))
		{
			$id = (!empty($id)) ? $id[0] : 0;
		}

		//empty citation object
		$row = new Citation($this->database);
		$row->load($id);

		// mark unpublished and save
		$row->published = 0;
		if (!$row->save($row))
		{
			$this->setError($row->getError());
			$this->displayTask();
			return;
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('CITATION_UNPUBLISHED')
		);
	}

	/**
	 * Display stats for citations
	 *
	 * @return  void
	 */
	public function statsTask()
	{
		// Load the object
		$row = new Citation($this->database);
		$this->view->stats = $row->getStats();

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save a citation
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$citation = array_map('trim', Request::getVar('citation', array(), 'post'));
		$exclude  = Request::getVar('exclude', '', 'post');
		$rollover = Request::getInt("rollover", 0);
		$this->tags     = Request::getVar('tags', '');
		$this->badges   = Request::getVar('badges', '');
		$this->sponsors = Request::getVar('sponsors', array(), 'post');

		// Bind incoming data to object
		$row = new Citation($this->database);
		if (!$row->bind($citation))
		{
			$this->row = $row;
			$this->setError( $row->getError() );
			$this->editTask();
			return;
		}

		//set params
		$cparams = new Registry($this->_getParams($row->id));
		$cparams->set('exclude', $exclude);
		$cparams->set('rollover', $rollover);
		$row->params = $cparams->toString();

		// New entry so set the created date
		if (!$row->id)
		{
			$row->created = \Date::toSql();
		}

		// Check content for missing required data
		if (!$row->check())
		{
			$this->row = $row;
			$this->setError($row->getError());
			$this->editTask();
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->row = $row;
			$this->setError($row->getError());
			$this->editTask();
			return;
		}

		// Incoming associations
		$arr = Request::getVar('assocs', array(), 'post');
		$ignored = array();
		foreach ($arr as $a)
		{
			$a = array_map('trim',$a);

			// Initiate extended database class
			$assoc = new Association($this->database);

			//check to see if we should delete
			if (isset($a['id']) && $a['tbl'] == '' && $a['oid'] == '')
			{
				// Delete the row
				if (!$assoc->delete($a['id']))
				{
					throw new Exception($assoc->getError(), 500);
				}
			}
			else if ($a['tbl'] != '' || $a['oid'] != '')
			{
				$a['cid'] = $row->id;

				// bind the data
				if (!$assoc->bind($a))
				{
					throw new Exception($assoc->getError(), 500);
				}

				// Check content
				if (!$assoc->check())
				{
					throw new Exception($assoc->getError(), 500);
				}

				// Store new content
				if (!$assoc->store())
				{
					throw new Exception($assoc->getError(), 500);
				}
			}
		}

		//save sponsors on citation
		if ($this->sponsors)
		{
			$cs = new Sponsor($this->database);
			$cs->addSponsors($row->id, $this->sponsors);
		}

		//add tags & badges
		$ct = new Tags($row->id);
		$ct->setTags($this->tags, User::get('id'), 0, 1, '');
		$ct->setTags($this->badges, User::get('id'), 0, 1, 'badge');

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('CITATION_SAVED')
		);
	}

	/**
	 * Check if an array has any values set other than $ignored values
	 *
	 * @param   array    $b        Array to check
	 * @param   array    $ignored  Values to ignore
	 * @return  boolean  True if empty
	 */
	private function _isEmpty($b, $ignored=array())
	{
		foreach ($ignored as $ignore)
		{
			if (array_key_exists($ignore, $b))
			{
				$b[$ignore] = NULL;
			}
		}
		if (array_key_exists('id',$b))
		{
			$b['id'] = NULL;
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
	 * @return  void
	 */
	public function removeTask()
	{
		// Incoming (we're expecting an array)
		$ids = Request::getVar('id', array());
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		// Make sure we have IDs to work with
		if (count($ids) > 0)
		{
			// Loop through the IDs and delete the citation
			$citation = new Citation($this->database);
			$assoc    = new Association($this->database);
			$author   = new Author($this->database);
			foreach ($ids as $id)
			{
				// Fetch and delete all the associations to this citation
				$assocs = $assoc->getRecords(array('cid' => $id));
				foreach ($assocs as $a)
				{
					$assoc->delete($a->id);
				}

				// Fetch and delete all the authors to this citation
				$authors = $author->getRecords(array('cid' => $id));
				foreach ($authors as $a)
				{
					$author->delete($a->id);
				}

				// Delete the citation
				$citation->delete($id);

				//citation tags
				$ct = new Tags($id);
				$ct->removeAll();
			}

			$message = Lang::txt('CITATION_REMOVED');
		}
		else
		{
			$message = Lang::txt('NO_SELECTION');
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, true),
			$message
		);
	}

	/**
	 * Get the params for a citation
	 *
	 * @param   integer  $citation  Citation ID
	 * @return  integer
	 */
	private function _getParams($citation = 0)
	{
		$this->database->setQuery("SELECT c.params from `#__citations` c WHERE id=" . $this->database->quote($citation));
		return $this->database->loadResult();
	}
}

