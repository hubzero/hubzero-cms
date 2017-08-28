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

require_once Component::path('com_citations') . DS . 'models' . DS . 'type.php';
use Hubzero\Component\AdminController;
use Components\Citations\Models\Type;
use Request;
use Route;
use Lang;
use App;

/**
 * Controller class for citation types
 */
class Types extends AdminController
{
	/**
	 * List types
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$ct = new Type($this->database);
		$this->view->types = Type::all();
		$this->_displayMessages();

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

		if (!($row instanceof Type))
		{
			// Incoming
			$id = Request::getInt('id', 0);
			$row = Type::oneOrNew($id);
		}

		$this->view->type = $row;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			\Notify::error($error);
		}
		$this->_displayMessages();
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

		$fields = Request::getVar('type', array(), 'post');
		$typeId = !empty($fields['id']) ? $fields['id'] : null;
		unset($fields['id']);

		$row = Type::oneOrNew($typeId);
		$row->set($fields);

		// Store new content
		if (!$row->save())
		{
			Notify::error($row->getError(), 'com.citations');
			$this->editTask($row);
			return;
		}

		Notify::success(Lang::txt('CITATION_TYPE_SAVED'), 'com.citations');
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Remove one or more types
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming (expecting an array)
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Ensure we have an ID to work with
		if (empty($ids))
		{
			Notify::error(Lang::txt('CITATION_NO_TYPE'), 'com.citations');
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		$deleteTypes = Type::all()->whereIn('id', $ids)->rows();
		if (!$deleteTypes->destroyAll())
		{
			Notify::error(Lang::txt('CITATION_TYPE_ERROR_REMOVED'), 'com.citations');
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
		}

		Notify::success(Lang::txt('CITATION_TYPE_REMOVED'), 'com.citations');
		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	private function _displayMessages($domain = 'com.citations')
	{
		foreach (Notify::messages($domain) as $message)
		{
			Notify::message($message['message'], $message['type']);
		}
	}
}
