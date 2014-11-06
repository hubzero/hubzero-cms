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
 * @author    David Benham <dbenham@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Courses plugin class for member options
 */
class plgCoursesMemberOptions extends \Hubzero\Plugin\Plugin
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
		$response = with(new \Hubzero\Base\Object)
			->set('name', $this->_name)
			->set('title', JText::_('PLG_COURSES_' . strtoupper($this->_name)))
			->set('default_access', 'registered')
			->set('display_menu_tab', false);

		if ($describe)
		{
			return $response;
		}

		if (!($active = JRequest::getVar('active')))
		{
			JRequest::setVar('active', ($active = $this->_name));
		}

		if ($response->get('name') == $active)
		{
			// Things we need from the form
			$recvEmailOptionID    = JRequest::getInt('memberoptionid', 0);
			$recvEmailOptionValue = JRequest::getInt('recvpostemail', 0);

			include_once(__DIR__ . DS . 'memberoption.class.php');

			switch ($action)
			{
				case 'editmemberoptions':
					$response->set('html', $this->edit($course, $user, $recvEmailOptionID, $recvEmailOptionValue));
				break;
				case 'savememberoptions':
					$response->set('html', $this->save($course, $user, $recvEmailOptionID, $recvEmailOptionValue));
				break;
				default:
					$response->set('html', $this->edit($course, $user, $recvEmailOptionID, $recvEmailOptionValue));
				break;
			}
		}

		// Return the output
		return $response;
	}

	/**
	 * Show an edit form
	 *
	 * @param   object  $course
	 * @param   object  $user
	 * @param   integer $recvEmailOptionID
	 * @param   unknown $recvEmailOptionValue
	 * @return  string
	 */
	protected function edit($course, $user, $recvEmailOptionID, $recvEmailOptionValue)
	{
		// Instantiate a view
		$view = $this->view('default', 'browse');

		// Load the options
		$database = JFactory::getDBO();
		$recvEmailOption = new courses_MemberOption($database);
		$recvEmailOption->loadRecord($course->get('gidNumber'), $user->get('id'), COURSES_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION);

		if ($recvEmailOption->id)
		{
			$view->recvEmailOptionID    = $recvEmailOption->id;
			$view->recvEmailOptionValue = $recvEmailOption->optionvalue;
		}
		else
		{
			$view->recvEmailOptionID    = 0;
			$view->recvEmailOptionValue = 0;
		}

		// Pass the view some info
		$view->option = $this->option;
		$view->course = $this->course;

		// Return the output
		return $view->loadTemplate();

	}

	/**
	 * Save setting
	 *
	 * @param   object  $course
	 * @param   object  $user
	 * @param   integer $recvEmailOptionID
	 * @param   unknown $recvEmailOptionValue
	 * @return  mixed
	 */
	protected function save($course, $user, $recvEmailOptionID, $recvEmailOptionValue)
	{
		$postSaveRedirect = JRequest::getVar('postsaveredirect', '');

		// Instantaite database object
		$database = JFactory::getDBO();

		// Save the COURSES_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION setting
		$row = new courses_MemberOption($database);

		//bind the data
		$rowdata = array(
			'id'          => $recvEmailOptionID,
			'userid'      => $user->get('id'),
			'gidNumber'   => $course->get('gidNumber'),
			'optionname'  => COURSES_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION,
			'optionvalue' => $recvEmailOptionValue
		);

		$row->bind($rowdata);

		// Check content
		if (!$row->check())
		{
			$this->setError($row->getError());
			return;
		}

		// Store content
		if (!$row->store())
		{
			$this->setError($row->getError());
			return $this->edit();
		}

		$app = JFactory::getApplication();
		$app->enqueueMessage('You have successfully updated your email settings','Message');

		if (!$postSaveRedirect)
		{
			$app->redirect(JRoute::_($this->course->link() . '&active=' . $this->_name . '&task=edit'));
		}
		else
		{
			$app->redirect($postSaveRedirect);
		}
	}
}

