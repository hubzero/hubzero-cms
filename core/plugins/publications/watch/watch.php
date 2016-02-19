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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param   object  $publication  Current publication
	 * @return  array
	 */
	public function &onPublicationSubAreas($publication)
	{
		$areas = array();

		if ($publication->category()->_params->get('plg_watch', 1) == 1)
		{
			$areas['watch'] = Lang::txt('PLG_PUBLICATION_WATCH');
		}

		return $areas;
	}

	/**
	 * Return data on a publication sub view (this will be some form of HTML)
	 *
	 * @param   object   $publication  Current publication
	 * @param   string   $option       Name of the component
	 * @param   integer  $miniview     View style
	 * @return  array
	 */
	public function onPublicationSub($publication, $option, $miniview=0)
	{
		$arr = array(
			'html'    => '',
			'metadata'=> '',
			'name'    => 'watch'
		);

		// Check if our area is in the array of areas we want to return results for
		$areas = array('watch');
		if (!array_intersect($areas, $this->onPublicationSubAreas($publication))
		 && !array_intersect($areas, array_keys($this->onPublicationSubAreas($publication))))
		{
			return false;
		}

		// Only show for logged-in users
		if (User::isGuest())
		{
			return false;
		}

		$this->publication = $publication;
		$this->action = strtolower(Request::getWord('action', ''));

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
	 * @return  string  HTML
	 */
	private function _status()
	{
		// Instantiate a view
		$view = $this->view('default', 'index')
			->set('publication', $this->publication)
			->set('watched', \Hubzero\Item\Watch::isWatching(
				$this->publication->get('id'),
				'publication',
				User::get('id')
			));

		// Return the output
		return $view->loadTemplate();
	}

	/**
	 * Subscribe
	 *
	 * @return  string  HTML
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
			$watch = \Hubzero\Item\Watch::oneByScope(
				$this->publication->get('id'),
				'publication',
				User::get('id'),
				$email
			);

			if ($this->action == 'unsubscribe' && !$watch->get('id'))
			{
				App::redirect(
					Route::url($this->publication->link()),
					Lang::txt('PLG_PUBLICATIONS_WATCH_FAIL_UNSUBSCRIBE'),
					'error'
				);
			}

			$watch->set('item_id', $this->publication->get('id'));
			$watch->set('item_type', 'publication');
			$watch->set('created_by', User::get('id'));
			$watch->set('state', ($this->action == 'subscribe' ? 1 : 2));
			$watch->save();

			if ($err = $watch->getError())
			{
				App::redirect(
					Route::url($this->publication->link()),
					$err,
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
	 * @param   object  $publication  Publication model
	 * @param   string  $activity
	 * @return  void
	 */
	public function onWatch($publication, $activity = 'newversion')
	{
		$this->publication = $publication;

		// Get subscribers
		$subscribers = \Hubzero\Item\Watch::all()
			->whereEquals('item_type', 'publication')
			->whereEquals('item_id', $publication->get('id'))
			->whereEquals('state', 1)
			->rows();

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
	 * @param   object  $subscriber
	 * @param   string  $message
	 * @param   string  $subject
	 * @param   string  $url
	 * @return  bool
	 */
	private function _sendEmail($subscriber, $message, $subject, $url)
	{
		$eview = new \Hubzero\Mail\View(array(
			'base_path' => PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'site',
			'name'   => 'emails',
			'layout' => 'watch_plain'
		));
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

		$plain = $eview->loadTemplate(false);
		$plain = str_replace("\n", "\r\n", $plain);

		// HTML
		$eview->setLayout('watch_html');

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

			return false;
		}

		return true;
	}
}