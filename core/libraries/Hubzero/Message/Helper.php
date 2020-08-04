<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Message;

use Hubzero\Base\Obj;
use Event;
use Lang;
use User;
use Date;

/**
 * Hubzero message class for handling message routing
 */
class Helper extends Obj
{
	/**
	 * Marks action items as completed
	 *
	 * @param   string   $type       Item type
	 * @param   array    $uids       User IDs
	 * @param   string   $component  Item component
	 * @param   string   $element    Element
	 * @return  boolean  True if no errors
	 */
	public function takeAction($type, $uids=array(), $component='', $element=null)
	{
		// Do we have the proper bits?
		if (!$element || !$component || !$type)
		{
			return false;
		}

		// Do we have any user IDs?
		if (count($uids) > 0)
		{
			// Loop through each ID
			foreach ($uids as $uid)
			{
				// Find any actions the user needs to take for this $component and $element
				$action = Action::blank();
				$mids = $action->getActionItems($component, $element, $uid, $type);

				// Check if the user has any action items
				if (count($mids) > 0)
				{
					$recipient = Recipient::blank();
					if (!$recipient->setState(1, $mids))
					{
						$this->setError(Lang::txt('Unable to update recipient records %s for user %s', implode(',', $mids), $uid));
					}
				}
			}
		}

		return true;
	}

	/**
	 * Send a message to one or more users
	 *
	 * @param   string   $type         Message type (maps to #__xmessage_component table)
	 * @param   string   $subject      Message subject
	 * @param   string   $message      Message to send
	 * @param   array    $from         Message 'from' data (e.g., name, address)
	 * @param   array    $to           List of user IDs
	 * @param   string   $component    Component name
	 * @param   integer  $element      ID of object that needs an action item
	 * @param   string   $description  Action item description
	 * @param   integer  $group_id     Parameter description (if any) ...
	 * @return  mixed    True if no errors else error message
	 */
	public function sendMessage($type, $subject, $message, $from=array(), $to=array(), $component='', $element=null, $description='', $group_id=0)
	{
		// Do we have a message?
		if (!$message)
		{
			return false;
		}

		// Do we have a subject line? If not, create it from the message
		if (!$subject && $message)
		{
			$subject = substr($message, 0, 70);
			if (strlen($subject) >= 70)
			{
				$subject .= '...';
			}
		}

		// Create the message object and store it in the database
		$xmessage = Message::blank();
		$xmessage->set('subject', $subject);
		$xmessage->set('message', $message);
		$xmessage->set('created', Date::toSql());
		$xmessage->set('created_by', User::get('id'));
		$xmessage->set('component', $component);
		$xmessage->set('type', $type);
		$xmessage->set('group_id', $group_id);

		if (!$xmessage->save())
		{
			return $xmessage->getError();
		}

		// Does this message require an action?
		// **DEPRECATED**
		/*$action = new Action($database);
		if ($element || $description)
		{
			$action->class       = $component;
			$action->element     = $element;
			$action->description = $description;
			if (!$action->store())
			{
				return $action->getError();
			}
		}*/

		// Do we have any recipients?
		if (count($to) > 0)
		{
			// Loop through each recipient
			foreach ($to as $uid)
			{
				// Create a recipient object that ties a user to a message
				$recipient = Recipient::blank();
				$recipient->set('uid', $uid);
				$recipient->set('mid', $xmessage->get('id'));
				$recipient->set('created', Date::toSql());
				$recipient->set('expires', Date::of(time() + (168 * 24 * 60 * 60))->toSql());
				$recipient->set('actionid', 0); //$action->id
				if (!$recipient->save())
				{
					return $recipient->getError();
				}

				// Get the user's methods for being notified
				$notify = Notify::blank();
				$methods = $notify->getRecords($uid, $type);

				$user = User::getInstance($uid);

				// Do we have any methods?
				if ($methods)
				{
					// Loop through each method
					foreach ($methods as $method)
					{
						$action = strtolower($method->method);

						if (!Event::trigger('xmessage.onMessage', array($from, $xmessage, $user, $action)))
						{
							$this->setError(Lang::txt('Unable to message user %s with method %s', $uid, $action));
						}
					}
				}
			}
		}

		return true;
	}
}
