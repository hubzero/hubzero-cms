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
 * Short description for 'plgCoursesMemberOptions'
 *
 * Long description (if any) ...
 */
class plgCoursesMemberOptions extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Short description for 'onCourseAreas'
	 *
	 * Long description (if any) ...
	 *
	 * @return     array Return description (if any) ...
	 */
	public function &onCourseAreas()
	{
		$area = array(
			'name' => 'memberoptions',
			'title' => JText::_('COURSE_MEMBEROPTIONS'),
			'default_access' => 'registered',
			'display_menu_tab' => false
		);

		return $area;
	}

	/**
	 * Short description for 'onCourse'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $course Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @param      unknown $authorized Parameter description (if any) ...
	 * @param      integer $limit Parameter description (if any) ...
	 * @param      integer $limitstart Parameter description (if any) ...
	 * @param      string $action Parameter description (if any) ...
	 * @param      unknown $access Parameter description (if any) ...
	 * @param      unknown $areas Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function onCourse( $course, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null)
	{
		// The output array we're returning
		$arr = array(
			'html'=>''
		);

		$user = JFactory::getUser();
		$this->course = $course;
		$this->option = $option;

		// Things we need from the form
		$recvEmailOptionID = JRequest::getInt('memberoptionid', 0);
		$recvEmailOptionValue = JRequest::getInt('recvpostemail', 0);

		include_once(JPATH_ROOT.DS.'plugins'.DS.'courses'.DS.'memberoptions'.DS.'memberoption.class.php');

		switch ($action)
		{
			case 'editmemberoptions':
				$arr['html'] .= $this->edit($course, $user, $recvEmailOptionID, $recvEmailOptionValue);
				break;
			case 'savememberoptions':
				$arr['html'] .= $this->save($course, $user, $recvEmailOptionID, $recvEmailOptionValue);
				break;
			default:
				$arr['html'] .= $this->edit($course, $user, $recvEmailOptionID, $recvEmailOptionValue);
				break;
		}

		return $arr;

	}

	/**
	 * Short description for 'edit'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $course Parameter description (if any) ...
	 * @param      object $user Parameter description (if any) ...
	 * @param      unknown $recvEmailOptionID Parameter description (if any) ...
	 * @param      unknown $recvEmailOptionValue Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	protected function edit($course, $user, $recvEmailOptionID, $recvEmailOptionValue)
	{
		// HTML output
		// Instantiate a view
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'=>'courses',
				'element'=>'memberoptions',
				'name'=>'browse'
			)
		);

		// Load the options
		/* @var $recvEmailOption courses_MemberOption */
		$database = JFactory::getDBO();
		$recvEmailOption = new courses_MemberOption($database);
		$recvEmailOption->loadRecord( $course->get('gidNumber'), $user->id, COURSES_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION);

		if($recvEmailOption->id)
		{
			$view->recvEmailOptionID = $recvEmailOption->id;
			$view->recvEmailOptionValue = $recvEmailOption->optionvalue;
		}
		else
		{
			$view->recvEmailOptionID = 0;
			$view->recvEmailOptionValue = 0;
		}

		// Pass the view some info
		$view->option = $this->option;
		$view->course = $this->course;

		// Return the output
		return $view->loadTemplate();

	}

	/**
	 * Short description for 'save'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $course Parameter description (if any) ...
	 * @param      object $user Parameter description (if any) ...
	 * @param      unknown $recvEmailOptionID Parameter description (if any) ...
	 * @param      unknown $recvEmailOptionValue Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	protected function save($course, $user, $recvEmailOptionID, $recvEmailOptionValue)
	{
		$postSaveRedirect = JRequest::getVar('postsaveredirect', '');

		//instantaite database object
		$database = JFactory::getDBO();

		// Save the COURSES_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION setting
		/* @var $row XForum */
		$row = new courses_MemberOption($database);

		//bind the data
		$rowdata = array(
			'id' => $recvEmailOptionID,
			'userid' => $user->id,
			'gidNumber' => $course->get('gidNumber'),
			'optionname' => COURSES_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION,
			'optionvalue' => $recvEmailOptionValue
		);

		$row->bind($rowdata);

		// Check content
		if (!$row->check())
		{
			$this->setError( $row->getError() );
			return;
		}

		// Store content
		if (!$row->store())
		{
			$this->setError( $row->getError() );
			return $this->edittopic();
		}

		$app = JFactory::getApplication();
		$app->enqueueMessage('You have successfully updated your email settings','Message');

		if (!$postSaveRedirect)
			$app->redirect( JRoute::_($this->course->link() . '&active=memberoptions&task=edit' ) );
		else
			$app->redirect( $postSaveRedirect );

	}
}

