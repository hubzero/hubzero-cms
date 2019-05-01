<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Admin\Controllers;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'type.php';

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
	 * List types
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$types = Type::all();

		// Output the HTML
		$this->view
			->set('types', $types)
			->display();
	}

	/**
	 * Edit a type
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!($row instanceof Type))
		{
			// Incoming
			$id = Request::getArray('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			$row = Type::oneOrNew($id);
		}

		// Output the HTML
		$this->view
			->set('config', $this->config)
			->set('type', $row)
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

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$fields = Request::getArray('type', array(), 'post');
		$typeId = !empty($fields['id']) ? $fields['id'] : null;
		unset($fields['id']);

		$row = Type::oneOrNew($typeId);
		$row->set($fields);

		// Store new content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('CITATION_TYPE_SAVED'));

		$this->cancelTask();
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

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming (expecting an array)
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$removed = 0;

		foreach ($ids as $id)
		{
			$type = Type::oneOrFail(intval($id));

			if (!$type->destroy())
			{
				Notify::error($type->getError());
				continue;
			}

			$removed++;
		}

		Notify::success(Lang::txt('CITATION_TYPE_REMOVED'));

		// Redirect
		$this->cancelTask();
	}
}
