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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Admin\Controllers;

use Components\Newsletter\Models\Newsletter;
use Components\Newsletter\Models\Primary;
use Components\Newsletter\Models\Secondary;
use Hubzero\Component\AdminController;
use stdClass;
use Request;
use Route;
use Lang;
use App;

/**
 * Newsletter stories Controller
 */
class Stories extends AdminController
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
	 * Edit Newsletter Story Task
	 *
	 * @param   object  $row
	 * @return 	void
	 */
	public function editTask($story = null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		//get request vars
		$id = Request::getInt('nid', 0);
		$type = strtolower(Request::getWord('type', 'primary'));

		//load campaign
		$newsletter = Newsletter::oneOrFail($id);

		if (!is_object($story))
		{
			$sid  = Request::getInt("sid", 0);

			if (strtolower($type) == 'primary')
			{
				$story = Primary::oneOrNew($sid);
			}
			else
			{
				$story = Secondary::oneOrNew($sid);
			}
		}

		// If we are creating an auto-generated newsletter
		if ($type == 'autogen')
		{
			// It should be noted that these are not served via the CMS, per se. Rather they
			// JavaScript will manipulate the DOM and save the HTML as a string into the Primary
			// Story content field.

			// The path where the Story Template Layouts are.
			$viewPath = dirname(__DIR__) . DS . 'views' . DS . 'storytemplates' . DS . 'tmpl';

			// Get available layouts
			$contents = Filesystem::listContents($viewPath);

			// Empty bucket to hold layout names;
			$layouts = array();

			// Make sure we aren't including any cruft.
			foreach ($contents as $file)
			{
				// Check for php extention
				if (Filesystem::extension($viewPath . DS . $file['path']) == 'php' && $file['path'] != '/index.php')
				{
					// Some trimming of the leading / and the .php; push into bucket
					array_push($layouts, rtrim(ltrim($file['path'], "//"), ".php"));
				}
			}

			// Display the alternative layout
			$this->view
				->set('enabledSources', Event::trigger('newsletter.onGetEnabledDigests'))
				->set('layouts', $layouts)
				->setLayout('_autogen');
		}
		else
		{
			// Output the HTML
			$this->view->setLayout('edit');
		}

		$this->view
			->set('nid', $id)
			->set('story', $story)
			->set('type', $type)
			->set('newsletter', $newsletter)
			->display();
	}

	/**
	 * Fetch AutoContent (from plugin) Task
	 *
	 * @return  void
	 */
	public function fetchAutoContentTask()
	{
		// Prevent direct access
		if (User::isGuest())
		{
			return false;
		}

		// Request the source variable
		$source = Request::getVar('source', '');
		$layout = Request::getVar('layout', '');
		$itemCount = Request::getInt('itemCount', 5);

		// Make sure we have something to work with
		if ($source != '' && $layout != '')
		{
			// Get a list of enabled plugins
			$enabledSources = Event::trigger('newsletter.onGetEnabledDigests');

			// Get the matching source's ID, based on plugin ordering
			$matches = array_keys($enabledSources, $source);
			$key = $matches[0];

			// Get the latest content
			$obj = new stdClass;
			$obj = Event::trigger('newsletter.onGetLatest', array($itemCount));

			// Only get the portion we are working with
			$obj = $obj[$key];

			// Instantiate the desired Story Layout view
			$view = new \Hubzero\Component\View(array(
				'name'   => 'storytemplates',
				'layout' => $layout,
			));

			// Pass the data through to the view
			$view->object = $obj;
			$view->display();
		}
		else
		{
			// Output a warning
			echo json_encode(array('status' => 'nothing specified'));
		}
		exit();
	}

	/**
	 * Save auto Task
	 *
	 * @return  void
	 */
	public function saveAutoTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Story information
		$title     = Request::getVar('title');
		$source    = Request::getVar('contentSource');
		$itemCount = Request::getInt('itemCount');
		$template  = Request::getVar('layout');

		// Newsletter ID
		$nid       = Request::getInt('nid', 0);

		// Ensure that we have everything
		if ($source != '' && $itemCount > 0 && $template != '')
		{
			// Enforce a 20 item maximum
			if ($itemCount > 20)
			{
				$itemCount = 20;
			}

			// Create the string
			$autogenString = "{{AUTOGEN_" . strtoupper($source) . "_" . $itemCount . "_" . strtoupper($template) . "}}";
		}
		else
		{
			Notify::warning(Lang::txt('COM_NEWSLETTER_STORY_MISSING_REQUIRED'));

			// Redirect if the information is lacking
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=add&type=autogen&id=' . $nid, false)
			);
		}

		$story = Primary::blank();
		$story->set('title', $title);
		$story->set('story', $autogenString);
		$story->set('nid', $nid);

		// Save the story
		if (!$story->save())
		{
			Notify::error($story->getError());
			return $this->editTask($story);
		}

		Notify::success(Lang::txt('COM_NEWSLETTER_STORY_SAVED_SUCCESS'));

		// Inform and redirect
		$this->cancelTask();
	}

	/**
	 * Save Newsletter Story Task
	 *
	 * @return  void
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

		// Incoming
		$fields = Request::getVar('story', array(), 'post', 'ARRAY', JREQUEST_ALLOWHTML);
		$type   = Request::getVar('type', 'primary');

		// If autogenerated, use its handler
		if ($type == 'autogen')
		{
			return $this->saveAutoTask();
		}

		if ($type == 'primary')
		{
			$story = Primary::oneOrNew($fields['id']);
		}
		else
		{
			$story = Secondary::oneOrNew($fields['id']);
		}

		$story->set($fields);

		// Save the story
		if (!$story->save())
		{
			Notify::error($story->getError());
			return $this->editTask($story);
		}

		Notify::success(Lang::txt('COM_NEWSLETTER_STORY_SAVED_SUCCESS'));

		// Inform and redirect
		$this->cancelTask();
	}

	/**
	 * Reorder Newsletter Story Task
	 *
	 * @return  void
	 */
	public function reorderTask()
	{
		//get request vars
		$id = Request::getInt('id', 0);
		$sid = Request::getInt('sid', 0);
		$type = Request::getWord('type', 'primary');
		$direction = Request::getWord('direction', 'down');

		//what kind of story do we want
		if (strtolower($type) == 'primary')
		{
			$story = Primary::oneOrFail($sid);
		}
		else
		{
			$story = Secondary::oneOrFail($sid);
		}

		//set vars
		$lowestOrder  = 1;
		$highestOrder = $story->_getCurrentHighestOrder($id);
		$currentOrder = $story->order;

		//move page up or down
		if ($direction == 'down')
		{
			$newOrder = $currentOrder + 1;
			if ($newOrder > $highestOrder)
			{
				$newOrder = $highestOrder;
			}
		}
		else
		{
			$newOrder = $currentOrder - 1;
			if ($newOrder < $lowestOrder)
			{
				$newOrder = $lowestOrder;
			}
		}

		$database = \App::get('db');

		//is there a nother story having the order we want?
		$sql = "SELECT * FROM {$story->getTableName()} WHERE `order`=" . $database->quote($newOrder) . " AND nid=" . $database->quote($id);
		$database->setQuery($sql);
		$moveTo = $database->loadResult();

		//if there isnt just update story
		if (!$moveTo)
		{
			$sql = "UPDATE {$story->getTableName()} SET `order`=" . $database->quote($newOrder) . " WHERE id=" . $database->quote($sid);
			$database->setQuery($sql);
			$database->query();
		}
		else
		{
			//swith orders
			$sql = "UPDATE {$story->getTableName()} SET `order`=" . $database->quote($newOrder) . " WHERE id=" . $database->quote($sid);
			$database->setQuery($sql);
			$database->query();

			$sql = "UPDATE {$story->getTableName()} SET `order`=" . $database->quote($currentOrder) . " WHERE id=" . $database->quote($moveTo);
			$database->setQuery($sql);
			$database->query();
		}

		//redirect back to campaigns list
		Notify::success(Lang::txt('COM_NEWSLETTER_STORY_REORDER_SUCCESS'));

		App::redirect(
			Route::url('index.php?option=com_newsletter&controller=newsletter&task=edit&id=' . $id . '#' . $type . '-stories', false)
		);
	}

	/**
	 * Delete story
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Get the request vars
		$id   = Request::getInt('id', 0);
		$sid  = Request::getInt('sid', 0);
		$type = Request::getWord('type', 'primary');

		if (strtolower($type) == 'primary')
		{
			$story = Primary::oneOrFail($sid);
		}
		else
		{
			$story = Secondary::oneOrFail($sid);
		}

		// Mark as deleted
		$story->set('deleted', 1);

		// Save so story is marked deleted
		if (!$story->save())
		{
			Notify::error(Lang::txt('COM_NEWSLETTER_STORY_DELETE_FAIL'));
			return $this->cancelTask();
		}

		Notify::success(Lang::txt('COM_NEWSLETTER_STORY_DELETE_SUCCESS'));

		$this->cancelTask();
	}

	/**
	 * Cancel task
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$story = Request::getVar('story', array());
		$nid   = Request::getInt('nid', 0);

		$id = !empty($story) ? $story['nid'] : $nid;

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=newsletters&task=edit&id=' . $id, false)
		);
	}
}
