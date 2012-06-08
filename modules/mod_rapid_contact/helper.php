<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Module class for displaying a quick contact form
 */
class modRapidContact extends JObject
{
	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $attributes = array();

	/**
	 * Constructor
	 * 
	 * @param      object $this->params JParameter
	 * @param      object $module Database row
	 * @return     void
	 */
	public function __construct($params, $module)
	{
		$this->params = $params;
		$this->module = $module;
	}

	/**
	 * Set a property
	 * 
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}

	/**
	 * Get a property
	 * 
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->attributes[$property])) 
		{
			return $this->attributes[$property];
		}
	}

	/**
	 * Check if a property is set
	 * 
	 * @param      string $property Property to check
	 * @return     boolean True if set
	 */
	public function __isset($property)
	{
		return isset($this->_attributes[$property]);
	}

	/**
	 * Display module content
	 * 
	 * @return     void
	 */
	public function display()
	{
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStylesheet('mod_rapid_contact');

		// Field labels
		$this->name_label    = $this->params->get('name_label', 'Name:');
		$this->email_label   = $this->params->get('email_label', 'Email:');
		$this->subject_label = $this->params->get('subject_label', 'Subject:');
		$this->message_label = $this->params->get('message_label', 'Message:');

		// Button text
		$this->button_text   = $this->params->get('button_text', 'Send Message');

		// Pre text
		$this->pre_text      = $this->params->get('pre_text', '');

		// Thank you message
		$this->page_text     = $this->params->get('page_text', 'Thank you for your contact.');

		// Error messages
		$this->error_text    = $this->params->get('error_text', 'Your message could not be sent. Please try again.');
		$this->no_email      = $this->params->get('no_email', 'Please write your email');
	    $this->invalid_email = $this->params->get('invalid_email', 'Please write a valid email');

		// From
		$this->from_name     = @$this->params->get('from_name', 'Rapid Contact');
		$this->from_email    = @$this->params->get('from_email', 'rapid_contact@yoursite.com');

		// To
		$this->recipient     = $this->params->get('email_recipient', '');

		// Enable Anti-spam?
		$this->enable_anti_spam = $this->params->get('enable_anti_spam', true);
		$this->anti_spam_q   = $this->params->get('anti_spam_q', 'How many eyes has a typical person?');
		$this->anti_spam_a   = $this->params->get('anti_spam_a', '2');

	    $this->mod_class_suffix = $this->params->get('moduleclass_sfx', '');

		$disable_https       = $this->params->get('disable_https', false);
		$exact_url           = $this->params->get('exact_url', true);
		if (!$exact_url) 
		{
			$this->url = JURI::current();
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
			$this->posted = $_POST['rp'];

			if ($this->enable_anti_spam) 
			{
				if ($_POST['rp']['anti_spam_answer'] != $this->anti_spam_a) 
				{
					$this->error = JText::_('Wrong anti-spam answer');
				}
			}
			if ($_POST['rp']['email'] === '') 
			{
				$this->error = $this->no_email;
			}
			if (!preg_match("#^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$#i", $_POST['rp']['email'])) 
			{
				$this->error = $this->invalid_email;
			}
			if ($this->error == '') 
			{
				$mySubject = $_POST['rp']['subject'];
				$myMessage = 'You received a message from '. $_POST['rp']['email'] ."\n\n". $_POST['rp']['message'];

				$mailSender = &JFactory::getMailer();
				$mailSender->addRecipient($this->recipient);
				$mailSender->setSender(array($this->from_email,$this->from_name));
				$mailSender->addReplyTo(array($_POST['rp']['email'], ''));
				$mailSender->setSubject($mySubject);
				$mailSender->setBody($myMessage);

				if (!$mailSender->Send()) 
				{
					$this->error = $this->error_text;
				} 
				else 
				{
					$this->replacement = $this->page_text;
				}
			}
		}

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
