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
 * @author    David Benham <dbenham@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Groups plugin class for Member Options
 */
class plgGroupsMemberOptions extends \Hubzero\Plugin\Plugin
{

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name' => 'memberoptions',
			'title' => Lang::txt('GROUP_MEMBEROPTIONS'),
			'default_access' => 'registered',
			'display_menu_tab' => $this->params->get('display_tab', 0),
			'icon' => '2699'
		);

		return $area;
	}

	/**
	 * Return data on a group view (this will be some form of HTML)
	 *
	 * @param      object  $group      Current group
	 * @param      string  $option     Name of the component
	 * @param      string  $authorized User's authorization level
	 * @param      integer $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      string  $action     Action to perform
	 * @param      array   $access     What can be accessed
	 * @param      array   $areas      Active area(s)
	 * @return     array
	 */
	public function onGroup($group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null)
	{
		// The output array we're returning
		$arr = array(
			'html' => ''
		);

		$user = User::getInstance();
		$this->group = $group;
		$this->option = $option;

		// Things we need from the form
		$recvEmailOptionID   = Request::getInt('memberoptionid', 0);
		$recvEmailOptionValue = Request::getInt('recvpostemail', 0);

		include_once(__DIR__ . DS . 'memberoption.class.php');

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
	 * Edit settings
	 *
	 * @param      object  $group
	 * @param      object  $user
	 * @param      integer $recvEmailOptionID
	 * @param      integer $recvEmailOptionValue
	 * @return     void
	 */
	protected function edit($group, $user, $recvEmailOptionID, $recvEmailOptionValue)
	{
		// HTML output
		// Instantiate a view
		$view = $this->view('default', 'browse');

		// Load the options
		$database = App::get('db');
		$recvEmailOption = new GroupsTableMemberoption($database);
		$recvEmailOption->loadRecord($group->get('gidNumber'), $user->get('id'), GROUPS_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION);

		if ($recvEmailOption->id)
		{
			$view->recvEmailOptionID    = $recvEmailOption->id;
			$view->recvEmailOptionValue = $recvEmailOption->optionvalue;
		}
		else
		{
			$view->recvEmailOptionID = 0;
			$view->recvEmailOptionValue = 0;
		}

		// Pass the view some info
		$view->option = $this->option;
		$view->group  = $this->group;

		// Return the output
		return $view->loadTemplate();

	}

	/**
	 * Save settings
	 *
	 * @param      object  $group
	 * @param      object  $user
	 * @param      integer $recvEmailOptionID
	 * @param      integer $recvEmailOptionValue
	 * @return     void
	 */
	protected function save($group, $user, $recvEmailOptionID, $recvEmailOptionValue)
	{
		$postSaveRedirect = Request::getVar('postsaveredirect', '');

		//instantaite database object
		$database = App::get('db');

		// Save the GROUPS_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION setting
		$row = new GroupsTableMemberoption($database);

		//bind the data
		$rowdata = array(
			'id'          => $recvEmailOptionID,
			'userid'      => $user->get('id'),
			'gidNumber'   => $group->get('gidNumber'),
			'optionname'  => GROUPS_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION,
			'optionvalue' => $recvEmailOptionValue
		);

		$row->bind($rowdata);

		// Check content
		if (!$row->check())
		{
			$this->setError($row->getError());
			return $this->edit($group, $user, $recvEmailOptionID, $recvEmailOptionValue);
		}

		// Store content
		if (!$row->store())
		{
			$this->setError($row->getError());
			return $this->edit($group, $user, $recvEmailOptionID, $recvEmailOptionValue);
		}

		if (Request::getInt('no_html'))
		{
			echo json_encode(array('success' => true));
			exit();
		}
		else
		{
			if (!$postSaveRedirect)
			{
				App::redirect(
					Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=memberoptions&action=edit'),
					Lang::txt('You have successfully updated your email settings')
				);
			}
			else
			{
				App::redirect(
					$postSaveRedirect,
					Lang::txt('You have successfully updated your email settings')
				);
			}
		}
	}

	/**
	 * Subscribe a person to emails on enrollment
	 *
	 * @param      integer $gidNumber
	 * @param      integer $userid
	 * @return     void
	 */
	public function onGroupUserEnrollment($gidNumber, $userid)
	{
		// get database
		$database = App::get('db');

		// get group
		$group = \Hubzero\User\Group::getInstance($gidNumber);

		// is auto-subscribe on for discussion forum
		$discussion_email_autosubscribe = $group->get('discussion_email_autosubscribe');

		// log variable
		Log::debug('$discussion_email_autosubscribe' . $discussion_email_autosubscribe);

		// if were not auto-subscribed then stop
		if (!$discussion_email_autosubscribe)
		{
			return;
		}

		// see if they've already got something, they shouldn't, but you never know
		$query = "SELECT COUNT(userid) FROM `#__xgroups_memberoption` WHERE gidNumber=" . $gidNumber . " AND userid=" . $userid . " AND optionname='receive-forum-email'";
		$database->setQuery($query);
		$count = $database->loadResult();
		if ($count)
		{
			$query = "UPDATE `#__xgroups_memberoption` SET optionvalue = 1 WHERE gidNumber=" . $gidNumber . " AND userid=" . $userid . " AND optionname='receive-forum-email'";
			$database->setQuery($query);
			$database->query();
		}
		else
		{
			$query = "INSERT INTO `#__xgroups_memberoption` (gidNumber, userid, optionname, optionvalue) VALUES ('" . $gidNumber . "', '" . $userid . "', 'receive-forum-email', '1')";
			$database->setQuery($query);
			$database->query();
		}
	}
}

