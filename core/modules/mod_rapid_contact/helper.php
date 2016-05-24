<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
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

namespace Modules\RapidContact;

use Hubzero\Module\Module;
use Hubzero\Utility\Sanitize;
use Hubzero\Mail\Message;
use Request;
use Config;
use Lang;

/**
 * Module class for displaying a quick contact form
 */
class Helper extends Module
{
	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		// Field labels
		$this->name_label    = $this->params->get('name_label', Lang::txt('MOD_RAPID_CONTACT_FIELD_NAME'));
		$this->email_label   = $this->params->get('email_label', Lang::txt('MOD_RAPID_CONTACT_FIELD_EMAIL'));
		$this->subject_label = $this->params->get('subject_label', Lang::txt('MOD_RAPID_CONTACT_FIELD_SUBJECT'));
		$this->message_label = $this->params->get('message_label', Lang::txt('MOD_RAPID_CONTACT_FIELD_MESSAGE'));

		// Button text
		$this->button_text   = $this->params->get('button_text', Lang::txt('MOD_RAPID_CONTACT_SEND'));

		// Pre text
		$this->pre_text      = $this->params->get('pre_text', '');

		// Thank you message
		$this->page_text     = $this->params->get('page_text', Lang::txt('MOD_RAPID_CONTACT_THANK_YOU'));

		// Error messages
		$this->error_text    = $this->params->get('error_text', Lang::txt('MOD_RAPID_CONTACT_ERROR_SENDING'));
		$this->no_email      = $this->params->get('no_email', Lang::txt('MOD_RAPID_CONTACT_ERROR_NO_EMAIL'));
		$this->invalid_email = $this->params->get('invalid_email', Lang::txt('MOD_RAPID_CONTACT_ERROR_INVALID_EMAIL'));

		// From
		$this->from_name     = $this->params->get('from_name', Lang::txt('MOD_RAPID_CONTACT'));
		$this->from_email    = $this->params->get('from_email', 'rapid_contact@yoursite.com');

		// To
		$this->recipient     = $this->params->get('email_recipient', Config::get('mailfrom'));
		if (!trim($this->recipient))
		{
			$this->recipient = Config::get('mailfrom');
		}

		// Enable Anti-spam?
		$this->enable_anti_spam = $this->params->get('enable_anti_spam', true);
		$this->anti_spam_q   = $this->params->get('anti_spam_q', Lang::txt('MOD_RAPID_CONTACT_ANTIPSAM'));
		$this->anti_spam_a   = $this->params->get('anti_spam_a', '2');

		$this->mod_class_suffix = $this->params->get('moduleclass_sfx', '');

		$disable_https       = $this->params->get('disable_https', false);
		$exact_url           = $this->params->get('exact_url', true);
		if (!$exact_url)
		{
			//$this->url = $this->_cleanXss(filter_var(Request::current(), FILTER_SANITIZE_URL));
			$this->url = Request::current();
		}
		else
		{
			if (!$disable_https)
			{
				$this->url = (!empty($_SERVER['HTTPS'])) ? 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] : 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			}
			else
			{
				$this->url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			}
		}

		//$qs = str_replace(array('"', '?'), '', urldecode($_SERVER['QUERY_STRING']));
		//$aqs = explode('?', $this->url);
		//$this->url = $aqs[0] . '?' . urlencode($qs);

		$fixed_url = $this->params->get('fixed_url', true);
		if ($fixed_url)
		{
			$this->url = $this->params->get('fixed_url_address', '');
		}

		$this->error = '';
		$this->replacement = '';

		$this->posted = array(
			'name'    => '',
			'email'   => '',
			'subject' => '',
			'message' => ''
		);

		if (isset($_POST['rp']))
		{
			$this->posted = Request::getVar('rp', array(), 'post');

			if ($this->enable_anti_spam)
			{
				if (!isset($this->posted['anti_spam_answer']) || ($this->posted['anti_spam_answer'] != $this->anti_spam_a))
				{
					$this->error = Lang::txt('MOD_RAPID_CONTACT_INVALID_ANTIPSAM_ANSWER');
				}
			}
			if ($this->posted['email'] === '')
			{
				$this->error = $this->no_email;
			}
			if (!preg_match("#^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$#i", $this->posted['email']))
			{
				$this->error = $this->invalid_email;
			}

			if ($this->error == '')
			{
				$mySubject  = Sanitize::clean($this->posted['subject']);
				$myMessage  = Lang::txt('MOD_RAPID_CONTACT_MESSAGE_FROM', $this->posted['name'], $this->posted['email'], Request::getVar('HTTP_REFERER', '', 'SERVER'), Config::get('sitename'));
				$myMessage .= "\n\n". Sanitize::clean($this->posted['message']);

				$this->from_email = $this->posted['email'];
				$this->from_name  = (isset($this->posted['name']) && Sanitize::clean($this->posted['name'])) ? Sanitize::clean($this->posted['name']) : $this->posted['email'];

				$mailSender = new Message();
				$mailSender->setSubject($mySubject)
				           ->addFrom($this->from_email, $this->from_name)
				           ->addTo($this->recipient)
				           ->addReplyTo($this->posted['email'], $this->posted['name'])
				           ->setBody($myMessage);

				if (!$mailSender->send())
				{
					$this->error = $this->error_text;
				}
				else
				{
					$this->replacement = $this->page_text;
				}

				// Reset the message field
				$this->posted['subject'] = '';
				$this->posted['message'] = '';
			}
		}

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}
}
