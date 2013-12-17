<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class GroupsHelperPages
{
	/**
	 * Is Current User a Page Approver?
	 *
	 * @return void
	 */
	public static function isPageApprover( $username = null)
	{
		$username  = (!is_null($username)) ? $username : JFactory::getUser()->get('username');
		return (in_array($username, self::getPageApprovers())) ? true : false;
	}
	
	
	/**
	 * Get page approvers
	 *
	 * @return void
	 */
	public static function getPageApprovers()
	{
		$approvers = JComponentHelper::getParams('com_groups')->get('approvers', '');
		return array_map("trim", explode(',', $approvers));
	}
	
	/**
	 * Get page approvers Emails and names
	 * (used for emailing purposes)
	 *
	 * @return void
	 */
	public static function getPageApproversEmail()
	{
		$emails    = array();
		$approvers = self::getPageApprovers();
		
		foreach ($approvers as $approver)
		{
			$profile = Hubzero_User_Profile::getInstance( $approver );
			if ($profile)
			{
				$emails[$profile->get('email')] = $profile->get('name');
			}
		}
		
		return $emails;
	}
	
	
	public static function sendApproveNotification( $type, $object )
	{
		// build title
		$title = JText::sprintf('Page "%s" Requires Approval', $object->get('title'));
		if ($type == 'module')
		{
			$title = JText::sprintf('Module "%s" Requires Approval', $object->get('title'));
		}
		
		// get approvers w/ emails
		$approvers = self::getPageApproversEmail();
		
		// get site config
		$jconfig =& JFactory::getConfig();
		
		// subject details
		$subject = $jconfig->getValue('config.sitename') . ' ' . JText::_('Groups') . ', ' . $title;
		
		// from details
		$from = array(
			'name'  => $jconfig->getValue('config.sitename') . ' ' . JText::_('Groups'),
			'email' => $jconfig->getValue('config.mailfrom')
		);
		
		// build html email
		$eview = new JView(array(
			'name'   => 'emails', 
			'layout' => $type
		));
		$eview->option     = JRequest::getCmd('option', 'com_groups');;
		$eview->controller = JRequest::getCmd('controller', 'groups');
		$eview->group      = Hubzero_Group::getInstance(JRequest::getCmd('cn', ''));
		$eview->object     = $object;
		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);
		
		// create new message
		$message = new \Hubzero\Mail\Message();
		
		// build message object and send
		$message->setSubject($subject)
				->addFrom($from['email'], $from['name'])
				->setTo($approvers)
				->addHeader('X-Mailer', 'PHP/' . phpversion())
				->addHeader('X-Component', 'com_groups')
				->addHeader('X-Component-Object', $type . '_approval')
				->addPart('Neat, huh?', 'text/plain')
				->addPart($html, 'text/html')
				->send();
	}
	
	/**
	 * Get code flags
	 *
	 * @return void
	 */
	public static function getCodeFlags()
	{
		return array(
			'php' => array(
				'minor'    => array(),
				'elevated' => array(
					'include', 
					'require', 
					'call_user_func', 
					'curl',
					'chgrp',
					'chmod',
					'file_put_contents',
					'file_get_contents',
					'lchgrp',
					'lchown',
					'link',
					'mkdir',
					'move_uploaded_file',
					'rename',
					'rmdir',
					'symlink',
					'tempnam',
					'touch',
					'unlink'
				),
				'severe'   => array(
					'die',
					'exit',
					'exec',
					'dl',
					'show_source',
					'apache_',
					'closelog',
					'debugger_',
					'define_syslog_variables',
					'escapeshellarg',
					'escapeshellcmd',
					'openlog',
					'passthru',
					'pclose',
					'pcntl_exec',
					'popen',
					'proc_',
					'shell_exec',
					'syslog',
					'system',
					'url_exec',
					'assert',
					'posix_',
					'phpinfo',
					'eval',
					'define_syslog_variables',
					'fp',
					'fput',
					'ftp_',
					'ini_',
					'inject_code',
					'mysql_',
					'php_uname',
					'phpAds_',
					'system',
					'xmlrpc_entity_decode',
				),
			),
			'mysql' => array(
				'minor'    => array(),
				'elevated' => array(),
				'severe'   => array(
					'drop',
					'rename',
					'truncate',
					'delete'
				)
			)
		);
	}
}