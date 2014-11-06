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
 * Courses Plugin class for manager dashboard
 */
class plgCoursesDashboard extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return data on a course view (this will be some form of HTML)
	 *
	 * @param   object   $course    Current course
	 * @param   object   $offering  Name of the component
	 * @param   boolean  $describe  Return plugin description only?
	 * @return  object
	 */
	public function onCourse($course, $offering, $describe=false)
	{
		if (!$offering->access('manage', 'section'))
		{
			return;
		}

		$response = with(new \Hubzero\Base\Object)
			->set('name', $this->_name)
			->set('title', JText::_('PLG_COURSES_' . strtoupper($this->_name)))
			->set('default_access', $this->params->get('plugin_access', 'managers'))
			->set('display_menu_tab', true)
			->set('icon', 'f083');

		if ($describe)
		{
			return $response;
		}

		$nonadmin = JFactory::getApplication()->getUserState('com_courses.offering' . $offering->get('id') . '.nonadmin', 0);
		if (!($active = JRequest::getVar('active')) && !$nonadmin)
		{
			JRequest::setVar('active', ($active = $this->_name));
		}

		if ($response->get('name') == $active)
		{
			// Set the page title
			$document = JFactory::getDocument();
			$document->setTitle($document->getTitle() . ': ' . JText::_('PLG_COURSES_' . strtoupper($this->_name)));

			$pathway = JFactory::getApplication()->getPathway();
			$pathway->addItem(
				JText::_('PLG_COURSES_' . strtoupper($this->_name)),
				$offering->link() . '&active=' . $this->_name
			);

			$view = with($this->view('default', 'overview'))
				->set('option', JRequest::getVar('option', 'com_courses'))
				->set('course', $course)
				->set('offering', $offering)
				->set('params', $this->params);

			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}

			$response->set('html', $view->loadTemplate());
		}

		// Return the output
		return $response;
	}
}

