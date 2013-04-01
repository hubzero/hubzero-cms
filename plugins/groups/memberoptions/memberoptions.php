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

jimport( 'joomla.plugin.plugin' );

/**
 * Short description for 'plgGroupsMemberOptions'
 * 
 * Long description (if any) ...
 */
class plgGroupsMemberOptions extends JPlugin
{

	/**
	 * Short description for 'plgGroupsMemberOptions'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgGroupsMemberOptions(&$subject, $config)
	{
		parent::__construct($subject, $config);
		
		$this->loadLanguage();
	}

	/**
	 * Short description for 'onGroupAreas'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     array Return description (if any) ...
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name' => 'memberoptions',
			'title' => JText::_('GROUP_MEMBEROPTIONS'),
			'default_access' => 'registered', 
			'display_menu_tab' => $this->params->get('display_tab', 0)
		);

		return $area;
	}
	//-----------

	/**
	 * Short description for 'onGroup'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $group Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @param      unknown $authorized Parameter description (if any) ...
	 * @param      integer $limit Parameter description (if any) ...
	 * @param      integer $limitstart Parameter description (if any) ...
	 * @param      string $action Parameter description (if any) ...
	 * @param      unknown $access Parameter description (if any) ...
	 * @param      unknown $areas Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function onGroup( $group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null)
	{
		ximport('Hubzero_Document');

		// The output array we're returning
		$arr = array(
			'html'=>''
		);

		$user =& JFactory::getUser();
		$this->group = $group;
		$this->option = $option;

		// Things we need from the form
		$recvEmailOptionID = JRequest::getInt('memberoptionid', 0);
		$recvEmailOptionValue = JRequest::getInt('recvpostemail', 0);

		include_once(JPATH_ROOT.DS.'plugins'.DS.'groups'.DS.'memberoptions'.DS.'memberoption.class.php');

		switch ($action)
		{
			case 'editmemberoptions':
				$arr['html'] .= $this->edit($group, $user, $recvEmailOptionID, $recvEmailOptionValue);
				break;
			case 'savememberoptions':
				$arr['html'] .= $this->save($group, $user, $recvEmailOptionID, $recvEmailOptionValue);
				break;
			default:
				$arr['html'] .= $this->edit($group, $user, $recvEmailOptionID, $recvEmailOptionValue);
				break;
		}

		return $arr;

	}

	/**
	 * Short description for 'edit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $group Parameter description (if any) ...
	 * @param      object $user Parameter description (if any) ...
	 * @param      unknown $recvEmailOptionID Parameter description (if any) ...
	 * @param      unknown $recvEmailOptionValue Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	protected function edit($group, $user, $recvEmailOptionID, $recvEmailOptionValue)
	{
		// HTML output
		// Instantiate a view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'groups',
				'element'=>'memberoptions',
				'name'=>'browse'
			)
		);

		// Load the options
		/* @var $recvEmailOption XGroups_MemberOption */
		$database =& JFactory::getDBO();
		$recvEmailOption = new XGroups_MemberOption($database);
		$recvEmailOption->loadRecord( $group->get('gidNumber'), $user->id, GROUPS_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION);

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
		$view->group = $this->group;

		// Return the output
		return $view->loadTemplate();

	}

	/**
	 * Short description for 'save'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $group Parameter description (if any) ...
	 * @param      object $user Parameter description (if any) ...
	 * @param      unknown $recvEmailOptionID Parameter description (if any) ...
	 * @param      unknown $recvEmailOptionValue Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	protected function save($group, $user, $recvEmailOptionID, $recvEmailOptionValue)
	{
		/* @var $group Hubzero_Group */

		$postSaveRedirect = JRequest::getVar('postsaveredirect', '');
		
		//instantaite database object
		$database =& JFactory::getDBO();

		// Save the GROUPS_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION setting
		/* @var $row XForum */
		$row = new XGroups_MemberOption($database);

		//bind the data
		$rowdata = array( 'id' => $recvEmailOptionID,
				'userid' => $user->id,
				'gidNumber' => $group->get('gidNumber'),
				'optionname' => GROUPS_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION,
				'optionvalue' => $recvEmailOptionValue );

		$row->bind($rowdata);

		// Check content
		if (!$row->check()) {
			$this->setError( $row->getError() );
			return;
		}

		// Store content
		if (!$row->store()) {
			$this->setError( $row->getError() );
			return $this->edittopic();
		}

		$app =& JFactory::getApplication();
		$app->enqueueMessage('You have successfully updated your email settings','Message');
		
		if(!$postSaveRedirect)
			$app->redirect( JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=memberoptions&action=edit' ) );
		else
			$app->redirect( $postSaveRedirect );

	}
	
	public function onGroupUserEnrollment($gidNumber, $userid)
	{
		//get database
		$database =& JFactory::getDBO();
		
		//get hubzero logger
		$logger = &Hubzero_Factory::getLogger();
		
		//get group
		$group = Hubzero_Group::getInstance( $gidNumber );
		
		//is auto-subscribe on for discussion forum
		$discussion_email_autosubscribe = $group->get('discussion_email_autosubscribe');
		
		//log variable
		$logger->logDebug('$discussion_email_autosubscribe' . $discussion_email_autosubscribe);
		
		//if were not auto-subscribed then stop
		if (!$discussion_email_autosubscribe)
		{
			return;
		}
		
		// see if they've already got something, they shouldn't, but you never know
		$query = "SELECT COUNT(userid) FROM #__xgroups_memberoption WHERE gidNumber=" . $gidNumber . " AND userid=" . $userid . " AND optionname='receive-forum-email'";   
		$database->setQuery( $query );
		$count = $database->loadResult();
		if ($count)
		{
			$query = "UPDATE #__xgroups_memberoption SET optionvalue = 1 WHERE gidNumber=" . $gidNumber . " AND userid=" . $userid . " AND optionname='receive-forum-email'";   
			$database->setQuery($query);
			$database->query();
		}
		else
		{
			$query = "INSERT INTO #__xgroups_memberoption(gidNumber, userid, optionname, optionvalue) VALUES('" . $gidNumber . "', '" . $userid . "', 'receive-forum-email', '1')";
			$database->setQuery( $query );
			$database->query();
		}
	}
}

