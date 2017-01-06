<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2017 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die;

/**
 * Email cloack plugin class.
 */
class plgContentEmailcloak extends \Hubzero\Plugin\Plugin
{
	/**
	 * Plugin that cloaks all emails in content from spambots via Javascript.
	 *
	 * @param   string   $context  The context of the content being passed to the plugin.
	 * @param   mixed    $row      An object with a "text" property or the string to be cloaked.
	 * @param   array    $params   Additional parameters. See {@see plgEmailCloak()}.
	 * @param   integer  $page     Optional page number. Unused. Defaults to zero.
	 * @return  boolean  True on success.
	 */
	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer' || $row instanceof \Hubzero\Base\Object)
		{
			return true;
		}

		if (is_object($row))
		{
			return $this->_cloak($row->text, $params);
		}

		return $this->_cloak($row, $params);
	}

	/**
	 * Genarate a search pattern based on link and text.
	 *
	 * @param   string  $link  The target of an email link.
	 * @param   string  $text  The text enclosed by the link.
	 * @return  string  A regular expression that matches a link containing the parameters.
	 */
	protected function _getPattern ($link, $text)
	{
		$pattern = '~(?:<a ([^>]*)href\s*=\s*"mailto:' . $link . '"([^>]*))>' . $text . '</a>~i';
		return $pattern;
	}

	/**
	 * Adds an attributes to the js cloaked email.
	 *
	 * @param   string  $jsEmail  Js cloaked email.
	 * @param   string  $before   Attributes before email.
	 * @param   string  $after    Attributes after email.
	 * @return  string  Js cloaked email with attributes.
	 */
	protected function _addAttributesToEmail($jsEmail, $before, $after)
	{
		if ($before !== '')
		{
			$before  = str_replace("'", "\'", $before);
			$jsEmail = str_replace(".innerHTML += '<a '", ".innerHTML += '<a {$before}'", $jsEmail);
		}

		if ($after !== '')
		{
			$after   = str_replace("'", "\'", $after);
			$jsEmail = str_replace("'\'>'", "'\'{$after}>'", $jsEmail);
		}

		return $jsEmail;
	}

	/**
	 * Cloak all emails in text from spambots via Javascript.
	 *
	 * @param   string   $text    The string to be cloaked.
	 * @param   array    $params  Additional parameters. Parameter "mode" (integer, default 1) replaces addresses with "mailto:" links if nonzero.
	 * @return  boolean  True on success.
	 */
	protected function _cloak(&$text, &$params)
	{
		/*
		 * Check for presence of {emailcloak=off} which is explicits disables this
		 * bot for the item.
		 */
		if (Hubzero\Utility\String::contains($text, '{emailcloak=off}') !== false)
		{
			$text = str_ireplace('{emailcloak=off}', '', $text);
			return true;
		}

		// Simple performance check to determine whether bot should process further.
		if (Hubzero\Utility\String::strpos($text, '@') === false)
		{
			return true;
		}

		$mode = $this->params->def('mode', 1);

		// any@email.address.com
		$searchEmail = '([\w\.\-\+]+\@(?:[a-z0-9\.\-]+\.)+(?:[a-zA-Z0-9\-]{2,10}))';
		// any@email.address.com?subject=anyText
		$searchEmailLink = $searchEmail . '([?&][\x20-\x7f][^"<>]+)';
		// anyText
		$searchText = '((?:[\x20-\x7f]|[\xA1-\xFF]|[\xC2-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF4][\x80-\xBF]{3})[^<>]+)';

		//Any Image link
		$searchImage = "(<img[^>]+>)";

		// Any Text with <span
		$searchTextSpan = '(<span[^>]+>|<span>|<strong>|<strong><span[^>]+>|<strong><span>)' . $searchText . '(</span>|</strong>|</span></strong>)';

		// Any address with <span
		$searchEmailSpan = '(<span[^>]+>|<span>|<strong>|<strong><span[^>]+>|<strong><span>)' . $searchEmail . '(</span>|</strong>|</span></strong>)';

		/*
		 * Search and fix derivatives of link code <a href="http://mce_host/ourdirectory/email@amail.com"
		 * >email@email.com</a>. This happens when inserting an email in TinyMCE, cancelling its suggestion to add
		 * the mailto: prefix...
		 */
		$pattern = $this->_getPattern($searchEmail, $searchEmail);
		$pattern = str_replace('"mailto:', '"http://mce_host([\x20-\x7f][^<>]+/)', $pattern);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[3][0];
			$mailText = $regs[5][0];

			// Check to see if mail text is different from mail addy
			$replacement = $this->cloak($mail, $mode, $mailText);

			// Ensure that attributes is not stripped out by email cloaking
			$replacement = $this->_addAttributesToEmail($replacement, $regs[1][0], $regs[4][0]);

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search and fix derivatives of link code <a href="http://mce_host/ourdirectory/email@amail.com"
		 * >anytext</a>. This happens when inserting an email in TinyMCE, cancelling its suggestion to add
		 * the mailto: prefix...
		 */
		$pattern = $this->_getPattern($searchEmail, $searchText);
		$pattern = str_replace('"mailto:', '"http://mce_host([\x20-\x7f][^<>]+/)', $pattern);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[3][0];
			$mailText = $regs[5][0];

			// Check to see if mail text is different from mail addy
			$replacement = $this->cloak($mail, $mode, $mailText, 0);

			// Ensure that attributes is not stripped out by email cloaking
			$replacement = $this->_addAttributesToEmail($replacement, $regs[1][0], $regs[4][0]);

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com"
		 * >email@amail.com</a>
		 */
		$pattern = $this->_getPattern($searchEmail, $searchEmail);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[2][0];
			$mailText = $regs[4][0];

			// Check to see if mail text is different from mail addy
			$replacement = $this->cloak($mail, $mode, $mailText);

			// Ensure that attributes is not stripped out by email cloaking
			$replacement = $this->_addAttributesToEmail($replacement, $regs[1][0], $regs[3][0]);

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com"
		 * ><anyspan >email@amail.com</anyspan></a>
		 */
		$pattern = $this->_getPattern($searchEmail, $searchEmailSpan);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[2][0];
			$mailText = $regs[4][0] . $regs[5][0] . $regs[6][0];

			// Check to see if mail text is different from mail addy
			$replacement = $this->cloak($mail, $mode, $mailText);

			// Ensure that attributes is not stripped out by email cloaking
			$replacement = html_entity_decode($this->_addAttributesToEmail($replacement, $regs[1][0], $regs[3][0]));

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com">
		 * <anyspan >anytext</anyspan></a>
		 */
		$pattern = $this->_getPattern($searchEmail, $searchTextSpan);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[2][0];
			$mailText = $regs[4][0] . addslashes($regs[5][0]) . $regs[6][0];

			$replacement = $this->cloak($mail, $mode, $mailText, 0);

			// Ensure that attributes is not stripped out by email cloaking
			$replacement = html_entity_decode($this->_addAttributesToEmail($replacement, $regs[1][0], $regs[3][0]));

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com">
		 * anytext</a>
		 */
		$pattern = $this->_getPattern($searchEmail, $searchText);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[2][0];
			$mailText = addslashes($regs[4][0]);

			$replacement = $this->cloak($mail, $mode, $mailText, 0);

			// Ensure that attributes is not stripped out by email cloaking
			$replacement = $this->_addAttributesToEmail($replacement, $regs[1][0], $regs[3][0]);

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com">
		 * <img anything></a>
		 */
		$pattern = $this->_getPattern($searchEmail, $searchImage);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[2][0];
			$mailText = $regs[4][0];

			$replacement = $this->cloak($mail, $mode, $mailText, 0);

			// Ensure that attributes is not stripped out by email cloaking
			$replacement = html_entity_decode($this->_addAttributesToEmail($replacement, $regs[1][0], $regs[3][0]));

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code <a href="mailto:email@example.org">
		 * <img anything>email@example.org</a>
		 */
		$pattern = $this->_getPattern($searchEmail, ($searchImage . $searchEmail));
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[2][0];
			$mailText = $regs[4][0] . ($regs[5][0]);

			$replacement = $this->cloak($mail, $mode, $mailText);

			// Ensure that attributes is not stripped out by email cloaking
			$replacement = html_entity_decode($this->_addAttributesToEmail($replacement, $regs[1][0], $regs[3][0]));

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code <a href="mailto:email@example.org">
		 * <img anything>any text</a>
		 */
		$pattern = $this->_getPattern($searchEmail, ($searchImage . $searchText));
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[2][0];
			$mailText = $regs[4][0] . addslashes($regs[5][0]);

			$replacement = $this->cloak($mail, $mode, $mailText, 0);

			// Ensure that attributes is not stripped out by email cloaking
			$replacement = html_entity_decode($this->_addAttributesToEmail($replacement, $regs[1][0], $regs[3][0]));

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com?
		 * subject=Text">email@amail.com</a>
		 */
		$pattern = $this->_getPattern($searchEmailLink, $searchEmail);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[2][0] . $regs[3][0];
			$mailText = $regs[5][0];

			// Needed for handling of Body parameter
			$mail = str_replace('&amp;', '&', $mail);

			// Check to see if mail text is different from mail addy
			$replacement = $this->cloak($mail, $mode, $mailText);

			// Ensure that attributes is not stripped out by email cloaking
			$replacement = $this->_addAttributesToEmail($replacement, $regs[1][0], $regs[4][0]);

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com?
		 * subject=Text">anytext</a>
		 */
		$pattern = $this->_getPattern($searchEmailLink, $searchText);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[2][0] . $regs[3][0];
			$mailText = addslashes($regs[5][0]);

			// Needed for handling of Body parameter
			$mail = str_replace('&amp;', '&', $mail);

			$replacement = $this->cloak($mail, $mode, $mailText, 0);

			// Ensure that attributes is not stripped out by email cloaking
			$replacement = $this->_addAttributesToEmail($replacement, $regs[1][0], $regs[4][0]);

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com?subject= Text"
		 * ><anyspan >email@amail.com</anyspan></a>
		 */
		$pattern = $this->_getPattern($searchEmailLink, $searchEmailSpan);

		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[2][0] . $regs[3][0];
			$mailText = $regs[4][0] . $regs[5][0] . $regs[6][0] . $regs[7][0];

			// Check to see if mail text is different from mail addy
			$replacement = $this->cloak($mail, $mode, $mailText);

			// Ensure that attributes is not stripped out by email cloaking
			$replacement = html_entity_decode($this->_addAttributesToEmail($replacement, $regs[1][0], $regs[3][0]));

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com?subject= Text">
		 * <anyspan >anytext</anyspan></a>
		 */
		$pattern = $this->_getPattern($searchEmailLink, $searchTextSpan);

		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[2][0] . $regs[3][0];
			$mailText = $regs[4][0] . $regs[5][0] . addslashes($regs[6][0]) . $regs[7][0];

			$replacement = $this->cloak($mail, $mode, $mailText, 0);

			// Ensure that attributes is not stripped out by email cloaking
			$replacement = html_entity_decode($this->_addAttributesToEmail($replacement, $regs[1][0], $regs[3][0]));

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code
		 * <a href="mailto:email@amail.com?subject=Text"><img anything></a>
		 */
		$pattern = $this->_getPattern($searchEmailLink, $searchImage);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[1][0] . $regs[2][0] . $regs[3][0];
			$mailText = $regs[5][0];

			// Needed for handling of Body parameter
			$mail = str_replace('&amp;', '&', $mail);

			// Check to see if mail text is different from mail addy
			$replacement = $this->cloak($mail, $mode, $mailText, 0);

			// Ensure that attributes is not stripped out by email cloaking
			$replacement = html_entity_decode($this->_addAttributesToEmail($replacement, $regs[1][0], $regs[4][0]));

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code
		 * <a href="mailto:email@amail.com?subject=Text"><img anything>email@amail.com</a>
		 */
		$pattern = $this->_getPattern($searchEmailLink, ($searchImage . $searchEmail));

		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[1][0] . $regs[2][0] . $regs[3][0];
			$mailText = $regs[4][0] . $regs[5][0] . $regs[6][0];

			// Needed for handling of Body parameter
			$mail = str_replace('&amp;', '&', $mail);

			// Check to see if mail text is different from mail addy
			$replacement = $this->cloak($mail, $mode, $mailText);

			// Ensure that attributes is not stripped out by email cloaking
			$replacement = html_entity_decode($this->_addAttributesToEmail($replacement, $regs[1][0], $regs[4][0]));

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code
		 * <a href="mailto:email@amail.com?subject=Text"><img anything>any text</a>
		 */
		$pattern = $this->_getPattern($searchEmailLink, ($searchImage . $searchText));

		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[1][0] . $regs[2][0] . $regs[3][0];
			$mailText = $regs[4][0] . $regs[5][0] . addslashes($regs[6][0]);

			// Needed for handling of Body parameter
			$mail = str_replace('&amp;', '&', $mail);

			// Check to see if mail text is different from mail addy
			$replacement = $this->cloak($mail, $mode, $mailText, 0);

			// Ensure that attributes is not stripped out by email cloaking
			$replacement = html_entity_decode($this->_addAttributesToEmail($replacement, $regs[1][0], $regs[4][0]));

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		// Search for plain text email@amail.com
		$pattern = '~' . $searchEmail . '([^a-z0-9]|$)~i';
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[1][0];
			$replacement = $this->cloak($mail, $mode);

			// Replace the found address with the js cloaked email
			$text = substr_replace($text, $replacement, $regs[1][1], strlen($mail));
		}

		return true;
	}

	/**
	 * Simple JavaScript email cloaker
	 *
	 * By default replaces an email with a mailto link with email cloaked
	 *
	 * @param   string   $mail    The -mail address to cloak.
	 * @param   boolean  $mailto  True if text and mailing address differ
	 * @param   string   $text    Text for the link
	 * @param   boolean  $email   True if text is an e-mail address
	 * @return  string   The cloaked email.
	 */
	public function cloak($mail, $mailto = true, $text = '', $email = true)
	{
		// Convert mail
		$mail = $this->convertEncoding($mail);

		// Split email by @ symbol
		$mail = explode('@', $mail);
		$mail_parts = explode('.', $mail[1]);

		// Random number
		$rand = rand(1, 100000);

		$replacement = '<span id="cloak' . $rand . '">' . Lang::txt('JLIB_HTML_CLOAKING') . '</span><script type="text/javascript">';
		$replacement .= "\n //<!--";
		$replacement .= "\n document.getElementById('cloak$rand').innerHTML = '';";
		$replacement .= "\n var prefix = '&#109;a' + 'i&#108;' + '&#116;o';";
		$replacement .= "\n var path = 'hr' + 'ef' + '=';";
		$replacement .= "\n var addy" . $rand . " = '" . @$mail[0] . "' + '&#64;';";
		$replacement .= "\n addy" . $rand . " = addy" . $rand . " + '" . implode("' + '&#46;' + '", $mail_parts) . "';";

		if ($mailto)
		{
			// Special handling when mail text is different from mail address
			if ($text)
			{
				// Convert text - here is the right place
				$text = $this->convertEncoding($text);

				if ($email)
				{
					// Split email by @ symbol
					$text = explode('@', $text);
					$text_parts = explode('.', $text[1]);
					$replacement .= "\n var addy_text" . $rand . " = '" . @$text[0] . "' + '&#64;' + '" . implode("' + '&#46;' + '", @$text_parts) . "';";
				}
				else
				{
					$replacement .= "\n var addy_text" . $rand . " = '" . $text . "';";
				}

				$replacement .= "\n document.getElementById('cloak$rand').innerHTML += '<a ' + path + '\'' + prefix + ':' + addy" . $rand . " + '\'>'+addy_text" . $rand . "+'<\/a>';";
			}
			else
			{
				$replacement .= "\n document.getElementById('cloak$rand').innerHTML += '<a ' + path + '\'' + prefix + ':' + addy" . $rand . " + '\'>' + addy" . $rand . "+'<\/a>';";
			}
		}
		else
		{
			$replacement .= "\n document.getElementById('cloak$rand').innerHTML += addy" . $rand . ";";
		}

		$replacement .= "\n //-->";
		$replacement .= "\n </script>";

		return $replacement;
	}

	/**
	 * Convert encoded text
	 *
	 * @param   string  $text  Text to convert
	 * @return  string  The converted text.
	 */
	protected function convertEncoding($text)
	{
		$text = html_entity_decode($text);

		// Replace vowels with character encoding
		$text = str_replace('a', '&#97;', $text);
		$text = str_replace('e', '&#101;', $text);
		$text = str_replace('i', '&#105;', $text);
		$text = str_replace('o', '&#111;', $text);
		$text = str_replace('u', '&#117;', $text);

		$text = htmlentities($text, ENT_QUOTES, 'UTF-8', false);

		return $text;
	}
}
