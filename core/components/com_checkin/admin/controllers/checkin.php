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

namespace Components\Checkin\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Checkin\Models\Inspector;
use Exception;
use Submenu;
use Request;
use Route;
use Lang;
use App;

/**
 * Checkin Controller
 */
class Checkin extends AdminController
{
	/**
	 * Determine a task and execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->model = new Inspector();

		parent::execute();
	}

	/**
	 * Display admin control panel
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Load the submenu.
		$this->addSubmenu(Request::getWord('option', 'com_checkin'));

		$this->view
			->set('state', $this->model->state())
			->set('items', $this->model->items())
			->set('total', $this->model->total())
			->setLayout('default')
			->display();
	}

	/**
	 * Checkin items
	 *
	 * @return  void
	 */
	public function checkinTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Initialise variables.
		$ids = Request::getVar('cid', array(), '', 'array');

		$msg = null;
		$cls = null;

		if (empty($ids))
		{
			$msg = Lang::txt('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
			$cls = 'warning';
		}
		else
		{
			// Checked in the items.
			$msg = Lang::txts('COM_CHECKIN_N_ITEMS_CHECKED_IN', $this->model->checkin($ids));
			$cls = 'success';
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			($msg ?: null),
			($cls ?: null)
		);
	}

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 * @return  void
	 */
	protected function addSubmenu($vName)
	{
		Submenu::addEntry(
			Lang::txt('JGLOBAL_SUBMENU_CHECKIN'),
			Route::url('index.php?option=com_checkin'),
			$vName == 'com_checkin'
		);
		Submenu::addEntry(
			Lang::txt('JGLOBAL_SUBMENU_CLEAR_CACHE'),
			Route::url('index.php?option=com_cache'),
			$vName == 'cache'
		);
		Submenu::addEntry(
			Lang::txt('JGLOBAL_SUBMENU_PURGE_EXPIRED_CACHE'),
			Route::url('index.php?option=com_cache&view=purge'),
			$vName == 'purge'
		);
	}
}
