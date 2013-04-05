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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Plugin');

/**
 * Courses Plugin class for pages
 */
class plgCoursesPages extends Hubzero_Plugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @return     array
	 */
	public function &onCourseAreas()
	{
		$area = array(
			'name' => $this->_name,
			'title' => JText::_('PLG_COURSES_' . strtoupper($this->_name)),
			'default_access' => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => true
		);
		return $area;
	}

	/**
	 * Return data on a course view (this will be some form of HTML)
	 * 
	 * @param      object  $course      Current course
	 * @param      string  $option     Name of the component
	 * @param      string  $authorized User's authorization level
	 * @param      integer $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      string  $action     Action to perform
	 * @param      array   $access     What can be accessed
	 * @param      array   $areas      Active area(s)
	 * @return     array
	 */
	public function onCourse($config, $course, $offering, $action='', $areas=null)
	{
		$return = 'html';
		$active = $this->_name;
		$active_real = $this->_name;

		// The output array we're returning
		$arr = array(
			'html'=>'',
			'name' => $active
		);

		//get this area details
		$this_area = $this->onCourseAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!in_array($this_area['name'], $areas)) 
			{
				//return $arr;
				$return = 'metadata';
			}
		}

		$total = $offering->pages(array('count' => true));
		if (!count($total))
		{
			return;
		}

		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($return == 'html') 
		{
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('courses', $this->_name);

			$action = strtolower(JRequest::getWord('group', ''));
			$active = strtolower(JRequest::getWord('unit', ''));
			if ($active == 'add')
			{
				$action = 'add';
			}
			if ($act = strtolower(JRequest::getWord('action', '', 'post')))
			{
				$action = $act;
			}

			ximport('Hubzero_Plugin_View');
			$this->view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'courses',
					'element' => $this->_name,
					'name'    => 'pages'
				)
			);
			$this->view->option     = JRequest::getCmd('option', 'com_courses');
			$this->view->controller = JRequest::getWord('controller', 'course');
			$this->view->course     = $course;
			$this->view->offering   = $offering;
			$this->view->config     = $config;
			$this->view->juser      = JFactory::getUser();

			switch ($action)
			{
				case 'add':
				case 'edit':   $this->_edit();   break;
				case 'save':   $this->_save();   break;
				case 'delete': $this->_delete(); break;

				default: $this->_list(); break;
			}

			$arr['html'] = $this->view->loadTemplate();
		}

		$arr['metadata']['count'] = $total;

		// Return the output
		return $arr;
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      object $url  URL to redirect to
	 * @return     string
	 */
	public function _list()
	{
		$this->view->setLayout('default');

		$active = JRequest::getWord('unit', '');

		$pages = $this->view->offering->pages();

		$page = $this->view->offering->page($active);
		if (!$active || !$page->exists())
		{
			$page = $pages[0];
		}
		$this->view->page  = $page;
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      object $url  URL to redirect to
	 * @return     string
	 */
	public function _edit($model=null)
	{
		if ($this->view->juser->get('guest'))
		{
			$return = JRoute::_('index.php?option=' . $this->view->option . '&gid=' . $this->view->course->get('alias') . '&offering=' . $this->view->offering->get('alias') . '&active=' . $this->_name);
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($return))
			);
			return;
		}
		if (!$this->view->offering->access('manage'))
		{
			return $this->_list();
		}

		$this->view->setLayout('edit');

		if (is_object($model))
		{
			$this->view->model = $model;
		}
		else
		{
			$page = JRequest::getWord('unit', '');

			$this->view->model = $this->view->offering->page($page); //new CoursesModelPage($page);
		}
		$this->view->notifications = $this->getPluginMessage();
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      object $url  URL to redirect to
	 * @return     string
	 */
	public function _save()
	{
		if ($this->view->juser->get('guest'))
		{
			$return = JRoute::_('index.php?option=' . $this->view->option . '&gid=' . $this->view->course->get('alias') . '&offering=' . $this->view->offering->get('alias') . '&active=' . $this->_name);
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($return))
			);
			return;
		}
		if (!$this->view->offering->access('manage'))
		{
			return $this->_list();
		}

		$page = JRequest::getVar('fields', array(), 'post');

		$model = new CoursesModelPage($page['id']);

		if (!$model->bind($page))
		{
			//$this->setError($model->getError());
			$this->addPluginMessage($model->getError(), 'error');
			return $this->_edit($model);
		}

		if (!$model->store(true))
		{
			//$this->setError($model->getError());
			$this->addPluginMessage($model->getError(), 'error');
			return $this->_edit($model);
		}

		//return $this->_list();
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->view->option . '&gid=' . $this->view->course->get('alias') . '&offering=' . $this->view->offering->get('alias') . '&active=' . $this->_name . '&unit=' . $model->get('url'))
		);
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      object $url  URL to redirect to
	 * @return     string
	 */
	public function _delete()
	{
		if ($this->view->juser->get('guest'))
		{
			$return = JRoute::_('index.php?option=' . $this->view->option . '&gid=' . $this->view->course->get('alias') . '&offering=' . $this->view->offering->get('alias') . '&active=' . $this->_name);
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($return))
			);
			return;
		}
		if (!$this->view->offering->access('manage'))
		{
			return $this->_list();
		}

		$model = $this->view->offering->page(JRequest::getVar('unit', ''));

		if ($model->exists())
		{
			$model->set('active', 0);

			if (!$model->store(true))
			{
				$this->addPluginMessage($model->getError(), 'error');
			}
		}

		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->view->option . '&gid=' . $this->view->course->get('alias') . '&offering=' . $this->view->offering->get('alias') . '&active=pages')
		);
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      string $url  URL to redirect to
	 * @param      string $msg  Message to send
	 * @param      string $type Message type (message, error, warning, info)
	 * @return     void
	 */
	public function setRedirect($url, $msg=null, $type='message')
	{
		if ($msg !== null)
		{
			$this->addPluginMessage($msg, $type);
		}
		$this->redirect($url);
	}
}
