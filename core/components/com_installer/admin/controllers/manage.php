<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
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

		$filters = Request::getArray('filters');
		if (empty($filters))
		{
			$data = User::getState($this->_option . '.data');
			$filters = $data['filters'];
		}
		else
		{
			User::setState($this->_option . '.data', array('filters' => $filters));
		}

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

		$filters['sort'] = Request::getState(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'name'
		);
		$filters['sort_Dir'] = Request::getState(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'ASC'
		);

		$entries = Extension::all();

		$e = $entries->getTableName();

		$entries
			->select('(2*protected+(1-protected)*enabled)', 'status')
			->select($e . '.*')
			->whereEquals('state', 0);

		if (isset($filters['search']) && $filters['search'])
		{
			$filters['search'] = str_replace('/', ' ', $filters['search']);
			if (stripos($filters['search'], 'id:') !== 0)
			{
				$entries->whereEquals('extension_id', str_replace('id:', '', $filters['search']));
			}
			else
			{
				$entries->whereLike('name', strtolower((string)$filters['search']));
			}
		}

		if (isset($filters['type']) && $filters['type'])
		{
			$entries->whereEquals('type', $filters['type']);
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

		if (isset($filters['group']) && $filters['group'])
		{
			$entries->whereEquals('folder', (int)$filters['group']);
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
			App::abort(500, Lang::txt('COM_INSTALLER_ERROR_NO_EXTENSIONS_SELECTED'));
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
					$ntext = 'COM_INSTALLER_N_EXTENSIONS_PUBLISHED';
				}
				elseif ($value == 0)
				{
					$ntext = 'COM_INSTALLER_N_EXTENSIONS_UNPUBLISHED';
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
			Notify::success(Lang::txt('COM_INSTALLER_UNINSTALL_SUCCESS', $success));
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

	/**
	 * Creates the content for the tooltip which shows compatibility information
	 *
	 * @param   string  $system_data  System_data information
	 * @return  string  Content for tooltip
	 */
	protected function createCompatibilityInfo($system_data)
	{
		$system_data = json_decode($system_data);

		if (empty($system_data->compatibility))
		{
			return '';
		}

		$compatibility = $system_data->compatibility;

		$info = Lang::txt('COM_INSTALLER_COMPATIBILITY_TOOLTIP_INSTALLED',
					$compatibility->installed->version,
					implode(', ', $compatibility->installed->value)
				)
				. '<br />'
				. Lang::txt('COM_INSTALLER_COMPATIBILITY_TOOLTIP_AVAILABLE',
					$compatibility->available->version,
					implode(', ', $compatibility->available->value)
				);

		return $info;
	}
}
