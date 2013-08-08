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
 * Module class for displaying a user's sessions
 */
class modMySessions
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
	 * @param      object $params JParameter
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
	 * Set the time when the session will tiemout
	 * 
	 * @param      integer $sess Session ID
	 * @return     void
	 */
	private function _setTimeout($sess)
	{
		$mwdb =& MwUtils::getMWDBO();

		$ms = new MwSession($mwdb);
		$ms->load($sess);
		$ms->timeout = 1209600;
		$ms->store();
	}

	/**
	 * Get the time when the session will tiemout
	 * 
	 * @param      integer $sess Session ID
	 * @return     string
	 */
	private function _getTimeout($sess)
	{
		$mwdb =& MwUtils::getMWDBO();

		$ms = new MwSession($mwdb);
		$remaining = $ms->getTimeout();

		$tl = 'unknown';

		if (is_numeric($remaining)) 
		{
			$days_left = floor($remaining/60/60/24);
			$hours_left = floor(($remaining - $days_left*60*60*24)/60/60);
			$minutes_left = floor(($remaining - $days_left*60*60*24 - $hours_left*60*60)/60);
			$left = array($days_left, $hours_left, $minutes_left);

			$tl  = '';
			$tl .= ($days_left > 0)    ? $days_left .' days, '    : '';
			$tl .= ($hours_left > 0)   ? $hours_left .' hours, '  : '';
			$tl .= ($minutes_left > 0) ? $minutes_left .' minute' : '';
			$tl .= ($minutes_left > 1) ? 's' : '';
		}
		return $tl;
	}

	/**
	 * Display module content
	 * 
	 * @return     void
	 */
	public function display()
	{
		//include mw libraries
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'mw.utils.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'mw.class.php');
		
		//get user object
		$this->juser = JFactory::getUser();
		
		//get database object
		$this->database = JFactory::getDBO();
		
		//Get a connection to the middleware database
		$mwdb =& MwUtils::getMWDBO();
		
		//get tool paras
		$this->toolsConfig = JComponentHelper::getParams('com_tools');
		
		//set ACL for com_tools
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$jacl =& JFactory::getACL();
			$jacl->addACL('com_tools', 'manage', 'users', 'super administrator');
			$jacl->addACL('com_tools', 'manage', 'users', 'administrator');
			$jacl->addACL('com_tools', 'manage', 'users', 'manager');

			$authorized = $this->juser->authorize('com_tools', 'manage');
		}
		else
		{
			$authorized = JFactory::getUser()->authorise('core.manage', 'com_tools');
		}

		// Ensure we have a connection to the middleware
		$this->error = false;
		if (!$mwdb || !$mwdb->connected() || !$this->toolsConfig->get('mw_on') || ($this->toolsConfig->get('mw_on') > 1 && !$authorized)) 
		{
			$this->error = true;
			return false;
		}
		
		//run middleware command to create screenshots
		//only take snapshots if screenshots are on
		if ($this->params->get('show_screenshots', 0))
		{
			$cmd = "/bin/sh ". JPATH_SITE . "/components/com_tools/scripts/mw screenshot " . $this->juser->get('username') . " 2>&1 </dev/null";
			exec($cmd, $results, $status);
		}
		
		//get sessions
		$session = new MwSession($mwdb);
		$this->sessions = $session->getRecords( $this->juser->get('username'), '', false );
		
		// Push the module CSS to the template
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStyleSheet($this->module->module);
		
		// Add the JavaScript that does the AJAX magic to the template
		Hubzero_Document::addModuleScript($this->module->module);
		
		//output module
		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}

