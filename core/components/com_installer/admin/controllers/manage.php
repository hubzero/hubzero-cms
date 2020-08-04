<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Installer\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Installer\Admin\Models\Extension;
use Request;
use Notify;
use Lang;
use Html;
use User;
use App;

include_once dirname(__DIR__) . DS . 'models' . DS . 'extension.php';

/**
 * Controller for managing extensions
 */
class Manage extends AdminController
{
	/**
	 * Constructor.
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('publish', 'publish');

		parent::execute();
	}

	/**
	 * Display a list of uninstalled extensions
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Include the component HTML helpers.
		Html::addIncludePath(dirname(__DIR__) . '/helpers/html');

		$limit = Request::getState(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			\Config::get('list_limit'),
			'int'
		);
		$start = Request::getState(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);

		$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'filter_search',
				''
			)),
			'client_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.client_id',
				'filter_location',
				''
			),
			'status' => Request::getState(
				$this->_option . '.' . $this->_controller . '.status',
				'filter_status',
				'',
				''
			),
			'type' => Request::getState(
				$this->_option . '.' . $this->_controller . '.type',
				'filter_type',
				'',
				''
			),
			'group' => Request::getState(
				$this->_option . '.' . $this->_controller . '.group',
				'filter_group',
				'',
				''
			),
			// Get sorting variables
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

		$entries = Extension::all();

		$e = $entries->getTableName();

		$entries
			->select('(2*protected+(1-protected)*enabled)', 'status')
			->select($e . '.*')
			->whereEquals('state', 0);

		// Filter by search in id
		if (!empty($filters['search']))
		{
			if (stripos($filters['search'], 'id:') === 0)
			{
				$entries->whereEquals($e . '.extension_id', (int) substr($filters['search'], 3));
			}
			else
			{
				$entries->whereLike($e . '.name', $filters['search'], 1)
					->orWhereLike($e . '.folder', $filters['search'], 1)
					->resetDepth();
			}
		}

		if (isset($filters['client_id']) && $filters['client_id'] != '')
		{
			$entries->whereEquals('client_id', (int)$filters['client_id']);
		}

		if (isset($filters['status']) && $filters['status'] != '')
		{
			if ($filters['status'] == '2')
			{
				$entries->whereEquals('protected', 1);
			}
			else
			{
				$entries->whereEquals('enabled', (int)$filters['status']);
			}
		}

		if (isset($filters['type']) && $filters['type'] != '')
		{
			$entries->whereEquals('type', $filters['type']);
		}

		if (isset($filters['group']) && $filters['group'])
		{
			$entries->whereEquals('folder', $filters['group']);
		}

		// Get records
		if ($filters['sort'] == 'name' || (!empty($filters['search']) && stripos($filters['search'], 'id:') !== 0))
		{
			$rows = $entries->rows();

			$lang = App::get('language');

			$result = array();
			foreach ($rows as $i => $item)
			{
				if (!empty($filters['search']))
				{
					if (!preg_match("/" . $filters['search'] . "/i", $item->name))
					{
						unset($result[$i]);
					}
				}

				$item->translate();

				$result[$i] = $item;
			}

			\Hubzero\Utility\Arr::sortObjects($result, $filters['sort'], $filters['sort_Dir'] == 'desc' ? -1 : 1, true, $lang->getLocale());

			$total = count($result);

			if ($total < $start)
			{
				$start = 0;
			}

			$rows = array_slice($result, $start, $limit ? $limit : null);
		}
		else
		{
			$total = with(clone $entries)->total();

			$rows = $entries
				->order($filters['sort'], $filters['sort_Dir'])
				->limit($limit)
				->start($start)
				->rows();
		}

		$pagination = new \Hubzero\Pagination\Paginator($total, $start, $limit);

		// Get the form.
		\Hubzero\Form\Form::addFormPath(dirname(__DIR__) . '/models/forms');
		\Hubzero\Form\Form::addFieldPath(dirname(__DIR__) . '/models/fields');
		$form = new \Hubzero\Form\Form('manage');
		$form->loadFile(dirname(__DIR__) . '/models/forms/manage.xml', false, '//form');

		// Check the session for previously entered form data.
		$data = User::getState($this->_option . '.data', array());

		// Bind the form data if present.
		if (!empty($data))
		{
			$form->bind($data);
		}

		// Check if there are no matching items
		if (!count($rows))
		{
			Notify::warning(Lang::txt('COM_INSTALLER_CUSTOMEXTS_MSG_MANAGE_NO_EXTENSIONS'));
		}

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('pagination', $pagination)
			->set('filters', $filters)
			->set('form', $form)
			->display();
	}

	/**
	 * Enable/Disable an extension (if supported).
	 *
	 * @return  void
	 */
	public function publishTask()
	{
		// Check for request forgeries.
		Request::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Initialise variables.
		$ids    = Request::getArray('cid', array());
		$values = array('publish' => 1, 'unpublish' => 0);
		$task   = $this->getTask();
		$value  = \Hubzero\Utility\Arr::getValue($values, $task, 0, 'int');

		if (empty($ids))
		{
			App::abort(500, Lang::txt('COM_INSTALLER_CUSTOMEXTS_ERROR_NO_EXTENSIONS_SELECTED'));
		}
		else
		{
			$success = 0;

			foreach ($ids as $id)
			{
				$model = Extension::oneOrFail($id);

				if ($value)
				{
					if (!$model->publish())
					{
						Notify::error($model->getError());
						continue;
					}
				}
				else
				{
					if (!$model->unpublish())
					{
						Notify::error($model->getError());
						continue;
					}
				}

				$success++;
			}

			// Change the state of the records.
			if ($success)
			{
				if ($value == 1)
				{
					$ntext = 'COM_INSTALLER_CUSTOMEXTS_N_EXTENSIONS_PUBLISHED';
				}
				elseif ($value == 0)
				{
					$ntext = 'COM_INSTALLER_CUSTOMEXTS_N_EXTENSIONS_UNPUBLISHED';
				}

				Notify::success(Lang::txts($ntext, $success));
			}
		}

		$this->cancelTask();
	}

	/**
	 * Remove an extension (Uninstall).
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_CORE_DELETE_NOT_PERMITTED'));
		}

		$ids  = Request::getArray('cid', array());
		\Hubzero\Utility\Arr::toInteger($ids, array());

		$success = 0;

		foreach ($ids as $id)
		{
			$model = Extension::oneOrFail($id);

			if (!$model->remove())
			{
				Notify::error($model->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			Notify::success(Lang::txt('COM_INSTALLER_CUSTOMEXTS_UNINSTALL_SUCCESS', $success));
		}

		$this->cancelTask();
	}

	/**
	 * Refreshes the cached metadata about an extension.
	 *
	 * Useful for debugging and testing purposes when the XML file might change.
	 *
	 * @return  void
	 */
	public function refreshTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$ids = Request::getArray('cid', array());
		\Hubzero\Utility\Arr::toInteger($ids, array());

		foreach ($ids as $id)
		{
			$model = Extension::oneOrFail($id);

			if (!$model->refreshManifestCache())
			{
				Notify::error($model->getError());
				continue;
			}
		}

		$this->cancelTask();
	}

//	/**
//	 * Creates the content for the tooltip which shows compatibility information
//	 *
//	 * @param   string  $system_data  System_data information
//	 * @return  string  Content for tooltip
//	 */
//	protected function createCompatibilityInfo($system_data)
//	{
//		$system_data = json_decode($system_data);
//
//		if (empty($system_data->compatibility))
//		{
//			return '';
//		}
//
//		$compatibility = $system_data->compatibility;
//
//		$info = Lang::txt('COM_INSTALLER_COMPATIBILITY_TOOLTIP_INSTALLED',
//					$compatibility->installed->version,
//					implode(', ', $compatibility->installed->value)
//				)
//				. '<br />'
//				. Lang::txt('COM_INSTALLER_COMPATIBILITY_TOOLTIP_AVAILABLE',
//					$compatibility->available->version,
//					implode(', ', $compatibility->available->value)
//				);
//
//		return $info;
//	}
}
