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

use Components\Newsletter\Tables\Newsletter;
use Components\Newsletter\Tables\PrimaryStory;
use Components\Newsletter\Tables\SecondaryStory;
use Hubzero\Component\AdminController;
use stdClass;
use Request;
use Route;
use Lang;
use App;

/**
 * Newsletter stories Controller
 */
class Story extends AdminController
{
	/**
	 * Add Newsletter Story Task
	 *
	 * @return 	void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit Newsletter Story Task
	 *
	 * @return 	void
	 */
	public function editTask()
	{
		//get request vars
		$this->view->type = strtolower(Request::getVar("type", "primary"));
		$this->view->id   = Request::getInt("id", 0);
		$this->view->sid  = Request::getInt("sid", 0);

		//load campaign
		$this->view->newsletter = new Newsletter($this->database);
		$this->view->newsletter->load($this->view->id);

		//default object
		$this->view->story = new stdClass;
		$this->view->story->id = null;
		$this->view->story->order = null;
		$this->view->story->title = null;
		$this->view->story->story = null;
		$this->view->story->readmore_title = null;
		$this->view->story->readmore_link  = null;

		//are we editing
		if ($this->view->sid)
		{
			if ($this->view->type == "primary")
			{
				$this->view->story = new PrimaryStory($this->database);
			}
			else
			{
				$this->view->story = new SecondaryStory($this->database);
			}

			$this->view->story->load($this->view->sid);
		}

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// If we are creating an auto-generated newsletter
		if ($this->view->type == 'autogen')
		{
				// Load available sources
				$this->view->enabledSources = Event::trigger('newsletter.onGetEnabledDigests');

				/* It should be noted that these are not served via the CMS, per se. Rather they
				 * JavaScript will manipulate the DOM and save the HTML as a string into the Primary
				 * Story content field.
				 */

				// The path where the Story Template Layouts are.
				$viewPath = dirname(__DIR__) . DS . 'views' . DS . 'storytemplates' . DS . 'tmpl';

				// Get available layouts
				$contents = Filesystem::listContents($viewPath);

				// Empty bucket to hold layout names;
				$this->view->layouts = array();

				// Make sure we aren't including any cruft.
				foreach ($contents as $file)
				{
					// Check for php extention
					if (Filesystem::extension($viewPath . DS . $file['path']) == 'php' && $file['path'] != '/index.php')
					{
						// Some trimming of the leading / and the .php; push into bucket
						array_push($this->view->layouts, rtrim(ltrim($file['path'], "//"), ".php"));
					}
				}

				// Display the alternative layout
				$this->view->setLayout('_autogen')->display();
		}
		else
		{
			// Output the HTML
			$this->view->setLayout('edit')->display();
		}
	}

	/**
	 * Fetch AutoContent (from plugin) Task
	 *
	 * @return 	void
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
				'name' => 'storytemplates',
				'layout' => $layout,
				));

			// Pass the data through to the view
			$view->object = $obj;

			$html = $view->display();
			echo $html;
			exit();
		}
		else
		{
		 // Output a warning
		 echo json_encode(array('status'=>'nothing specified'));
		 exit();
		}
	}

	public function saveAutoTask()
	{
		// Story information
		$title = Request::getVar('title');
		$source = Request::getVar('contentSource');
		$itemCount = Request::getInt('itemCount');
		$template = Request::getVar('layout');

		// Newsletter ID
		$nid = Request::getInt('nid');

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
			// Redirect if the information is lacking
			App::redirect(
				Route::url('index.php?option=com_newsletter&controller=story&task=add&type=autogen&id=' . $nid, false),
				Lang::txt('COM_NEWSLETTER_STORY_MISSING_REQUIRED'), 'error'
			);
		}

		$newsletterStory = new PrimaryStory($this->database);

		// Check to make sure we have an order
		if (!isset($story['order']) || $story['order'] == '' || $story['order'] == 0)
		{
			$currentHighestOrder = $newsletterStory->_getCurrentHighestOrder($nid);
			$newOrder = $currentHighestOrder + 1;

			$order = $newOrder;
		}

		$newsletterStory->title = $title;
		$newsletterStory->story = $autogenString;
		$newsletterStory->order = $order;
		$newsletterStory->nid 	= $nid;

		//save the story
		if (!$newsletterStory->save($newsletterStory))
		{
			$this->setError($newsletterStory->getError());
			$this->editTask($nid);
			return;
		}

		//inform and redirect
		App::redirect(
			Route::url('index.php?option=com_newsletter&controller=newsletter&task=edit&id=' . $nid, false),
			Lang::txt('COM_NEWSLETTER_STORY_SAVED_SUCCESS')
		);
	}
	/**
	 * Save Newsletter Story Task
	 *
	 * @return 	void
	 */
	public function saveTask($nid = 0)
	{
		//get story
		$story = Request::getVar("story", array(), 'post', 'ARRAY', JREQUEST_ALLOWHTML);
		$type  = Request::getVar("type", "primary");

		$id = !empty($story) && isset($story['nid']) ? $story['nid'] : $nid;

		// If autogenerated, use its handler
		if ($type == "autogen")
		{
			$this->saveAutoTask();
		}

		//are we working with a primary or secondary story
		if ($type == "primary")
		{
			$newsletterStory = new PrimaryStory($this->database);
		}
		else
		{
			$newsletterStory = new SecondaryStory($this->database);
		}

		//check to make sure we have an order
		if (!isset($story['order']) || $story['order'] == '' || $story['order'] == 0)
		{
			$currentHighestOrder = $newsletterStory->_getCurrentHighestOrder($id);
			$newOrder = $currentHighestOrder + 1;

			$story['order'] = $newOrder;
		}

		//save the story
		if (!$newsletterStory->save($story))
		{
			$this->setError($newsletterStory->getError());
			$this->editTask();
			return;
		}

		//inform and redirect
		App::redirect(
			Route::url('index.php?option=com_newsletter&controller=newsletter&task=edit&id=' . $newsletterStory->nid, false),
			Lang::txt('COM_NEWSLETTER_STORY_SAVED_SUCCESS')
		);
	}


	/**
	 * Reorder Newsletter Story Task
	 *
	 * @return 	void
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
			$story = new PrimaryStory($this->database);
		}
		else
		{
			$story = new SecondaryStory($this->database);
		}

		//load the story
		$story->load($sid);

		//set vars
		$lowestOrder = 1;
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
		App::redirect(
			Route::url('index.php?option=com_newsletter&controller=newsletter&task=edit&id=' . $id . '#' . $type . '-stories', false),
			Lang::txt('COM_NEWSLETTER_STORY_REORDER_SUCCESS')
		);
	}


	/**
	 * Delete Newsletter Task
	 *
	 * @return 	void
	 */
	public function deleteTask()
	{
		//get the request vars
		$id   = Request::getInt('id', 0);
		$sid  = Request::getInt('sid', 0);
		$type = Request::getWord('type', 'primary');

		if (strtolower($type) == 'primary')
		{
			$story = new PrimaryStory($this->database);
		}
		else
		{
			$story = new SecondaryStory($this->database);
		}

		//load the story
		$story->load($sid);

		//mark as deleted
		$story->deleted = 1;

		//save so story is marked deleted
		if (!$story->save($story))
		{
			$this->setError(Lang::txt('COM_NEWSLETTER_STORY_DELETE_FAIL'));
			$this->editTask();
			return;
		}

		//redirect back to campaigns list
		App::redirect(
			Route::url('index.php?option=com_newsletter&controller=newsletter&task=edit&id=' . $id, false),
			Lang::txt('COM_NEWSLETTER_STORY_DELETE_SUCCESS')
		);
	}


	/**
	 * Display all campaigns task
	 *
	 * @return 	void
	 */
	public function cancelTask()
	{
		$story = Request::getVar("story", array());
		$nid = Request::getInt('nid', 0);

		$id = !empty($story) ? $story['nid'] : $nid;

		App::redirect(
			Route::url('index.php?option=com_newsletter&controller=newsletter&task=edit&id=' . $id, false)
		);
	}
}
