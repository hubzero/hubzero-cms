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

namespace Components\Tags\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Tags\Models\Cloud;
use Components\Tags\Models\Tag;
use Exception;
use Request;
use Config;
use Notify;
use Cache;
use Event;
use Route;
use Lang;
use App;

/**
 * Tags controller class for managing entries
 */
class Entries extends AdminController
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

		parent::execute();
	}

	/**
	 * List all tags
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array(
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
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'by' => Request::getState(
				$this->_option . '.' . $this->_controller . '.by',
				'filterby',
				'all'
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'raw_tag'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);
		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		$t = new Cloud();

		// Record count
		$this->view->total = $t->tags('count', $this->view->filters);

		$this->view->filters['limit'] = ($this->view->filters['limit'] == 0) ? 'all' : $this->view->filters['limit'];

		// Get records
		$this->view->rows = $t->tags('list', $this->view->filters);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit an entry
	 *
	 * @param   object  $tag  Tag being edited
	 * @return  void
	 */
	public function editTask($tag=NULL)
	{
		Request::setVar('hidemainmenu', 1);

		// Load a tag object if one doesn't already exist
		if (!is_object($tag))
		{
			// Incoming
			$id = Request::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			$tag = new Tag(intval($id));
		}

		$this->view->tag = $tag;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$fields = Request::getVar('fields', array(), 'post');

		$row = new Tag(intval($fields['id']));
		if (!$row->bind($fields))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		$row->set('admin', 0);
		if (isset($fields['admin']) && $fields['admin'])
		{
			$row->set('admin', 1);
		}

		// Store new content
		if (!$row->store(true))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_TAGS_TAG_SAVED'));

		// Redirect to main listing
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Remove one or more entries
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Make sure we have an ID
		if (empty($ids))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_TAGS_ERROR_NO_ITEMS_SELECTED'),
				'error'
			);
			return;
		}

		foreach ($ids as $id)
		{
			$id = intval($id);

			// Remove references to the tag
			Event::trigger('tags.onTagDelete', array($id));

			// Remove the tag
			$tag = new Tag($id);
			$tag->delete();
		}

		$this->cleancacheTask(false);

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_TAGS_TAG_REMOVED')
		);
	}

	/**
	 * Clean cached tags data
	 *
	 * @param   boolean  $redirect  Redirect after?
	 * @return  void
	 */
	public function cleancacheTask($redirect=true)
	{
		Cache::clean('tags');

		if (!$redirect)
		{
			return true;
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Merge two tags into one
	 *
	 * @return     void
	 */
	public function mergeTask()
	{
		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$step = Request::getInt('step', 1);
		$step = ($step) ? $step : 1;

		// Make sure we have some IDs to work with
		if ($step == 1
		&& (!$ids || count($ids) < 1))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		$idstr = implode(',', $ids);

		switch ($step)
		{
			case 1:
				Request::setVar('hidemainmenu', 1);

				// Instantiate a new view
				$this->view->step = 2;
				$this->view->idstr = $idstr;
				$this->view->tags = array();

				// Loop through the IDs of the tags we want to merge
				foreach ($ids as $id)
				{
					// Add the tag object to an array
					$this->view->tags[] = new Tag(intval($id));
				}

				// Get all tags
				$cloud = new Cloud();

				// Set any errors
				if ($this->getError())
				{
					$this->view->setError($this->getError());
				}

				// Output the HTML
				$this->view->display();
			break;

			case 2:
				// Check for request forgeries
				Request::checkToken();

				// Get the string of tag IDs we plan to merge
				$ind = Request::getVar('ids', '', 'post');
				if ($ind)
				{
					$ids = explode(',', $ind);
				}
				else
				{
					$ids = array();
				}

				// Incoming
				$tag_exist = Request::getInt('existingtag', 0, 'post');
				$tag_new   = Request::getVar('newtag', '', 'post');

				// Are we merging tags into a totally new tag?
				if ($tag_new)
				{
					// Yes, we are
					$newtag = new Tag($tag_new);
					if (!$newtag->exists())
					{
						$newtag->set('raw_tag', $tag_new);
					}
					if (!$newtag->store(true))
					{
						$this->setError($newtag->getError());
					}
					$mtag = $newtag->get('id');
				}
				else
				{
					// No, we're merging into an existing tag
					$mtag = $tag_exist;
				}

				if ($this->getError())
				{
					throw new Exception($this->getError(), 500);
				}

				foreach ($ids as $id)
				{
					if ($mtag == $id)
					{
						continue;
					}

					$oldtag = new Tag(intval($id));
					if (!$oldtag->mergeWith($mtag))
					{
						$this->setError($oldtag->getError());
					}
				}

				if ($this->getError())
				{
					Notify::error($this->getError());
				}

				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					Lang::txt('COM_TAGS_TAGS_MERGED')
				);
			break;
		}
	}

	/**
	 * Copy all tag associations from one tag to another
	 *
	 * @return     void
	 */
	public function pierceTask()
	{
		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$step = Request::getInt('step', 1);
		$step = ($step) ? $step : 1;

		// Make sure we have some IDs to work with
		if ($step == 1
		 && (!$ids || count($ids) < 1))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		$idstr = implode(',', $ids);

		switch ($step)
		{
			case 1:
				Request::setVar('hidemainmenu', 1);

				$this->view->step = 2;
				$this->view->idstr = $idstr;
				$this->view->tags = array();

				// Loop through the IDs of the tags we want to merge
				foreach ($ids as $id)
				{
					// Load the tag's info
					$this->view->tags[] = new Tag(intval($id));
				}

				// Get all tags
				$cloud = new Cloud();

				// Set any errors
				if ($this->getError())
				{
					$this->view->setError($this->getError());
				}

				// Output the HTML
				$this->view->display();
			break;

			case 2:
				// Check for request forgeries
				Request::checkToken();

				// Get the string of tag IDs we plan to merge
				$ind = Request::getVar('ids', '', 'post');
				if ($ind)
				{
					$ids = explode(',', $ind);
				}
				else
				{
					$ids = array();
				}

				// Incoming
				$tag_exist = Request::getInt('existingtag', 0, 'post');
				$tag_new   = Request::getVar('newtag', '', 'post');

				// Are we merging tags into a totally new tag?
				if ($tag_new)
				{
					// Yes, we are
					$newtag = new Tag($tag_new);
					if (!$newtag->exists())
					{
						$newtag->set('raw_tag', $tag_new);
					}
					if (!$newtag->store(true))
					{
						$this->setError($newtag->getError());
					}
					$mtag = $newtag->get('id');
				}
				else
				{
					// No, we're merging into an existing tag
					$mtag = $tag_exist;
				}

				foreach ($ids as $id)
				{
					if ($mtag == $id)
					{
						continue;
					}

					$oldtag = new Tag(intval($id));
					if (!$oldtag->copyTo($mtag))
					{
						$this->setError($oldtag->getError());
					}
				}

				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					Lang::txt('COM_TAGS_TAGS_COPIED')
				);
			break;
		}
	}
}

