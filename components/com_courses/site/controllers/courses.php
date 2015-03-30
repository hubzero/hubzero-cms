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

/**
 * Courses controller class
 */
class CoursesControllerCourses extends \Hubzero\Component\SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		if ($section_id = JRequest::getInt('section', 0, 'get'))
		{
			$section = CoursesModelSection::getInstance($section_id);
			if ($section->exists())
			{
				$offering = CoursesModelOffering::getInstance($section->get('offering_id'));
				$offering->section($section->get('alias'));

				$this->setRedirect(
					JRoute::_($offering->link('overview'))
				);
			}
		}

		$this->registerTask('__default', 'intro');

		$this->_authorize('course');

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @param      array $course_pages List of roup pages
	 * @return     void
	 */
	public function _buildPathway($course_pages = array())
	{
		$pathway = JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task && $this->_task != 'intro')
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option . '_' . $this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return     void
	 */
	public function _buildTitle()
	{
		//set title used in view
		$this->_title = JText::_(strtoupper($this->_option));

		if ($this->_task && $this->_task != 'intro')
		{
			$this->_title .= ': ' . JText::_(strtoupper($this->_option . '_' . $this->_task));
		}

		//set title of browser window
		$document = JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Display component main page
	 *
	 * @return     void
	 */
	public function introTask()
	{
		//build the title
		$this->_buildTitle();

		//build pathway
		$this->_buildPathway();

		// Push some needed scripts to the template
		$model = CoursesModelCourses::getInstance();

		$this->view->popularcourses = $model->courses(array(
			'limit' => 12,
			'sort'  => 'students',
			'state' => 1
		), true);

		// Output HTML
		$this->view->config   = $this->config;
		$this->view->database = $this->database;
		$this->view->user     = $this->juser;
		$this->view->title    = $this->_title;

		$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}

	/**
	 * Display a list of courses on the site and options for filtering/browsing them
	 *
	 * @return     void
	 */
	public function browseTask()
	{
		// Filters
		$this->view->filters = array(
			'state'  => 1,
			'search' => JRequest::getVar('search', ''),
			'sortby' => strtolower(JRequest::getWord('sortby', 'title')),
			'group'  => JRequest::getVar('group', '')
		);
		if ($this->view->filters['group'])
		{
			$group = \Hubzero\User\Group::getInstance($this->view->filters['group']);
			if ($group)
			{
				$this->view->filters['group_id'] = $group->get('gidNumber');
			}
		}
		if (!in_array($this->view->filters['sortby'], array('alias', 'title', 'popularity')))
		{
			$this->view->filters['sortby'] = 'title';
		}
		switch ($this->view->filters['sortby'])
		{
			case 'popularity':
				$this->view->filters['sort']  = 'students';
				$this->view->filters['sort_Dir'] = 'DESC';
			break;
			case 'title':
			case 'alias':
			default:
				$this->view->filters['sort']  = $this->view->filters['sortby'];
				$this->view->filters['sort_Dir'] = 'ASC';
			break;
		}
		// Filters for returning results
		$this->view->filters['limit']  = JRequest::getInt('limit', JFactory::getConfig()->getValue('config.list_limit'));
		$this->view->filters['limit']  = ($this->view->filters['limit']) ? $this->view->filters['limit'] : 'all';
		$this->view->filters['start']  = JRequest::getInt('limitstart', 0);
		$this->view->filters['index']  = strtolower(JRequest::getWord('index', ''));
		if ($this->view->filters['index'] && !in_array($this->view->filters['index'], array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z')))
		{
			$this->view->filters['index'] = '';
		}
		$this->view->filters['tag'] = JRequest::getVar('tag', '');

		$model = CoursesModelCourses::getInstance();

		// Get a record count
		$this->view->filters['count'] = true;
		$this->view->total   = $model->courses($this->view->filters);

		// Get records
		$this->view->filters['count'] = false;
		$this->view->courses = $model->courses($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		//build the title
		$this->_buildTitle();

		//build pathway
		$this->_buildPathway();

		// Output HTML
		$this->view->model  = $model;
		$this->view->title  = $this->_title;
		$this->view->config = $this->config;
		$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}

	/**
	 * Public url for badge info
	 *
	 * @return     void
	 */
	public function badgeTask()
	{
		if ($badge_id = JRequest::getInt('badge_id', false))
		{
			$badge = new CoursesModelSectionBadge($badge_id);

			if (!$badge->get('id'))
			{
				JError::raiseError(500, JText::_('COM_COURSES_BADGE_NOT_FOUND'));
				return;
			}
			else
			{
				$this->view->badge  = $badge;
				$this->view->config = $this->config;
				$this->view->action = JRequest::getWord('action', 'default');
				$this->view->token  = JRequest::getVar('validation_token', false);
			}
		}
		else
		{
			JError::raiseError(500, JText::_('COM_COURSES_BADGE_NOT_FOUND'));
			return;
		}

		$this->view->display();
	}

	/**
	 * Set access permissions for a user
	 *
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, false);
		if (!$this->juser->get('guest'))
		{
			$asset  = $this->_option;
			if ($assetId)
			{
				$asset .= ($assetType != 'component') ? '.' . $assetType : '';
				$asset .= ($assetId) ? '.' . $assetId : '';
			}

			$at = '';
			if ($assetType != 'component')
			{
				$at .= '.' . $assetType;
			}

			// Admin
			$this->config->set('access-admin-' . $assetType, $this->juser->authorise('core.admin', $asset));
			$this->config->set('access-manage-' . $assetType, $this->juser->authorise('core.manage', $asset));
			// Permissions
			//$this->config->set('access-create-' . $assetType, $this->juser->authorise('core.create' . $at, $asset));
			$this->config->set('access-delete-' . $assetType, $this->juser->authorise('core.delete' . $at, $asset));
			$this->config->set('access-edit-' . $assetType, $this->juser->authorise('core.edit' . $at, $asset));
			$this->config->set('access-edit-state-' . $assetType, $this->juser->authorise('core.edit.state' . $at, $asset));
			$this->config->set('access-edit-own-' . $assetType, $this->juser->authorise('core.edit.own' . $at, $asset));
		}
	}
}

