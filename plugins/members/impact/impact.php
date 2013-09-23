<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Members Plugin class for author's impact
 */
class plgMembersImpact extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin('members', 'impact');
		$this->_params = new $paramsClass($this->_plugin->params);
		$this->_database =& JFactory::getDBO();
		$this->_juser =& JFactory::getUser();
		$this->_pubconfig =& JComponentHelper::getParams( 'com_publications' );
		
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
			.'com_publications' . DS . 'tables' . DS . 'logs.php');
			
		$this->_stats = NULL;
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @return     array
	 */
	public function &onMembersAreas($user, $member)
	{
		//default areas returned to nothing
		$areas = array();

		//if this is the logged in user show them
		if ($user->get('id') == $member->get('uidNumber'))
		{
			// Check if user has any publications			
			$pubLog = new PublicationLog($this->_database);
			$this->_stats = $pubLog->getAuthorStats($user->get('id'), 0, false );
			
			if ($this->_stats)
			{
				$areas['impact'] = JText::_('PLG_MEMBERS_IMPACT');
			}
		}

		return $areas;
	}

	/**
	 * Perform actions when viewing a member profile
	 * 
	 * @param      object $user   Current user
	 * @param      object $member Current member page
	 * @param      string $option Start of records to pull
	 * @param      array  $areas  Active area(s)
	 * @return     array
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;
		$returnmeta = true;

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member)))) 
			{
				$returnhtml = false;
			}
		}

		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		ximport('Hubzero_View_Helper_Html');

		// Add stylesheet
		$document =& JFactory::getDocument();
		$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'publications' . DS . 'css' . DS . 'impact.css');
		
		require_once( JPATH_ROOT . DS . 'components'.DS
			. 'com_publications' . DS . 'helpers' . DS . 'helper.php');
			
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
			.'com_publications' . DS . 'tables' . DS . 'version.php');

		if ($returnhtml) 
		{
			// Which view 
			$task = JRequest::getVar('action', '');

			switch ($task) 
			{
				case 'view':
				default:        $arr['html'] = $this->_view($member->get('uidNumber'));   break;
			}
		}

		//get meta
		$arr['metadata'] = array();
		$arr['metadata']['count'] = $this->_stats ? count($this->_stats) : 0;

		return $arr;
	}

	/**
	 * View entries
	 * 
	 * @param      int $uid
	 * @return     string
	 */
	protected function _view($uid = 0) 
	{
		// Build the final HTML		
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => 'impact',
				'name'    => 'stats'
			)
		);	
		
		// Start url
		$route = $this->_project->provisioned 
					? 'index.php?option=com_publications' . a . 'task=submit'
					: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';

		// Get pub stats for each publication		
		$pubLog = new PublicationLog($this->_database);
		$view->pubstats = $pubLog->getAuthorStats($uid, 0, false);

		// Get date of first log
		$view->firstlog = $pubLog->getFirstLogDate();

		// Test
		$view->totals = $pubLog->getTotals($uid);

		// Output HTML
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->uid 			= $uid;	
		$view->pubconfig 	= $this->_pubconfig;
		$view->title		= $this->_area['title'];
		$view->helper		= new PublicationHelper($this->_database);

		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}
}
