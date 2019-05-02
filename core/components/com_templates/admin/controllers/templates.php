<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Templates\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Templates\Models\Style;
use Components\Templates\Models\Template;
use Request;
use Notify;
use Route;
use Lang;
use App;

/**
 * Templates controller for templates
 */
class Templates extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('apply', 'save');
		$this->registerTask('save2copy', 'save');

		parent::execute();
	}

	/**
	 * List all entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'filter_search',
				''
			)),
			'client_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.client_id',
				'filter_client_id',
				'*'
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'name'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$model = Template::all()
			->whereEquals('type', 'template');

		if ($filters['search'])
		{
			$filters['search'] = strtolower((string)$filters['search']);

			if (stripos($filters['search'], 'id:') === 0)
			{
				$model->whereEquals('id', (int) substr($filters['search'], 3));
			}
			else
			{
				$model->whereLike('element', $filters['search'], 1)
					->orWhereLike('name', $filters['search'], 1)
					->resetDepth();
			}
		}

		if ($filters['client_id'] && $filters['client_id'] != '*')
		{
			$model->whereEquals('client_id', (int)$filters['client_id']);
		}

		// Get records
		$rows = $model
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		$preview = $this->config->get('template_positions_display');

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->set('preview', $preview)
			->display();
	}

	/**
	 * Display template details and files
	 *
	 * @return  void
	 */
	public function filesTask()
	{
		// Access checks.
		if (!User::authorise('core.edit', $this->option)
		 || !User::authorise('core.create', $this->option))
		{
			App::abort(403, Lang::txt('JERROR_CORE_ACTION_NOT_PERMITTED'));
		}

		Request::setVar('hidemainmenu', 1);

		// Incoming
		$id = Request::getArray('id', array(0));

		if (is_array($id) && !empty($id))
		{
			$id = $id[0];
		}

		$template = Template::oneOrNew(intval($id));

		$files = $template->files();

		// Output the HTML
		$this->view
			->set('template', $template)
			->set('files', $files)
			->setLayout('files')
			->display();
	}
}
