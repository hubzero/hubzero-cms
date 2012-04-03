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
 * @package	  HUBzero
 * @package   hubzero-cms
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'ximport'
 * 
 * Long description (if any) ...
 * 
 * @param  string $path Parameter description (if any) ...
 * @return string Return description (if any) ...
 */
function ximport($path) {
	if (substr(strtolower($path),0,7) == 'hubzero') {
		return JLoader::import('.' . str_replace('_', '.', $path), JPATH_ROOT . DS . 'libraries');
	} else {
		return JLoader::import('.' . $path, JPATH_PLUGINS . DS . "xhub" . DS . "xlibraries");
	}
}

ximport('Hubzero_Factory');

jimport('joomla.application.router');

/**
 * Class to create and parse routes for XHub application
 */

class XRouter extends JRouter
{
	/**
	 * Class constructor
	 *
	 * @access public
	 */
	function __construct($options = array()) {
		parent::__construct($options);
	}

	/**
	 * Short description for 'parse'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object &$uri Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	function parse(&$uri)
	{
		$vars = array();

			// Get the application
		$app =& JFactory::getApplication();

		if($app->getCfg('force_ssl') == 2 && strtolower($uri->getScheme()) != 'https') {
			//forward to https
			$uri->setScheme('https');
			$app->redirect($uri->toString());
		}

		// Get the path
		$path = $uri->getPath();

		//Remove the suffix
		if($this->_mode == JROUTER_MODE_SEF)
		{
			// Get the application
			$app =& JFactory::getApplication();

			if($app->getCfg('sef_suffix') && !(substr($path, -9) == 'index.php' || substr($path, -1) == '/'))
			{
				if($suffix = pathinfo($path, PATHINFO_EXTENSION))
				{
					$path = str_replace('.'.$suffix, '', $path);
					$vars['format'] = $suffix;
				}
			}
		}

		//Remove basepath
		$path = substr_replace($path, '', 0, strlen(JURI::base(true)));

		//Remove prefix
		$path = str_replace('index.php', '', $path);

		//Set the route
		$uri->setPath(trim($path , '/'));
		$vars += parent::parse($uri);

		/* HUBzero Extensions Follow to force registration and email confirmation */

		$juser = &JFactory::getUser();

		if (!$juser->get('guest'))
		{
			$xhub =& Hubzero_Factory::getHub();
			$session =& JFactory::getSession();
			$registration_incomplete = $session->get('registration.incomplete');

			if ($registration_incomplete)
			{
				if (($vars['option'] == 'com_user'))
				{
					if (($vars['view'] == 'logout') || ($vars['task'] == 'logout'))
						return $vars;
				}

				if ($vars['option'] == 'com_register') // register component can be accessed with incomplete registration
				{
						return $vars;
				}

				if ($uri->getPath() != 'legal/terms')
				{
					$vars = array();
					/*
					$vars['option'] = 'com_register';

					if ($juser->get('tmp_user'))
						$vars['task'] = 'create';
					else
						$vars['task'] = 'update';

					$vars['act'] = '';
                    */
                    
					$vars['option'] = 'com_members';
					$vars['id'] = $juser->get("id");
					$vars['active'] = 'profile';
					
					$this->setVars($vars);
					JRequest::set($vars, 'get', true );  // overwrite existing
					return $vars;
				}
			}

			$xprofile = &Hubzero_Factory::getProfile();

			if (is_object($xprofile) && ($xprofile->get('emailConfirmed') != 1) && ($xprofile->get('emailConfirmed') != 3))
			{
				if ($vars['option'] == 'com_user')
				{
					if (($vars['view'] == 'logout') || ($vars['task'] == 'logout'))
						return $vars;
				}
				else if ($vars['option'] == 'com_register')
				{
					if (!empty($vars['task']))
						if ( ($vars['task'] == 'unconfirmed') || ($vars['task'] == 'change') || ($vars['task'] == 'resend') || ($vars['task'] == 'confirm') )
							return $vars;
				}

				$vars = array();
				$vars['option'] = 'com_register';
				$vars['task'] = 'unconfirmed';

				$this->setVars($vars);
				JRequest::set($vars, 'get', true ); // overwrite existing

				return $vars;
			}
		}

		return $vars;
	}

	/**
	 * Short description for 'build'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $url Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	function &build($url)
	{
		$uri =& parent::build($url);

		// Get the path data
		$route = $uri->getPath();

		//Add the suffix to the uri
		if($this->_mode == JROUTER_MODE_SEF && $route)
		{
			$app =& JFactory::getApplication();

			if($app->getCfg('sef_suffix') && !(substr($route, -9) == 'index.php' || substr($route, -1) == '/'))
			{
				if($format = $uri->getVar('format', 'html'))
				{
					$route .= '.'.$format;
					$uri->delVar('format');
				}
			}

			if($app->getCfg('sef_rewrite'))
			{
				//Transform the route
				$route = str_replace('index.php/', '', $route);
			}
		}

		//Add basepath to the uri
		$uri->setPath(JURI::base(true).'/'.$route);

		if (!empty($_SERVER['REWROTE_FROM']))
		{
			if (stripos($uri->toString(), $_SERVER['REWROTE_TO']->getPath()) !== false)
			{
				$uri->setPath(str_replace($_SERVER['REWROTE_TO']->getPath(),'',$uri->getPath()));
				$uri->setHost($_SERVER['REWROTE_FROM']->getHost());
				$uri->setScheme($_SERVER['REWROTE_FROM']->getScheme());
			}
		}

		return $uri;
	}

	/**
	 * Short description for '_parseRawRoute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object &$uri Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	function _parseRawRoute(&$uri)
	{
		$vars = array();

		$menu =& JFactory::getApplication()->getMenu(true);

		//Handle an empty URL (special case)
		if(!$uri->getVar('Itemid') && !$uri->getVar('option'))
		{
			$item = $menu->getDefault();
			if(!is_object($item)) return $vars; // No default item set

			//Set the information in the request
			$vars = $item->query;

			//Get the itemid
			$vars['Itemid'] = $item->id;

			// Set the active menu item
			$menu->setActive($vars['Itemid']);

			return $vars;
		}

		//Get the variables from the uri
		$this->setVars($uri->getQuery(true));

		//Get the itemid, if it hasn't been set force it to null
		$this->setVar('Itemid', JRequest::getInt('Itemid', null));

		//Only an Itemid ? Get the full information from the itemid
		if(count($this->getVars()) == 1)
		{
			$item = $menu->getItem($this->getVar('Itemid'));
			if($item !== NULL && is_array($item->query)) {
				$vars = $vars + $item->query;
			}
		}

		// Set the active menu item
		$menu->setActive($this->getVar('Itemid'));

		return $vars;
	}

	/**
	 * Short description for '_parseSefRoute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object &$uri Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	function _parseSefRoute(&$uri)
	{
		$vars   = array();
		$app = JFactory::getApplication();

		if ($app->getCfg('sef_groups'))
		{
			$servername = $app->getCfg('live_site');
			$serveruri = JURI::getInstance($servername);
			$sfqdn = $serveruri->getHost();
			$rfqdn = $uri->getHost();

			if ($rfqdn != $sfqdn)
			{
				list($rhostname, $rdomainname) = explode('.', $rfqdn, 2);
				list($shostname, $sdomainname) = explode('.', $sfqdn, 2);

				if ( ($rdomainname == $sdomainname) || ($rdomain = $sfqdn))
				{
					ximport('Hubzero_Group');
					$suri = JURI::getInstance();
					$group = Hubzero_Group::getInstance($rhostname);

					if (!empty($group) && ($group->type == 3)) // only special groups get internal redirection abilities
					{
						$_SERVER['REWROTE_FROM'] = clone($suri);
						$uri->setHost($sfqdn);
						$uri->setPath('groups/'.$rhostname.'/'.$uri->getPath());
						$suri->setHost($sfqdn);
						$suri->setPath('/groups/'.$rhostname.'/'.$suri->getPath());
						$_SERVER['HTTP_HOST'] = $suri->getHost();
						$_SERVER['SERVER_NAME'] = $suri->getHost();
						$_SERVER['SCRIPT_URI'] = $suri->toString(array('scheme','host','port','path'));
						$_SERVER['REDIRECT_SCRIPT_URI'] = $suri->toString(array('scheme','host','port','path'));
						$_SERVER['REDIRECT_SCRIPT_URL'] = $suri->getPath();
						$_SERVER['REDIRECT_URL'] = $suri->getPath();
						$_SERVER['SCRIPT_URL'] = $suri->getPath();
						$_SERVER['REQUEST_URI'] = $suri->toString(array('path','query','fragment'));
						$suri->setPath('/groups/'.$rhostname);
						$_SERVER['REWROTE_TO'] = clone($suri);
					}
				}
			}

		}

		$menu  =& JSite::getMenu(true);
		$route = $uri->getPath();

		//Get the variables from the uri
		$vars = $uri->getQuery(true);

		//Handle an empty URL (special case)
		if(empty($route))
		{

			//If route is empty AND option is set in the query, assume it's non-sef url, and parse apropriately
			if(isset($vars['option']) || isset($vars['Itemid'])) {
				return $this->_parseRawRoute($uri);
			}

			$item = $menu->getDefault();

			//Set the information in the request
			$vars = $item->query;

			//Get the itemid
			$vars['Itemid'] = $item->id;

			// Set the active menu item
			$menu->setActive($vars['Itemid']);

			return $vars;
		}

		/*
		 * Parse the application route
		 */

		if(substr($route, 0, 9) == 'component')
		{
			$segments	= explode('/', $route);
			$route      = str_replace('component/'.$segments[1], '', $route);

			$vars['option'] = 'com_'.$segments[1];
			$vars['Itemid'] = null;
		}
		else
		{
			//Need to reverse the array (highest sublevels first)
			$items = array_reverse($menu->getMenu());

			foreach ($items as $item)
			{
				$lenght = strlen($item->route); //get the lenght of the route

				if($lenght > 0 && strpos($route.'/', $item->route.'/') === 0 && $item->type != 'menulink')
				{
					// HUBzero Extension to pass local URLs through menu unchanged

					if ($item->type == 'url') { // Pass local URLs through, but record Itemid
						if (strpos("://",$item->route[0]) === false) {
							$vars['Itemid'] = $item->id;
							break;
						}
					}

					// End HUBzero Extension to pass local URLs through menu unchanged

					$route   = substr($route, $lenght);

					$vars['Itemid'] = $item->id;
					$vars['option'] = $item->component;
					break;
				}
			}
		}

		// HUBzero Extension to parse com_content component specially

		if (empty($vars['option'])) {
			$vars = $this->_parseContentRoute(explode('/',ltrim($route,"/")));
			if (!empty($vars['option'])) {
				$route = false;
			}
		}

		// End HUBzero Extension to parse com_content component specially

		// HUBzero Extension to route based on unprefixed component name (if other routing fails to match)

		if (empty($vars['option']))
		{
			$segments	= explode('/', $route);

			if ($segments[0] == 'search') {   // @FIXME: search component should probably be configurable

				$xhub = Hubzero_Factory::getHub();
				$segments[0] = $xhub->getCfg('search','ysearch');
			}

			$file = JPATH_BASE.DS.'components'.DS.'com_'.$segments[0].DS.$segments[0].".php";

			if (file_exists($file))
			{
				$vars['option'] = 'com_'.$segments[0];

				if (!isset($vars['Itemid'])) {
					$vars['Itemid'] = null;
				}

				$route = preg_replace('/^' . $segments[0]. '/', '', $route);
			}
		}

		// End HUBzero Extension to route based on unprefixed component name (if other routing fails to match)

		// Set the active menu item
		if ( isset($vars['Itemid']) ) {
			$menu->setActive(  $vars['Itemid'] );
		}

		if (empty($vars['Itemid'])) {
			$vars['Itemid'] =  '-1';
		}

		//Set the variables
		$this->setVars($vars);

		/*
		 * Parse the component route
		 */
		if(!empty($route) && isset($this->_vars['option']) )
		{
			$segments = explode('/', $route);
			array_shift($segments);

			// Handle component	route
			$component = preg_replace('/[^A-Z0-9_\.-]/i', '', $this->_vars['option']);

			// Use the component routing handler if it exists
			$path = JPATH_SITE.DS.'components'.DS.$component.DS.'router.php';

			if (file_exists($path) && count($segments))
			{
				if ($component != "com_search") { // Cheap fix on searches
					//decode the route segments
					if ($component == "com_content") { // @FIXME: HUBZERO don't do : to - conversion except in com_content
						$segments = $this->_decodeSegments($segments);
					}
				}
				else { // fix up search for URL
					$total = count($segments);
					for($i=0; $i<$total; $i++) {
						// urldecode twice because it is encoded twice
						$segments[$i] = urldecode(urldecode(stripcslashes($segments[$i])));
					}
				}

				require_once $path;
				$function =  substr($component, 4).'ParseRoute';
				$vars =  $function($segments);

				$this->setVars($vars);
			}
		}
		else
		{
			// HUBzero Extension to check redirection table if otherwise unable to match URL to content

			if (!isset($vars['option'])) {
				jimport('joomla.juri');
				$db =& JFactory::getDBO();
				$sql = "SELECT * FROM #__redirection WHERE oldurl=" . $db->Quote($route);
	        	$db->setQuery($sql);
		        $row = $db->loadObject();

				if (!empty($row))
				{
					$myuri = JURI::getInstance( $row->newurl );
					$vars = $myuri->getQuery(true);

					if ( isset($vars['Itemid']) ) {
						$menu->setActive(  $vars['Itemid'] );
					}
				}
			}

			// End HUBzero Extension to check redirection table if otherwise unable to match URL to content

			//Set active menu item
			if($item =& $menu->getActive()) {
				$vars = $item->query;
			}

		}

		// HUBzero Extension to pass common query parameters to apache (for logging)

		if (!empty($vars['option']))
			apache_note('component',$vars['option']);
		if (!empty($vars['view']))
			apache_note('view',$vars['view']);
		if (!empty($vars['task']))
			apache_note('task',$vars['task']);
		if (!empty($vars['action']))
			apache_note('action',$vars['action']);
		if (!empty($vars['id']))
			apache_note('action',$vars['id']);

		// End HUBzero Extension pass common query parameters to apache (for logging)

		return $vars;
	}

	/**
	 * Short description for '_buildRawRoute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$uri Parameter description (if any) ...
	 * @return     void
	 */
	function _buildRawRoute(&$uri)
	{
	}

	/**
	 * Short description for '_buildSefRoute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object &$uri Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	function _buildSefRoute(&$uri)
	{
		// Get the route
		$route = $uri->getPath();

		// Get the query data
		$query = $uri->getQuery(true);

		if(!isset($query['option'])) {
			// HUBzero Extension to handle section, category, alias routing of com_content pages

			$parts = $this->_buildContentRoute($query);

			if (empty($parts)) {
				return;
			}

			$query['option'] = 'com_content';
			$parts = $this->_encodeSegments($parts);
			$result	= implode('/', $parts);
			$tmp	= ($result != "") ? '/'.$result : '';
			//$tmp = 'component/'.substr($query['option'], 4).'/'.$tmp;
			$route .= '/'.$tmp;

			// Unset unneeded query information
			unset($query['Itemid']);
			unset($query['option']);

			//Set query again in the URI
			$uri->setQuery($query);
			$uri->setPath($route);
			// End HUBzero Extension to handle section, category, alias routing of com_content pages
			return;
		}

		$menu =& JFactory::getApplication()->getMenu();

		/*
		 * Build the component route
		 */
		$component	= preg_replace('/[^A-Z0-9_\.-]/i', '', $query['option']);
		$tmp 		= '';

		// Use the component routing handler if it exists
		$path = JPATH_BASE.DS.'components'.DS.$component.DS.'router.php';

		// Use the custom routing handler if it exists
		if (file_exists($path) && !empty($query))
		{
			require_once $path;
			$function	= substr($component, 4).'BuildRoute';
			$parts		= $function($query);

			// encode the route segments
			if ($component != "com_search") { // Cheep fix on searches
				if ($component == "com_content") { // @FIXME: quick fix for joomla breaking ':' in urls in com_wiki/com_topics (others?) {
					$parts = $this->_encodeSegments($parts);
				}
			}
			else { // fix up search for URL
				$total = count($parts);
				for($i=0; $i<$total; $i++) {
					// urlencode twice because it is decoded once after redirect
					$parts[$i] = urlencode(urlencode(stripcslashes($parts[$i])));
				}
			}

			$result	= implode('/', $parts);
			$tmp	= ($result != "") ? '/'.$result : '';
		}

		/*
		 * Build the application route
		 */
		$built = false;
		if (isset($query['Itemid']) && !empty($query['Itemid']))
		{
			$item = $menu->getItem($query['Itemid']);

			if (is_object($item) && $query['option'] == $item->component) {
				$tmp = $item->route.$tmp;
				$built = true;
			}
		}

		if(!$built) {
			//$tmp = 'component/'.substr($query['option'], 4).'/'.$tmp;
			$tmp = substr($query['option'], 4).'/'.$tmp; /* HUBZERO: strip 'component' from url */
		}

		$route .= '/'.$tmp;

		// Unset unneeded query information
		unset($query['Itemid']);
		unset($query['option']);

		//Set query again in the URI
		$uri->setQuery($query);
		$uri->setPath($route);
	}

	/**
	 * Short description for '_processParseRules'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object &$uri Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	function _processParseRules(&$uri)
	{
		// Process the attached parse rules
		$vars = parent::_processParseRules($uri);

		// Process the pagination support
		if($this->_mode == JROUTER_MODE_SEF)
		{
			$app =& JFactory::getApplication();

			if($start = $uri->getVar('start'))
			{
				$uri->delVar('start');
				$vars['limitstart'] = $start;
			}
		}

		return $vars;
	}

	/**
	 * Short description for '_processBuildRules'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object &$uri Parameter description (if any) ...
	 * @return     void
	 */
	function _processBuildRules(&$uri)
	{
		// Make sure any menu vars are used if no others are specified
		if(($this->_mode != JROUTER_MODE_SEF) && $uri->getVar('Itemid') && count($uri->getQuery(true)) == 2)
		{
			$menu =& JFactory::getApplication()->getMenu();

			// Get the active menu item
			$itemid = $uri->getVar('Itemid');
			$item = $menu->getItem($itemid);

			$uri->setQuery($item->query);
			$uri->setVar('Itemid', $itemid);
		}

		// Process the attached build rules
		parent::_processBuildRules($uri);

		// Get the path data
		$route = $uri->getPath();

		if($this->_mode == JROUTER_MODE_SEF && $route)
		{
			$app =& JFactory::getApplication();

			if ($limitstart = $uri->getVar('limitstart'))
			{
				$uri->setVar('start', (int) $limitstart);
				$uri->delVar('limitstart');
			}
		}

		$uri->setPath($route);
	}

	/**
	 * Short description for '_createURI'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $url Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	function &_createURI($url)
	{
		//Create the URI
		$uri =& parent::_createURI($url);

		// Set URI defaults
		$menu =& JFactory::getApplication()->getMenu();

		// Get the itemid form the URI
		$itemid = $uri->getVar('Itemid');

		if(is_null($itemid))
		{
			if($option	= $uri->getVar('option'))
			{
				$item	= $menu->getItem($this->getVar('Itemid'));
				if(isset($item) && $item->component == $option) {
					$uri->setVar('Itemid', $item->id);
				}
			}
			else
			{
				if($option = $this->getVar('option')) {
					$uri->setVar('option', $option);
				}

				if($itemid = $this->getVar('Itemid')) {
					$uri->setVar('Itemid', $itemid);
				}
			}
		}
		else
		{
			if(!$uri->getVar('option'))
			{
				$item = $menu->getItem($itemid);
				$uri->setVar('option', $item->component);
			}
		}
		return $uri;
	}

	/**
	 * Short description for '_buildContentRoute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array &$query Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	function _buildContentRoute(&$query)
	{
		$segments = array();

		if (!empty($query['view']) && $query['view'] != 'article')
			return $segments;

		if (empty($query['id']))
		{
			$section = empty($query['section']) ? '' : $query['section'];
			$category = empty($query['category']) ? '' : $query['category'];
			$alias = empty($query['alias']) ? '' : $query['alias'];

			if (!empty($section))
				$segments[] = $section;

			if (!empty($category) && $category != $section)
				$segments[] = $category;

			if (!empty($alias) && $alias != $category)
				$segments[] = $alias;

			return($segments);
		}

		$db =& JFactory::getDBO();
		$id = intval($query['id']);

		$sql = "SELECT #__sections.alias AS section, #__categories.alias AS category, #__content.alias AS alias FROM jos_sections, jos_categories, jos_content WHERE #__content.id='" . $id . "' AND #__content.sectionid=#__sections.id AND #__content.catid=#__categories.id LIMIT 1;";
		$db->setQuery($sql);
		$row =& $db->loadObject();

		if (!empty($row))
		{
			$segments[] = $row->section;

			if ($row->category != $row->section)
				$segments[] = $row->category;

			if ($row->alias != $row->category)
				$segments[] = $row->alias;

			unset($query['view']);
			unset($query['id']);
			unset($query['catid']);

			return $segments;
		}
		else {
			$sql = "SELECT #__content.alias AS alias FROM jos_content WHERE #__content.id='" . $id . "' AND #__content.sectionid=0 AND #__content.catid=0 LIMIT 1;";
			$db->setQuery($sql);
			$row =& $db->loadObject();

			if (!empty($row)) {
				$segments[] = $row->alias;
				unset($query['view']);
				unset($query['id']);
				return $segments;
			}
		}

		$segments[] = 'content';
		$segments[] = $id;
		unset($query['view']);
		unset($query['id']);
		return $segments;
	}

	/**
	 * Short description for '_parseContentRoute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array &$segments Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	function _parseContentRoute(&$segments)
	{
		$view = 'article';
		$menu =& JFactory::getApplication()->getMenu(true);
		$item =& $menu->getActive();
		$db = & JFactory::getDBO();
		$count = count($segments);

		if (($count == 1) && (is_numeric($segments[0])))
		{
			$vars['option'] = 'com_content';
			return $vars;
		}

		if (empty($segments) || empty($segments[0]))
		{
			return array();

			if (empty($item->query['view']) || $item->query['view'] != 'article')
				return array();

			$section = empty($item->query['section']) ? '' : $item->query['section'];
			$category = empty($item->query['category']) ? '' : $item->query['category'];
			$alias = empty($item->query['alias']) ? '' : $item->query['alias'];

			if (empty($section) && !empty($category))
				$section = $category;
			else if (!empty($section) && empty($category))
				$category = $section;

			if (empty($alias) && !empty($category))
				$alias = $category;

			if (!empty($alias)) {

				$query = "SELECT #__content.id from `#__content`, `#__categories`, `#__sections` WHERE " .
					"#__content.alias='" . mysql_real_escape_string($alias) . "' AND ";

				if (!empty($category))
					$query .= "#__content.catid=#__categories.id AND " . "#__categories.alias='" . mysql_real_escape_string($category) . "' AND " .
						"#__content.sectionid=#__sections.id AND " . "#__sections.alias='" . mysql_real_escape_string($section) . "'";
				else
					$query .= "#__content.catid=0 AND #__content.sectionid=0";

				$query .= " AND #__content.state='1' LIMIT 1;";

				$db->setQuery($query);
				$row =& $db->loadResult();
				$vars['id'] = $row;
			}

			return $vars;
		}

		if (!empty($id) || empty($segments[0]))
			array_shift($segments);

		//decode the route segments
		//$segments = $this->_decodeSegments($segments);
		$count = count($segments);

		if ($count > 3) {
			//echo "XRouter::_parseContentRoute(): Too many component segments<br>";
			return array();
		}

		$query = "SELECT `#__content`.id,`#__content`.alias,`#__content`.catid,`#__categories`.alias,`#__content`.sectionid,`#__sections`.alias " .
			"FROM `#__content`,`#__categories`,`#__sections` " .
			"WHERE `#__content`.catid=`#__categories`.id AND `#__content`.sectionid=`#__sections`.id ";

		$segments = array_map('mysql_real_escape_string', $segments);
		if ($count == 3)
		{
			if (is_numeric($segments[2]))
				$query .= " AND #__content.id='" . $segments[2] . "' ";
			else
				$query .= " AND #__content.alias='" . $segments[2] . "' ";

			if (is_numeric($segments[1]))
				$query .= " AND #__content.catid='" . $segments[1] . "' ";
			else
				$query .= " AND #__categories.alias='" . $segments[1] . "' ";

			if (is_numeric($segments[0]))
				$query .= " AND #__content.sectionid='" . $segments[0] . "' ";
			else
				$query .= " AND #__sections.alias='" . $segments[0] . "' ";

			$query .= " AND #__content.state='1' LIMIT 1;";
		}
		else if ($count == 2)
		{
			if (!empty($id)) {
				$query = "SELECT #__content.id from `#__content`, `#__categories`, `#__sections` WHERE " .
					"#__content.alias='" . $segments[1] . "' AND " .
					"#__content.catid=#__categories.id AND " .
					"#__categories.alias='" . $segments[0] . "' AND " .
					"#__categories.section=#__sections.id AND " .
					"#__sections.id=(SELECT sectionid FROM `#__content` WHERE id='" . $id . "') AND #__content.state='1' LIMIT 1;";
			} else {
				$query = "SELECT #__content.id from `#__content`, `#__categories`, `#__sections` WHERE " .
					"#__content.alias='" . $segments[1] . "' AND " .
					"#__content.catid=#__categories.id AND " .
					"#__categories.alias='" . $segments[0] . "' AND " .
					"#__categories.section=#__sections.id AND " .
					"#__sections.alias='" . $segments[0] . "' AND #__content.state='1' LIMIT 1;";
			}
		}
		else if ($count == 1 && 0)
		{
			$query = "SELECT #__content.id from `#__content`, `#__categories`, `#__sections` WHERE " .
				"#__content.alias='" . $segments[0] . "' AND " .
				"#__content.catid=(SELECT catid FROM `#__content` WHERE id='" . $id ."') AND #__content.state='1' LIMIT 1;";
		}
		else if ($count == 1)
		{
			$page = $segments[0];
			$category = $segments[0];
			$section = $segments[0];

			$query = "SELECT #__content.id from `#__content`, `#__categories`, `#__sections` WHERE " .
				"#__content.alias='" . $page . "' AND " .
				"(" .
					"(#__content.catid=#__categories.id AND " . "#__categories.alias='" . $category . "' AND " .
					"#__content.sectionid=#__sections.id AND " . "#__sections.alias='" . $section . "')" .
					" OR " .
					"(#__content.catid=0 AND #__content.sectionid=0) " .
				") AND #__content.state='1' LIMIT 1;";

		}
		else if ($count == 0)
		{
			$page = '';
			$category = '';
			$section = '';

			$routesegments = explode('/', $item->route);
			$rcount = count($routesegments);
			//echo "routesegments = "; print_r($routesegments); echo "<br>";
			if ($rcount > 2) {
				$section = $routesegments[$rcount-3];
				$category = $routesegments[$rcount-2];
				$page = $routesegments[$rcount-1];
			}
			if ($rcount > 1) {
				$section = $routesegments[$rcount-2];
				$category = $routesegments[$rcount-1];
				$page = $category;
			}
			else if ($rcount > 0) {
				$section = $routesegments[$rcount-1];
				$category = $section;
				$page = $category;
			}

			$query = "SELECT #__content.id from `#__content`, `#__categories`, `#__sections` WHERE " .
				"#__content.alias='" . $page . "' AND " .
				"#__content.catid=#__categories.id AND " .
				"#__categories.alias='" . $category . "' AND " .
				"#__categories.section=#__sections.id AND " .
				"#__sections.alias='" . $section . "' AND #__content.state='1' LIMIT 1;";
		}

		$db->setQuery($query);
		$row = $db->loadResult();

		if (!empty($row))
		{
			$segments = array();
			$vars['option'] = 'com_content';
			$vars['id'] = $row;
			$vars['view'] = 'article';
			$item->query['view'] = 'article';
			return $vars;
		}

		return array();
	}
}

jimport('joomla.event.plugin');

/**
 * Short description for 'class'
 * 
 * Long description (if any) ...
 */
class plgSystemXhub extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @since 1.5
	 */
	function plgSystemXhub(& $subject) 
	{
		parent::__construct($subject, NULL);
	}

	/**
	 * Short description for 'onAfterRoute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	function onAfterRoute()
	{
		$app = &JFactory::getApplication();
		if (!JPluginHelper::isEnabled('system', 'jquery'))
		{
			JHTML::_('behavior.mootools');
		}
	}

	/**
	 * Short description for 'onAfterInitialise'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	function onAfterInitialise()
	{
		$app = &JFactory::getApplication();
		$router = &$app->getRouter();
		$options = array();

		// Get routing mode
		$options['mode'] = $app->getCfg('sef');
		if($app->getCfg('sef_rewrite')) {
			$options['mode'] = 2;
		}

		// Create a JRouter object
		if (get_class($router) == 'JRouterSite') {
			$router = new XRouter($options);
			$router->setMode($app->getCfg('sef'));
		}

		// Get the session object
		$session =& JFactory::getSession();
		$sid = $session->getId();
		$start = $session->get('session.timer.start');
		$now = $session->get('session.timer.now');
		$last = $session->get('session.timer.last');

		$sname = $session->getName();

		$lifetime = $app->getCfg('lifetime') * 60;
		$expired = ($now - $last) >= $lifetime;
		$newsession = ($start == $now) && ($start == $last);
		$knownsession = !empty($_COOKIE[$sname]) && $_COOKIE[$sname] == $sid;

		$myuser = $session->get('user');
		$jid = (is_object($myuser)) ? $myuser->get('id') : '0';

		if (empty($jid))
		{
			apache_note('userid','-');
			apache_note('auth','-');
		}
		else
		{
			apache_note('userid',$jid);
			apache_note('auth','session');
		}

		apache_note('jsession',$sid);

		if ( empty($jid) )
		{
			ximport('Hubzero_Log');
			jimport('joomla.utilities.utility');
			jimport('joomla.user.helper');
			$hash = JUtility::getHash('XHUB_REMEMBER');
			$username = '-';
			if ($str = JRequest::getString($hash, '', 'cookie', JREQUEST_ALLOWRAW | JREQUEST_NOTRIM))
			{
				jimport('joomla.utilities.simplecrypt');

				//Create the encryption key, apply extra hardening using the user agent string
				$key = JUtility::getHash(@$_SERVER['HTTP_USER_AGENT']);

				//$crypt = new JSimpleCrypt($key);
				$crypt = new JSimpleCrypt();
				$str = $crypt->decrypt($str);
				$user = @unserialize($str);
				// We should store userid not username in cookie, will save us a database query here
				$username = $user['username'];

				if ($id = JUserHelper::getUserId($username)) {
					$myuser = JUser::getInstance($id);
					if (is_object($myuser))
					{
						apache_note('userid',$myuser->get('id'));
						apache_note('auth','cookie');
                        $authlog = Hubzero_Factory::getAuthLogger();
                    	$authlog->logAuth( $username . ' ' . $_SERVER['REMOTE_ADDR'] . ' detect');
					}
				}
			}
		}
	}

	/**
	 * Short description for 'onLoginFailure'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $response Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	function onLoginFailure($response)
	{
		$authlog = Hubzero_Factory::getAuthLogger();
		$authlog->logAuth( $_POST['username'] . ' ' . $_SERVER['REMOTE_ADDR'] . ' invalid');
		apache_note('auth','invalid');

		return true;
	}
}

?>
