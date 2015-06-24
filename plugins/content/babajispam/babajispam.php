<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseber <nkissebe@purdue.edu>
 * @copyright Copyright 2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Babajispam Content Plugin
 */
class plgContentBabajispam extends JPlugin
{
	/**
	 * Finder before save content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param  string   $context  The context of the content passed to the plugin (added in 1.6)
	 * @param  object   $article  A JTableContent object
	 * @param  boolean  $isNew    If the content is just about to be created
	 * @since  2.5
	 */
	public function onContentBeforeSave($context, $article, $isNew)
	{
		if (JFactory::getApplication()->isAdmin()
		 || JFactory::getUser()->authorise('core.manage', JRequest::getCmd('option')))
		{
		//	return;
		}

		if ($article instanceof \Hubzero\Base\Object)
		{
			$key = $this->_key($context);

			$content = ltrim($article->get($key));
		}
		else if (is_object($article) || is_array($article))
		{
			return;
		}
		else
		{
			$content = $article;
		}

		if (!$content) return;

		$ip = JRequest::ip();
		$uid = JFactory::getUser()->get('id');
		$username = JFactory::getUser()->get('username');
		$email = JFactory::getUser()->get('email');
		$fallback = 'option=' . JRequest::getCmd('option') . '&controller=' . JRequest::getCmd('controller') . '&task=' . JRequest::getCmd('task');
		$from = JRequest::getVar('REQUEST_URI', $fallback, 'server');
		$from = $from ?: $fallback;
		$hash     = md5($content);

		$data = $this->onContentDetectSpam($content,$email,$username);

		if ($data['is_spam'])
		{
			JFactory::getSpamLogger()->info('spam ' . $data['service'] . ' ' . $data['reason'] . ' ' . $ip . ' ' . $uid . ' ' . $username . ' ' . $hash . ' ' . $from);

			if (!JFactory::getSession()->get('spam' . $hash))
			{
				$obj = new stdClass;
				$obj->failed = $content;
				JFactory::getSpamLogger()->info($hash . " " . json_encode($obj));
				JFactory::getSession()->set('spam' . $hash, 1);

				// Increment spam hits count...go to spam jail!
				\Hubzero\User\Reputation::incrementSpamCount();
			}

			if ($message = $this->params->get('message'))
			{
				JFactory::getApplication()->enqueueMessage($message, 'error');
			}
			return false;
		}

		JFactory::getSpamLogger()->info('ham ' . $data['service'] . ' ' . $data['reason'] . ' ' . $ip . ' ' . $uid . ' ' . $username . ' ' . $hash . ' ' . $from);

	}

	/**
	 * Check if the context provided the content field name as
	 * it may vary between models.
	 *
	 * @param   string  $context  A dot-notation string
	 * @return  string
	 */
	private function _key($context)
	{
		$parts = explode('.', $context);
		$key = 'content';
		if (isset($parts[2]))
		{
			$key = $parts[2];
		}
		return $key;
	}

	/**
	 * Event for checking content
	 *
	 * @param   string   $content  The context of the content passed to the plugin (added in 1.6)
	 * @return  array
	 */
	public function onContentDetectSpam($context, $email = '', $username = '')
	{
		$spam = 0;
		$reason = 0;

		// International phone number match (let match be a little fuzzy)
		// This is the payload of babaji spam so gets you right on the edge of
		// of being marked spam. Pretty much any other rule hit should
		// trigger marking this as spam.

		if (preg_match("/(^|[^\d])(([\s\-\+]*\d[\s\-\+]*){11,12})([^\d\-\+]|$)/", $context))
		{
			$spam += 50;
			$reason |= 1;
		}

		// Spammer like to include variants of the name Babaji in the spam

		$baba = array("/(^|\s)baba(\s|$)/","/(^|\s)ji(\s|$)/","/(^|\s)b.{0,3}a.{0,3}b.{0,3}a.{0,4}j.{0,3}i(\s|$)/");

		foreach ($baba as $b)
		{
			if ( (($b{0} == "/") && preg_match($b, $context)) || (($b[0] != "/") && strpos($context,$b) !== false))
			{
				$spam += 10;
				$reason |= 2;
			}

			if ( (($b[0] == "/") && preg_match($b, $email)) || (($b[0] != "/") && strpos($email,$b) !== false))
			{
				$spam += 10;
				$reason |= 4;
			}

			if ( (($b[0] == "/") && preg_match($b, $username)) || (($b[0] != "/") && strpos($username,$b) !== false))
			{
				$spam += 10;
				$reason |= 8;
			}
		}

		// Spammer likes to include various obfuscated texts

		$keywords = array("ßåßå", "Vå§hïkåråñ", "Lðvê", "§þê¢ïålï§†", "þrðßlêm", "Mµ†hkårñï", "jï", "Pℝℴℬℒℰℳ)","mðhïñï", "vå§hïkåråñ",
				"vå§hïKÄRÄñ", "mårrïågê", "§ðlµ", "†ïðñ§", "Äll", "vððÐðð", "ßLåÇk", "MåGïÇ",
				"/Black\-{0,1}Magic/i","Haryana","Ambala", "ĹŐVĔ", "MÁŔŔĨĔĞĔ", "ŚPĔČĨÁĹĨŚŤ", "ßÁßÁĴĨ" );

		foreach ($keywords as $k)
		{
			if ( (($k[0] == "/") && preg_match($k, $context)) || (($k[0] != "/") && stripos($context,$k) !== false))
			{
				$spam += 10;
				$reason |= 16;
			}
			if ( (($k[0] == "/") && preg_match($k, $email)) || (($k[0] != "/") && stripos($email,$k) !== false))
			{
				$spam += 10;
				$reason |= 32;
			}
			if ( (($k[0] == "/") && preg_match($k, $username)) || (($k[0] != "/") && stripos($username,$k) !== false))
			{
				$spam += 10;
				$reason |= 64;
			}
		}

		// This is to catch phone number plus little content (unique word count < 5)
		if (count(array_unique(str_word_count($context, 1))) < 5)
		{
			$spam += 10;
			$reason |= 128;
		}

		$reasons = "";

		for ($i = 7; $i >= 0; $i--)
		{
			$mask = 1 << $i;

			$reasons .= ($reason & $mask) ? "X" : "O";
		}

		$reasons .= "-" . $spam;

		$data = array(
			'service' => $this->_name,
			'is_spam' => ($spam >= 60),
			'reason' => $reasons
		);

		return $data;
	}
}

