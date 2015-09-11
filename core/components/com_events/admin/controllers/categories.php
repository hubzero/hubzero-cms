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

namespace Components\Events\Admin\Controllers;

use Components\Events\Tables\Category;
use Components\Events\Tables\Configs;
use Hubzero\Component\AdminController;
use Filesystem;
use Exception;
use Request;
use Route;
use Lang;
use App;

/**
 * Events controller for categories
 */
class Categories extends AdminController
{
	/**
	 * Determines task and attempts to execute it
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->config = new Configs($this->database);
		$this->config->load();

		parent::execute();
	}

	/**
	 * Display a list of entries
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->section = $this->_option;

		// Incoming
		$limit = Request::getState(
			$this->_option . '.limit',
			'limit',
			Config::get('list_limit'),
			'int'
		);
		$limitstart = Request::getVar('limitstart', 0, '', 'int');

		$this->view->section_name = '';
		if (intval($this->view->section) > 0)
		{
			$table = 'content';

			$this->database->setQuery("SELECT name FROM `#__sections` WHERE id='" . $this->view->section . "'");
			$this->view->section_name = $this->database->loadResult();
			if ($this->database->getErrorNum())
			{
				throw new Exception($this->database->getErrorMsg(), 500);
			}
			$this->view->section_name .= ' Section';
		}
		else if (strpos($this->view->section, 'com_') === 0)
		{
			$table = substr($this->view->section, 4);

			$this->database->setQuery("SELECT name FROM `#__extensions` WHERE type='component' AND element='" . $this->view->section . "'");
			$this->view->section_name = $this->database->loadResult();
			if ($this->database->getErrorNum())
			{
				throw new Exception($this->database->getErrorMsg(), 500);
			}
		}
		else
		{
			$table = $this->view->section;
		}

		// Get the total number of records
		$this->database->setQuery("SELECT count(*) FROM `#__categories` WHERE extension='" . $this->view->section . "'");
		$this->view->total = $this->database->loadResult();
		if ($this->database->getErrorNum())
		{
			throw new Exception($this->database->stderr(), 500);
		}

		// dmcd may 22/04  added #__events_categories table to fetch category color property
		$this->database->setQuery("SELECT  c.*, c.alias AS name, 'Public' AS groupname, u.name AS editor, cc.color AS color, "
			. "COUNT(DISTINCT s2.checked_out) AS checked_out, COUNT(DISTINCT s1.id) AS num"
			. "\nFROM #__categories AS c"
			. "\nLEFT JOIN #__users AS u ON u.id = c.checked_out"
			. "\nLEFT JOIN #__$table AS s1 ON s1.catid = c.id"
			. "\nLEFT JOIN #__$table AS s2 ON s2.catid = c.id AND s2.checked_out > 0"
			. "\nLEFT JOIN #__${table}_categories AS cc ON cc.id = c.id"
			. "\nWHERE extension='" . $this->view->section . "'"
			. "\nGROUP BY c.id"
			. "\nORDER BY c.title"
			. "\nLIMIT $limitstart,$limit"
		);

		// Execute query
		$this->view->rows = $this->database->loadObjectList();
		if ($this->database->getErrorNum())
		{
			throw new Exception($this->database->stderr(), 500);
		}

		$this->view->limit = $limit;
		$this->view->limitstart = $limitstart;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Show a form for adding an entry
	 *
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @return     void
	 */
	public function editTask()
	{
		Request::setVar('hidemainmenu', 1);

		// Incoming
		$id = Request::getInt('id', 0);

		// Load the category
		$this->view->row = new Category($this->database);
		$this->view->row->load($id);

		// Fail if checked out not by 'me'
		if ($this->view->row->checked_out && $this->view->row->checked_out <> User::get('id'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_EVENTS_CAL_LANG_CATEGORY_CHECKEDOUT')
			);
			return;
		}

		if ($this->view->row->id)
		{
			// Existing record
			$this->view->row->checkout(User::get('id'));
		}
		else
		{
			// New record
			$this->view->row->section = $this->_option;
			$this->view->row->color = '';
		}

		// Make order list
		$order = array();

		$max = intval($this->view->row->getCategoryCount()) + 1;
		for ($i=1; $i < $max; $i++)
		{
			$order[] = \Html::select('option', $i, $i, 'value', 'text');
		}

		$ipos[] = \Html::select('option', 'left', Lang::txt('left'), 'value', 'text');
		$ipos[] = \Html::select('option', 'right', Lang::txt('right'), 'value', 'text');

		$this->view->iposlist = \Html::select(
			'genericlist',
			$ipos,
			'image_position',
			'class="inputbox" size="1"',
			'value',
			'text',
			($this->view->row->image_position ? $this->view->row->image_position : 'left'),
			false,
			false
		);

		$imgFiles = $this->readDirectory(PATH_CORE . DS . 'site' . DS . 'media' . DS . 'images');
		$images = array(\Html::select('option', '', Lang::txt('Select Image'), 'value', 'text'));
		foreach ($imgFiles as $file)
		{
			if (preg_match("/bmp|gif|jpg|jpe|jpeg|png/", $file))
			{
				$images[] = \Html::select('option', $file, $file, 'value', 'text');
			}
		}

		$this->view->imagelist = \Html::select(
			'genericlist',
			$images,
			'image',
			'class="inputbox" size="1"' . " onchange=\"javascript:if (document.forms[0].image.options[selectedIndex].value!='') {document.imagelib.src='../images/stories/' + document.forms[0].image.options[selectedIndex].value} else {document.imagelib.src='../images/M_images/blank.png'}\"",
			'value',
			'text',
			$this->view->row->image,
			false,
			false
		);

		$this->view->orderlist = \Html::select(
			'genericlist',
			$order,
			'ordering',
			'class="inputbox" size="1"',
			'value',
			'text',
			$this->view->row->ordering,
			false,
			false
		);

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
	 * Read the contents of a directory
	 *
	 * @param      string  $path     Path to read
	 * @param      string  $filter   Filters to apply
	 * @param      boolean $recurse  Recursive?
	 * @param      boolean $fullpath Full path?
	 * @return     array
	 */
	private function readDirectory($path, $filter='.', $recurse=false, $fullpath=false)
	{
		$arr = array(null);

		// Get the files and folders
		$files   = Filesystem::files($path, $filter, $recurse, $fullpath);
		$folders = Filesystem::directories($path, $filter, $recurse, $fullpath);
		// Merge files and folders into one array
		if (is_array($files) && is_array($folders))
		{
			$arr = array_merge($files, $folders);
		}
		// Sort them all
		asort($arr);
		return $arr;
	}

	/**
	 * Save an entry
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		//get category info
		$category = Request::getVar('category', array());

		//set path
		$category['parent_id'] = 21; // root
		$category['level']     = 1;
		$category['path']      = (isset($category['alias'])) ? $category['alias'] : '';
		$category['published'] = 1;
		$category['access']    = 1;

		$row = new Category($this->database);

		if (!$row->save($category))
		{
			throw new Exception($row->getError(), 500);
		}
		//$row->updateOrder("section='$row->section'");

		//if ($oldtitle = Request::getVar('oldtitle', null, 'post'))
		//{
		//	if ($oldtitle != $row->title)
		//	{
		//		$this->database->setQuery("UPDATE #__menu SET name='$row->title' WHERE name='$oldtitle' AND type='content_category'");
		//		$this->database->query();
		//	}
		//}

		//// Update Section Count
		//if ($row->section != 'com_weblinks')
		//{
		//	$this->database->setQuery("UPDATE #__sections SET count=count+1 WHERE id = '$row->section'");
		//}

		//if (!$this->database->query())
		//{
		//	throw new Exception($this->database->getErrorMsg(), 500);
		//}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Publish one or more entries
	 *
	 * @return     void
	 */
	public function publishTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array());
		if (!is_array($ids))
		{
			$ids = array();
		}

		// Make sure we have an ID
		if (empty($ids))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		// Instantiate a category object
		$event = new Category($this->database);

		// Loop through the IDs and publish the category
		foreach ($ids as $id)
		{
			$event->publish($id);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_EVENTS_CAL_LANG_CATEGORY_PUBLISHED')
		);
	}

	/**
	 * Unpublish one or more entries
	 *
	 * @return     void
	 */
	public function unpublishTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array());
		if (!is_array($ids))
		{
			$ids = array();
		}

		// Make sure we have an ID
		if (empty($ids))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		// Instantiate a category object
		$event = new Category($this->database);

		// Loop through the IDs and unpublish the category
		foreach ($ids as $id)
		{
			$event->unpublish($id);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_EVENTS_CAL_LANG_CATEGORY_UNPUBLISHED')
		);
	}

	/**
	 * Move an item up one in the ordering
	 *
	 * @return     void
	 */
	public function orderupTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id = Request::getVar('id', array(0));
		$id = intval($id[0]);

		// Load the category, reorder, save
		$row = new Category($this->database);
		$row->load($id);
		$row->move(-1, "section='$row->section'");

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Move an item down one in the ordering
	 *
	 * @return     void
	 */
	public function orderdownTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id = Request::getVar('id', array(0));
		$id = intval($id[0]);

		// Load the category, reorder, save
		$row = new Category($this->database);
		$row->load($id);
		$row->move(1, "section='$row->section'");

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Remove one or more categories
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array());
		if (!is_array($ids))
		{
			$ids = array();
		}

		// Make sure we have an ID
		if (empty($ids))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		$cids = array();
		if (count($ids) > 0)
		{
			// Loop through each category ID
			foreach ($ids as $id)
			{
				// Load the category
				$cat = new Category($this->database);
				$cat->load($id);
				// Check its count of items in it
				if ($cat->count > 0)
				{
					// Category is NOT empty
					$cids[] = $cat->name;
				}
				else
				{
					// Empty category, go ahead and delete
					$cat->delete($id);
				}
			}
		}

		if (count($cids))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_EVENTS_CAL_LANG_CATEGORY_NOTEMPTY', implode("\', \'", $cids)),
				'warning'
			);
			return;
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_EVENTS_CAL_LANG_CATEGORY_REMOVED')
		);
	}

	/**
	 * Cancel a task by redirecting to main page
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		// Checkin the category
		$row = new Category($this->database);
		$row->bind($_POST);
		$row->checkin();

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}
}

