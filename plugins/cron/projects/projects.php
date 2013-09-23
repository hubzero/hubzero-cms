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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Cron plugin for projects
 */
class plgCronProjects extends JPlugin
{
	/**
	 * Return a list of events
	 * 
	 * @return     array
	 */
	public function onCronEvents()
	{
		$obj = new stdClass();
		$obj->plugin = 'projects';

		$obj->events = array(
			array(
				'name'   => 'computeStats',
				'label'  => JText::_('Compute and log overall projects usage stats'),
				'params' => ''
			),
			array(
				'name'   => 'googleSync',
				'label'  => JText::_('Auto sync project repositories connected with GDrive'),
				'params' => ''
			),
			array(
				'name'   => 'gitGc',
				'label'  => JText::_('Rn git gc for project repos to optimize disk usage'),
				'params' => ''
			)
		);
		
		return $obj;
	}
	
	/**
	 * Compute and log overall projects usage stats
	 * 
	 * @return     boolean
	 */
	public function computeStats($params=null)
	{
		$database = JFactory::getDBO();
		$juri =& JURI::getInstance();
		
		$jconfig =& JFactory::getConfig();
		$pconfig = JComponentHelper::getParams('com_projects');
		
		$period = 'alltime';
		
		$publishing = 
			is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'publication.php')
			&& JPluginHelper::isEnabled('projects', 'publications')
			? 1 : 0;
		
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
				.'com_projects' . DS . 'tables' . DS . 'project.php');
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
					.'com_projects' . DS . 'tables' . DS . 'project.activity.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
					.'com_projects' . DS . 'tables' . DS . 'project.microblog.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
					.'com_projects' . DS . 'tables' . DS . 'project.comment.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
					.'com_projects' . DS . 'tables' . DS . 'project.owner.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
					.'com_projects' . DS . 'tables' . DS . 'project.type.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
					.'com_projects' . DS . 'tables' . DS . 'project.todo.php' );
		
		if ($publishing)
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'publication.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'version.php');
		}			
					
		require_once( JPATH_ROOT . DS . 'components'. DS
				.'com_projects' . DS . 'helpers' . DS . 'html.php');	
			
		$obj = new Project( $database );
		
		// Get all test projects
		$testProjects = $obj->getTestProjects();
		
		// Compute and store stats
		$view->stats  = $obj->getStats($period, 1, $pconfig, $testProjects, $publishing);
		
		return true;
	}
	
	/**
	 * Auto sync project repositories connected with GDrive
	 * 
	 * @return     boolean
	 */
	public function googleSync($params=null)
	{
		$database = JFactory::getDBO();
		$juri =& JURI::getInstance();
		
		$jconfig =& JFactory::getConfig();
		$pconfig = JComponentHelper::getParams('com_projects');
		
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
				.'com_projects' . DS . 'tables' . DS . 'project.php');
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
				.'com_projects' . DS . 'tables' . DS . 'project.owner.php');	
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'connect.php' );
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'html.php' );
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'imghandler.php' );	
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'helper.php' );		
			
				
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' 
			. DS . 'com_projects' . DS . 'tables' . DS . 'project.remote.file.php');
		require_once( JPATH_SITE . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'remote' . DS . 'google.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
			.'com_publications' . DS . 'tables' . DS . 'attachment.php');
			
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
			.'com_publications' . DS . 'tables' . DS . 'publication.php');
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
			.'com_publications' . DS . 'tables' . DS . 'version.php');
			
		
		// Get all projects
		$obj = new Project( $database );
		$projects = $obj->getValidProjects(array(), array(), $pconfig, false, 'alias' );

		if (!$projects)
		{
			return true;
		}
		
		$prefix = $pconfig->get('offroot', 0) ? '' : JPATH_ROOT;
		$webdir = DS . trim($pconfig->get('webpath'), DS);
		
		// Get plugin
		JPluginHelper::importPlugin( 'projects', 'files');
		$dispatcher =& JDispatcher::getInstance();
		
		JRequest::setVar('auto', 1);
				
		foreach ($projects as $alias)
		{			
			// Load project
			$project = $obj->getProject($alias, 0);
						
			$pparams 	= new JParameter( $project->params );
			$connected 	= $pparams->get('google_dir_id');
			$token 		= $pparams->get('google_token');
			
			if (!$connected || !$token)
			{
				continue;
			}
			
			// Unlock sync
			$obj->saveParam($project->id, 'google_sync_lock', '');
			
			// Plugin params
			$plugin_params = array(
				$project, 
				'com_projects', 
				true, 
				$project->created_by_user, 
				NULL, 
				NULL,
				'sync',
				array('files')
			);
			
			$sections = $dispatcher->trigger( 'onProject', $plugin_params);
			
			//echo 'synced ' . $alias . ' <br />';
		}
		
		return true;
				
	}
	
	/**
	 * Optimize project repos
	 * 
	 * @return     boolean
	 */
	public function gitGc($params=null)
	{
		$database = JFactory::getDBO();
		$juri =& JURI::getInstance();
		
		$jconfig =& JFactory::getConfig();
		$pconfig = JComponentHelper::getParams('com_projects');
		
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
				.'com_projects' . DS . 'tables' . DS . 'project.php');
		
		// Get all projects
		$obj = new Project( $database );
		$projects = $obj->getValidProjects(array(), array(), $pconfig, false, 'alias' );
		
		if (!$projects)
		{
			return true;
		}
		
		$prefix = $pconfig->get('offroot', 0) ? '' : JPATH_ROOT;
		$webdir = DS . trim($pconfig->get('webpath'), DS);
		
		include_once( JPATH_ROOT . DS . 'components' . DS .'com_projects' . DS . 'helpers' . DS . 'githelper.php' );
		$git = new ProjectsGitHelper(
			$pconfig->get('gitpath', '/opt/local/bin/git'), 
			0,
			$pconfig->get('offroot', 0) ? '' : JPATH_ROOT
		);
		
		foreach ($projects as $project)
		{
			$path = $prefix . $webdir . DS . strtolower($project) . DS . 'files';
			
			// Make sure there is .git directory
			if (!is_dir($path . DS . '.git'))
			{
				continue;
			}
			
			$command = 'gc --aggressive';
			$git->callGit($path, $command);
			//echo 'optimized ' . $project . ' repo <br />';
		}

		return true;
	}
}

