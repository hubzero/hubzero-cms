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

namespace Components\Messages\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Messages\Models\Cfg;
use Hubzero\Base\Object;
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

		$item = new Object;

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
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);

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
