<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_mailto
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

class MailtoViewMailto extends JViewLegacy
{
	function display($tpl = null)
	{
		$data = $this->getData();
		if ($data === false)
		{
			return false;
		}

		$this->set('data', $data);

		parent::display($tpl);
	}

	function &getData()
	{
		$data = new stdClass();

		$data->link = urldecode(Request::getVar('link', '', 'method', 'base64'));

		if ($data->link == '')
		{
			App::abort(403, Lang::txt('COM_MAILTO_LINK_IS_MISSING'));
			$false = false;
			return $false;
		}

		// Load with previous data, if it exists
		$mailto  = Request::getString('mailto', '', 'post');
		$sender  = Request::getString('sender', '', 'post');
		$from    = Request::getString('from', '', 'post');
		$subject = Request::getString('subject', '', 'post');

		if (User::get('id') > 0)
		{
			$data->sender = User::get('name');
			$data->from   = User::get('email');
		}
		else
		{
			$data->sender = $sender;
			$data->from   = $from;
		}

		$data->subject = $subject;
		$data->mailto  = $mailto;

		return $data;
	}
}
