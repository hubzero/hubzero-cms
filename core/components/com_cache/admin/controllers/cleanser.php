<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cache\Admin\Controllers;

use Components\Cache\Helpers\Helper;
use Components\Cache\Models\Manager;
use Hubzero\Component\AdminController;
use Request;
use Route;
use Lang;
use App;

/**
 * Cache Controller
 */
class Cleanser extends AdminController
{
	/**
	 * Determine a task and execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->model = new Manager();

		parent::execute();
	}

	/**
	 * Display
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Set the default view name and format from the Request.
		$vName = Request::getCmd('view', 'cache');

		$data       = $this->model->data();
		$client     = $this->model->client();
		$pagination = $this->model->pagination();
		$state      = $this->model->state();

		// Check for errors.
		if (count($errors = $this->model->getErrors()))
		{
			App::abort(500, implode("\n", $errors));
		}

		Helper::addSubmenu($vName);

		$this->view
			->set('data', $data)
			->set('client', $client)
			->set('state', $state)
			->set('pagination', $pagination)
			->setLayout($vName)
			->display();
	}

	/**
	 * Delete
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$cid = Request::getArray('cid', array(), 'post', 'array');

		if (empty($cid))
		{
			App::abort(500, Lang::txt('JERROR_NO_ITEMS_SELECTED'));
		}
		else
		{
			$this->model->cleanlist($cid);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&client=' . $this->model->client()->id, false)
		);
	}

	/**
	 * Purge
	 *
	 * @return  void
	 */
	public function purgeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$ret = $this->model->purge();

		$msg = Lang::txt('COM_CACHE_EXPIRED_ITEMS_HAVE_BEEN_PURGED');
		$msgType = 'message';

		if ($ret === false)
		{
			$msg = Lang::txt('Error purging expired items');
			$msgType = 'error';
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&view=purge', false),
			$msg,
			$msgType
		);
	}
}
