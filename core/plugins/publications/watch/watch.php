<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Display Watch feature on publication page
 */
class plgPublicationsWatch extends \Hubzero\Plugin\Plugin
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
	 * @param      object $publication 	Current publication
	 * @return     array
	 */
	public function &onPublicationSubAreas( $publication )
	{
		$areas = array(
			'watch' => Lang::txt('PLG_PUBLICATION_WATCH')
		);
		return $areas;
	}

	/**
	 * Return data on a publication sub view (this will be some form of HTML)
	 *
	 * @param      object  $publication 	Current publication
	 * @param      string  $option    		Name of the component
	 * @param      integer $miniview  		View style
	 * @return     array
	 */
	public function onPublicationSub( $publication, $option, $miniview=0 )
	{
		$arr = array(
			'html'    => '',
			'metadata'=> '',
			'name'    => 'watch'
		);

		// Only show for logged-in users
		if (User::isGuest())
		{
			return false;
		}

		$this->database = App::get('db');
		$this->publication = $publication;

		// Item watch class
		$this->watch   = new \Hubzero\Item\Watch($this->database);
		$this->action  = strtolower(Request::getWord('action', ''));

		switch ($this->action)
		{
			case 'subscribe':
			case 'unsubscribe':
				$arr['html'] = $this->_subscribe();
			break;

			default:
				$arr['html'] = $this->_status();
			break;
		}

		return $arr;
	}

	/**
	 * Show subscription status
	 *
	 * @return  HTML
	 */
	private function _status()
	{
		// Instantiate a view
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'publications',
				'element' =>'watch',
				'name'    =>'index'
			)
		);

		$view->publication = $this->publication;

		// Is user watching item?
		$view->watched = $this->watch->isWatching(
			$this->publication->get('id'),
			'publication',
			User::get('id')
		);

		// Return the output
		return $view->loadTemplate();
	}

	/**
	 * Subscribe
	 *
	 * @return  HTML
	 */
	private function _subscribe()
	{
		// Incoming
		$confirm = Request::getInt('confirm', 0);
		$email   = Request::getVar('email', '');

		// Login required
		if (User::isGuest() || !$this->publication->exists())
		{
			App::redirect(
				Route::url($this->publication->link())
			);
		}

		// Save subscription
		if ($confirm)
		{
			$this->watch->loadRecord(
				$this->publication->get('id'),
				'publication',
				User::get('id'),
				$email
			);
			if ($this->action == 'unsubscribe' && !$this->watch->id)
			{
				App::redirect(
					Route::url($this->publication->link()),
					Lang::txt('PLG_PUBLICATIONS_WATCH_FAIL_UNSUBSCRIBE'),
					'error'
				);
			}
			$this->watch->item_id    = $this->publication->get('id');
			$this->watch->item_type  = 'publication';
			$this->watch->created_by = User::get('id');
			$this->watch->state      = $this->action == 'subscribe' ? 1 : 2;
			if ($this->watch->check())
			{
				$this->watch->store();
			}

			if ($this->watch->getError())
			{
				App::redirect(
					Route::url($this->publication->link()),
					$this->watch->getError(),
					'error'
				);
			}
			else
			{
				$msg = $this->action == 'subscribe'
					? Lang::txt('PLG_PUBLICATIONS_WATCH_SUCCESS_SUBSCRIBED')
					: Lang::txt('PLG_PUBLICATIONS_WATCH_SUCCESS_UNSUBSCRIBED');

				App::redirect(
					Route::url($this->publication->link()),
					$msg
				);
			}
		}
	}

	/**
	 * Notify subscribers of new activity
	 *
	 * @param      object  $publication 	Publication model
	 * @return     array
	 */
	public function onWatch( $publication, $activity = 'newversion')
	{
		$database = App::get('db');
		$this->publication = $publication;

		// Item watch class
		$watch   = new \Hubzero\Item\Watch($database);

		$filters = array(
			'item_type' => 'publication',
			'item_id'   => $publication->get('id'),
			'state'     => 1
		);

		// Get subscribers
		$subscribers = $watch->getRecords($filters);

		// Determine message and url
		switch ($activity)
		{
			case 'newversion':
				$message = Lang::txt('PLG_PUBLICATIONS_WATCH_MESSAGE_NEWVERSION');
				$subject = Lang::txt('PLG_PUBLICATIONS_WATCH_PUBLICATIONS') . ': ' . Lang::txt('PLG_PUBLICATIONS_WATCH_SUBJECT_EMAIL');
				$url = Route::url($this->publication->link('version'));
			break;
		}

		// Do we have subscribers?
		if (!empty($message) && count($subscribers) > 0)
		{
			foreach ($subscribers as $subscriber)
			{
				// Check that user wants to receive update on specific activity
				// TBD

				// Send message
				if ($subscriber->email)
				{
					$this->_sendEmail($subscriber, $message, $subject, $url);
				}
			}
		}

		return;
	}

	/**
	 * Handles the actual sending of emails
	 *
	 * @return bool
	 **/
	private function _sendEmail($subscriber, $message, $subject, $url)
	{
		$eview = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'publications',
				'element' =>'watch',
				'name'    =>'emails',
				'layout'  =>'_plain'
			)
		);
		$eview->delimiter   = '~!~!~!~!~!~!~!~!~!~!';
		$eview->message     = $message;
		$eview->subject     = $subject;
		$eview->publication = $this->publication;
		$eview->url         = $url;

		$name = Config::get('sitename') . ' ' . Lang::txt('PLG_PUBLICATIONS_WATCH_SUBSCRIBER');
		$email = $subscriber->email;

		$eview->unsubscribeLink = Route::url($this->publication->link() . '&active=watch&action=unsubscribe&confirm=1&email=' . $email);

		// Get profile information
		if ($subscriber->created_by)
		{
			$user  = User::getInstance($subscriber->created_by);
			$name  = $user ? $user->get('name') : $name;
			$email = $user ? $user->get('email') : $email;
		}

		$plain = $eview->loadTemplate();
		$plain = str_replace("\n", "\r\n", $plain);

		// HTML
		$eview->setLayout('_html');

		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		if (empty($email))
		{
			return false;
		}

		// Build message
		$message = new \Hubzero\Mail\Message();
		$message->setSubject($subject)
				->addFrom(Config::get('mailfrom'), Config::get('sitename'))
				->addTo($email, $name)
				->addHeader('X-Component', 'com_publications')
				->addHeader('X-Component-Object', 'publications_watch_email');

		$message->addPart($plain, 'text/plain');
		$message->addPart($html, 'text/html');

		// Send mail
		if (!$message->send())
		{
			$this->setError('Failed to mail %s', $email);
		}

		$mailed[] = $email;
	}
}