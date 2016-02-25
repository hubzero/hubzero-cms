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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Groups Plugin class for messages
 */
class plgGroupsMessages extends \Hubzero\Plugin\Plugin
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
			'name' => 'messages',
			'title' => Lang::txt('PLG_GROUPS_MESSAGES'),
			'default_access' => $this->params->get('plugin_access','members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon' => '2709'
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
		$return = 'html';
		$active = 'messages';

		// The output array we're returning
		$arr = array(
			'html'=>''
		);

		//get this area details
		$this_area = $this->onGroupAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit)
		{
			if (!in_array($this_area['name'], $areas))
			{
				return $arr;
			}
		}

		// Are we returning HTML?
		if ($return == 'html')
		{
			//get group members plugin access level
			$group_plugin_acl = $access[$active];

			//get the group members
			$members = $group->get('members');

			// Set some variables so other functions have access
			$this->authorized = $authorized;
			$this->members = $members;
			$this->group = $group;
			$this->_option = $option;
			$this->action = $action;

			//if set to nobody make sure cant access
			if ($group_plugin_acl == 'nobody')
			{
				$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
				return $arr;
			}

			//check if guest and force login if plugin access is registered or members
			if (User::isGuest()
			 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members'))
			{
				$url = Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=' . $active, false, true);

				App::redirect(
					Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url)),
					Lang::txt('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
					'warning'
				);
				return;
			}

			//check to see if user is member and plugin access requires members
			if (!in_array(User::get('id'), $members)
			 && $group_plugin_acl == 'members'
			 && $authorized != 'admin')
			{
				$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
				return $arr;
			}

			//push styles to the view
			$this->css()
			     ->js();

			$task = strtolower(trim($action));

			switch ($task)
			{
				case 'send':        $arr['html'] = $this->_send();   break;
				case 'new':         $arr['html'] = $this->_create(); break;
				case 'viewmessage': $arr['html'] = $this->_view();   break;
				case 'sent':
				default:            $arr['html'] = $this->_sent();   break;
			}
		}

		// Return data
		return $arr;
	}

	/**
	 * Show sent messages
	 *
	 * @return     string
	 */
	protected function _sent()
	{
		// Set the page title
		Document::setTitle(Lang::txt(strtoupper($this->_name)) . ': ' . $this->group->get('description') . ': ' . Lang::txt('PLG_GROUPS_MESSAGES_SENT'));

		// Filters for returning results
		$filters = array(
			'limit'    => Request::getInt('limit', 10),
			'start'    => Request::getInt('limitstart', 0),
			'group_id' => $this->group->get('gidNumber')
		);

		// Instantiate our message object
		$database = App::get('db');
		$recipient = new \Hubzero\Message\Message($database);

		// Retrieve data
		$total = $recipient->getSentMessagesCount($filters);

		$rows = $recipient->getSentMessages($filters);

		// Instantiate a view
		$view = $this->view('default', 'sent');

		// Pass some info to the view
		$view->option = $this->_option;
		$view->group = $this->group;
		$view->authorized = $this->authorized;
		$view->rows = $rows;
		$view->filters = $filters;
		$view->total = $total;

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		// Return the output
		return $view->loadTemplate();
	}

	/**
	 * Show a message
	 *
	 * @return     string
	 */
	protected function _view()
	{
		//get the message id
		$message = Request::getVar('msg','','get');

		//if there is no message id show all sent messages
		if (!$message)
		{
			return $this->_sent();
		}

		//insantiate db
		$database = App::get('db');

		// Load the message and parse it
		$xmessage = new \Hubzero\Message\Message($database);
		$xmessage->load($message);
		$xmessage->message = stripslashes($xmessage->message);
		$xmessage->message = str_replace("\n", "\n ", $xmessage->message);
		$xmessage->message = preg_replace_callback("/[^=\"\'](https?:|mailto:|ftp:|gopher:|news:|file:)" . "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])/", array('plgGroupsMessages', 'autolink'), $xmessage->message);
		$xmessage->message = nl2br($xmessage->message);
		$xmessage->message = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $xmessage->message);

		if (substr($xmessage->component, 0, 4) == 'com_')
		{
			$xmessage->component = substr($xmessage->component, 4);
		}

		// Instantiate the view
		$view = $this->view('default', 'message');

		// Pass the view some info
		$view->option = $this->_option;
		$view->group = $this->group;
		$view->authorized = $this->authorized;
		$view->xmessage = $xmessage;
		$view->no_html = Request::getInt('no_html', 0);

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		// Return the output
		return $view->loadTemplate();
	}

	/**
	 * Show a form for creating a message
	 *
	 * @return     string
	 */
	protected function _create()
	{
		// Ensure only admins and group managers can create messages
		if ($this->authorized != 'manager' && $this->authorized != 'admin')
		{
			return false;
		}

		// Set the page title
		Document::setTitle(Lang::txt(strtoupper($this->_name)).': '.$this->group->get('description').': '.Lang::txt('PLG_GROUPS_MESSAGES_SEND'));

		// Instantiate a vew
		$view = $this->view('default', 'create');

		//get all member roles
		$db = App::get('db');
		$sql = "SELECT * FROM `#__xgroups_roles` WHERE gidNumber=".$db->quote($this->group->get('gidNumber'));
		$db->setQuery($sql);
		$member_roles = $db->loadAssocList();

		//get all group members
		$members = $this->group->get('members');

		// Pass the view some info
		$view->option = $this->_option;
		$view->group = $this->group;
		$view->authorized = $this->authorized;
		$view->params = $this->params;

		$view->member_roles = $member_roles;
		$view->members = $members;
		$view->users = Request::getVar('users', array('all'));
		$view->no_html = Request::getInt('no_html', 0);

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		// Return the output
		return $view->loadTemplate();
	}

	/**
	 * Send a message
	 *
	 * @return     mixed
	 */
	protected function _send()
	{
		// Ensure the user is logged in
		if (User::isGuest())
		{
			return false;
		}

		//message
		$message = Lang::txt('PLG_GROUPS_MESSAGES_FROM_GROUP', $this->group->get('cn'));

		// Incoming array of users to message
		$mbrs = Request::getVar('users', array(0), 'post');
		switch ($mbrs[0])
		{
			case 'invitees':
				$mbrs = $this->group->get('invitees');
				$action = 'group_invitees_message';
				$group_id = $this->group->get('gidNumber');
			break;
			case 'applicants':
				$mbrs = $this->group->get('applicants');
				$action = 'group_pending_message';
				$group_id = $this->group->get('gidNumber');
			break;
			case 'managers':
				$mbrs = $this->group->get('managers');
				$action = 'group_managers_message';
				$group_id = $this->group->get('gidNumber');
			break;
			case 'all':
				$mbrs = $this->group->get('members');
				$action = 'group_members_message';
				$group_id = $this->group->get('gidNumber');
			break;
			default:
				$message = Lang::txt('PLG_GROUPS_MESSAGES_FOR_GROUP_MEMBER', $this->group->get('cn'));

				foreach ($mbrs as $mbr)
				{
					if (strstr($mbr, '_'))
					{
						$role = explode('_', $mbr);
						$db = App::get('db');
						$sql = "SELECT uidNumber FROM #__xgroups_member_roles WHERE roleid=" . $db->Quote($role[1]);
						$db->setQuery($sql);
						$member_roles = $db->loadAssocList();
						foreach ($member_roles as $member)
						{
							$members[] = $member['uidNumber'];
						}
						$mbrs = $members;
						$action = 'group_role_message';
						$group_id = $this->group->get('gidNumber');
					}
					else
					{
						$action = '';
						$group_id = 0;
						break;
					}
				}
			break;
		}

		// Incoming message and subject
		$s = Request::getVar('subject', Lang::txt('PLG_GROUPS_MESSAGES_SUBJECT'));
		$m = Request::getVar('message', '');

		// Ensure we have a message
		if (!$s || !$m)
		{
			$html  = '<p class="error">You must enter all required fields</p>';
			$html .= $this->_create();
			return $html;
		}

		// get all group members
		$recipients = array();
		foreach ($mbrs as $mbr)
		{
			if ($profile = \Hubzero\User\Profile::getInstance($mbr))
			{
				$recipients[$profile->get('email')] = $profile->get('name');
			}
		}

		// add invite emails if sending to invitees
		if ($action == 'group_invitees_message')
		{
			// Get invite emails
			$db = App::get('db');
			$group_inviteemails = new \Hubzero\User\Group\InviteEmail();
			$current_inviteemails = $group_inviteemails->getInviteEmails($this->group->get('gidNumber'), true);

			foreach ($current_inviteemails as $current_inviteemail)
			{
				$recipients[$current_inviteemail] = $current_inviteemail;
			}
		}

		// define from details
		$from = array(
			'name'  => $this->group->get('description') . " Group on " . Config::get("fromname"),
			'email' => Config::get("mailfrom")
		);

		// create url
		$sef = Route::url('index.php?option='.$this->_option.'&cn='. $this->group->get('cn'));
		$sef = ltrim($sef, '/');

		// create subject
		$subject = $s . " [Email sent on Behalf of " . User::get('name') . "]";

		//message
		$plain  = Lang::txt('PLG_GROUPS_MESSAGES_FROM_GROUP', $this->group->get('cn'));
		$plain .= "\r\n------------------------------------------------\r\n\r\n";
		$plain .= $m;

		// create message
		$plain .= "\r\n\r\n------------------------------------------------\r\n". Request::base() . $sef . "\r\n";

		// create message object
		$message = new \Hubzero\Mail\Message();

		// set message details and send
		$message->setSubject($subject)
				->addFrom($from['email'], $from['name'])
				->setTo($recipients)
				->addPart($plain, 'text/plain')
				->send();

		// add invite emails if sending to invitees
		if ($action == 'group_invitees_message')
		{
			// Get invite emails
			$db = App::get('db');
			$group_inviteemails = new \Hubzero\User\Group\InviteEmail();
			$current_inviteemails = $group_inviteemails->getInviteEmails($this->group->get('gidNumber'), true);

			$headers  = 'From: ' . $from['name'] . ' <' . $from['email'] . '>' . "\r\n";
			$headers .= 'Reply-To: ' . $from['replytoname'] . ' <' . $from['replytoemail'] . '>' . "\r\n";
			foreach ($current_inviteemails as $current_inviteemail)
			{
				mail($current_inviteemail, $subject, $message, $headers);
			}
		}

		// Log the action
		if ($action)
		{
			// log invites
			\Components\Groups\Models\Log::log(array(
				'gidNumber' => $this->group->get('gidNumber'),
				'action'    => $action,
				'comments'  => array(User::get('id'))
			));
		}

		// Determine if we're returning HTML or not
		// (if no - this is an AJAX call)
		$no_html = Request::getInt('no_html', 0);
		if (!$no_html)
		{
			$html = '';
			if ($this->getError())
			{
				$html .= '<p class="error">' . $this->getError() . '</p>';
			}
			$html .= $this->_sent();

			return $html;
		}
	}

	/**
	 * Auto-link mailto, ftp, and http strings in text
	 *
	 * @param      array  $matches Text to autolink
	 * @return     string
	 */
	public function autolink($matches)
	{
		$href = $matches[0];

		if (substr($href, 0, 1) == '!')
		{
			return substr($href, 1);
		}

		$href = str_replace('"', '', $href);
		$href = str_replace("'", '', $href);
		$href = str_replace('&#8221', '', $href);

		$h = array('h', 'm', 'f', 'g', 'n');
		if (!in_array(substr($href,0,1), $h))
		{
			$href = substr($href, 1);
		}
		$name = trim($href);
		if (substr($name, 0, 7) == 'mailto:')
		{
			$name = substr($name, 7, strlen($name));
			$name = self::obfuscate($name);

			$href = 'mailto:' . $name;
		}
		$l = sprintf(
			' <a class="ext-link" href="%s" rel="external">%s</a>', $href, $name
		);
		return $l;
	}

	/**
	 * Obfuscate an email address
	 *
	 * @param      string $email Address to obfuscate
	 * @return     string
	 */
	public static function obfuscate($email)
	{
		$length = strlen($email);
		$obfuscatedEmail = '';
		for ($i = 0; $i < $length; $i++)
		{
			$obfuscatedEmail .= '&#'. ord($email[$i]) . ';';
		}

		return $obfuscatedEmail;
	}
}

