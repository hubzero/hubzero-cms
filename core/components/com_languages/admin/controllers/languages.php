<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Languages\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Languages\Models\Language;
use Components\Languages\Helpers\Multilangstatus;
use Request;
use Notify;
use Route;
use Lang;
use App;

require_once dirname(dirname(__DIR__)) . '/models/language.php';

/**
 * Languages Controller for installed languages
 */
class Languages extends AdminController
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
		$this->registerTask('save2new', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		parent::execute();
	}

	/**
	 * Display all sections
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'filter_search',
				''
			),
			'published' => Request::getState(
				$this->_option . '.' . $this->_controller . '.published',
				'filter_published',
				-1,
				'int'
			),
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'filter_access',
				-1,
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

		$query = Language::all();

		$l = $query->getTableName();
		$m = '#__menu';
		$v = '#__viewlevels';

		$query->select($l . '.*');

		// Join over the asset groups.
		$query->select($v . '.title', 'access_level');
		$query->join($v, $v . '.id', $l . '.access', 'left');

		// Select the language home pages
		$query->select($m . '.home');
		$query->join($m, $m . '.language', $l . '.lang_code AND ' . $m . '.home=1 AND ' . $m . '.language <> \'*\'', 'left');
			//->whereEquals($m . '.home', 1)
			//->where($m . '.language', '<>', '*');

		if ($filters['published'] >= 0)
		{
			$query->whereEquals($l . '.published', (int) $filters['published']);
		}

		if ($filters['search'])
		{
			$entries->whereLike($l . '.title', strtolower((string)$filters['search']));
		}

		if ($filters['access'] >= 0)
		{
			$entries->whereEquals($l . '.access', (int)$filters['access']);
		}

		$rows = $query
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('items', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Displays a form for editing
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		if (!is_object($row))
		{
			$id = Request::getArray('lang_id', array(0));
			if (is_array($id))
			{
				$id = (!empty($id) ? intval($id[0]) : 0);
			}

			$row = Language::oneOrNew($id);
		}

		// Output the HTML
		$this->view
			->set('item', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a record and redirects to listing
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

		// Incoming
		$fields = Request::getArray('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$row = Language::oneOrNew($fields['lang_id'])->set($fields);

		// Store new content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_LANGUAGES_SAVE_SUCCESS'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		if ($this->getTask() == 'save2new')
		{
			return App::redirect(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit', false));
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Deletes one or more records and redirects to listing
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

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$i = 0;

		// Loop through each ID
		foreach ($ids as $id)
		{
			// Remove this category
			$row = Language::oneOrFail(intval($id));

			if (!$row->destroy())
			{
				Notify::error($row->getError());
				continue;
			}

			$i++;
		}

		if ($i)
		{
			Cache::clean('_system');
			Cache::clean($this->_option);

			Notify::success(Lang::txt('COM_LANGUAGES_N_ITEMS_DELETED', $i));
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$section = Request::getInt('section_id', 0);

		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$state = ($this->getTask() == 'publish' ? Language::STATE_PUBLISHED : Language::STATE_UNPUBLISHED);

		// Check for an ID
		if (count($ids) < 1)
		{
			Notify::warning(Lang::txt('COM_LANGUAGES_VIEW_OVERRIDES_NO_ITEM_SELECTED'));
			return $this->cancelTask();
		}

		$i = 0;

		foreach ($ids as $id)
		{
			// Update record(s)
			$row = Language::oneOrFail(intval($id));
			$row->set('published', $state);

			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$i++;
		}

		// set message
		if ($i)
		{
			if ($state == Language::STATE_PUBLISHED)
			{
				$message = Lang::txt('COM_LANGUAGES_N_ITEMS_PUBLISHED', $i);
			}
			else
			{
				$message = Lang::txt('COM_LANGUAGES_N_ITEMS_UNPUBLISHED', $i);
			}

			Notify::success($message);
		}

		$this->cancelTask();
	}

	/**
	 * Multilingual status
	 *
	 * @return  void
	 */
	public function multilangstatusTask()
	{
		require_once dirname(dirname(__DIR__)) . '/helpers/multilangstatus.php';

		$this->view
			->set('homes', Multilangstatus::getHomes())
			->set('switchers', Multilangstatus::getLangswitchers())
			->set('contentlangs', Multilangstatus::getContentlangs())
			->set('site_langs', Multilangstatus::getSitelangs())
			->set('statuses', Multilangstatus::getStatus())
			->set('homepages', Multilangstatus::getHomepages())
			->display();
	}
}
