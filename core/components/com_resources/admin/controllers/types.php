<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
			\Notify::error($error);
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
		Request::checkToken();

		// Initiate extended database class
		$row = new Type($this->database);
		if (!$row->bind($_POST))
		{
			$this->setError($row->getError());
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
		$p = new \Hubzero\Config\Registry(Request::getVar('params', array(), 'post'));

		$row->params = $p->toString();

		// Make sure a category is set
		if (!$row->category)
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_SELECT_CATEGORY'));
			$this->editTask($row);
			return;
		}

		// Check content
		if (!$row->check())
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
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
		Request::checkToken();

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
