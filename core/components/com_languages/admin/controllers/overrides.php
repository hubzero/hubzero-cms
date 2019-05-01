<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Languages\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Languages\Models\Override;
use Components\Languages\Models\Overrider;
use Filesystem;
use Request;
use Notify;
use Route;
use User;
use Lang;
use App;

require_once dirname(dirname(__DIR__)) . '/models/override.php';
require_once dirname(dirname(__DIR__)) . '/models/overrider.php';

/**
 * Languages Overrides Controller
 */
class Overrides extends AdminController
{
	/**
	 * The prefix to use with controller messages
	 *
	 * @var  string
	 */
	protected $text_prefix = 'COM_LANGUAGES_VIEW_OVERRIDES';

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
		$default = $this->config->get('site', 'en-GB') . '0';

		$filters = array(
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'filter_search',
				''
			),
			'language_client' => Request::getState(
				$this->_option . '.' . $this->_controller . '.language_client',
				'filter_language_client',
				$default,
				'cmd'
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
			),
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			)
		);

		$old_language_client = User::getState('com_languages.overrides.filter.language_client', '');
		$language_client     = $filters['language_client'];

		if ($old_language_client != $language_client)
		{
			$client_id = substr($language_client, -1);
			$language  = substr($language_client, 0, -1);
		}
		else
		{
			$client_id = User::getState('com_languages.overrides.filter.client', 0);
			$language  = User::getState('com_languages.overrides.filter.language', 'en-GB');
		}

		// Add filters to the session because they won't be stored there
		// by 'getUserStateFromRequest' if they aren't in the current request
		User::setState('com_languages.overrides.filter.client', $client_id);
		User::setState('com_languages.overrides.filter.language', $language);

		$client = \Hubzero\Base\ClientManager::client($client_id);

		$filters['client'] = $client->name;
		$filters['language'] = $language;

		$model = new Override($filters);

		$rows = $model->all();

		$pagination = new \Hubzero\Pagination\Paginator(
			$model->total(),
			$filters['start'],
			$filters['limit']
		);

		$languages = $model->languages();

		// Output the HTML
		$this->view
			->set('items', $rows)
			->set('filters', $filters)
			->set('pagination', $pagination)
			->set('languages', $languages)
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
			$client_id = User::getState('com_languages.overrides.filter.client', 0);
			$language  = User::getState('com_languages.overrides.filter.language', 'en-GB');

			$client = \Hubzero\Base\ClientManager::client($client_id);

			$model = new Override(array(
				'client'   => $client->name,
				'language' => $language
			));

			$row = $model->one(Request::getString('id'));
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

		$model = new Override(array(
			'client'   => $fields['client'],
			'language' => $fields['language']
		));

		if (!isset($fields['id']))
		{
			$fields['id'] = $fields['key'];
		}

		$row = $model->one($fields['id']);

		// Store new content
		if (!$model->save($fields, (isset($fields['both']) && $fields['both'])))
		{
			Notify::error($model->getError());
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
	 * Method for deleting one or more overrides
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Get items to dlete from the request
		$cid = Request::getArray('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
			Notify::warning(Lang::txt($this->text_prefix . '_NO_ITEM_SELECTED'));
		}
		else
		{
			$client_id = User::getState('com_languages.overrides.filter.client', 0);
			$language  = User::getState('com_languages.overrides.filter.language', 'en-GB');

			$client = \Hubzero\Base\ClientManager::client($client_id);

			// Get the model
			$model = new Override(array(
				'client'   => $client->name,
				'language' => $language
			));

			// Remove the items
			if ($model->delete($cid))
			{
				Notify::success(Lang::txts($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
			}
			else
			{
				Notify::error($model->getError());
			}
		}

		$this->cancelTask();
	}

	/**
	 * Method for refreshing the cache in the database with the known language strings
	 *
	 * @return  void
	 */
	public function refreshTask()
	{
		User::setState('com_languages.overrides.cachedtime', null);

		// Empty the database cache first
		try
		{
			$db = App::get('db');
			$db->setQuery('TRUNCATE TABLE ' . $db->qn(Overrider::blank()->getTableName()));
			$db->query();
		}
		catch (\RuntimeException $e)
		{
			$this->setError($e->getMessage());
		}

		if (!$this->getError())
		{
			// Initialize some variables
			$client   = User::getState('com_languages.overrides.filter.client', 0) ? 'administrator' : 'site';
			$language = User::getState('com_languages.overrides.filter.language', 'en-GB');

			$files = array();

			foreach (array(PATH_CORE, PATH_APP) as $base)
			{
				$path = $base . '/bootstrap/language/' . ($base == PATH_CORE ? ucfirst($client) : $client) . '/' . $language;

				// Parse common language directory
				if (Filesystem::exists($path))
				{
					$files = Filesystem::files($path, $language . '.*ini$', false, true);
				}

				// Parse language directories of components
				$files = array_merge($files, Filesystem::files($base . '/components', $language . '.*ini$', 3, true));

				// Parse language directories of modules
				$files = array_merge($files, Filesystem::files($base . '/modules', $language . '.*ini$', 3, true));

				// Parse language directories of templates
				$files = array_merge($files, Filesystem::files($base . '/templates', $language . '.*ini$', 3, true));

				// Parse language directories of plugins
				$files = array_merge($files, Filesystem::files($base . '/plugins', $language . '.*ini$', 3, true));
			}

			// Parse all found ini files and add the strings to the database cache
			foreach ($files as $file)
			{
				$strings = Override::parseFile($file);

				if ($strings && count($strings))
				{
					foreach ($strings as $key => $string)
					{
						$entry = Overrider::blank()->set(array(
							'constant' => $key,
							'string'   => $string,
							'file'     => $file
						));

						if (!$entry->save())
						{
							$this->setError($entry->getError());
						}
					}
				}
			}

			// Update the cached time
			User::setState('com_languages.overrides.cachedtime.' . $client . '.' . $language, time());
		}

		// Prepare the response data
		$response = new \stdClass;
		$response->success = ($this->getError() ? false : true);
		$response->error   = $this->getError();
		$response->data    = true;

		echo json_encode($response);
	}

	/**
	 * Method for searching language strings
	 *
	 * @return  void
	 */
	public function searchTask()
	{
		$results = array();

		$limitstart = Request::getInt('more');
		$searchstring = Request::getString('searchstring');

		// Create the search query
		$query = Overrider::all();

		if (Request::getCmd('searchtype') == 'constant')
		{
			$query->whereLike('constant', $searchstring);
		}
		else
		{
			$query->whereLike('string', $searchstring);
		}

		$total = with(clone $query)->total();

		// Consider the limitstart according to the 'more' parameter and load the results
		$query->start($limitstart)
			->limit(10);

		$results['results'] = $query->rows()->toObject();

		// Check whether there are more results than already loaded
		if ($total > $limitstart + 10)
		{
			// If this is set a 'More Results' link will be displayed in the view
			$results['more'] = $limitstart + 10;
		}

		// Get the message queue
		$messages = Notify::messages();

		// Build the sorted messages list
		if (is_array($messages) && count($messages))
		{
			foreach ($messages as $message)
			{
				if (isset($message['type']) && isset($message['message']))
				{
					$lists[$message['type']][] = $message['message'];
				}
			}
		}

		// If messages exist add them to the output
		if (isset($lists) && is_array($lists))
		{
			$data->messages = $lists;
		}

		// Prepare the response data
		$response = new \stdClass;
		$response->success = ($this->getError() ? false : true);
		$response->error   = $this->getError();
		$response->data    = $results;

		echo json_encode($response);
	}
}
