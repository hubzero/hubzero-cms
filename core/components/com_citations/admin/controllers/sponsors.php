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

namespace Components\Citations\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Citations\Tables\Sponsor;
use Request;
use Route;
use Lang;
use App;

/**
 * Controller class for citation types
 */
class Sponsors extends AdminController
{
	/**
	 * List types
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$cs = new Sponsor($this->database);
		$this->view->sponsors = $cs->getSponsor();

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new type
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a type
	 *
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		$this->view->config = $this->config;

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array(0));
			if (is_array($id))
			{
				$id = (!empty($id) ? $id[0] : 0);
			}

			$sponsor = new Sponsor($this->database);
			$row = $sponsor->getSponsor($id);
		}

		$this->view->sponsor = $row;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			\Notify::error($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a type
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$s = Request::getVar('sponsor', array(), 'post');

		$row = new Sponsor($this->database);
		if (!$row->bind($s))
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		if (!$row->check())
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('CITATION_SPONSOR_SAVED')
		);
	}

	/**
	 * Remove one or more types
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Incoming (expecting an array)
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Ensure we have an ID to work with
		if (empty($ids))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('CITATION_NO_SPONSOR'),
				'error'
			);
			return;
		}

		$cs = new Sponsor($this->database);
		foreach ($ids as $id)
		{
			// Delete the type
			$cs->delete($id);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('CITATION_SPONSOR_REMOVED')
		);
	}
}
