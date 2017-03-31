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

namespace Components\Templates\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Templates\Models\File;
use Request;
use Notify;
use Route;
use Lang;
use User;
use App;

/**
 * Source controller for templates
 */
class Source extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('apply', 'save');
		$this->registerTask('cancel', 'display');

		parent::execute();
	}

	/**
	 * List all entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=templates', false)
		);
	}

	/**
	 * Edit an entry
	 *
	 * @param   object  $file
	 * @return  void
	 */
	public function editTask($file = null)
	{
		// Access checks.
		if (!User::authorise('core.edit', $this->option))
		{
			App::abort(403, Lang::txt('JERROR_CORE_ACTION_NOT_PERMITTED'));
		}

		if (!$file)
		{
			$recordId = base64_decode(Request::getVar('id'));
			$context  = 'com_templates.edit.source';

			if (preg_match('#\.\.#', $recordId))
			{
				App::abort(500, Lang::txt('COM_TEMPLATES_ERROR_SOURCE_FILE_NOT_FOUND'));
			}

			$recordId = explode(':', $recordId);

			if (count($recordId) < 2)
			{
				App::abort(500, Lang::txt('COM_TEMPLATES_ERROR_SOURCE_FILE_NOT_FOUND'));
			}

			$file = new File($recordId[1], $recordId[0]);
		}

		$this->view
			->set('file', $file)
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

		// Access checks.
		if (!User::authorise('core.edit', $this->option))
		{
			App::abort(403, Lang::txt('JERROR_CORE_ACTION_NOT_PERMITTED'));
		}

		$fields = Request::getVar('fields', array(), 'post');

		$file = new File($fields['filename'], $fields['extension_id']);

		if (!$file->save($fields['source']))
		{
			Notify::error($file->getError());
		}

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($file);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=templates', false)
		);
	}
}
