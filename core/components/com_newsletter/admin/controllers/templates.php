<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Admin\Controllers;

use Components\Newsletter\Models\Template;
use Hubzero\Component\AdminController;
use stdClass;
use Request;
use Notify;
use Route;
use Lang;
use App;

/**
 * Newsletter templates Controller
 */
class Templates extends AdminController
{
	/**
	 * Override execute method
	 *
	 * @return 	void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Display Newsletter Templates Task
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		//get the templates
		$templates = Template::all()
			->ordered()
			->rows();

		// Output the HTML
		$this->view
			->setLayout('display')
			->set('templates', $templates)
			->display();
	}

	/**
	 * Edit Newsletter Template Task
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row = null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		// Load object
		if (!is_object($row))
		{
			// Incoming
			$id = Request::getArray('id', array(0));
			$id = is_array($id) ? $id[0] : $id;

			$row = Template::oneOrNew($id);
		}

		// check to see if tempalte is editable
		if ($row->editable == 0 && $row->editable != null)
		{
			Notify::warning(Lang::txt('COM_NEWSLETTER_TEMPLATE_NOT_EDITABLE'));
			return $this->cancelTask();
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->set('config', $this->config)
			->set('template', $row)
			->display();
	}

	/**
	 * Save Newsletter Template Task
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

		// Incoming data
		$fields = Request::getArray('fields', array(), 'post');

		// Initiate model
		$row = Template::oneOrNew($fields['id'])->set($fields);

		// save mailing list
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Set success message
		Notify::success(Lang::txt('COM_NEWSLETTER_TEMPLATE_SAVED_SUCCESS'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect back to list
		$this->cancelTask();
	}

	/**
	 * Delete Task
	 *
	 * @return 	void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// get the request vars
		$ids = Request::getArray('id', array());

		// make sure we have ids
		$success = 0;

		//make sure we have ids
		if (isset($ids) && count($ids) > 0)
		{
			foreach ($ids as $id)
			{
				// instantiate mailing list object
				$row = Template::oneOrFail($id);

				//check to make sure this isnt our default templates
				if ($row->editable == 0)
				{
					Notify::warning(Lang::txt('COM_NEWSLETTER_TEMPLATE_DELETE_FAILED'));
					continue;
				}

				// mark as deleted
				$row->set('deleted', 1);

				//save template marking as deleted
				if (!$row->save())
				{
					Notify::error(Lang::txt('COM_NEWSLETTER_TEMPLATE_DELETE_FAILED'));
					continue;
				}

				$success++;
			}
		}

		if ($success)
		{
			Notify::success(Lang::txt('COM_NEWSLETTER_TEMPLATE_DELETE_SUCCESS'));
		}

		// Redirect back to campaigns list
		$this->cancelTask();
	}

	/**
	 * Duplicate Task
	 *
	 * @return 	void
	 */
	public function duplicateTask()
	{
		if (!User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		//get request vars
		$ids = Request::getArray('id', array());
		$id = (isset($ids[0])) ? $ids[0] : null;

		//are we editing or adding a new tempalte
		if (!$id)
		{
			return $this->cancelTask();
		}

		//get template we want to duplicate
		$template = Template::oneOrFail($id);

		//set var so edit task can use
		$new_template = Template::blank()->set($template->toArray());
		$new_template->set('id', null);
		$new_template->set('name', $template->get('name') . ' (copy)');
		$new_template->set('editable', 1);

		//save copied template
		if (!$new_template->save())
		{
			Notify::error(Lang::txt('COM_NEWSLETTER_TEMPLATE_DUPLICATE_FAILED'));

			return $this->cancelTask();
		}

		//set success message & redirect
		Notify::success(Lang::txt('COM_NEWSLETTER_TEMPLATE_DUPLICATE_SUCCESS'));

		$this->cancelTask();
	}
}
