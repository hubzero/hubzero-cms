<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
			$recordId = base64_decode(Request::getString('id'));
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

		$fields = Request::getArray('fields', array(), 'post');

		$file = new File($fields['filename'], $fields['extension_id']);

		if (!$file->save($fields))
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
