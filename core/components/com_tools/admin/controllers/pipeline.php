<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Admin\Controllers;

use Components\Tools\Models\Tool;
use Components\Resources\Models\Entry;
use Hubzero\Component\AdminController;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use App;

/**
 * Tools controller class
 */
class Pipeline extends AdminController
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

		parent::execute();
	}

	/**
	 * Display entries in the pipeline
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		$filters = array(
			// Get filters
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'search_field' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search_field',
				'search_field',
				'all'
			)),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				-1,
				'int'
			),
			// Sorting
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'toolname'
			),
			'sort_Dir' => strtoupper(Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)),
			// Get paging variables
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

		$filters['sortby'] = $filters['sort'] . ' ' . $filters['sort_Dir'];

		// In case limit has been changed, adjust limitstart accordingly
		$filters['start'] = ($filters['limit'] != 0 ? (floor($filters['start'] / $filters['limit']) * $filters['limit']) : 0);

		// Get a record count
		$total = Tool::getToolCount($filters, true);

		// Get records
		$rows = Tool::getToolSummaries($filters, true);

		// Display results
		$this->view
			->set('total', $total)
			->set('rows', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Edit an entry
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming instance ID
			$id = Request::getInt('id', 0);

			// Do we have an ID?
			if (!$id)
			{
				return $this->cancelTask();
			}

			$row = Tool::getInstance($id);
		}

		// Display results
		$this->view
			->set('row', $row)
			->setLayout('edit')
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		Request::checkToken();

		// Incoming instance ID
		$fields = Request::getArray('fields', array(), 'post');

		// Do we have an ID?
		if (!$fields['id'])
		{
			Notify::warning(Lang::txt('COM_TOOLS_ERROR_MISSING_ID'));
			return $this->cancelTask();
		}

		$row = Tool::getInstance(intval($fields['id']));
		if (!$row)
		{
			Request::setVar('id', $fields['id']);

			Notify::error(Lang::txt('COM_TOOLS_ERROR_TOOL_NOT_FOUND'));
			return $this->editTask();
		}

		$oldstate = $row->state;

		$row->title = trim($fields['title']);
		$row->ticketid = intval($fields['ticketid']);
		$row->state = intval($fields['state']);

		if (!$row->title)
		{
			Notify::error(Lang::txt('COM_TOOLS_ERROR_MISSING_TITLE'));
			return $this->editTask($row);
		}

		$row->update();

		// If the tool state was changed...
		if ($oldstate != $row->state && file_exists(\Component::path('com_resources') . '/models/entry.php'))
		{
			// Trash the associated resource page
			require_once \Component::path('com_resources') . '/models/entry.php';

			$resource = Entry::oneByAlias($row->toolname);

			if ($resource && $resource->get('id'))
			{
				if ($row->state == 7 || $row->state == 8) // published
				{
					$resource->set('published', Entry::STATE_PUBLISHED);
				}
				else if ($row->state == 9) // abandoned
				{
					$resource->set('published', Entry::STATE_TRASHED);
				}
				else
				{
					$resource->set('published', Entry::STATE_UNPUBLISHED);
				}

				if (!$resource->save())
				{
					Notify::error($resource->getError());
				}
			}
		}

		Notify::success(Lang::txt('COM_TOOLS_ITEM_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->cancelTask();
	}

	/**
	 * Temp function to issue new service DOIs for tool versions published previously
	 *
	 * @return  void
	 */
	public function batchdoiTask()
	{
		$yearFormat = 'Y';

		//  Limit one-time batch size
		$limit = Request::getInt('limit', 2);

		// Store output
		$created = array();
		$failed = array();

		$database = App::get('db');

		// Initiate extended database classes
		require_once \Component::path('com_resources') . '/models/entry.php';
		require_once \Component::path('com_resources') . '/models/doi.php';

		$objV     = new \Components\Tools\Tables\Version($database);
		$objA     = new \Components\Tools\Tables\Author($database);

		$live_site = rtrim(Request::base(), '/');
		$sitename = Config::get('sitename');

		// Get config
		$config = \Component::params($this->_option);

		// Get all tool publications without new DOI
		$database->setQuery("SELECT * FROM `#__doi_mapping` WHERE `doi`='' OR `doi` IS NULL");
		$rows = $database->loadObjectList();

		if ($rows)
		{
			$i = 0;
			foreach ($rows as $row)
			{
				if ($limit && $i == $limit)
				{
					// Output status message
					if ($created)
					{
						foreach ($created as $cr)
						{
							echo '<p>' . $cr . '</p>';
						}
					}
					echo '<p>' . Lang::txt('COM_TOOLS_REGISTERED_DOIS', count($created), count($failed)) . '</p>';
					return;
				}

				// Skip entries with no resource information loaded / non-tool resources
				$resource = Entry::oneOrNew($row->rid);
				if (!$resource->get('id') || !$row->alias)
				{
					continue;
				}

				// Get version info
				$database->setQuery(
					"SELECT *
					FROM `#__tool_version`
					WHERE `toolname`=" . $database->quote($row->alias) . "
					AND `revision`=" . $database->quote($row->local_revision) . "
					AND state!=3
					LIMIT 1"
				);
				$results = $database->loadObjectList();

				if ($results)
				{
					$title   = $results[0]->title ? $results[0]->title : $resource->title;
					$pubyear = $results[0]->released ? trim(Date::of($results[0]->released)->toLocal($yearFormat)) : date('Y');
				}
				else
				{
					// Skip if version not found
					continue;
				}

				// Collect metadata
				$metadata = array();
				$metadata['targetURL'] = $live_site . '/resources/' . $row->rid . '/?rev=' . $row->local_revision;
				$metadata['title']     = htmlspecialchars($title);
				$metadata['pubYear']   = $pubyear;
				$metadata['version']   = $results[0]->version;
				$metadata['license']   = $results[0]->license;

				// Get authors
				$objA = new \Components\Tools\Tables\Author($database);
				$authors = $objA->getAuthorsDOI($row->rid);

				// Register DOI
				$objDOI = \Components\Resources\Models\Doi::blank();
				$doiSuccess = $objDOI->register($authors, $config, $metadata);
				if ($doiSuccess)
				{
					$database->setQuery(
						"UPDATE `#__doi_mapping`
						SET `doi`=" . $database->quote($doiSuccess) . ",
						`doi_shoulder`=" . $database->quote($this->config->get('doi_shoulder')) . "
						WHERE `rid`=" . $database->quote($row->rid) . "
						AND `local_revision`=" . $database->quote($row->local_revision)
					);
					if (!$database->query())
					{
						$failed[] = $doiSuccess;
					}
					else
					{
						$created[] = $doiSuccess;
					}
				}
				else
				{
					$o = $objDOI->getError() . '<br />';
					foreach ($metadata as $key => $val)
					{
						$o .= $key . ': ' . $val . '<br />';
					}

					echo $o;
				}

				$i++;
			}
		}

		// Output status message
		if ($created)
		{
			foreach ($created as $cr)
			{
				echo '<p>' . $cr . '</p>';
			}
		}
		echo '<p>' . Lang::txt('COM_TOOLS_REGISTERED_DOIS', count($created), count($failed)) . '</p>';
	}
}
