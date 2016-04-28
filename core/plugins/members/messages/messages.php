<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Members Plugin class for messages
 */
class plgMembersMessages extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param   object  $user    User
	 * @param   object  $member  Profile
	 * @return  array   Plugin name
	 */
	public function &onMembersAreas($user, $member)
	{
		//default areas returned to nothing
		$areas = array();

		//if this is the logged in user show them
		if ($user->get('id') == $member->get('id'))
		{
			$areas['messages'] = Lang::txt('PLG_MEMBERS_MESSAGES');
			$areas['icon'] = '2709';
		}

		return $areas;
	}

	/**
	 * Event call to return data for a specific member
	 *
	 * @param   object  $user    User
	 * @param   object  $member  Profile
	 * @param   string  $option  Component name
	 * @param   string  $areas   Plugins to return data
	 * @return  array   Return array of html
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;

		$arr = array(
			'html' => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member))))
			{
				$returnhtml = false;
			}
		}

		// Get our database object
		$database = App::get('db');

		// Are we returning HTML?
		if ($returnhtml)
		{
			$task = Request::getVar('action','');
			if (!$task)
			{
				$task = Request::getVar('inaction','');
			}

			$mid = Request::getInt('msg',0);
			if ($mid)
			{
				$task = 'view';
			}

			if (!$task)
			{
				$task = "inbox";
			}

			switch ($task)
			{
				case 'sendtoarchive': $body = $this->sendtoarchive($database, $option, $member); break;
				case 'sendtotrash':   $body = $this->sendtotrash($database, $option, $member);   break;
				case 'sendtoinbox':   $body = $this->sendtoinbox($database, $option, $member);   break;
				case 'markasread':    $body = $this->markasread($database, $option, $member);    break;
				case 'markasunread':  $body = $this->markasunread($database, $option, $member);  break;
				case 'savesettings':  $body = $this->savesettings($database, $option, $member);  break;
				case 'emptytrash':    $body = $this->emptytrash($database, $option, $member);    break;
				case 'delete':        $body = $this->delete($database, $option, $member);        break;

				case 'send':          $body = $this->send($database, $option, $member);          break;
				case 'new':           $body = $this->create($database, $option, $member);        break;

				case 'view':          $body = $this->message($database, $option, $member, $mid); break;
				case 'sent':          $body = $this->sent($database, $option, $member);          break;
				case 'settings':      $body = $this->settings($database, $option, $member);      break;
				case 'archive':       $body = $this->archive($database, $option, $member);       break;
				case 'trash':         $body = $this->trash($database, $option, $member);         break;
				case 'inbox':
				default:              $body = $this->inbox($database, $option, $member);         break;
			}

			//html for the messages
			$view = $this->view('default', 'default');
			$view->option = $option;
			$view->member = $member;
			$view->task   = $task;

			$view->filters = array();
			$view->filters['limit'] = Request::getState(
				$option . '.plugin.messages.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			);
			$view->filters['start'] = Request::getState(
				$option . '.plugin.messages.limitstart',
				'limitstart',
				0,
				'int'
			);

			$view->body = $body;
			$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();
			$arr['html'] = $view->loadTemplate();
		}

		//get meta
		$arr['metadata'] = array();

		//get the number of unread messages
		$recipient = new \Hubzero\Message\Recipient($database);
		$inboxCount = $recipient->getMessagesCount($member->get('id'), array('state' => '0'));
		$unreadMessages = $recipient->getUnreadMessages($member->get('id'), 0);

		//return total message count
		$arr['metadata']['count'] = $inboxCount;

		//if we have unread messages show alert
		if (count($unreadMessages) > 0)
		{
			$title = count($unreadMessages) . ' unread message(s).';
			$link = Route::url($member->link() . '&active=messages');
			$arr['metadata']['alert'] = "<a class=\"alrt\" href=\"{$link}\"><span><strong>Messages Alert</strong>{$title}</span></a>";
		}

		// Return data
		return $arr;
	}

	/**
	 * Show inbox
	 *
	 * @param      object  $database JDatabase
	 * @param      string  $option   Name of the component
	 * @param      object  $member   Current member
	 * @return     string
	 */
	public function inbox($database, $option, $member)
	{
		$view = $this->view('inbox', 'default');
		$view->option = $option;
		$view->member = $member;

		// Filters for returning results
		$filters = array();
		$filters['limit'] = Request::getState(
			$option . '.plugin.messages.limit',
			'limit',
			Config::get('list_limit'),
			'int'
		);
		$filters['start'] = Request::getState(
			$option . '.plugin.messages.limitstart',
			'limitstart',
			0,
			'int'
		);
		$filters['state'] = 0;

		$view->filter = Request::getVar('filter', '');
		$filters['filter'] = ($view->filter) ? 'com_' . $view->filter : '';

		$recipient = new \Hubzero\Message\Recipient($database);

		$view->total = $recipient->getMessagesCount($member->get('id'), $filters);

		$view->rows = $recipient->getMessages($member->get('id'), $filters);

		$pageNav = new \Hubzero\Pagination\Paginator(
			$view->total,
			$filters['start'],
			$filters['limit']
		);

		$xmc = new \Hubzero\Message\Component($database);
		$view->components = $xmc->getComponents();

		$pageNav->setAdditionalUrlParam('id', $member->get('id'));
		$pageNav->setAdditionalUrlParam('active', 'messages');
		$pageNav->setAdditionalUrlParam('task', 'inbox');
		$pageNav->setAdditionalUrlParam('action', '');

		$view->pagenavhtml = $pageNav->render();

		return $view->loadTemplate();
	}

	/**
	 * Show archived messages
	 *
	 * @param      object  $database JDatabase
	 * @param      string  $option   Name of the component
	 * @param      object  $member   Current member
	 * @return     string
	 */
	public function archive($database, $option, $member)
	{
		$view = $this->view('archive', 'default');
		$view->option = $option;
		$view->member = $member;

		// Filters for returning results
		$filters = array();
		$filters['limit'] = Request::getState(
			$option . '.plugin.messages.limit',
			'limit',
			Config::get('list_limit'),
			'int'
		);
		$filters['start'] = Request::getState(
			$option . '.plugin.messages.limitstart',
			'limitstart',
			0,
			'int'
		);
		$filters['state'] = 1;
		$view->filter = Request::getVar('filter', '');
		$filters['filter'] = ($view->filter) ? 'com_' . $view->filter : '';

		$recipient = new \Hubzero\Message\Recipient($database);

		$view->total = $recipient->getMessagesCount($member->get('id'), $filters);

		$view->rows = $recipient->getMessages($member->get('id'), $filters);

		$pageNav = new \Hubzero\Pagination\Paginator(
			$view->total,
			$filters['start'],
			$filters['limit']
		);

		$xmc = new \Hubzero\Message\Component($database);
		$view->components = $xmc->getComponents();

		$pageNav->setAdditionalUrlParam('id', $member->get('id'));
		$pageNav->setAdditionalUrlParam('active', 'messages');
		$pageNav->setAdditionalUrlParam('task', 'archive');
		$pageNav->setAdditionalUrlParam('action', '');

		$view->pagenavhtml = $pageNav->render();

		return $view->loadTemplate();
	}

	/**
	 * Show trashed messages
	 *
	 * @param      object  $database JDatabase
	 * @param      string  $option   Name of the component
	 * @param      object  $member   Current member
	 * @return     string
	 */
	public function trash($database, $option, $member)
	{
		$view = $this->view('trash', 'default');
		$view->option = $option;
		$view->member = $member;

		// Filters for returning results
		$filters = array();
		$filters['limit'] = Request::getState(
			$option . '.plugin.messages.limit',
			'limit',
			Config::get('list_limit'),
			'int'
		);
		$filters['start'] = Request::getState(
			$option . '.plugin.messages.limitstart',
			'limitstart',
			0,
			'int'
		);
		$filters['state'] = 2;
		$view->filter = Request::getVar('filter', '');
		$filters['filter'] = ($view->filter) ? 'com_' . $view->filter : '';

		$recipient = new \Hubzero\Message\Recipient($database);

		$view->total = $recipient->getMessagesCount($member->get('id'), $filters);

		$view->rows = $recipient->getMessages($member->get('id'), $filters);

		$pageNav = new \Hubzero\Pagination\Paginator(
			$view->total,
			$filters['start'],
			$filters['limit']
		);

		$xmc = new \Hubzero\Message\Component($database);
		$view->components = $xmc->getComponents();

		$pageNav->setAdditionalUrlParam('id', $member->get('id'));
		$pageNav->setAdditionalUrlParam('active', 'messages');
		$pageNav->setAdditionalUrlParam('task', 'trash');
		$pageNav->setAdditionalUrlParam('action', '');

		$view->pagenavhtml = $pageNav->render();

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Show sent messages
	 *
	 * @param      object  $database JDatabase
	 * @param      string  $option   Name of the component
	 * @param      object  $member   Current member
	 * @return     string
	 */
	public function sent($database, $option, $member)
	{
		$view = $this->view('sent', 'default');
		$view->option = $option;
		$view->member = $member;

		// Filters for returning results
		$filters = array();
		$filters['limit'] = Request::getState(
			$option . '.plugin.messages.limit',
			'limit',
			Config::get('list_limit'),
			'int'
		);
		$filters['start'] = Request::getState(
			$option . '.plugin.messages.limitstart',
			'limitstart',
			0,
			'int'
		);
		$filters['created_by'] = $member->get('id');

		$recipient = new \Hubzero\Message\Message($database);

		$view->total = $recipient->getSentMessagesCount($filters);

		$view->rows = $recipient->getSentMessages($filters);

		$pageNav = new \Hubzero\Pagination\Paginator(
			$view->total,
			$filters['start'],
			$filters['limit']
		);

		$pageNav->setAdditionalUrlParam('id', $member->get('id'));
		$pageNav->setAdditionalUrlParam('active', 'messages');
		$pageNav->setAdditionalUrlParam('task', 'sent');
		$pageNav->setAdditionalUrlParam('action', '');

		$view->pagenavhtml = $pageNav->render();

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Show a form for settings
	 *
	 * @param      object  $database JDatabase
	 * @param      string  $option   Name of the component
	 * @param      object  $member   Current member
	 * @return     string
	 */
	public function settings($database, $option, $member)
	{
		$xmc = new \Hubzero\Message\Component($database);
		$components = $xmc->getRecords();

		$view = $this->view('settings', 'default');
		$view->option = $option;
		$view->member = $member;
		$view->components = $components;
		if (!$components)
		{
			if ($this->getError())
			{
				$view->setError($this->getError());
			}
			return $view->loadTemplate();
		}

		$settings = array();
		foreach ($components as $component)
		{
			$settings[$component->action] = array();
		}

		// Fetch message methods
		$notimethods = Event::trigger('xmessage.onMessageMethods', array());

		// A var for storing the default notification method
		$default_method = null;

		// Instantiate our notify object
		$notify = new \Hubzero\Message\Notify($database);

		// Get the user's selected methods
		$methods = $notify->getRecords($member->get('id'));
		if ($methods)
		{
			foreach ($methods as $method)
			{
				$settings[$method->type]['methods'][] = $method->method;
				$settings[$method->type]['ids'][$method->method] = $method->id;
			}
		}
		else
		{
			$default_method = $this->params->get('default_method');
		}

		// Fill in any settings that weren't set.
		foreach ($settings as $key=>$val)
		{
			if (count($val) <= 0)
			{
				// If the user has never changed their settings, set up the defaults
				if ($default_method !== null)
				{
					$settings[$key]['methods'][] = 'internal';
					$settings[$key]['methods'][] = $default_method;
					$settings[$key]['ids']['internal'] = 0;
					$settings[$key]['ids'][$default_method] = 0;
				}
				else
				{
					$settings[$key]['methods'] = array();
					$settings[$key]['ids'] = array();
				}
			}
		}

		$view->settings = $settings;
		$view->notimethods = $notimethods;
		return $view->loadTemplate();
	}

	/**
	 * Create a message
	 *
	 * @param      object  $database JDatabase
	 * @param      string  $option   Name of the component
	 * @param      object  $member   Current member
	 * @return     void
	 */
	private function create($database, $option, $member)
	{
		$view = $this->view('create', 'default');

		//list of message to's
		$tos = array();

		//get members name and id
		$mbrs = Request::getVar('to', array());
		foreach ($mbrs as $mbr)
		{
			$mem = User::getInstance($mbr);
			$tos[] = $mem->get('name') . ' (' . $mem->get('id') . ')';
		}

		$view->option = $option;
		$view->member = $member;
		$view->tos = implode(',', $tos);
		$view->no_html = Request::getInt('no_html', 0);

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * View a message
	 *
	 * @param      object  $database JDatabase
	 * @param      string  $option   Name of the component
	 * @param      object  $member   Current member
	 * @param      integer $mid      MEssage ID
	 * @return     void
	 */
	public function message($database, $option, $member, $mid)
	{
		$xmessage = new \Hubzero\Message\Message($database);
		$xmessage->load($mid);
		$xmessage->message = stripslashes($xmessage->message);

		$xmr = new \Hubzero\Message\Recipient($database);
		$xmr->loadRecord($mid, $member->get('id'));

		$xmessage->message = str_replace("\n","\n ",$xmessage->message);
		$UrlPtrn  = "[^=\"\'](https?:|mailto:|ftp:|gopher:|news:|file:)" . "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])";
		$xmessage->message = preg_replace_callback("/$UrlPtrn/", array($this,'autolink'), $xmessage->message);
		$xmessage->message = nl2br($xmessage->message);
		$xmessage->message = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;', $xmessage->message);

		if (substr($xmessage->component,0,4) == 'com_')
		{
			$xmessage->component = substr($xmessage->component, 4);
		}

		$xseen = new \Hubzero\Message\Seen($database);
		$xseen->mid = $mid;
		$xseen->uid = $member->get('id');
		$xseen->loadRecord();

		if (User::get('id') == $member->get('id'))
		{
			if (!$xseen->whenseen || $xseen->whenseen == $database->getNullDate())
			{
				$xseen->mid = $mid;
				$xseen->uid = $member->get('id');
				$xseen->whenseen = Date::toSql();
				$xseen->store(true);
			}
		}

		if (substr($xmessage->type, -8) == '_message')
		{
			$u = User::getInstance($xmessage->created_by);
			$from = '<a href="'.Route::url('index.php?option=' . $option . '&id=' . $u->get('id')) . '">' . $u->get('name') . '</a>' . "\n";
		}
		else
		{
			$from = Lang::txt('PLG_MEMBERS_MESSAGES_SYSTEM', $xmessage->component);
		}

		$view = $this->view('message', 'default');
		$view->option = $option;
		$view->member = $member;
		$view->xmr = $xmr;
		$view->xmessage = $xmessage;
		$view->from = $from;

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Move message to archive
	 *
	 * @param      object  $database JDatabase
	 * @param      string  $option   Name of the component
	 * @param      object  $member   Current member
	 * @return     void
	 */
	public function sendtoarchive($database, $option, $member)
	{
		$limit = Request::getInt('limit', Config::get('list_limit'));
		$start = Request::getInt('limitstart', 0);
		$mids  = Request::getVar('mid', array());

		if (count($mids) > 0)
		{
			// Check for request forgeries
			Request::checkToken(['get', 'post']);

			foreach ($mids as $mid)
			{
				$recipient = new \Hubzero\Message\Recipient($database);
				$recipient->mid = $mid;
				$recipient->uid = $member->get('id');
				$recipient->loadRecord();
				$recipient->state = 1;
				if (!$recipient->store())
				{
					$this->setError($recipient->getError());
				}

				$xseen = new \Hubzero\Message\Seen($database);
				$xseen->mid = $mid;
				$xseen->uid = $member->get('id');
				$xseen->loadRecord();
				if ($xseen->whenseen == '' || $xseen->whenseen == $database->getNullDate() || $xseen->whenseen == NULL)
				{
					$xseen->whenseen = Date::toSql();
					$xseen->store(true);
				}
			}
			$this->addPluginMessage("You have successfully moved <b><u>" . count($mids) . "</u></b> message(s) to your archive.", "passed");
		}
		else
		{
			$this->addPluginMessage("No messages selected.", "warning");
		}

		return App::redirect(Route::url($member->link() . '&active=messages&task=' . Request::getWord('activetab', 'archive') . '&start=' . $start . '&limit=' . $limit));
	}

	/**
	 * Move message to inbox
	 *
	 * @param      object  $database JDatabase
	 * @param      string  $option   Name of the component
	 * @param      object  $member   Current member
	 * @return     void
	 */
	public function sendtoinbox($database, $option, $member)
	{
		$limit = Request::getInt('limit', Config::get('list_limit'));
		$start = Request::getInt('limitstart', 0);
		$mids  = Request::getVar('mid', array());

		if (count($mids) > 0)
		{
			// Check for request forgeries
			Request::checkToken(['get', 'post']);

			foreach ($mids as $mid)
			{
				$recipient = new \Hubzero\Message\Recipient($database);
				$recipient->mid = $mid;
				$recipient->uid = $member->get('id');
				$recipient->loadRecord();
				$recipient->state = 0;
				if (!$recipient->store())
				{
					$this->setError($recipient->getError());
				}
			}
			$this->addPluginMessage("You have successfully moved <b><u>" . count($mids) . "</u></b> message(s) to your inbox.", "passed");
		}
		else
		{
			$this->addPluginMessage("No messages selected.", "warning");
		}

		return App::redirect(Route::url($member->link() . '&active=messages&task=' . Request::getWord('activetab', 'inbox') . '&start=' . $start . '&limit=' . $limit));
	}

	/**
	 * Mark messages as "trashed"
	 *
	 * @param      object  $database JDatabase
	 * @param      string  $option   Name of the component
	 * @param      object  $member   Current member
	 * @return     void
	 */
	public function sendtotrash($database, $option, $member)
	{
		$limit = Request::getInt('limit', Config::get('list_limit'));
		$start = Request::getInt('limitstart', 0);
		$mids  = Request::getVar('mid', array());

		if (count($mids) > 0)
		{
			// Check for request forgeries
			Request::checkToken(['get', 'post']);

			foreach ($mids as $mid)
			{
				$recipient = new \Hubzero\Message\Recipient($database);
				$recipient->mid = $mid;
				$recipient->uid = $member->get('id');
				$recipient->loadRecord();

				$xseen = new \Hubzero\Message\Seen($database);
				$xseen->mid = $mid;
				$xseen->uid = $member->get('id');
				$xseen->loadRecord();
				if ($xseen->whenseen == '' || $xseen->whenseen == $database->getNullDate() || $xseen->whenseen == NULL)
				{
					$xseen->whenseen = Date::toSql();
					$xseen->store(true);
				}

				$recipient->state = 2;
				$recipient->expires = Date::of(time()+(10*60*60*60))->toSql();
				if (!$recipient->store())
				{
					$this->setError($recipient->getError());
				}
			}
			$this->addPluginMessage("You have successfully moved <b><u>" . count($mids) . "</u></b> message(s) to your trash.", "passed");
		}
		else
		{
			$this->addPluginMessage("No messages selected.", "warning");
		}

		return App::redirect(Route::url($member->link() . '&active=messages&task=' . Request::getWord('activetab', 'trash') . '&start=' . $start . '&limit=' . $limit));
	}

	/**
	 * Delete "trashed" messages
	 *
	 * @param      object  $database JDatabase
	 * @param      string  $option   Name of the component
	 * @param      object  $member   Current member
	 * @return     void
	 */
	public function emptytrash($database, $option, $member)
	{
		$recipient = new \Hubzero\Message\Recipient($database);
		$recipient->uid = $member->get('id');
		if (!$recipient->deleteTrash())
		{
			$this->setError($recipient->getError());
		}

		return $this->trash($database, $option, $member);
	}

	/**
	 * Delete a message
	 *
	 * @param      object  $database JDatabase
	 * @param      string  $option   Name of the component
	 * @param      object  $member   Current member
	 * @return     void
	 */
	public function delete($database, $option, $member)
	{
		$limit = Request::getInt('limit', Config::get('list_limit'));
		$start = Request::getInt('limitstart', 0);
		$mids  = Request::getVar('mid', array());

		if (count($mids) > 0)
		{
			// Check for request forgeries
			Request::checkToken(['get', 'post']);

			foreach ($mids as $mid)
			{
				$recipient = new \Hubzero\Message\Recipient($database);
				$recipient->mid = $mid;
				$recipient->uid = $member->get('id');
				$recipient->loadRecord();
				if (!$recipient->delete())
				{
					$this->setError($recipient->getError());
				}
			}
			$this->addPluginMessage('You have successfully deleted <b><u>' . count($mids) . '</u></b> message(s).', 'passed');
		}
		else
		{
			$this->addPluginMessage("No messages selected.", "warning");
		}

		return App::redirect(Route::url($member->link() . '&active=messages&task=' . Request::getWord('activetab', 'inbox') . '&start=' . $start . '&limit=' . $limit));
	}

	/**
	 * Mark messages as read
	 *
	 * @param      object  $database JDatabase
	 * @param      string  $option   Name of the component
	 * @param      object  $member   Current member
	 * @return     void
	 */
	public function markasread($database, $option, $member)
	{
		$limit = Request::getInt('limit', Config::get('list_limit'));
		$start = Request::getInt('limitstart', 0);
		$ids   = Request::getVar('mid', array());

		if (count($ids) > 0)
		{
			// Check for request forgeries
			Request::checkToken(['get', 'post']);

			foreach ($ids as $mid)
			{
				$xseen = new \Hubzero\Message\Seen($database);
				$xseen->mid = $mid;
				$xseen->uid = $member->get('id');
				//$xseen->loadRecord();
				if ($xseen->whenseen == '' || $xseen->whenseen == $database->getNullDate() || $xseen->whenseen == NULL)
				{
					$xseen->whenseen = Date::toSql();
					$xseen->store(true);
				}
			}
			$this->addPluginMessage('You have successfully marked <b><u>' . count($ids) . '</u></b> message(s) as read.', 'passed');
		}
		else
		{
			$this->addPluginMessage("No messages selected.", "warning");
		}

		return App::redirect(Route::url($member->link() . '&active=messages&task=' . Request::getWord('activetab', 'inbox') . '&start=' . $start . '&limit=' . $limit));
	}

	/**
	 * Mark messages as unread
	 *
	 * @param      object  $database JDatabase
	 * @param      string  $option   Name of the component
	 * @param      object  $member   Current member
	 * @return     void
	 */
	public function markasunread($database, $option, $member)
	{
		$limit = Request::getInt('limit', Config::get('list_limit'));
		$start = Request::getInt('limitstart', 0);
		$ids   = Request::getVar('mid', array());

		if (count($ids) > 0)
		{
			// Check for request forgeries
			Request::checkToken(['get', 'post']);

			$sql = "DELETE FROM `#__xmessage_seen` WHERE `uid`=" . $member->get('id') . " AND `mid` IN(" . implode(',', $ids) . ")";
			$database = App::get('db');
			$database->setQuery($sql);
			$database->query();

			$this->addPluginMessage('You have successfully marked <b><u>' . count($ids) . '</u></b> message(s) as unread.', 'passed');
		}
		else
		{
			$this->addPluginMessage("No messages selected.", "warning");
		}

		return App::redirect(Route::url($member->link() . '&active=messages&task=' . Request::getWord('activetab', 'inbox') . '&start=' . $start . '&limit=' . $limit));
	}

	/**
	 * Save settings
	 *
	 * @param      object  $database JDatabase
	 * @param      string  $option   Name of the component
	 * @param      object  $member   Current member
	 * @return     void
	 */
	public function savesettings($database, $option, $member)
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		//$override = Request::getInt('override',0);
		$settings = Request::getVar('settings',array());
		$ids = Request::getVar('ids',array());

		// Ensure we have data to work with
		if ($settings && count($settings) > 0)
		{
			// Loop through each setting
			foreach ($settings as $key=>$value)
			{
				foreach ($value as $v)
				{
					if ($v)
					{
						// Instantiate a Notify object and set its values
						$notify = new \Hubzero\Message\Notify($database);
						$notify->uid = $member->get('id');
						$notify->method = $v;
						$notify->type = $key;
						$notify->priority = 1;
						// Do we have an ID for this setting?
						// Determines if the store() method is going to INSERT or UPDATE
						if ($ids[$key][$v] > 0)
						{
							$notify->id = $ids[$key][$v];
							$ids[$key][$v] = -1;
							//echo 'updated: '.$key.':'.$v.'<br />';
						//} else {
							//echo 'created: '.$key.':'.$v.'<br />';
						}
						// Save
						if (!$notify->store())
						{
							$this->setError(Lang::txt('PLG_MEMBERS_MESSAGES_ERROR_NOTIFY_FAILED', $notify->method));
						}
					}
				}
			}

			$notify = new \Hubzero\Message\Notify($database);
			foreach ($ids as $key=>$value)
			{
				foreach ($value as $k=>$v)
				{
					if ($v > 0)
					{
						$notify->delete($v);
						//echo 'deleted: '.$v.'<br />';
					}
				}
			}

			// If they previously had everything turned off, we need to remove that entry saying so
			$records = $notify->getRecords($member->get('id'), 'all');
			if ($records)
			{
				foreach ($records as $record)
				{
					$notify->delete($record->id);
				}
			}
		}
		else
		{
			// This creates a single entry to let the system know that the user has explicitly chosen "none" for all options
			// It ensures we can know the difference between someone who has never changed their settings (thus, no database entries)
			// and someone who purposely wants everything turned off.
			$notify = new \Hubzero\Message\Notify($database);
			$notify->uid = $member->get('id');

			$records = $notify->getRecords($member->get('id'), 'all');
			if (!$records)
			{
				$notify->clearAll();
				$notify->method = 'none';
				$notify->type = 'all';
				$notify->priority = 1;
				if (!$notify->store())
				{
					$this->setError(Lang::txt('PLG_MEMBERS_MESSAGES_ERROR_NOTIFY_FAILED', $notify->method));
				}
			}
		}

		// Push through to the settings view
		$this->addPluginMessage(Lang::txt('You have successfully saved your message settings.'), 'passed');
		return App::redirect(Route::url($member->link() . '&active=messages&action=settings'));
	}

	/**
	 * Send a message
	 *
	 * @return     mixed
	 */
	public function send($database, $option, $member)
	{
		// Ensure the user is logged in
		if (User::isGuest())
		{
			return false;
		}

		// Check for request forgeries
		Request::checkToken();

		// Incoming array of users to message
		$mbrs = array_map("trim", explode(',', Request::getVar('mbrs', array(), 'post')));

		//array to hold members
		$email_users = array();

		//
		foreach ($mbrs as $mbr)
		{
			if (is_numeric($mbr))
			{
				$email_users[] = $mbr;
			}
			else
			{
				preg_match("/\((\d+)\)/", $mbr, $matches);
				$email_users[] = $matches[1];
			}
		}

		// Incoming message and subject
		$subject = Request::getVar('subject', Lang::txt('PLG_MEMBERS_MESSAGES_SUBJECT_MESSAGE'));
		$message = Request::getVar('message', '');
		$no_html = Request::getInt('no_html', 0);

		if (!$subject || !$message)
		{
			if (!$no_html)
			{
				$this->addPluginMessage(Lang::txt('You must select a message recipient and enter a message.'), 'error');
				return $this->redirect(Route::url($member->link() . '&active=messages&action=new'));
			}
			return App::abort(500, Lang::txt('You must select a message recipient and enter a message.'));
		}

		// Build the "from" data for the e-mail
		$from = array();
		$from['name']  = $member->get('name');
		$from['email'] = $member->get('email');

		// Send the message
		if (!Event::trigger('xmessage.onSendMessage', array('member_message', $subject, $message, $from, $email_users, $option)))
		{
			$this->setError(Lang::txt('PLG_MEMBERS_MESSAGES_ERROR_MSG_USER_FAILED'));
		}

		// Determine if we're returning HTML or not
		// (if no - this is an AJAX call)
		if (!$no_html)
		{
			$this->addPluginMessage(Lang::txt('You have successfully sent a message.'), 'passed');
			return App::redirect(Route::url($member->link() . '&active=messages&task=inbox'));
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
			$name = \Hubzero\Utility\String::obfuscate($name);

			$href = 'mailto:' . $name;
		}
		$l = sprintf(
			' <a class="ext-link" href="%s" rel="external">%s</a>', $href, $name
		);
		return $l;
	}

	/**
	 * Build a select list of methods
	 *
	 * @param      array  $notimethods Methods
	 * @param      string $name        Field name
	 * @param      array  $values      Option values
	 * @param      array  $ids         Option IDs
	 * @return     string
	 */
	public static function selectMethod($notimethods, $name, $values=array(), $ids=array())
	{
		$out = '';
		$i = 0;
		foreach ($notimethods as $notimethod)
		{
			$out .= '<td>' . "\n";
			$out .= "\t" . '<input type="checkbox" name="settings[' . $name . '][]" class="opt-' . $notimethod . '" value="' . $notimethod . '"';
			$out .= (in_array($notimethod, $values))
						  ? ' checked="checked"'
						  : '';
			$out .= ' />' . "\n";
			$out .= "\t" . '<input type="hidden" name="ids[' . $name . '][' . $notimethod . ']" value="';
			if (isset($ids[$notimethod]))
			{
				$out .= $ids[$notimethod];
			}
			else
			{
				$out .= '0';
			}
			$out .= '" />' . "\n";
			$out .= "\t" . '</td>' . "\n";
			$i++;
		}
		return $out;
	}
}
