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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Publications\Tables;
use stdClass;
use Document;
use Request;
use Config;
use Route;
use Lang;
use App;

/**
 * Manage publication categories (former resource types)
 */
class Categories extends AdminController
{
	/**
	 * List types
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array(
			'limit' => Request::getState(
				$this->_option . '.categories.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.categories.limitstart',
				'limitstart',
				0,
				'int'
			),
			'search' => Request::getState(
				$this->_option . '.categories.search',
				'search',
				''
			),
			'sort' => Request::getState(
				$this->_option . '.categories.sort',
				'filter_order',
				'id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.categories.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$this->view->filters['state'] = 'all';

		// Instantiate an object
		$rt = new \Components\Publications\Tables\Category($this->database);

		// Get a record count
		$this->view->total = $rt->getCount($this->view->filters);

		// Get records
		$this->view->rows = $rt->getCategories($this->view->filters);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Add a new type
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a type
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming (expecting an array)
			$id = Request::getVar('id', array(0));
			$id = is_array($id) ? $id[0] : $id;

			// Load the object
			$row = new \Components\Publications\Tables\Category($this->database);
			$row->load($id);
		}

		$this->view->row = $row;

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		$this->view->config = $this->config;

		// Get all contributable master types
		$objMT = new \Components\Publications\Tables\MasterType($this->database);
		$this->view->types = $objMT->getTypes('alias', 1);

		// Push some styles to the template
		Document::addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'publications.css');

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a publication and fall through to edit view
	 *
	 * @return  void
	 */
	public function applyTask()
	{
		$this->saveTask(true);
	}

	/**
	 * Save a type
	 *
	 * @param   boolean  $redirect
	 * @return  void
	 */
	public function saveTask($redirect = false)
	{
		// Check for request forgeries
		Request::checkToken();

		$prop = Request::getVar('prop', array(), 'post');

		$url = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $prop['id'];

		// Initiate extended database class
		$row = new \Components\Publications\Tables\Category($this->database);
		if (!$row->bind($prop))
		{
			App::redirect($url, $row->getError(), 'error');
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

			include_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'models' . DS . 'elements.php');
			$re = new \Components\Publications\Models\Elements($elements);
			$row->customFields = $re->toString();
		}

		// Get parameters
		$params = Request::getVar('params', '', 'post');
		if (is_array($params))
		{
			$txt = array();
			foreach ($params as $k => $v)
			{
				$txt[] = "$k=$v";
			}
			$row->params = implode("\n", $txt);
		}

		// Check content
		if (!$row->check())
		{
			App::redirect($url, $row->getError(), 'error');
			return;
		}

		// Store new content
		if (!$row->store())
		{
			App::redirect($url, $row->getError(), 'error');
			return;
		}

		// Redirect to edit view?
		if ($redirect)
		{
			App::redirect(
				$url,
				Lang::txt('COM_PUBLICATIONS_CATEGORY_SAVED')
			);
		}
		else
		{
			App::redirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				Lang::txt('COM_PUBLICATIONS_CATEGORY_SAVED')
			);
		}
	}

	/**
	 * Change status
	 * Redirects to list
	 *
	 * @param   integer  $dir
	 * @return  void
	 */
	public function changestatusTask($dir = 0)
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array(0), '', 'array');

		// Initialize
		$row = new \Components\Publications\Tables\Category($this->database);

		foreach ($ids as $id)
		{
			if (intval($id))
			{
				// Load row
				$row->load($id);
				$row->state = $row->state == 1 ? 0 : 1;

				// Save
				if (!$row->store())
				{
					App::redirect(
						Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
						$row->getError(),
						'error'
					);
					return;
				}
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_PUBLICATIONS_CATEGORY_ITEM_STATUS_CHNAGED')
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

		include_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'models' . DS . 'elements.php');
		$elements = new \Components\Publications\Models\Elements();
		echo $elements->getElementOptions($field->name, $field, $ctrl);
	}
}
