<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Languages\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Languages\Models\Extension;
use Request;
use Notify;
use Cache;
use Lang;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'extension.php';

/**
 * Languages Controller for installed languages
 */
class Installed extends AdminController
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
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		parent::execute();
	}

	/**
	 * Display all records
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'client_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.client_id',
				'client',
				0,
				'int'
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'folder'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$client = \Hubzero\Base\ClientManager::client($filters['client_id']);

		$query = Extension::all()
			->whereEquals('state', 0)
			->whereEquals('enabled', 1);

		if ($filters['client_id'] >= 0)
		{
			$query->whereEquals('client_id', (int) $filters['client_id']);
		}

		$rows = $query
			->paginated('limitstart', 'limit')
			->rows();

		foreach ($rows as $row)
		{
			foreach ($row->info() as $key => $val)
			{
				$row->set($key, $val);
			}

			if ($this->config->get($client->name, 'en-GB') == $row->language)
			{
				$row->set('published', 1);
			}
			else
			{
				$row->set('published', 0);
			}
		}

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Task to set the default language
	 *
	 * @return  void
	 */
	public function setDefaultTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$cid = Request::getCmd('cid', '');
		$client_id = Request::getState(
			$this->_option . '.' . $this->_controller . '.client_id',
			'client',
			0,
			'int'
		);

		$client = \Hubzero\Base\ClientManager::client($client_id);
		/*$client->path = '/bootstrap/' . ucfirst($client->name);

		$model = $this->getModel('installed');
		if ($model->publish($cid))
		{
			Notify::success(Lang::txt('COM_LANGUAGES_MSG_DEFAULT_LANGUAGE_SAVED'));
		}
		else
		{
			Notify::error($this->getError());
		}
		*/
		$params = $this->config;
		$params->set($client->name, $cid);

		$extension = Extension::component();
		$extension->set('params', $params->toString());

		if (!$extension->save())
		{
			Notify::error($extension->getError());
		}
		else
		{
			Notify::success(Lang::txt('COM_LANGUAGES_MSG_DEFAULT_LANGUAGE_SAVED'));
		}

		Cache::clean('_system');

		$this->cancelTask();
	}
}
