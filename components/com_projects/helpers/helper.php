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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Projects helper class
 */
class ProjectsHelper extends JObject {

	/**
	 * Project ID
	 *
	 * @var mixed
	 */
	private $_id = 0;

	/**
	 * JDatabase
	 *
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * Container for properties
	 *
	 * @var array
	 */
	private $_data = array();

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( $db )
	{
		$this->_db = $db;
	}

	/**
	 * Get project path
	 *
	 * @param      string $projectAlias
	 * @param      string $webdir
	 * @param      boolean $offroot
	 * @param      string $case
	 * @return     string
	 */
	public static function getProjectPath( $projectAlias = '', $webdir = '', $offroot = 0, $case = 'files' )
	{
		if (!$projectAlias || ! $webdir)
		{
			return false;
		}

		// Build upload path for project files
		$dir = strtolower($projectAlias);

		if (substr($webdir, 0, 1) != DS)
		{
			$webdir = DS.$webdir;
		}
		if (substr($webdir, -1, 1) == DS)
		{
			$webdir = substr($webdir, 0, (strlen($webdir) - 1));
		}
		$path  = $case ? $webdir . DS . $dir. DS . $case : $webdir . DS . $dir ;
		$path  = $offroot ? $path : JPATH_ROOT . $path;
		return $path;
	}

	/**
	 * Get tabs
	 *
	 * @return    array
	 */
	public static function getTabs( &$plugins )
	{
		// Make sure we have name and title
		$tabs = array();
		for ($i = 0, $n = count($plugins); $i <= $n; $i++)
		{
			if (empty($plugins[$i]) || !isset($plugins[$i]['name']))
			{
				unset($plugins[$i]);
			}
			else
			{
				$tabs[] = $plugins[$i]['name'];
			}
		}

		return $tabs;
	}

	/**
	 * Get group members
	 *
	 * @param  string $groupname
	 * @return void
	 */
	public static function getGroupMembers($groupname)
	{
		$team = array();
		if ($groupname)
		{
			$group = \Hubzero\User\Group::getInstance($groupname);
			if ($group && $group->get('gidNumber'))
			{
				$members 	= $group->get('members');
				$managers 	= $group->get('managers');
				$team 		= array_merge($members, $managers);
				$team 		= array_unique($team);
			}
		}

		return $team;
	}

	/**
	 * Send hub message
	 *
	 * @param      string 	$option
	 * @param      array 	$config
	 * @param      object 	$project
	 * @param      array 	$addressees
	 * @param      string 	$subject
	 * @param      string 	$component
	 * @param      string 	$layout
	 * @param      string 	$message
	 * @param      string 	$reviewer
	 * @return     void
	 */
	public static function sendHUBMessage(
		$option, $config, $project,
		$addressees = array(), $subject = '',
		$component = '', $layout = '',
		$message = '', $reviewer = '')
	{
		if (!$layout || !$subject || !$component || empty($addressees))
		{
			return false;
		}

		// Is messaging turned on?
		if ($config->get('messaging') != 1)
		{
			return false;
		}

		// Set up email config
		$jconfig = JFactory::getConfig();
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_('COM_PROJECTS');
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Html email
		$from['multipart'] = md5(date('U'));

		// Get message body
		$eview = new \Hubzero\Component\View(array(
			'base_path' => JPATH_ROOT . DS . 'components' . DS . 'com_projects',
			'name'   => 'emails',
			'layout' => $layout . '_plain'
		));

		$eview->option 			= $option;
		$eview->hubShortName 	= $jconfig->getValue('config.sitename');
		$eview->project 		= $project;
		$eview->params 			= new JParameter( $project->params );
		$eview->config 			= $config;
		$eview->message			= $message;
		$eview->reviewer		= $reviewer;

		// Get profile of author group
		if ($project->owned_by_group)
		{
			$eview->nativegroup = \Hubzero\User\Group::getInstance( $project->owned_by_group );
		}
		$body = array();
		$body['plaintext'] 	= $eview->loadTemplate();
		$body['plaintext'] 	= str_replace("\n", "\r\n", $body['plaintext']);

		// HTML email
		$eview->setLayout($layout . '_html');
		$body['multipart'] = $eview->loadTemplate();
		$body['multipart'] = str_replace("\n", "\r\n", $body['multipart']);

		// Send HUB message
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger( 'onSendMessage',
			array(
				$component,
				$subject,
				$body,
				$from,
				$addressees,
				$option
			)
		);
	}
}
