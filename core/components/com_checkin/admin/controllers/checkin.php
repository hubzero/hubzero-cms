<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
		$ids = Request::getArray('cid', array(), '', 'array');

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
