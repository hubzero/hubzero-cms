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
	 * Get suggestions for new project members
	 *
	 * @param      object $project
	 * @param      string $option
	 * @param      int $uid
	 * @param      array $config
	 * @param      array $params
	 * @return     void
	 */
	public static function getSuggestions( $project, $option, $uid = 0, $config, $params )
	{
		$suggestions = array();
		$creator = $project->created_by_user == $uid ? 1 : 0;
		$goto  = 'alias=' . $project->alias;

		// Adding a picture
		if ($creator && !$project->picture)
		{
			$suggestions[] = array(
				'class' => 's-picture',
				'text' => JText::_('COM_PROJECTS_WELCOME_ADD_THUMB'),
				'url' => JRoute::_('index.php?option=' . $option . a . $goto . '&task=edit')
			);
		}

		// Adding grant information
		if ($creator && $config->get('grantinfo') && !$params->get('grant_title', ''))
		{
			$suggestions[] = array(
				'class' => 's-about',
				'text' => JText::_('COM_PROJECTS_WELCOME_ADD_GRANT_INFO'),
				'url' => JRoute::_('index.php?option=' . $option . a . $goto . '&task=edit') . '?edit=settings'
			);
		}

		// Adding about text
		if ($creator && !$project->about && $project->private == 0)
		{
			$suggestions[] = array(
				'class' => 's-about',
				'text' => JText::_('COM_PROJECTS_WELCOME_ADD_ABOUT'),
				'url' => JRoute::_('index.php?option=' . $option . a . $goto . '&task=edit')
			);
		}

		// File upload
		if ($project->counts['files'] == 0 || (!$creator && $project->num_visits < 5)
			|| ($creator && $project->num_visits < 5 &&  $project->counts['files'] == 0 ) )
		{
			$text = $creator ? JText::_('COM_PROJECTS_WELCOME_UPLOAD_FILES') : JText::_('COM_PROJECTS_WELCOME_SHARE_FILES');
			$suggestions[] = array(
				'class' => 's-files',
				'text' => $text,
				'url' =>  JRoute::_('index.php?option=' . $option . a . $goto . '&active=files')
			);
		}

		// Inviting others
		if ($project->counts['team'] == 1 && $project->role == 1)
		{
			$suggestions[] = array(
				'class' => 's-team',
				'text' => JText::_('COM_PROJECTS_WELCOME_INVITE_USERS'),
				'url' => JRoute::_('index.php?option=' . $option . a . $goto . '&task=edit').'?edit=team'
			);
		}

		// Todo items
		if ($project->counts['todo'] == 0 || (!$creator && $project->num_visits < 5)
			|| ($creator && $project->num_visits < 5 &&  $project->counts['todo'] == 0 ) )
		{
			$suggestions[] = array(
				'class' => 's-todo',
				'text' => JText::_('COM_PROJECTS_WELCOME_ADD_TODO'),
				'url' => JRoute::_('index.php?option=' . $option . a . $goto . '&active=todo')
			);
		}

		// Notes
		if ($project->counts['notes'] == 0 || (!$creator && $project->num_visits < 5)
			|| ($creator && $project->num_visits < 5 &&  $project->counts['notes'] == 0 )  )
		{
			$suggestions[] = array(
				'class' => 's-notes',
				'text' => JText::_('COM_PROJECTS_WELCOME_START_NOTE'),
				'url' => JRoute::_('index.php?option=' . $option . a . $goto . '&active=notes')
			);
		}
		return $suggestions;
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
	 * Covert param to array of values
	 *
	 * @param      string $param
	 *
	 * @return     array
	 */
	public static function getParamArray($param = '')
	{
		if ($param)
		{
			$array = explode(',', $param);
			return array_map('trim', $array);
		}
		return array();
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
	 * Suggest alias name from title
	 *
	 * @param  string $title
	 * @return     void
	 */
	public static function suggestAlias ($title = '')
	{
		if ($title)
		{
			$name = preg_replace('/ /', '', $title);
			$name = strtolower($name);
			$name = preg_replace('/[^a-z0-9]/', '', $name);
			$name = substr($name, 0, 30);
			return $name;
		}
		return false;
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

	/**
	 * Show Git info
	 *
	 * @param      string 	$gitpath
	 * @param      string 	$path
	 * @param      string 	$fhash
	 * @param      string 	$file
	 * @param      int 		$showstatus
	 * @return     void
	 */
	public function showGitInfo ($gitpath = '', $path = '', $fhash = '', $file = '', $showstatus = 1)
	{
		if (!$gitpath || !$path || !is_dir($path) || !$file)
		{
			return false;
		}
		chdir($path);

		// Get all file revisions
		exec ($gitpath . ' log --diff-filter=AM --pretty=format:%H ' . escapeshellarg($file) . ' 2>&1', $out);

		$versions = array();
		$hashes = array();
		$added = array();
		$latest = array();

		if (count($out) > 0 && $out[0] != '')
		{
			foreach ($out as $line)
			{
				if (preg_match("/[a-zA-Z0-9]/", $line) && strlen($line) == 40)
				{
					$hashes[]  = $line;
				}
			}
			// Start with oldest commit
			$hashes = array_reverse($hashes);
		}

		if (!empty($hashes))
		{
			$i = 0;
			foreach ($hashes as $hash)
			{
				$out1 = array();
				$out2 = array();

				exec($gitpath . ' log --pretty=format:%cd ' . escapeshellarg($hash) . ' 2>&1', $out1);
				$arr = explode("\t", $out1[0]);
				$timestamp = strtotime($arr[0]);
				$versions[$i]['date'] =  date ('m/d/Y g:i A', $timestamp);

				exec($gitpath . ' log --pretty=format:%an ' . escapeshellarg($hash) . ' 2>&1', $out2);
				$arr = explode("\t", $out2[0]);
				$versions[$i]['author'] = $arr[0];
				$versions[$i]['num']    = $i+1;
				$versions[$i]['latest'] = ($i == (count($hashes) - 1)) ? 1 : 0;

				$versions[$i]['hash'] = $hash;
				if ($hash == $fhash)
				{
					$versions[$i]['changes'] = count($hashes) - 1 - $i;
					$added = $versions[$i];
				}
				if ($i == (count($hashes) - 1))
				{
					$latest = $versions[$i];
				}

				$i++;
			}

			if ($showstatus == 1)
			{
				// Review stage
				$status = '';
				if (empty($added))
				{
					$added = $latest;
				}
				if (!empty($added))
				{
					$status .= 'Added at revision #'.$added['num'];
					if ($added['latest'] == 1)
					{
						$status .= ' (latest) ';
					}
					else
					{
						$status .= ' ('.$added['changes'].' new update(s)';
						$status .= ' - last update by '.$latest['author'].' on '.$latest['date'].')';
					}
				}
				return $status;
			}
			elseif ($showstatus == 2)
			{
				// Get latest commit
				return $latest;
			}
			elseif ($showstatus == 3)
			{
				// Get added commit
				return $added;
			}
			else
			{
				return $versions;
			}
		}
	}

	/**
	 * Check file for viruses
	 *
	 * @param      string 	$fpath		Full path to scanned file
	 *
	 * @return     mixed
	 */
	public static function virusCheck( $fpath = '' )
	{
		jimport('joomla.filesystem.file');
		if (!JFile::isSafe($fpath))
		{
			unlink($fpath);
			return true;
		}

		return false;
	}

	/**
	 * Authorize reviewer
	 *
	 * @return     void
	 */
	public static function checkReviewerAuth($reviewer, $config)
	{
		if ($reviewer != 'sponsored' && $reviewer != 'sensitive' && $reviewer != 'general')
		{
			return false;
		}

		$juser = JFactory::getUser();
		if ($juser->get('guest'))
		{
			return false;
		}

		$sdata_group 	= $config->get('sdata_group', '');
		$ginfo_group 	= $config->get('ginfo_group', '');
		$admingroup 	= $config->get('admingroup', '');
		$group      	= '';
		$authorized 	= false;

		// Get authorized group
		if ($reviewer == 'sensitive' && $sdata_group)
		{
			$group = \Hubzero\User\Group::getInstance($sdata_group);
		}
		elseif ($reviewer == 'sponsored' && $ginfo_group)
		{
			$group = \Hubzero\User\Group::getInstance($ginfo_group);
		}
		elseif ($reviewer == 'general' && $admingroup)
		{
			$group = \Hubzero\User\Group::getInstance($admingroup);
		}

		if ($group)
		{
			// Check if they're a member of this group
			$ugs = \Hubzero\User\Helper::getGroups($juser->get('id'));
			if ($ugs && count($ugs) > 0)
			{
				foreach ($ugs as $ug)
				{
					if ($group && $ug->cn == $group->get('cn'))
					{
						$authorized = true;
					}
				}
			}
		}

		return $authorized;
	}
}
