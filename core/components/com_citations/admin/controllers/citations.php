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

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'citation.php';

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
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		parent::execute();
	}

	/**
	 * List citations
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		$filters = array(
			// Get filters
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'published' => array(0, 1),
			'scope' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.scope',
				'scope',
				'all'
			))
		);

		// Get records
		$rows = Citation::getFilteredRecords($filters)
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->display();
	}

	/**
	 * Edit a citation
	 *
	 * @param   object
	 * @return	void
	 */
	public function editTask($row = null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Stop menu from working?
		Request::setVar('hidemainmenu', 1);

		if (!($row instanceof Citation))
		{
			// Get request vars - expecting an array id[]=4232
			$id = Request::getArray('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			$row = Citation::oneOrNew($id);
		}

		//if we have an id load that citation data
		if (!$row->isNew())
		{
			$assocs = array();
			foreach ($row->associations as $assoc)
			{
				$assocs[] = $assoc;
			}

			// Get sponsors for citation
			$row_sponsors = $row->sponsors->fieldsByKey('id');

			// Get the citations tags
			$tags = Format::citationTags($row, false);

			// Get the badges
			$badges = Format::citationBadges($row, false);

			// Parse citation params
			$params = new Registry($row->params);
		}
		else
		{
			// Set the creator
			$row->set('uid', User::get('id'));

			// It's new - no associations to get
			$assocs = array();

			// Array of sponsors - empty
			$row_sponsors = array();

			// Empty tags and badges arrays
			$tags = array();
			$badges = array();

			// Empty params object
			$params = new Registry('');
		}

		// Get all citations sponsors
		$sponsors = Sponsor::all();

		// Get all citation types
		$types = Type::all();

		// Set any errors
		if ($this->getError())
		{
			Notify::error($this->getError());
		}

		// Output the HTML
		$this->view
			->set('config', $this->config)
			->set('row', $row)
			->set('sponsors', $sponsors)
			->set('types', $types)
			->set('tags', $tags)
			->set('badges', $badges)
			->set('params', $params)
			->set('row_sponsors', $row_sponsors)
			->set('assocs', $assocs)
			->setLayout('edit')
			->display();
	}

	/**
	 * Publish a citation
	 *
	 * @return	void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$state = $this->getTask() == 'publish' ? 1 : 0;

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$success = 0;
		foreach ($ids as $id)
		{
			$row = Citation::oneOrFail($id);

			// Mark record and save
			$row->set('published', $state);

			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			$msg = $this->getTask() == 'publish'
				? Lang::txt('CITATION_PUBLISHED')
				: Lang::txt('CITATION_UNPUBLISHED');

			Notify::success($msg);
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Toggle affliliation 
	 *
	 * @return  void
	 */
	public function affiliateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$success = 0;
		foreach ($ids as $id)
		{
			$row = Citation::oneOrFail($id);

			$affiliatedId = ($row->get('affiliated') == 1) ? 0 : 1;

			$row->set('affiliated', $affiliatedId);

			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			Notify::success(Lang::txt('CITATION_AFFILIATED'));
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Toggle fundedby 
	 *
	 * @return  void
	 */
	public function fundTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$success = 0;
		foreach ($ids as $id)
		{
			$row = Citation::oneOrFail($id);

			$fundedbyId = ($row->get('fundedby') == 1) ? 0 : 1;

			$row->set('fundedby', $fundedbyId);

			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			Notify::success(Lang::txt('CITATION_FUNDED'));
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Display stats for citations
	 *
	 * @return  void
	 */
	public function statsTask()
	{
		$filters = array(
			'scope'     => 'all',
			'published' => array(0, 1)
		);

		$stats = Citation::getYearlyStats($filters);

		// Output the HTML
		$this->view
			->set('stats', $stats)
			->display();
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

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$citation = array_map('trim', Request::getArray('citation', array(), 'post'));
		$exclude  = Request::getString('exclude', '', 'post');
		$rollover = Request::getInt("rollover", 0);
		$this->tags = Request::getString('tags', '');
		$this->badges = Request::getString('badges', '');
		$this->sponsors = array_filter(Request::getArray('sponsors', array(), 'post'));
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

		// Set params
		$cparams = new Registry($this->_getParams($row->id));
		$cparams->set('exclude', $exclude);
		$cparams->set('rollover', $rollover);
		$row->set('params', $cparams->toString());

		// Incoming associations
		$assocParams = Request::getArray('assocs', array(), 'post');
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
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		$row->sponsors()->sync($this->sponsors);

		//add tags & badges
		$row->updateTags($this->tags);
		$row->updateTags($this->badges, 'badge');

		Notify::success(Lang::txt('CITATION_SAVED', $row->id));

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Remove one or more citations
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming (we're expecting an array)
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$removed = 0;

		// Loop through the IDs and delete the citation
		foreach ($ids as $id)
		{
			$citation = Citation::oneOrFail($id);

			if (!$citation->destroy())
			{
				Notify::error($citation->getError());
				continue;
			}

			// Trigger before delete event
			Event::trigger('onCitationAfterDelete', array($id));

			$removed++;
		}

		if ($removed)
		{
			Notify::error(Lang::txt('NO_SELECTION'));
		}

		$this->cancelTask();
	}

	/**
	 * Get the params for a citation
	 *
	 * @param   integer  $citation  Citation ID
	 * @return  string
	 */
	private function _getParams($citation = 0)
	{
		$this->database->setQuery("SELECT c.params from `#__citations` c WHERE id=" . $this->database->quote($citation));
		return $this->database->loadResult();
	}
}
