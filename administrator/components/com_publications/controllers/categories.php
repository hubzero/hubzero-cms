<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Manage publication categories (former resource types)
 */
class PublicationsControllerCategories extends \Hubzero\Component\AdminController
{
	/**
	 * List types
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$app = JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.categories.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.categories.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['search']     = trim($app->getUserStateFromRequest(
			$this->_option . '.categories.search',
			'search',
			''
		));
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.categories.sort',
			'filter_order',
			'id'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.categories.sortdir',
			'filter_order_Dir',
			'ASC'
		));

		$this->view->filters['state'] = 'all';

		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS
		. 'assets' . DS . 'css' . DS . 'publications.css');

		// Instantiate an object
		$rt = new PublicationCategory($this->database);

		// Get a record count
		$this->view->total = $rt->getCount($this->view->filters);

		// Get records
		$this->view->rows = $rt->getCategories($this->view->filters);

		// initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

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
	 * @return     void
	 */
	public function addTask()
	{
		$this->view->setLayout('edit');
		$this->editTask();
	}

	/**
	 * Edit a type
	 *
	 * @return     void
	 */
	public function editTask($row=null)
	{
		if ($row)
		{
			$this->view->row = $row;
		}
		else
		{
			// Incoming (expecting an array)
			$id = JRequest::getVar('id', array(0));
			if (is_array($id))
			{
				$id = $id[0];
			}
			else
			{
				$id = 0;
			}

			// Load the object
			$this->view->row = new PublicationCategory($this->database);
			$this->view->row->load($id);
		}

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		$this->view->config = $this->config;

		// Get all contributable master types
		$objMT = new PublicationMasterType($this->database);
		$this->view->types = $objMT->getTypes('alias', 1);

		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS
			. 'assets' . DS . 'css' . DS . 'publications.css');

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save a publication and fall through to edit view
	 *
	 * @return void
	 */
	public function applyTask()
	{
		$this->saveTask(true);
	}

	/**
	 * Save a type
	 *
	 * @return     void
	 */
	public function saveTask($redirect = false)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$prop = JRequest::getVar('prop', array(), 'post');

		$url = 'index.php?option=' . $this->_option . '&controller='
			. $this->_controller . '&task=edit&id[]=' . $prop['id'];

		// Initiate extended database class
		$row = new PublicationCategory($this->database);
		if (!$row->bind($prop))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->setRedirect($url);
			return;
		}

		// Get the custom fields
		$fields = JRequest::getVar('fields', array(), 'post');
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

			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications'
				. DS . 'models' . DS . 'elements.php');
			$re = new PublicationsElements($elements);
			$row->customFields = $re->toString();
		}

		// Get parameters
		$params = JRequest::getVar('params', '', 'post');
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
			$this->setRedirect($url, $row->getError(), 'error');
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->setRedirect($url, $row->getError(), 'error');
			return;
		}

		// Redirect to edit view?
		if ($redirect)
		{
			$this->setRedirect(
				$url,
				JText::_('COM_PUBLICATIONS_CATEGORY_SAVED')
			);
		}
		else
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_PUBLICATIONS_CATEGORY_SAVED')
			);
		}
		return;
	}

	/**
	 * Change status
	 * Redirects to list
	 *
	 * @return     void
	 */
	public function changestatusTask($dir = 0)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array(0), '', 'array');

		// Initialize
		$row = new PublicationCategory($this->database);

		foreach ($ids as $id)
		{
			if (intval($id))
			{
				// Load row
				$row->load( $id );
				$row->state = $row->state == 1 ? 0 : 1;

				// Save
				if (!$row->store())
				{
					$this->addComponentMessage($row->getError(), 'error');
					$this->setRedirect(
						'index.php?option=' . $this->_option . '&controller=' . $this->_controller
					);
					return;
				}
			}
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_PUBLICATIONS_CATEGORY_ITEM_STATUS_CHNAGED')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect('index.php?option=' . $this->_option . '&controller=' . $this->_controller);
	}

	/**
	 * Strip any non-alphanumeric characters and make lowercase
	 *
	 * @param      string  $txt    String to normalize
	 * @param      boolean $dashes Allow dashes and underscores
	 * @return     string
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
	 * @return	void
	 */
	public function elementTask()
	{
		$ctrl = JRequest::getVar('ctrl', 'fields');

		$option = new stdClass;
		$option->label = '';
		$option->value = '';

		$field = new stdClass;
		$field->label       = JRequest::getVar('name', 0);
		$field->element     = '';
		$field->description = '';
		$field->text        = $field->label;
		$field->name        = $field->label;
		$field->default     = '';
		$field->type        = JRequest::getVar('type', '');
		$field->options     = array(
			$option,
			$option
		);

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'models' . DS . 'elements.php');
		$elements = new PublicationsElements();
		echo $elements->getElementOptions($field->name, $field, $ctrl);
	}
}
