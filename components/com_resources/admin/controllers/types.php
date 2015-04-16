<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Resources\Admin\Controllers;

use Components\Resources\Tables\Type;
use Hubzero\Component\AdminController;
use stdClass;
use Request;
use Config;
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
		$this->view->filters = array(
			'limit' => Request::getState(
				$this->_option . '.types.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.types.limitstart',
				'limitstart',
				0,
				'int'
			),
			'sort' => Request::getState(
				$this->_option . '.types.sort',
				'filter_order',
				'category'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.types.sortdir',
				'filter_order_Dir',
				'DESC'
			),
			'category' => Request::getState(
				$this->_option . '.types.category',
				'category',
				27,
				'int'
			)
		);

		// Instantiate an object
		$rt = new Type($this->database);

		// Get a record count
		$this->view->total = $rt->getAllCount($this->view->filters);

		// Get records
		$this->view->rows = $rt->getAllTypes($this->view->filters);

		// Get the category names
		$this->view->cats = $rt->getTypes('0');

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit a type
	 *
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming (expecting an array)
			$id = Request::getVar('id', array(0));
			if (is_array($id))
			{
				$id = $id[0];
			}

			// Load the object
			$row = new Type($this->database);
			$row->load($id);
		}

		$this->view->row = $row;

		// Get the categories
		$this->view->categories = $this->view->row->getTypes(0);
		$this->view->config = $this->config;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
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
		Request::checkToken() or jexit('Invalid Token');

		// Initiate extended database class
		$row = new Type($this->database);
		if (!$row->bind($_POST))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Get the custom fields
		$fields = Request::getVar('fields', array(), 'post');
		if (is_array($fields))
		{
			$elements = new stdClass();
			$elements->fields = array();

			foreach ($fields as $val)
			{
				if ($val['title'])
				{
					$element = new stdClass();
					$element->default  = (isset($val['default'])) ? $val['default'] : '';
					$element->name     = (isset($val['name']) && trim($val['name']) != '') ? $val['name'] : $this->_normalize(trim($val['title']));
					$element->label    = $val['title'];
					$element->type     = (isset($val['type']) && trim($val['type']) != '')     ? $val['type']     : 'text';
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

			include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php');
			$re = new \Components\Resources\Models\Elements($elements);
			$row->customFields = $re->toString();
		}

		// Get parameters
		$p = new \JRegistry('');
		$p->loadArray(Request::getVar('params', array(), 'post'));

		$row->params = $p->toString();

		// Make sure a category is set
		if (!$row->category)
		{
			$this->addComponentMessage(Lang::txt('COM_RESOURCES_ERROR_SELECT_CATEGORY'), 'error');
			$this->editTask($row);
			return;
		}

		// Check content
		if (!$row->check())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if ($this->_task == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_RESOURCES_ITEM_SAVED')
		);
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
		Request::checkToken() or jexit('Invalid Token');

		// Incoming (expecting an array)
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Ensure we have an ID to work with
		if (empty($ids))
		{
			// Redirect with error message
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_RESOURCES_NO_ITEM_SELECTED'),
				'error'
			);
			return;
		}

		$rt = new Type($this->database);

		foreach ($ids as $id)
		{
			// Check if the type is being used
			$total = $rt->checkUsage($id);

			if ($total > 0)
			{
				// Redirect with error message
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					Lang::txt('COM_RESOURCES_TYPE_BEING_USED', $id),
					'error'
				);
				return;
			}

			// Delete the type
			$rt->delete($id);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_RESOURCES_ITEMS_REMOVED', count($ids))
		);
	}

	/**
	 * Retrieve an element's options (typically called via AJAX)
	 *
	 * @return  void
	 */
	public function elementTask()
	{
		$ctrl = Request::getVar('ctrl', 'fields');

		$option = new stdClass;
		$option->label = '';
		$option->value = '';

		$field = new stdClass;
		$field->label       = Request::getVar('name', 0);
		$field->element     = '';
		$field->description = '';
		$field->text        = $field->label;
		$field->name        = $field->label;
		$field->default     = '';
		$field->type        = Request::getVar('type', '');
		$field->options     = array(
			$option,
			$option
		);

		include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php');
		$elements = new \Components\Resources\Models\Elements();
		echo $elements->getElementOptions($field->name, $field, $ctrl);
	}
}
