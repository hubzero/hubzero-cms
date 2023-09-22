<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Admin\Controllers;

use Components\Newsletter\Models\Campaign;
use Hubzero\Component\AdminController;
use stdClass;
use Request;
use Notify;
use Route;
use Lang;
use App;

// require the campaign model
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'campaign.php';

/**
 * Campaign Controller
 */
class Campaigns extends AdminController
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
		//$this->registerTask('publish', 'state');
		//$this->registerTask('unpublish', 'state');

		parent::execute();
	}

	/**
	 * Display Campaigns
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'title'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$campaigns = Campaign::all();

		if ($filters['search'])
		{
			$filters['search'] = strtolower((string)$filters['search']);
			$campaigns->whereLike('title', $filters['search']);
		}

		$rows = $campaigns
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->setLayout('display')
			->set('campaigns', $rows) // was 'campaigns'
			->set('filters', $filters)
			->display();
	}

	/**
	 * Edit or add Campaigns
	 * (swiped from admin/controllers/newsletters.php)
	 *
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

		// Load or create object
		if (!is_object($row))
		{
			// Incoming
			$id = Request::getArray('id', array(0));
			$id = is_array($id) ? $id[0] : $id;

			$row = Campaign::oneOrNew($id);
		}

		// Output the HTML
		$this->view
			->set('campaign', $row)
			->set('config', $this->config)
			->setLayout('edit')
			->display();		
	}

	/**
	 * Save campaign task
	 * (swiped from admin/controllers/newsletters.php)
	 *
	 * @return 	void
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

		// Incoming data from edit form
		$fields = Request::getArray('campaign', array(), 'post');

		// Initiate model
		$row = Campaign::oneOrNew($fields['id'])->set($fields);

		// did we have params
		// if so, it's a request to reset the secret
		$p = Request::getArray('params', array(), 'post');

		if (!empty($p))
		{
			// set new secret if indicated
			if (null !== $p['reset_secret'] && $p['reset_secret'] == 1)
			{
				$row->set('secret', Campaign::generateSecret(null));
			}
		}

		// Save campaign
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Set success message
		Notify::success(Lang::txt('COM_NEWSLETTER_SAVED_SUCCESS'));

		if ($this->getTask() == 'apply')
		{
			// If we just created campaign go back to edit form so we can add content
			return $this->editTask($row);
		}

		// Redirect back to campaigns list
		$this->cancelTask();
	}
}
