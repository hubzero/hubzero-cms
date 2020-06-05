<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return  array
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
	 * @param   object   $group       Current group
	 * @param   string   $option      Name of the component
	 * @param   string   $authorized  User's authorization level
	 * @param   integer  $limit       Number of records to pull
	 * @param   integer  $limitstart  Start of records to pull
	 * @param   string   $action      Action to perform
	 * @param   array    $access      What can be accessed
	 * @param   array    $areas       Active area(s)
	 * @return  array
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
		$recvEmailOptionID    = Request::getInt('memberoptionid', 0);
		$recvEmailOptionValue = Request::getInt('recvpostemail', 0);

		include_once __DIR__ . DS . 'models' . DS . 'memberoption.php';

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
	 * @param   object   $group
	 * @param   object   $user
	 * @param   integer  $recvEmailOptionID
	 * @param   integer  $recvEmailOptionValue
	 * @return  string
	 */
	protected function edit($group, $user, $recvEmailOptionID, $recvEmailOptionValue)
	{
		// Load the options
		$recvEmailOption = Plugins\Groups\Memberoptions\Models\Memberoption::oneByUserAndOption(
			$group->get('gidNumber'),
			$user->get('id'),
			'receive-forum-email'
		);

		$view = $this->view('default', 'browse')
			->set('recvEmailOptionID', $recvEmailOption->get('id', 0))
			->set('recvEmailOptionValue', $recvEmailOption->get('optionvalue', 0))
			->set('option', $this->option)
			->set('group', $this->group);

		// Return the output
		return $view->loadTemplate();
	}

	/**
	 * Save settings
	 *
	 * @param   object   $group
	 * @param   object   $user
	 * @param   integer  $recvEmailOptionID
	 * @param   integer  $recvEmailOptionValue
	 * @return  void
	 */
	protected function save($group, $user, $recvEmailOptionID, $recvEmailOptionValue)
	{
		$postSaveRedirect = Request::getString('postsaveredirect', '');

		// Save the GROUPS_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION setting
		$row = Plugins\Groups\Memberoptions\Models\Memberoption::blank()->set(array(
			'id'          => $recvEmailOptionID,
			'userid'      => $user->get('id'),
			'gidNumber'   => $group->get('gidNumber'),
			'optionname'  => 'receive-forum-email',
			'optionvalue' => $recvEmailOptionValue
		));

		// Store content
		if (!$row->save())
		{
			$this->setError($row->getError());
			return $this->edit($group, $user, $recvEmailOptionID, $recvEmailOptionValue);
		}

		if (Request::getInt('no_html'))
		{
			echo json_encode(array('success' => true));
			exit();
		}

		if (!$postSaveRedirect)
		{
			$postSaveRedirect = Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=memberoptions&action=edit');
		}

		App::redirect(
			$postSaveRedirect,
			Lang::txt('GROUP_MEMBEROPTIONS_UPDATED')
		);
	}

	/**
	 * Subscribe a person to emails on enrollment
	 *
	 * @param   integer  $gidNumber
	 * @param   integer  $userid
	 * @return  void
	 */
	public function onGroupUserEnrollment($gidNumber, $userid)
	{
		// get group
		$group = Hubzero\User\Group::getInstance($gidNumber);

		// is auto-subscribe on for discussion forum
		$autosubscribe = $group->get('discussion_email_autosubscribe');

		// log variable
		Log::debug('$discussion_email_autosubscribe' . $autosubscribe);

		// if were not auto-subscribed then stop
		if (!$autosubscribe)
		{
			return;
		}

		include_once __DIR__ . DS . 'models' . DS . 'memberoption.php';

		// see if they've already got something, they shouldn't, but you never know
		$row = Plugins\Groups\Memberoptions\Models\Memberoption::oneByUserAndOption(
			$gidNumber,
			$userid,
			'receive-forum-email'
		);
		if ($row->get('id'))
		{
			// They already have a record, so ignore.
			return;
		}
		$row->set('gidNumber', $gidNumber);
		$row->set('userid', $userid);
		$row->set('optionname', 'receive-forum-email');
		$row->set('optionvalue', 1);
		$row->save();
	}
}
