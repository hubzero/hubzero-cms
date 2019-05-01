<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Admin\Controllers;

use Components\Resources\Models\Type;
use Components\Resources\Models\Entry;
use Hubzero\Component\AdminController;
use stdClass;
use Request;
use Notify;
use Route;
use Lang;
use App;

/**
 * Manage resource types
 */
class Types extends AdminController
{
	/**
	 * Determines task being called and attempts to execute it
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
	 * List resource types
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$filters = array(
			'sort' => Request::getState(
				$this->_option . '.types.sort',
				'filter_order',
				'type'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.types.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			'category' => Request::getState(
				$this->_option . '.types.category',
				'category',
				27,
				'int'
			)
		);

		// Get the categories
		$categories = Type::all()
			->whereEquals('category', 0)
			->order('type', 'asc')
			->rows();

		// Get records
		$rows = Type::all()
			->whereEquals('category', $filters['category'])
			->ordered('filter_order', 'filter_order_Dir')
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->set('cats', $categories)
			->display();
	}

	/**
	 * Edit a type
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming (expecting an array)
			$id = Request::getArray('id', array(0));
			if (is_array($id))
			{
				$id = $id[0];
			}

			// Load the object
			$row = Type::oneOrNew($id);
		}

		// Get the categories
		$categories = Type::all()
			->whereEquals('category', 0)
			->order('type', 'asc')
			->rows();

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			Notify::error($error);
		}

		// Output the HTML
		$this->view
			->set('row', $row)
			->set('categories', $categories)
			->set('config', $this->config)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a type
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

		$type = Request::getArray('type', array(), 'post');

		// Initiate extended database class
		$row = Type::oneOrNew($type['id'])->set($type);

		// Update contributable in the row
		isset($type['contributable']) ? $row->set('contributable', 1) : $row->set('contributable', 0);
		isset($type['collection']) ? $row->set('collection', 1) : $row->set('collection', 0);

		// Get the custom fields
		$fields = Request::getArray('fields', array(), 'post');
		if (is_array($fields))
		{
			$elements = new stdClass();
			$elements->fields = array();

			foreach ($fields as $val)
			{
				if (!isset($val['title']))
				{
					continue;
				}
				if ($val['title'])
				{
					$element = new stdClass();
					$element->default  = (isset($val['default'])) ? $val['default'] : '';
					$element->name     = (isset($val['name']) && trim($val['name']) != '') ? $val['name'] : $this->_normalize(trim($val['title']));
					$element->label    = $val['title'];
					$element->type     = (isset($val['type']) && trim($val['type']) != '') ? $val['type'] : 'text';
					$element->required = (isset($val['required'])) ? $val['required'] : '0';
					foreach ($val as $key => $v)
					{
						if (!in_array($key, array('default', 'type', 'title', 'name', 'required', 'options')))
						{
							$element->$key = $v;
						}
					}
					if (isset($val['options']))
					{
						$element->options = array();
						foreach ($val['options'] as $option)
						{
							if (trim($option['label']))
							{
								$opt = new stdClass();
								$opt->label = $option['label'];
								$opt->value = $option['label'];
								$element->options[] = $opt;
							}
						}
					}
					$elements->fields[] = $element;
				}
			}

			include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'elements.php';
			$re = new \Components\Resources\Models\Elements($elements);

			$row->set('customFields', $re->toString());
		}

		// Get parameters
		$p = new \Hubzero\Config\Registry(Request::getArray('params', array(), 'post'));

		$row->set('params', $p->toString());

		// Store new content
		if (!$row->save())
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_RESOURCES_ITEM_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Strip any non-alphanumeric characters and make lowercase
	 *
	 * @param   string   $txt     String to normalize
	 * @param   boolean  $dashes  Allow dashes and underscores
	 * @return  string
	 */
	private function _normalize($txt, $dashes=false)
	{
		$allowed = "a-zA-Z0-9";
		if ($dashes)
		{
			$allowed = "a-zA-Z0-9\-_";
		}
		return strtolower(preg_replace("/[^$allowed]/", '', $txt));
	}

	/**
	 * Remove one or more types
	 *
	 * @return  void  Redirects back to main listing
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming (expecting an array)
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Ensure we have an ID to work with
		if (empty($ids))
		{
			// Redirect with error message
			Notify::warning(Lang::txt('COM_RESOURCES_NO_ITEM_SELECTED'));
			return $this->cancelTask();
		}

		$removed = 0;

		foreach ($ids as $id)
		{
			// Check if the type is being used
			$type = Type::oneOrFail(intval($id));

			// Uuuuugggghhhhhhhh
			if ($type->isForTools())
			{
				Notify::warning(Lang::txt('Core resource (ID #%s) type cannot be removed', $id));
				continue;
			}

			$usage = Entry::all()
				->whereEquals('type', $id)
				->total();

			if ($usage)
			{
				Notify::error(Lang::txt('COM_RESOURCES_TYPE_BEING_USED', $id));
				continue;
			}

			// Delete the type
			if (!$type->destroy())
			{
				Notify::error($type->getError());
				continue;
			}

			$removed++;
		}

		if ($removed)
		{
			Notify::success(Lang::txt('COM_RESOURCES_ITEMS_REMOVED', $removed));
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

		$state = $this->getTask() == 'publish' ? Type::STATE_PUBLISHED : Type::STATE_UNPUBLISHED;

		// Incoming
		$ids = Request::getArray('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for a resource
		if (count($ids) < 1)
		{
			Notify::warning(Lang::txt('COM_RESOURCES_SELECT_ENTRY_TO', $this->_task));
			return $this->cancelTask();
		}

		// Loop through all the IDs
		$success = 0;
		foreach ($ids as $id)
		{
			// Load the article
			$row = Type::oneOrNew(intval($id));
			$row->set('state', $state);

			// Store new content
			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			switch ($this->getTask())
			{
				case 'publish':
					$message = Lang::txt('COM_RESOURCES_ITEMS_PUBLISHED', $success);
				break;
				case 'unpublish':
					$message = Lang::txt('COM_RESOURCES_ITEMS_UNPUBLISHED', $success);
				break;
			}

			Notify::success($message);
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Retrieve an element's options (typically called via AJAX)
	 *
	 * @return  void
	 */
	public function elementTask()
	{
		$ctrl = Request::getString('ctrl', 'fields');

		$option = new stdClass;
		$option->label = '';
		$option->value = '';

		$field = new stdClass;
		$field->label       = Request::getString('name', 0);
		$field->element     = '';
		$field->description = '';
		$field->text        = $field->label;
		$field->name        = $field->label;
		$field->default     = '';
		$field->type        = Request::getString('type', '');
		$field->options     = array(
			$option,
			$option
		);

		include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'elements.php';

		$elements = new \Components\Resources\Models\Elements();
		echo $elements->getElementOptions($field->name, $field, $ctrl);
	}
}
