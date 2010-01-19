<?php
/**
 * @package		HUBzero
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

function ximport($path) {
	return JLoader::import('.' . $path, JPATH_PLUGINS . DS . "xhub" . DS . "xlibraries");
}

ximport('xfactory');

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

	function parse(&$uri)
	{
		$vars = array();

		$session = &JFactory::getSession();
		$xhub = &XFactory::getHub();

		$redirect = $session->get('session.rewrite');

		if (!empty($redirect))
		{
			$query = $uri->getQuery();

			$session->clear('session.rewrite');

			if (!empty($query))
				$xhub->redirect($redirect . '?' . $query );
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
		$juser = &JFactory::getUser();
		//$xlog =& XFactory::getLogger();
		
		//$dbgname = $juser->get('guest')  ? 'guest' : $juser->get('username');
		//$xlog->logDebug("Page load by $dbgname");

		if (!$juser->get('guest'))
		{
			$xhub =& XFactory::getHub();
			$session =& JFactory::getSession();
			$registration_incomplete = $session->get('registration.incomplete');

			if ($registration_incomplete) 
			{ 
				if (($vars['option'] == 'com_hub') || ($vars['option'] == 'com_user'))
				{
					if ( ($vars['task'] == 'logout') )
						return $vars;
				}

				if ($vars['option'] == 'com_myaccount')
				{
					if ($vars['task'] == 'register')
						return $vars;
				}

				if ($uri->getPath() != 'legal/terms')
				{
					$vars = array();
					$vars['option'] = 'com_hub';
					$vars['view'] = 'registration';
					$vars['task'] = 'update';
					$vars['act'] = '';
				
					$this->setVars($vars);
					JRequest::set($vars, 'get', true );  // overwrite existing
					return $vars;
				}
			}

			$xprofile = &XFactory::getProfile();

			if (is_object($xprofile) && ($xprofile->get('emailConfirmed') != 1) && ($xprofile->get('emailConfirmed') != 3))
			{
				/*if ($vars['option'] == 'com_email') 
				{
					if (!empty($vars['task']))
					if ( ($vars['task'] == 'unconfirmed') || ($vars['task'] == 'change') || ($vars['task'] == 'resend') || ($vars['task'] == 'confirm') )
						return $vars;
				}
				else*/
				if ($vars['option'] == 'com_hub')
				{
					if (!empty($vars['task']))
					if ( ($vars['task'] == 'unconfirmed') || ($vars['task'] == 'change') || ($vars['task'] == 'resend') || ($vars['task'] == 'confirm') )
						return $vars;
					if ( ($vars['task'] == 'logout') )
						return $vars;
				}
				else if ($vars['option'] == 'com_user')
				{
					if ( ($vars['task'] == 'logout') )
						return $vars;
				}

				$vars = array();
				//$vars['option'] = 'com_email';
				$vars['option'] = 'com_hub';
				$vars['view'] = 'registration';
				$vars['task'] = 'unconfirmed';

				$this->setVars($vars);
				JRequest::set($vars, 'get', true ); // overwrite existing

				return $vars;
			}
		}

		return $vars;
	}

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

		return $uri;
	}

	function _parseRawRoute(&$uri)
	{
		$vars = array();

		$menu =& JSite::getMenu(true);

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
			$vars = $vars + $item->query;
		}

		// Set the active menu item
		$menu->setActive($this->getVar('Itemid'));

		return $vars;
	}

	function _parseSefRoute(&$uri)
	{
		$menu =& JSite::getMenu(true);
		$route = ltrim($uri->getPath(), '/');
		$vars = $uri->getQuery(true);

		if (empty($route) && empty($vars['option']))
			$route = 'home';
		
		//Need to reverse the array (highest sublevels first)
		$items = array_reverse($menu->getMenu());
		$mlen = 0;

		foreach ($items as $item)
		{
			$lenght = strlen($item->route); //get the lenght of the route

			if(($lenght > $mlen) && strpos($route.'/', $item->route.'/') === 0 && $item->type != 'menulink')
			{
				$mlen = $lenght;
				$mitem = $item;
			}
		}

		if (!empty($mitem)) // route matches a menu path
		{
			// TODO: review this section as it may be trying to handle
			// situations that may no longer exist

			$remainder = substr($route, $mlen+1);
			if (empty($remainder) || ($mitem->type == 'component' && $mitem->component != 'com_content'))
                        {
			if (($mitem->type == 'component') || empty($remainder))
				$route = $remainder;

			$vars = $mitem->query;

			if (!empty($mitem->component))
			{
				$vars['option'] = $mitem->component;

				if (empty($vars['Itemid']))
					$vars['Itemid'] = $mitem->id;
			}
			else if (empty($route))
			{
				$route = trim($mitem->link,'/');
				
				if (empty($vars['Itemid']))
					$vars['Itemid'] = $mitem->id;
			}
		}
		}

		$segments = explode('/', $route);

		if (empty($segments[0]))
			array_shift($segments);

		if (empty($vars['option']))
		{
			if (substr($route, 0, 10) == 'component/')
			{
				$route = ltrim( str_replace('component/'.$segments[1], '', $route), '/');
				$vars['option'] = 'com_'.$segments[1];
				array_shift($segments);
				array_shift($segments);
			}
			else if ($route == 'logout')
			{
				$vars['option'] = 'com_hub';
			}
			else if ($route == 'login')
			{
				$vars['option'] = 'com_hub';
			}
			else if ($route == 'register' || substr($route, 0, strlen('registration')) == 'registration' || $route == 'lostusername' || $route == 'lostpassword')
			{
				$vars['option'] = 'com_hub';
			}
			else if (substr($route, 0, 6) == 'search')
				$vars['option'] = 'com_xsearch';
		}

		// Set the active menu item
		if ( isset($vars['Itemid']) )
			$menu->setActive( $vars['Itemid'] );

		if (empty($vars['option']))
		{
			$file = JPATH_BASE.DS.'components'.DS.'com_'.$segments[0].DS.$segments[0].".php";
		
			if (file_exists($file)) 
			{
				$vars['option'] = 'com_' . $segments[0];
				array_shift($segments);
			}
		}

		if (empty($vars['option']) || $vars['option'] == 'com_content')
			$vars = array_merge($vars, $this->_parseContentRoute($segments));

		// Handle component route
 
 		if( !empty($vars['option']) && (($vars['option'] != 'com_content') || (!empty($route))))
		{
			$component = preg_replace('/[^A-Z0-9_\.-]/i', '', $vars['option']);
			// Use the component routing handler if it exists
			$path = JPATH_BASE.DS.'components'.DS.$component.DS.'router.php';
			
			if (file_exists($path))
			{
				//decode the route segments
				//$segments = $this->_decodeSegments($segments);

				require_once $path;
				$function = substr($component, 4).'ParseRoute';

				if (count($segments) || ($component != 'com_content'))
					$vars = array_merge($vars, $function($segments));

				// handle rerouting to symbolic com_content content

				if (!empty($vars['option']) && ($vars['option'] == 'com_content') && (!empty($vars['route'])))
					$vars = array_merge($vars, $this->_parseContentRoute(explode('/',ltrim($vars['route'],"/"))));
			}
		}
		else
		{
			//Set active menu item
			if($item =& $menu->getActive())
				$vars = array_merge($vars, $item->query);
		}

		if (empty($vars['Itemid']))
		{
		 	$item =& $menu->getActive();

			if (empty($item))
			{
				$item = $menu->getDefault();
				$menu->setActive( $item->id );
			}
			
			$vars['Itemid'] = $item->id;
		}
			
		if (empty($vars['option']))
		{
			jimport('joomla.juri');
			$db =& JFactory::getDBO();
			$sql = "SELECT * FROM #__redirection WHERE oldurl=" . $db->Quote($route);
		        $db->setQuery($sql);
		        $row =& $db->loadObject();

			if (!empty($row))
			{
				$myuri = JURI::getInstance( $row->newurl );
				$vars = $myuri->getQuery(true);
			}
			else
				JError::raiseError(404, JText::_("Page Not Found"));
		}

		$this->setVars($vars);
	
		//Set the variables

		return $this->_vars;
	}

	function _buildRawRoute(&$uri)
	{
	}

	function _buildSefRoute(&$uri)
	{
		// Get the route
		$route = $uri->getPath();

		// Get the query data
		$query = $uri->getQuery(true);

		if(!isset($query['option'])) {
			return;
		}

		$menu =& JSite::getMenu();

		/*
		 * Build the component route
		 */
		$component = preg_replace('/[^A-Z0-9_\.-]/i', '', $query['option']);
		$tmp = '';

		// Use the component routing handler if it exists
		$path = JPATH_BASE.DS.'components'.DS.$component.DS.'router.php';

		// Use the custom routing handler if it exists
		if (file_exists($path) && !empty($query))
		{
			require_once $path;
			$function = substr($component, 4).'BuildRoute';
			$parts = $function($query);

			// encode the route segments
			if ($component == 'com_content')
				$parts = $this->_encodeSegments($parts);

			$result = implode('/', $parts);
			$tmp = ($result != "") ? '/'.$result : '';
		}

		/*
		 * Build the application route
		 */
		if (!empty($query['Itemid']))
		{
			$item = $menu->getItem($query['Itemid']);

			if (!empty($item) && $query['option'] == $item->component) {
				$tmp = !empty($tmp) ? $item->route.'/'.$tmp : $item->route;
			}
		}
		else
		{
			//$tmp = 'component/'.substr($query['option'], 4).'/'.$tmp;
			if ($component != 'com_hub') {
				$tmp = substr($query['option'], 4).'/'.$tmp;
			}
		}

		$route .= '/'.$tmp;

		// Unset unneeded query information
		unset($query['Itemid']);
		unset($query['option']);

		//Set query again in the URI
		$uri->setQuery($query);
		$uri->setPath($route);
	}

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

	function _processBuildRules(&$uri)
	{
		// Make sure any menu vars are used if no others are specified
		if(($this->_mode != JROUTER_MODE_SEF) && $uri->getVar('Itemid') && count($uri->getQuery(true)) == 2)
		{
			$menu =& JSite::getMenu();

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

	function &_createURI($url)
	{
		//Create the URI
		$uri =& parent::_createURI($url);

		// Set URI defaults
		$menu =& JSite::getMenu();

		// Get the itemid form the URI
		$itemid = $uri->getVar('Itemid');

		if(is_null($itemid))
		{
			if($option = $uri->getVar('option'))
			{
				$item = $menu->getItem($this->getVar('Itemid'));
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

	function _parseContentRoute(&$segments)
	{
		$view = 'article';
		$menu =& JSite::getMenu(true);
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
					"#__content.alias='" . $alias . "' AND ";

				if (!empty($category))
					$query .= "#__content.catid=#__categories.id AND " . "#__categories.alias='" . $category . "' AND " .
						"#__content.sectionid=#__sections.id AND " . "#__sections.alias='" . $section . "'";
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
		$segments = $this->_decodeSegments($segments);
		$count = count($segments);
		
		if ($count > 3) {
			//echo "XRouter::_parseContentRoute(): Too many component segments<br>";
			return array();
		}

		$query = "SELECT `#__content`.id,`#__content`.alias,`#__content`.catid,`#__categories`.alias,`#__content`.sectionid,`#__sections`.alias " .
			"FROM `#__content`,`#__categories`,`#__sections` " .
			"WHERE `#__content`.catid=`#__categories`.id AND `#__content`.sectionid=`#__sections`.id ";

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
			//$routesegments = explode('/', $item->route);
			//$rcount = count($routesegments);
			//if ($rcount > 1) {
			//	$category = $routesegments[$rcount-1];
			//	$section = $routesegments[$rcount-2];
			//} 
			//else if ($rcount > 0) {
			//	$category = $routesegments[$rcount-1];
			//	$section = $routesegments[$rcount-1];
			//}

			$queryn = "SELECT #__content.id from `#__content`, `#__categories`, `#__sections` WHERE " .
				"#__content.alias='" . $page . "' AND " .
				"#__content.catid=#__categories.id AND " .
				"#__categories.alias='" . $category . "' AND " .
				"#__categories.section=#__sections.id AND " .
				"#__sections.alias='" . $section . "' AND #__content.state='1' LIMIT 1;";
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
		$row =& $db->loadResult();

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

	/**
	 * Returns a reference to the global Router object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 *	<pre> $router = &JRouter::getInstance();</pre>
	 *
	 * @access public
	 * @return JRouter The Router object.
	 * @since 1.5
	 */

	function &getInstance($options = array())
	{
		static $instance;

		if (!is_object($instance))
			$instance = new XRouter($options);

		return $instance;
	}

}

jimport('joomla.event.plugin');

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
	function plgSystemXhub(& $subject) {
		parent::__construct($subject, NULL);
	}

	function onAfterInitialise()
	{
		jimport('joomla.database.table');
		ximport('xsession');

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
			$router = XRouter::getInstance($options);
			$router->setMode($app->getCfg('sef'));
			JHTML::_('behavior.mootools');
			$jdocument =& JFactory::getDocument();
			$template = $app->getTemplate();

			if (file_exists( JPATH_ROOT . '/templates/' . $template . '/js/hub.js' ))
				$jdocument->addScript('/templates/' . $template . '/js/hub.js');
			else
				$jdocument->addScript('/components/com_hub/js/hub.js');

			if (file_exists( JPATH_ROOT . '/templates/' . $template . '/css/main.css'))
				$jdocument->addStyleSheet('/templates/' . $template . '/css/main.css');
			else
				$jdocument->addStyleSheet('/components/com_hub/css/main.css');
		}


		// Get the session object
		$session =& JFactory::getSession();

		$start = $session->get('session.timer.start');
		$now = $session->get('session.timer.now');
		$last = $session->get('session.timer.last');

		$sname = $session->getName();

		$lifetime = $app->getCfg('lifetime') * 60;
		$expired = ($now - $last) >= $lifetime;
		$newsession = ($start == $now) && ($start == $last);
		$knownsession = !empty($_COOKIE[$sname]) && $_COOKIE[$sname] == $session->getId();

		$table = & JTable::getInstance('session');
		$table->load( $session->getId() );
		XSessionHelper::purge();

		/*
		if (!empty($_COOKIE[$sname]) && $_COOKIE[$sname] != $session->getId())
		{
			echo "DETECTED LOGIN SESSION CHANGE.<br>";
			echo "OLD SESSION: $_COOKIE[$sname].<br>";
			echo "NEW SESSION: " . $session->getId() . "<br>";
		}
		*/

		/*
		if (($newsession && $knownsession) || $expired) // joomla started a new session or session is stale
		{
			if (empty($table->data)) {
				//$table->delete(); // delete the expired or improperly restarted session
				//session_regenerate_id();
				//unset($_COOKIE[$sname]); // make this appear as a new session below
				//$xtable->ip = $_SERVER['REMOTE_ADDR'];
				//$table->insert($session->getId(),0);
				//$xtable->insert($session->getId(),0);
				XSessionHelper::set_ip($session->getId(), $_SERVER['REMOTE_ADDR']);
			} 
		}
		*/

		if ( empty($_COOKIE[$sname]) ) // new session, log it
		{
			ximport('xlog');
			jimport('joomla.utilities.utility');
			$hash = JUtility::getHash('XHUB_REMEMBER');
			$username = '-';
			if ($str = JRequest::getString($hash, '', 'cookie', JREQUEST_ALLOWRAW | JREQUEST_NOTRIM))
			{
				jimport('joomla.utilities.simplecrypt');

				//Create the encryption key, apply extra hardening using the user agent string
				$key = JUtility::getHash(@$_SERVER['HTTP_USER_AGENT']);

				$crypt = new JSimpleCrypt($key);
				$str = $crypt->decrypt($str);
				$user = unserialize($str);
				$username = $user['username'];
			}

			$authlog = XFactory::getAuthLogger();
			$authlog->logAuth( $username . ' ' . $_SERVER['REMOTE_ADDR'] . ' detect');
		}

		// drop/refresh an xhub session tracker cookie
	
		// jimport('joomla.utilities.utility');
		// setcookie( JUtility::getHash('XHUB_TRACKER'), $session->getId(), $lifetime, '/' );
	
		//setcookie('b3192990284393a84bff56f22eb7042d', $session->getId(), $lifetime, '/' );

		XSessionHelper::set_ip($session->getId(), $_SERVER['REMOTE_ADDR']);
	}

	function onLoginFailure($response)
	{
		$authlog = XFactory::getAuthLogger();
		$authlog->logAuth( $_POST['username'] . ' ' . $_SERVER['REMOTE_ADDR'] . ' invalid');

		return true; /* we expect the Joomla user plugin to handle the rest */
	}
}

?>
