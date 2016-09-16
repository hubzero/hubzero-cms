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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

			$filters = array(
				'limit' => Request::getState(
					$option . '.plugin.messages.limit',
					'limit',
					Config::get('list_limit'),
					'int'
				),
				'start' => Request::getState(
					$option . '.plugin.messages.limitstart',
					'limitstart',
					0,
					'int'
				)
			);

			$notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();

			$view = $this->view('default', 'default')
				->set('option', $option)
				->set('member', $member)
				->set('task', $task)
				->set('filters', $filters)
				->set('body', $body)
				->set('notifications', $notifications);

			$arr['html'] = $view->loadTemplate();
		}

		//get meta
		$arr['metadata'] = array();

		//get the number of unread messages
		$recipient = Hubzero\Message\Recipient::blank();
		$inboxCount = $recipient->getMessagesCount($member->get('id'), array('state' => 0));
		$unreadMessages = $recipient->getUnreadMessages($member->get('id'), 0);

		//return total message count
		$arr['metadata']['count'] = $inboxCount;

		//if we have unread messages show alert
		if ($unreadMessages->count() > 0)
		{
			$title = $unreadMessages->count() . ' unread message(s).';
			$link = Route::url($member->link() . '&active=messages');
			$arr['metadata']['alert'] = "<a class=\"alrt\" href=\"{$link}\"><span><strong>Messages Alert</strong>{$title}</span></a>";
		}

		// Return data
		return $arr;
	}

	/**
	 * Show inbox
	 *
	 * @param   object  $database  Database
	 * @param   string  $option    Name of the component
	 * @param   object  $member    Current member
	 * @return  string
	 */
	public function inbox($database, $option, $member)
	{
		// Filters for returning results
		$filters = array(
			'limit' => Request::getState(
				$option . '.plugin.messages.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$option . '.plugin.messages.limitstart',
				'limitstart',
				0,
				'int'
			),
			'state'  => 0,
			'filter' => Request::getVar('filter', '')
		);

		$filters['filter'] = ($filters['filter'] ? 'com_' . $filters['filter'] : '');

		// Retrieve data
		$recipient = Hubzero\Message\Recipient::blank();

		$total = $recipient->getMessagesCount($member->get('id'), $filters);

		$rows = $recipient->getMessages($member->get('id'), $filters);

		$components = Hubzero\Message\Component::blank()->getComponents();

		// Output view
		$view = $this->view('inbox', 'default')
			->set('option', $option)
			->set('member', $member)
			->set('filters', $filters)
			->set('components', $components)
			->set('total', $total)
			->set('rows', $rows);

		return $view->loadTemplate();
	}

	/**
	 * Show archived messages
	 *
	 * @param   object  $database  Database
	 * @param   string  $option    Name of the component
	 * @param   object  $member    Current member
	 * @return  string
	 */
	public function archive($database, $option, $member)
	{
		// Filters for returning results
		$filters = array(
			'limit' => Request::getState(
				$option . '.plugin.messages.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$option . '.plugin.messages.limitstart',
				'limitstart',
				0,
				'int'
			),
			'state'  => 1,
			'filter' => Request::getVar('filter', '')
		);

		$filters['filter'] = ($filters['filter'] ? 'com_' . $filters['filter'] : '');

		// Retrieve data
		$recipient = Hubzero\Message\Recipient::blank();

		$total = $recipient->getMessagesCount($member->get('id'), $filters);

		$rows = $recipient->getMessages($member->get('id'), $filters);

		$components = Hubzero\Message\Component::blank()->getComponents();

		// Output view
		$view = $this->view('archive', 'default')
			->set('option', $option)
			->set('member', $member)
			->set('filters', $filters)
			->set('components', $components)
			->set('total', $total)
			->set('rows', $rows);

		return $view->loadTemplate();
	}

	/**
	 * Show trashed messages
	 *
	 * @param   object  $database  Database
	 * @param   string  $option    Name of the component
	 * @param   object  $member    Current member
	 * @return  string
	 */
	public function trash($database, $option, $member)
	{
		// Filters for returning results
		$filters = array(
			'limit' => Request::getState(
				$option . '.plugin.messages.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$option . '.plugin.messages.limitstart',
				'limitstart',
				0,
				'int'
			),
			'state'  => 2,
			'filter' => Request::getVar('filter', '')
		);

		$filters['filter'] = ($filters['filter'] ? 'com_' . $filters['filter'] : '');

		// Retrieve data
		$recipient = Hubzero\Message\Recipient::blank();

		$total = $recipient->getMessagesCount($member->get('id'), $filters);

		$rows = $recipient->getMessages($member->get('id'), $filters);

		$components = Hubzero\Message\Component::blank()->getComponents();

		// Output view
		$view = $this->view('trash', 'default')
			->set('option', $option)
			->set('member', $member)
			->set('filters', $filters)
			->set('components', $components)
			->set('total', $total)
			->set('rows', $rows);

		return $view->loadTemplate();
	}

	/**
	 * Show sent messages
	 *
	 * @param   object  $database  Database
	 * @param   string  $option    Name of the component
	 * @param   object  $member    Current member
	 * @return  string
	 */
	public function sent($database, $option, $member)
	{
		// Filters for returning results
		$filters = array(
			'limit' => Request::getState(
				$option . '.plugin.messages.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$option . '.plugin.messages.limitstart',
				'limitstart',
				0,
				'int'
			),
			'created_by' => $member->get('id')
		);

		$recipient = Hubzero\Message\Message::blank();

		$total = $recipient->getSentMessagesCount($filters);

		$rows = $recipient->getSentMessages($filters);

		// Output view
		$view = $this->view('sent', 'default')
			->set('option', $option)
			->set('member', $member)
			->set('filters', $filters)
			->set('total', $total)
			->set('rows', $rows);

		return $view->loadTemplate();
	}

	/**
	 * Show a form for settings
	 *
	 * @param   object  $database  Database
	 * @param   string  $option    Name of the component
	 * @param   object  $member    Current member
	 * @return  string
	 */
	public function settings($database, $option, $member)
	{
		$xmc = Hubzero\Message\Component::blank();
		$components = $xmc->getRecords();

		$view = $this->view('settings', 'default')
			->set('option', $option)
			->set('member', $member)
			->set('components', $components);

		if (!$components->count())
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
			$settings[$component->get('action')] = array();
		}

		// Fetch message methods
		$notimethods = Event::trigger('xmessage.onMessageMethods', array());

		// A var for storing the default notification method
		$default_method = null;

		// Instantiate our notify object
		$notify = Hubzero\Message\Notify::blank();

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

		$view->set('settings', $settings);
		$view->set('notimethods', $notimethods);

		return $view->loadTemplate();
	}

	/**
	 * Create a message
	 *
	 * @param   object  $database  Database
	 * @param   string  $option    Name of the component
	 * @param   object  $member    Current member
	 * @return  string
	 */
	private function create($database, $option, $member)
	{
		// list of message to's
		$tos = array();

		// get members name and id
		$mbrs = Request::getVar('to', array());
		foreach ($mbrs as $mbr)
		{
			$mem = User::getInstance($mbr);
			$tos[] = $mem->get('name') . ' (' . $mem->get('id') . ')';
		}

		$view = $this->view('create', 'default')
			->set('option', $option)
			->set('member', $member)
			->set('tos', implode(',', $tos))
			->set('no_html', Request::getInt('no_html', 0))
			->setErrors($this->getErrors());

		return $view->loadTemplate();
	}

	/**
	 * View a message
	 *
	 * @param   object   $database  Database
	 * @param   string   $option    Name of the component
	 * @param   object   $member    Current member
	 * @param   integer  $mid       Message ID
	 * @return  string
	 */
	public function message($database, $option, $member, $mid)
	{
		$xmessage = Hubzero\Message\Message::oneOrFail($mid);

		$recipient = Hubzero\Message\Recipient::oneByMessageAndUser($mid, $member->get('id'));

		if (substr($xmessage->get('component'),0,4) == 'com_')
		{
			$xmessage->set('component', substr($xmessage->get('component'), 4));
		}

		if (User::get('id') == $member->get('id'))
		{
			if (!$recipient->markAsRead())
			{
				$this->setError($recipient->getError());
			}
		}

		$view = $this->view('message', 'default')
			->set('option', $option)
			->set('member', $member)
			->set('xmr', $recipient)
			->set('xmessage', $xmessage)
			->setErrors($this->getErrors());

		return $view->loadTemplate();
	}

	/**
	 * Move message to archive
	 *
	 * @param   object  $database  Database
	 * @param   string  $option    Name of the component
	 * @param   object  $member    Current member
	 * @return  void
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
				$recipient = Hubzero\Message\Recipient::oneByMessageAndUser($mid, $member->get('id'));
				$recipient->set('mid', $mid);
				$recipient->set('uid', $member->get('id'));
				$recipient->set('state', 1);

				if (!$recipient->save())
				{
					$this->setError($recipient->getError());
					continue;
				}

				if (!$recipient->markAsRead())
				{
					$this->setError($recipient->getError());
					continue;
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
	 * @param   object  $database  Database
	 * @param   string  $option    Name of the component
	 * @param   object  $member    Current member
	 * @return  void
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
				$recipient = Hubzero\Message\Recipient::oneByMessageAndUser($mid, $member->get('id'));
				$recipient->set('mid', $mid);
				$recipient->set('uid', $member->get('id'));
				$recipient->set('state', 0);

				if (!$recipient->save())
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
	 * @param   object  $database  Database
	 * @param   string  $option    Name of the component
	 * @param   object  $member    Current member
	 * @return  void
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
				$recipient = Hubzero\Message\Recipient::oneByMessageAndUser($mid, $member->get('id'));
				$recipient->set('mid', $mid);
				$recipient->set('uid', $member->get('id'));
				$recipient->set('state', 2);
				$recipient->set('expires', Date::of(time()+(10*60*60*60))->toSql());

				if (!$recipient->save())
				{
					$this->setError($recipient->getError());
					continue;
				}

				if (!$recipient->markAsRead())
				{
					$this->setError($recipient->getError());
					continue;
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
	 * @param   object  $database  Database
	 * @param   string  $option    Name of the component
	 * @param   object  $member    Current member
	 * @return  void
	 */
	public function emptytrash($database, $option, $member)
	{
		$recipient = Hubzero\Message\Recipient::blank();

		if (!$recipient->deleteTrash($member->get('id')))
		{
			$this->setError($recipient->getError());
		}

		return $this->trash($database, $option, $member);
	}

	/**
	 * Delete a message
	 *
	 * @param   object  $database  Database
	 * @param   string  $option    Name of the component
	 * @param   object  $member    Current member
	 * @return  void
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
				$recipient = Hubzero\Message\Recipient::oneByMessageAndUser($mid, $member->get('id'));
				if (!$recipient->get('id'))
				{
					// User isn't a recipient
					// This shouldn't ever happen
					continue;
				}
				if (!$recipient->destroy())
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
	 * @param   object  $database  Database
	 * @param   string  $option    Name of the component
	 * @param   object  $member    Current member
	 * @return  void
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
				$recipient = Hubzero\Message\Recipient::oneByMessageAndUser($mid, $member->get('id'));
				if (!$recipient->get('id'))
				{
					// User isn't a recipient
					// This shouldn't ever happen
					continue;
				}
				if (!$recipient->markAsRead())
				{
					$this->setError($recipient->getError());
					continue;
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
	 * @param   object  $database  Database
	 * @param   string  $option    Name of the component
	 * @param   object  $member    Current member
	 * @return  void
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

			foreach ($ids as $mid)
			{
				$recipient = Hubzero\Message\Recipient::oneByMessageAndUser($mid, $member->get('id'));

				if (!$recipient->markAsUnread())
				{
					$this->setError($recipient->getError());
					continue;
				}
			}
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
	 * @param   object  $database  Database
	 * @param   string  $option    Name of the component
	 * @param   object  $member    Current member
	 * @return  void
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
			foreach ($settings as $key => $value)
			{
				foreach ($value as $v)
				{
					if ($v)
					{
						// Instantiate a Notify object and set its values
						$notify = Hubzero\Message\Notify::blank();
						$notify->set('uid', $member->get('id'));
						$notify->set('method', $v);
						$notify->set('type', $key);
						$notify->set('priority', 1);

						// Do we have an ID for this setting?
						// Determines if the save() method is going to INSERT or UPDATE
						if ($ids[$key][$v] > 0)
						{
							$notify->set('id', $ids[$key][$v]);
							$ids[$key][$v] = -1;
						}

						// Save
						if (!$notify->save())
						{
							$this->setError(Lang::txt('PLG_MEMBERS_MESSAGES_ERROR_NOTIFY_FAILED', $notify->get('method')));
						}
					}
				}
			}

			foreach ($ids as $key => $value)
			{
				foreach ($value as $k => $v)
				{
					if ($v > 0)
					{
						$notify = Hubzero\Message\Notify::oneOrNew($v);
						$notify->destroy();
					}
				}
			}

			// If they previously had everything turned off, we need to remove that entry saying so
			$records = $notify->getRecords($member->get('id'), 'all');

			if ($records->count())
			{
				foreach ($records as $record)
				{
					$record->destroy();
				}
			}
		}
		else
		{
			// This creates a single entry to let the system know that the user has explicitly chosen "none" for all options
			// It ensures we can know the difference between someone who has never changed their settings (thus, no database entries)
			// and someone who purposely wants everything turned off.
			$notify = Hubzero\Message\Notify::blank();
			$notify->set('uid', $member->get('id'));

			$records = $notify->getRecords($member->get('id'), 'all');

			if (!$records->count())
			{
				$notify->deleteByUser($member->get('id'));

				$notify->set('uid', $member->get('id'));
				$notify->set('method', 'none');
				$notify->set('type', 'all');
				$notify->set('priority', 1);

				if (!$notify->save())
				{
					$this->setError(Lang::txt('PLG_MEMBERS_MESSAGES_ERROR_NOTIFY_FAILED', $notify->get('method')));
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
	 * @param   object  $database  Database
	 * @param   string  $option    Name of the component
	 * @param   object  $member    Current member
	 * @return  mixed
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
			// User ID
			if (is_numeric($mbr))
			{
				$email_users[] = $mbr;
			}
			// Some Name (###)
			else if (preg_match("/\((\d+)\)/", $mbr, $matches))
			{
				preg_match("/\((\d+)\)/", $mbr, $matches);
				$email_users[] = $matches[1];
			}
			else
			{
				// Username?
				$usr = User::getInstance($mbr);

				if ($id = $usr->get('id'))
				{
					$email_users[] = $id;
				}
				else
				{
					// User not found
					// Maybe it was a group?
					$grp = Hubzero\User\Group::getInstance($mbr);

					if ($grp && $grp->get('gidNumber'))
					{
						$email_users = array_merge($email_users, $grp->get('members'));
					}
				}
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
	 * Build a select list of methods
	 *
	 * @param   array   $notimethods  Methods
	 * @param   string  $name         Field name
	 * @param   array   $values       Option values
	 * @param   array   $ids          Option IDs
	 * @return  string
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
