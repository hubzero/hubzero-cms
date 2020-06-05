<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Messages\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Messages\Models\Cfg;
use Hubzero\Base\Obj;
use Request;
use Notify;
use User;
use Lang;
use App;

/**
 * Messages config controller class.
 */
class Configs extends AdminController
{
	/**
	 * Display a list of blog entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$rows = Cfg::all()
			->whereEquals('user_id', (int) User::get('id'))
			->rows();

		$item = new Obj;

		foreach ($rows as $row)
		{
			$item->set($row->get('cfg_name'), $row->get('cfg_value'));
		}

		// Output the HTML
		$this->view
			->set('item', $item)
			->display();
	}

	/**
	 * Method to save the form data.
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

		$userId = User::get('id');

		if (!$userId)
		{
			Notify::error(Lang::txt('COM_MESSAGES_ERR_INVALID_USER'));
			return $this->cancelTask();
		}

		if (!Cfg::removeForUser($userId))
		{
			Notify::error(Lang::txt('COM_MESSAGES_ERR_REMOVING_SETTINGS'));
			return $this->cancelTask();
		}

		$db = App::get('db');
		$fields = Request::getArray('fields', array(), 'post', 'none', 2);

		$tuples = array();
		foreach ($fields as $k => $v)
		{
			$tuples[] =  '(' . $userId . ', ' . $db->quote($k) . ', ' . $db->quote($v) . ')';
		}

		if ($tuples)
		{
			$model = Cfg::blank();

			$db->setQuery(
				'INSERT INTO ' . $model->getTableName() .
				' (user_id, cfg_name, cfg_value)'.
				' VALUES ' . implode(',', $tuples)
			);
			$db->query();

			if ($error = $db->getErrorMsg())
			{
				Notify::error($error);
			}
		}

		$this->cancelTask();
	}
}
