<?php
/**
 * @package		Joomla.Site
 * @subpackage	Application
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

/**
 * Class to create and parse routes for the site application
 *
 * @package		Joomla.Site
 * @subpackage	Application
 * @since		1.5
 */
class JRouterSite extends JRouter
{
	/**
	 * Component-router objects
	 *
	 * @var    array
	 * @since  3.3
	 */
	protected $componentRouters = array();

	/**
	 * Parse the URI
	 *
	 * @param	object	The URI
	 *
	 * @return	array
	 */
	public function parse(&$uri)
	{
		$vars = array();

		// Get the application
		$app = JApplication::getInstance('site');

		if ($app->getCfg('force_ssl') == 2 && strtolower($uri->getScheme()) != 'https') {
			//forward to https
			$uri->setScheme('https');
			$app->redirect((string)$uri);
		}

		// Get the path
		$path = $uri->getPath();

		// Remove the base URI path.
		$path = substr_replace($path, '', 0, strlen(JURI::base(true)));

		// Check to see if a request to a specific entry point has been made.
		if (preg_match("#.*?\.php#u", $path, $matches)) {

			// Get the current entry point path relative to the site path.
			$scriptPath = realpath($_SERVER['SCRIPT_FILENAME'] ? $_SERVER['SCRIPT_FILENAME'] : str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']));
			$relativeScriptPath = str_replace('\\', '/', str_replace(JPATH_SITE, '', $scriptPath));

			// If a php file has been found in the request path, check to see if it is a valid file.
			// Also verify that it represents the same file from the server variable for entry script.
			if (file_exists(JPATH_SITE.$matches[0]) && ($matches[0] == $relativeScriptPath)) {

				// Remove the entry point segments from the request path for proper routing.
				$path = str_replace($matches[0], '', $path);
			}
		}

		// Identify format
		if ($this->_mode == JROUTER_MODE_SEF) {
			if ($app->getCfg('sef_suffix') && !(substr($path, -9) == 'index.php' || substr($path, -1) == '/')) {
				if ($suffix = pathinfo($path, PATHINFO_EXTENSION)) {
					$vars['format'] = $suffix;
				}
			}
		}

		//Remove prefix
		$path = str_replace('index.php', '', $path);

		//Set the route
		$uri->setPath(trim($path , '/'));

		$vars += parent::parse($uri);

		if (empty($vars['option']) && isset($_POST['option']))
		{
			$vars['option'] = JRequest::getCmd('option', '', 'post');
		}

		if (empty($vars['option']))
		{
			JError::raiseError(404, JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
		}

		/* START: HUBzero Extensions Follow to force registration and email confirmation */
		$juser = JFactory::getUser();

		if (!$juser->get('guest'))
		{
			$session = JFactory::getSession();
			$registration_incomplete = $session->get('registration.incomplete');

			if ($registration_incomplete)
			{
				if ($vars['option'] == 'com_users')
				{
					if (($vars['view'] == 'logout') || ($vars['task'] == 'logout'))
						return $vars;
				}

				if ($vars['option'] == 'com_members'
				 && ((isset($vars['controller']) && $vars['controller'] == 'register') || (isset($vars['view']) && $vars['view'] == 'register'))) // register component can be accessed with incomplete registration
				{
					$session->set('linkaccount', false);
					return $vars;
				}

				if ($uri->getPath() != 'legal/terms')
				{
					$originalVars = $vars;
					$vars = array();

					if ($juser->get('tmp_user')) // joomla tmp users
					{
						$vars['option'] = 'com_members';
						$vars['controller']	= 'register';
						$vars['task']	= 'create';
						$vars['act']	= '';
					}
					else if (substr($juser->get('email'), -8) == '@invalid') // force auth_link users to registration update page
					{
						// First, allow ticket creation
						if ($originalVars['option'] == 'com_support' && $originalVars['controller'] == 'tickets' && $originalVars['task'] == 'save')
						{
							// Do nothing...allow it to pass through
							$vars = $originalVars;
						}
						elseif ($session->get('linkaccount', true))
						{
							$vars['option'] = 'com_users';
							$vars['view']   = 'link';
						}
						else
						{
							$vars['option'] = 'com_members';
							$vars['controller']	= 'register';
							$vars['task']	= 'update';
							$vars['act']	= '';
						}
					}
					else // otherwise, send to profile to fill in missing info
					{
						$o 	= JRequest::getVar('option', '');
						$t 	= JRequest::getVar('task', '');
						$nh = JRequest::getInt('no_html', 0);

						//are we trying to use the tag autocompletor when forcing registration update?
						if ($o == 'com_tags' && $t == 'autocomplete' && $nh)
						{
							$vars['option'] = 'com_tags';
						}
						else
						{
							$vars['option'] = 'com_members';
							$vars['id']		= $juser->get("id");
							$vars['active'] = 'profile';
						}
					}

					$this->setVars($vars);
					JRequest::set($vars, 'get', true );  // overwrite existing
					return $vars;
				}
			}

			$xprofile = \Hubzero\User\Profile::getInstance($juser->get('id'));

			if (is_object($xprofile) && ($xprofile->get('emailConfirmed') != 1) && ($xprofile->get('emailConfirmed') != 3))
			{
				if ($vars['option'] == 'com_users')
				{
					if ((isset($vars['view']) && $vars['view'] == 'logout') || (isset($vars['task']) && $vars['task'] == 'logout'))
						return $vars;
				}
				else if ($uri->getPath() == 'legal/terms')
				{
					return $vars;
				}
				else if ($vars['option'] == 'com_members' && ((isset($vars['controller']) && $vars['controller'] == 'register') || (isset($vars['view']) && $vars['view'] == 'register')))
				{
					if (!empty($vars['task']))
						if ( ($vars['task'] == 'unconfirmed') || ($vars['task'] == 'change') || ($vars['task'] == 'resend') || ($vars['task'] == 'confirm') )
						return $vars;
				}
				// allow picture to show if not confirmed
				else if ($vars['option'] == 'com_members'
					&& (isset($vars['task']) && $vars['task'] == 'download')
					&& (isset($vars['active']) && strpos($vars['active'], 'Image:') !== false)
					&& JFactory::getSession()->get('userchangedemail', 0) == 1)
				{
					return $vars;
				}

				$vars = array();
				$vars['option'] = 'com_members';
				$vars['controller'] = 'register';
				$vars['task'] = 'unconfirmed';

				$this->setVars($vars);
				JRequest::set($vars, 'get', true ); // overwrite existing

				return $vars;
			}

			if (!$juser->get('approved'))
			{
				if ($vars['option'] == 'com_users')
				{
					if (($vars['view'] == 'logout') || ($vars['task'] == 'logout'))
					{
						return $vars;
					}
				}
				else if ($uri->getPath() == 'legal/terms')
				{
					return $vars;
				}
				else if ($vars['option'] == 'com_support' && $vars['controller'] == 'tickets' && $vars['task'] == 'save')
				{
					return $vars;
				}
				else if ($vars['option'] == 'com_support' && $vars['controller'] == 'tickets' && $vars['task'] == 'new')
				{
					return $vars;
				}

				$vars = array();
				$vars['option'] = 'com_users';
				$vars['view']   = 'unapproved';

				$this->setVars($vars);
				JRequest::set($vars, 'get', true ); // overwrite existing

				return $vars;
			}

			$badpassword = $session->get('badpassword',false);
			$expiredpassword = $session->get('expiredpassword',false);

			if ($badpassword || $expiredpassword) {
				if ($vars['option'] == 'com_members' && isset($vars['task']) && $vars['task'] == 'changepassword') {
					return $vars;
				}

				if ($vars['option'] == 'com_users' && ($vars['view'] == 'logout' || $vars['task'] == 'logout' || JRequest::getWord('task') == 'logout')) {
					return $vars;
				}

				if ($vars['option'] == 'com_support' && $vars['task'] == 'save') {
					return $vars;
				}

				if ($uri->getPath() == 'legal/terms')
				{
					return $vars;
				}

				// @FIXME: should double check shadowFlag here in case password gets chanegd
				// out of band.

				// @FIXME: should we clear POST and GET data

				$vars = array();
				$vars['option'] = 'com_members';
				$vars['task'] = 'changepassword';

				if ($badpassword) {
					$vars['message'] = "Your password does not meet current site requirements. Please change your password now.";
				}

				if ($expiredpassword) {
					$vars['message'] = "Your password has expired. Please change your password now.";
				}

				$this->setVars($vars);
				JRequest::set($vars, 'get', true ); // overwrite existing
			}
		}

		// Call system plugins for parsing routes
		if ($responses = JDispatcher::getInstance()->trigger('onParseRoute', array($vars)))
		{
			// We're assuming here that if a plugin returns vars, we'll take them wholesale.
			// This also means that plugins need to be ordered in terms of priority, as we'll
			// return the first response that isn't empty.
			foreach ($responses as $response)
			{
				if (is_array($response) && !empty($response))
				{
					$this->setVars($response);
					JRequest::set($response, 'get', true);
					return $response;
				}
			}
		}

		/* END: HUBzero Extensions Follow to force registration and email confirmation */

		return $vars;
	}

	public function build($url)
	{
		$uri = parent::build($url);

		// Get the path data
		$route = $uri->getPath();

		//Add the suffix to the uri
		if ($this->_mode == JROUTER_MODE_SEF && $route) {
			$app = JApplication::getInstance('site');

			if ($app->getCfg('sef_suffix') && !(substr($route, -9) == 'index.php' || substr($route, -1) == '/')) {
				if ($format = $uri->getVar('format', 'html')) {
					$route .= '.'.$format;
					$uri->delVar('format');
				}
			}

			if ($app->getCfg('sef_rewrite')) {
				//Transform the route
				if ($route == 'index.php')
				{
					$route = '';
				}
				else
				{
					$route = str_replace('index.php/', '', $route);
				}
			}
		}

		//Add basepath to the uri
		$uri->setPath(JURI::base(true).'/'.$route);

		/* START: HUBzero Extension for SEF Groups */
		if (!empty($_SERVER['REWROTE_FROM']))
		{
			if (stripos($uri->toString(), $_SERVER['REWROTE_TO']->getPath()) !== false)
			{
				$uri->setPath(str_replace($_SERVER['REWROTE_TO']->getPath(),'',$uri->getPath()));
				$uri->setHost($_SERVER['REWROTE_FROM']->getHost());
				$uri->setScheme($_SERVER['REWROTE_FROM']->getScheme());
			}
		}
		/* END: HUBzero Extension for SEF Groups */

		return $uri;
	}

	protected function _parseRawRoute(&$uri)
	{
		$vars	= array();
		$app	= JApplication::getInstance('site');
		$menu	= $app->getMenu(true);

		//Handle an empty URL (special case)
		if (!$uri->getVar('Itemid') && !$uri->getVar('option')) {
			$item = $menu->getDefault(JFactory::getLanguage()->getTag());
			if (!is_object($item)) {
				// No default item set
				return $vars;
			}

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

		// Only an Itemid  OR if filter language plugin set? Get the full information from the itemid
		if (count($this->getVars()) == 1 || ( $app->getLanguageFilter() && count( $this->getVars()) == 2 )) {

			$item = $menu->getItem($this->getVar('Itemid'));
			if ($item !== NULL && is_array($item->query)) {
				$vars = $vars + $item->query;
			}
		}

		// Set the active menu item
		$menu->setActive($this->getVar('Itemid'));

		return $vars;
	}

	protected function _parseSefRoute(&$uri)
	{
		$vars	= array();
		$app	= JApplication::getInstance('site');

		// Call System plugin to before parsing sef route
		JDispatcher::getInstance()->trigger('onBeforeParseSefRoute', array($uri));

		/* START: HUBzero Extension for SEF Groups */
		$app = JFactory::getApplication();

		if ($app->getCfg('sef_groups'))
		{
			$servername = rtrim(JURI::base(),'/');

			$serveruri = JURI::getInstance($servername);
			$sfqdn = $serveruri->getHost();
			$rfqdn = $uri->getHost();

			if ($rfqdn != $sfqdn)
			{
				list($rhostname, $rdomainname) = explode('.', $rfqdn, 2);
				list($shostname, $sdomainname) = explode('.', $sfqdn, 2);

				if ( ($rdomainname == $sdomainname) || ($rdomain = $sfqdn))
				{
					$suri = JURI::getInstance();
					$group = \Hubzero\User\Group::getInstance($rhostname);

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
		/* END: HUBzero Extension for SEF Groups */

		$menu	= $app->getMenu(true);
		$route	= $uri->getPath();

		// Remove the suffix
		if ($this->_mode == JROUTER_MODE_SEF)
		{
			if ($app->getCfg('sef_suffix'))
			{
				if ($suffix = pathinfo($route, PATHINFO_EXTENSION))
				{
					$route = str_replace('.' . $suffix, '', $route);
				}
			}
		}

		// Get the variables from the uri
		$vars = $uri->getQuery(true);

		// Handle an empty URL (special case)
		if (empty($route) && (JRequest::getVar('option','','post') == '')) {
			// If route is empty AND option is set in the query, assume it's non-sef url, and parse apropriately
			if (isset($vars['option']) || isset($vars['Itemid'])) {
				return $this->_parseRawRoute($uri);
			}

			$item = $menu->getDefault(JFactory::getLanguage()->getTag());
			// if user not allowed to see default menu item then avoid notices
			if (is_object($item)) {
				//Set the information in the request
				$vars = $item->query;

				//Get the itemid
				$vars['Itemid'] = $item->id;

				// Set the active menu item
				$menu->setActive($vars['Itemid']);
			}
			return $vars;
		}

		/*
		 * Parse the application route
		 */
		$segments	= explode('/', $route);
		if (count($segments) > 1 && $segments[0] == 'component')
		{
			$vars['option'] = 'com_'.$segments[1];
			$vars['Itemid'] = null;
			$route = implode('/', array_slice($segments, 2));
		}
		else
		{
			//Need to reverse the array (highest sublevels first)
			$items = array_reverse($menu->getMenu());

			$found 				= false;
			$route_lowercase 	= JString::strtolower($route);
			$lang_tag 			= JFactory::getLanguage()->getTag();

			foreach ($items as $item) {
				//sqlsrv  change
				if (isset($item->language)){
					$item->language = trim($item->language);
				}
				$depth = substr_count(trim($item->route,'/'),'/') + 1; // HUBzero: keep searching for better matches with higher depth
				$length = strlen($item->route); //get the length of the route
				if ($length > 0 && JString::strpos($route_lowercase.'/', $item->route.'/') === 0 && $item->type != 'alias' && (!$app->getLanguageFilter() || $item->language == '*' || $item->language == $lang_tag)) {
					/* START: HUBzero Extension to handle external url menu items differently */
					if ($item->type == 'url') {

						// If menu route exactly matches url route,
						// redirect (if necessary) to menu link
						if (trim($item->route,"/") == trim($route,"/")) {
							if (trim($item->route,"/") != trim($item->link,"/")
							 && trim($uri->base(true) . '/' . $item->route,"/") != trim($item->link,"/") // Added because it would cause redirect loop for instals not in top-level webroot
							 && trim($uri->base(true) . '/index.php/' . $item->route,"/") != trim($item->link,"/")) { // Added because it would cause redirect loop for instals not in top-level webroot
								$app->redirect($item->link);
							}
						}

						/* START: HUBzero extension to pass local URLs through, but record Itemid (we want the content parser to handle this) */
						if (strpos($item->route, "://") === false) {
							$vars['Itemid'] = $item->id;
							break;
						}
						/* END: HUBzero extension to pass local URLs through */
					}
					/* END: HUBzero Extension to handle external url menu items differently */

					// We have exact item for this language
					if ($item->language == $lang_tag) {
						$found = $item;
						$foundDepth = $depth; // HUBzero: track depth so we can replace with a better match later
						break;
					}
					// Or let's remember an item for all languages
					elseif (!$found || ($depth>=$foundDepth)) { // HUBzero: deeper or equal depth matches later on are prefered
						$found = $item;
						$foundDepth = $depth; // HUBzero: track depth so we can replace with a better match later
					}
				}
			}

			if (!$found) {
				$found = $menu->getDefault($lang_tag);
			}
			else {
				$route = substr($route, strlen($found->route));
				if ($route) {
					$route = substr($route, 1);
				}

				/* START: HUBzero extension to set vars if found (lines previously outside of if statement below) */
				$vars['Itemid'] = $found->id;
				$vars['option'] = $found->component;
				/* END: HUBzero extension to set vars if found */
			}

			/* START: HUBzero extension to ignore the following two Joomla lines (moved to if statement above) */
			//$vars['Itemid'] = $found->id;
			//$vars['option'] = $found->component;
			/* END: HUBzero extension to ignore the following two Joomla lines */
		}

		/* START: HUBzero Extension to parse com_content component specially */
		if (empty($vars['option'])) {
			//$bits = explode('/',ltrim($route,"/"));
			$vars = $this->_parseContentRoute($segments);
			if (!empty($vars['option'])) {
				$route = false;
			}
		}
		/* END: HUBzero Extension to parse com_content component specially */

		/* START: HUBzero Extension to route based on unprefixed component name (if other routing fails to match) */
		if (empty($vars['option']))
		{
			$segments	= explode('/', $route);

			if ($segments[0] == 'search') {   // @FIXME: search component should probably be configurable
				$plugin = JPluginHelper::getPlugin( 'system', 'hubzero' );
				$param = new JParameter( $plugin->params );
				$search = $param->get('search','search');
				if (empty($search)) {
					$search = 'search';
				}
				$segments[0] = $search;
			}
			elseif ($segments[0] == 'ysearch')
			{
				// Hack for fallback search when hubgraph fails...
				// We use ysearch as our keyword (even though ysearch doesn't exist anymore),
				// just so we can distinguish between a generic search and a redirect from 
				// hubgraph (when it fails), which would otherwise result in an infinite loop.
				$segments[0] = 'search';
			}

			$file = JPATH_BASE.DS.'components'.DS.'com_'.$segments[0].DS.$segments[0].".php";
			$file2 = JPATH_BASE.DS.'components'.DS.'com_'.$segments[0].DS.'site'.DS.$segments[0].".php";

			if (file_exists($file) || file_exists($file2))
			{
				$vars['option'] = 'com_'.$segments[0];

				if (!isset($vars['Itemid'])) {
					$vars['Itemid'] = null;
				}

				$route = preg_replace('/^' . $segments[0]. '/', '', $route);
			}
		}
		/* END: HUBzero Extension to route based on unprefixed component name (if other routing fails to match) */

		// Set the active menu item
		if (isset($vars['Itemid'])) {
			$menu->setActive( $vars['Itemid']);
		}

		// @FIXME: START FROM HUBZERO J1.5, NOT SURE WHAT TO DO WITH IT
		/* START: HUBzero Extension to do ???? */
		//if (empty($vars['Itemid'])) {
		//	$vars['Itemid'] =  '-1';
		//}
		/* END: HUBzero Extension to do ???? */
		// @FIXME: START FROM HUBZERO J1.5, NOT SURE WHAT TO DO WITH IT

		// Set the variables
		$this->setVars($vars);

		/*
		 * Parse the component route
		 */
		if (!empty($route) && isset($this->_vars['option'])) {
			$segments = explode('/', $route);
			if (empty($segments[0])) {
				array_shift($segments);
			}

			// Handle component	route
			$component = preg_replace('/[^A-Z0-9_\.-]/i', '', $this->_vars['option']);

			// Use the component routing handler if it exists
			$path = JPATH_SITE . '/components/' . $component . '/router.php';
			$path2 = JPATH_SITE . '/components/' . $component . '/site/router.php';

			if ((file_exists($path) || file_exists($path2)) && count($segments)) {
				if ($component != "com_search") { // Cheap fix on searches
					//decode the route segments
					/* START: HUBzero Extension: don't do : to - conversion except in com_content */
					/*
					$segments = $this->_decodeSegments($segments);
					 */
					if ($component == "com_content") {
						$segments = $this->_decodeSegments($segments);
					}
					/* END: HUBzero Extension: don't do : to - conversion except in com_content */
				} else {
					// fix up search for URL
					$total = count($segments);
					for ($i=0; $i<$total; $i++) {
						// urldecode twice because it is encoded twice
						$segments[$i] = urldecode(urldecode(stripcslashes($segments[$i])));
					}
				}

				/*require_once $path;
				$function = substr($component, 4).'ParseRoute';
				$function = str_replace(array("-", "."), "", $function);
				$vars =  $function($segments);*/
				$routes = $this->getComponentRouter($component);
				$vars = $routes->parse($segments);

				$this->setVars($vars);
			}
		} else {
			/* START: HUBzero Extension to check redirection table if otherwise unable to match URL to content */
			if (!isset($vars['option'])) {
				jimport('joomla.juri');
				$db = JFactory::getDBO();
				$db->setQuery("SELECT * FROM `#__redirect_links` WHERE `old_url`=" . $db->Quote($uri->current()));
				$row = $db->loadObject();

				if (!empty($row))
				{
					$myuri = JURI::getInstance($row->new_url);
					$vars = $myuri->getQuery(true);

					if (isset($vars['Itemid'])) {
						$menu->setActive($vars['Itemid']);
					}
				}
			}
			/* END: HUBzero Extension to check redirection table if otherwise unable to match URL to content */
			//Set active menu item

			if ($item = $menu->getActive()) {
				$vars = $item->query;
			}
		}

		// Call System plugin to before parsing sef route
		JDispatcher::getInstance()->trigger('onAfterParseSefRoute', array($vars));

		/* START: HUBzero Extension to pass common query parameters to apache (for logging) */
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
		/* END: HUBzero Extension to pass common query parameters to apache (for logging) */

		return $vars;
	}

	protected function _buildRawRoute(&$uri)
	{
	}

	protected function _buildSefRoute(&$uri)
	{
		// Call System plugin to before parsing sef route
		JDispatcher::getInstance()->trigger('onBeforeBuildSefRoute', array($uri));

		// Get the route
		$route = $uri->getPath();

		// Get the query data
		$query = $uri->getQuery(true);

		if (!isset($query['option']) || (isset($query['option']) && $query['option'] == 'com_content' && isset($query['task']) && $query['task'] == 'view')) {
			/* START: HUBzero Extension to handle section, category, alias routing of com_content pages */
			$parts = $this->_buildContentRoute($query);

			if (empty($parts)) {
				return;
			}

			$query['option'] = 'com_content';
			$parts  = $this->_encodeSegments($parts);
			$result = implode('/', $parts);
			$tmp    = ($result != '') ? '/' . $result : '';
			//$tmp = 'component/'.substr($query['option'], 4).'/'.$tmp;
			$route .= '/' . $tmp;

			// Unset unneeded query information
			unset($query['Itemid']);
			unset($query['option']);

			//Set query again in the URI
			$uri->setQuery($query);
			$uri->setPath($route);
			/* END: HUBzero Extension to handle section, category, alias routing of com_content pages */
			return;
		}

		$app  = JApplication::getInstance('site');
		$menu = $app->getMenu();

		/*
		 * Build the component route
		 */
		$component = preg_replace('/[^A-Z0-9_\.-]/i', '', $query['option']);
		$tmp       = '';

		// Use the component routing handler if it exists
		$path  = JPATH_SITE . '/components/' . $component . '/router.php';
		$path2 = JPATH_SITE . '/components/' . $component . '/site/router.php';

		// Use the custom routing handler if it exists
		if ((file_exists($path) || file_exists($path2)) && !empty($query)) {
			/*require_once $path;
			$function	= substr($component, 4).'BuildRoute';
			$function   = str_replace(array("-", "."), "", $function);
			$parts		= $function($query);*/
			$routes = $this->getComponentRouter($component);
			$query = $routes->preprocess($query);
			$parts = $routes->build($query);

			// encode the route segments
			if ($component != 'com_search') {
				// Cheep fix on searches
				/* START: HUBzero Extension to fix joomla break ':' in urls in com_wiki/com_topics (others?) */
				/*
				$parts = $this->_encodeSegments($parts);
				 */
				if ($component == 'com_content') {
					$parts = $this->_encodeSegments($parts);
				}
				/* END: HUBzero Extension to fix joomla break ':' in urls in com_wiki/com_topics (others?) */
			} else {
				// fix up search for URL
				$total = count($parts);
				for ($i = 0; $i < $total; $i++)
				{
					// urlencode twice because it is decoded once after redirect
					$parts[$i] = urlencode(urlencode(stripcslashes($parts[$i])));
				}
			}

			$result = implode('/', $parts);
			$tmp    = ($result != '') ? $result : '';
		}

		/*
		 * Build the application route
		 */
		$built = false;
		if (isset($query['Itemid']) && !empty($query['Itemid'])) {
			$item = $menu->getItem($query['Itemid']);
			if (is_object($item) && $query['option'] == $item->component) {
				// @FIXME: START FROM HUBZERO J1.5, NOT SURE WHAT TO DO WITH IT
				/* START: HUBzero Extension to fix ???? */
				/*
				$tmp = !empty($tmp) ? $item->route.'/'.$tmp : $item->route;
				*/
				//$tmp = $item->route.$tmp;
				/* END: HUBzero Extension to fix ???? */
				// @FIXME: END FROM HUBZERO J1.5, NOT SURE WHAT TO DO WITH IT
				if (!$item->home || $item->language!='*') {
					$tmp = !empty($tmp) ? $item->route.'/'.$tmp : $item->route;
				}
				$built = true;
			}
		}

		if (!$built) {
			/* START: HUBzero Extension to strip 'component' from url */
			/*
			$tmp = 'component/'.substr($query['option'], 4).'/'.$tmp;
			*/
			$tmp = (isset($query['option'])) ? substr($query['option'], 4).'/'.$tmp : $tmp;
		}

		if ($tmp) {
			$route .= '/'.$tmp;
		}
		elseif ($route=='index.php') {
			$route = '';
		}

		// Unset unneeded query information
		if (isset($item) && $query['option'] == $item->component) {
			unset($query['Itemid']);
		}
		unset($query['option']);

		//Set query again in the URI
		$uri->setQuery($query);
		$uri->setPath($route);

		// Call System plugin to before parsing sef route
		JDispatcher::getInstance()->trigger('onAfterBuildSefRoute', array($uri));
	}

	protected function _processParseRules(&$uri)
	{
		// Process the attached parse rules
		$vars = parent::_processParseRules($uri);

		// Process the pagination support
		if ($this->_mode == JROUTER_MODE_SEF) {
			$app = JApplication::getInstance('site');

			if ($start = $uri->getVar('start')) {
				$uri->delVar('start');
				$vars['limitstart'] = $start;
			}
		}

		return $vars;
	}

	protected function _processBuildRules(&$uri)
	{
		// Make sure any menu vars are used if no others are specified
		if (($this->_mode != JROUTER_MODE_SEF) && $uri->getVar('Itemid') && count($uri->getQuery(true)) == 2) {

			$app	= JApplication::getInstance('site');
			$menu	= $app->getMenu();

			// Get the active menu item
			$itemid = $uri->getVar('Itemid');
			$item = $menu->getItem($itemid);

			if ($item) {
				$uri->setQuery($item->query);
			}
			$uri->setVar('Itemid', $itemid);
		}

		// Process the attached build rules
		parent::_processBuildRules($uri);

		// Get the path data
		$route = $uri->getPath();

		if ($this->_mode == JROUTER_MODE_SEF && $route) {
			$app = JApplication::getInstance('site');

			if ($limitstart = $uri->getVar('limitstart')) {
				$uri->setVar('start', (int) $limitstart);
				$uri->delVar('limitstart');
			}
		}

		$uri->setPath($route);
	}

	protected function _createURI($url)
	{
		//Create the URI
		$uri = parent::_createURI($url);

		// Set URI defaults
		$app	= JApplication::getInstance('site');
		$menu	= $app->getMenu();

		// Get the itemid form the URI
		$itemid = $uri->getVar('Itemid');

		if (is_null($itemid)) {
			if ($option = $uri->getVar('option')) {
				$item  = $menu->getItem($this->getVar('Itemid'));
				if (isset($item) && $item->component == $option) {
					$uri->setVar('Itemid', $item->id);
				}
			} else {
				if ($option = $this->getVar('option')) {
					$uri->setVar('option', $option);
				}

				if ($itemid = $this->getVar('Itemid')) {
					$uri->setVar('Itemid', $itemid);
				}
			}
		} else {
			if (!$uri->getVar('option')) {
				if ($item = $menu->getItem($itemid)) {
					$uri->setVar('option', $item->component);
				}
			}
		}

		return $uri;
	}

	/**
	 * Short description for '_buildContentRoute'
	 *
	 * Long description (if any) ...
	 *
	 * @param	   array &$query Parameter description (if any) ...
	 * @return	   array Return description (if any) ...
	 */
	function _buildContentRoute(&$query)
	{
		$segments = array();
		$db       = JFactory::getDBO();

		// Don't parse calls to other com_content views
		if (!empty($query['view']) && $query['view'] != 'article')
		{
			return $segments;
		}

		if (!empty($query['id']))
		{
			$q = "SELECT `path` FROM `#__menu` WHERE link='index.php?option=com_content&view=article&id={$query['id']}' AND published=1";
			$db->setQuery($q);
			if ($menuitem = $db->loadResult())
			{
				$segments = explode('/', $menuitem);
			}
			else
			{
				$q  = "SELECT cat.`path`, con.`alias` AS con_alias, cat.`alias` AS cat_alias FROM `#__content` AS con";
				$q .= " LEFT JOIN `#__categories` AS cat ON con.catid = cat.id";
				$q .= " WHERE con.state=1 AND con.`id` = '{$query['id']}'";
				$db->setQuery($q);
				if ($result = $db->loadObject())
				{
					if ($result->cat_alias == 'uncategorised')
					{
						$segments[] = $result->con_alias;
					}
					else
					{
						$segments   = explode('/', $result->path);
						$segments[] = $result->con_alias;
					}
				}
			}
		}

		unset($query['task']);
		unset($query['view']);
		unset($query['id']);
		return $segments;
	}

	/**
	 * Short description for '_parseContentRoute'
	 *
	 * Long description (if any) ...
	 *
	 * @param	   array &$segments Parameter description (if any) ...
	 * @return	   array Return description (if any) ...
	 */
	function _parseContentRoute(&$segments)
	{
		$vars  = array();
		$view  = 'article';
		$menu  = JFactory::getApplication()->getMenu(true);
		$item  = $menu->getActive();
		$db    = JFactory::getDBO();
		$count = count($segments);

		// Item is numeric, assume user knows the article ID, and is trying to access directly
		if (($count == 1) && (is_numeric($segments[0])))
		{
			$vars['option'] = 'com_content';
			$vars['id']     = $segments[0];
			$vars['view']   = 'article';
			$item->query['view'] = 'article';
		}
		// Count 1 - we're either looking for an article alias that matches and is in the uncategorised category,
		// or, an article alias and category series that are all the same (ex: about/about/about - supported for legacy reasons)
		else if ($count == 1)
		{
			// First, do query
			$query  = "SELECT con.`id`, cat.`alias`, cat.`path` FROM `#__content` AS con";
			$query .= " LEFT JOIN `#__categories` AS cat ON con.catid = cat.id";
			$query .= " WHERE con.state=1 AND con.`alias` = " . $db->quote(strtolower($segments[0]));
			$db->setQuery($query);
			$result = $db->loadObject();

			if (empty($result))
			{
				return $vars;
			}

			// Now, check for uncategorised article with provided alias
			if ($result->alias == 'uncategorised')
			{
				// Success, that's it
				$segments = array();
				$vars['option'] = 'com_content';
				$vars['id']     = $result->id;
				$vars['view']   = 'article';
				$item->query['view'] = 'article';
			}
			else
			{
				// It wasn't uncategorised, so now try and see if its in a category scheme of all the same aliases
				$path  = explode('/', $result->path);
				$found = true;

				foreach ($path as $p)
				{
					if ($p != $segments[0])
					{
						$found = false;
						continue;
					}
				}

				if ($found)
				{
					// Success, that's it
					$segments = array();
					$vars['option'] = 'com_content';
					$vars['id']     = $result->id;
					$vars['view']   = 'article';
					$item->query['view'] = 'article';
				}
			}
		}
		else if ($count > 1)
		{
			// Build the path
			$path = array();
			for ($i=0; $i < ($count-1); $i++)
			{
				$path[] = $segments[$i];
			}

			$path = implode('/', $path);

			// Now, do query (path is all but last segment, and last segment is article alias)
			$query  = "SELECT con.`id` FROM `#__content` AS con";
			$query .= " LEFT JOIN `#__categories` AS cat ON con.catid = cat.id";
			$query .= " WHERE con.state=1 AND con.`alias` = " . $db->quote(strtolower($segments[$count-1]));
			$query .= " AND cat.`path` = " . $db->quote(strtolower($path));
			$db->setQuery($query);

			if ($result = $db->loadResult())
			{
				// Success, that's it
				$segments = array();
				$vars['option'] = 'com_content';
				$vars['id']     = $result;
				$vars['view']   = 'article';
				$item->query['view'] = 'article';
			}
		}

		return $vars;
	}

	/**
	 * Get component router
	 *
	 * @param   string  $component  Name of the component including com_ prefix
	 * @return  object  Component router
	 * @since   1.3.2
	 */
	public function getComponentRouter($component)
	{
		if (!isset($this->componentRouters[$component]))
		{
			$compname = ucfirst(substr($component, 4));

			$client = 'Site'; //\JFactory::getApplication()->isAdmin() ? 'Admin' : 'Site';

			$name  = $compname . 'Router';
			$name2 = '\\Components\\' . $compname . '\\' . $client . '\\Router';

			if (!class_exists($name) && !class_exists($name2))
			{
				// Use the component routing handler if it exists
				$path = JPATH_SITE . '/components/' . $component . '/router.php';
				$path2 = JPATH_SITE . '/components/' . $component . '/' . strtolower($client) . '/router.php';

				// Use the custom routing handler if it exists
				if (file_exists($path))
				{
					require_once $path;
				}
				else if (file_exists($path2))
				{
					require_once $path2;
				}
			}

			if (class_exists($name))
			{
				$reflection = new ReflectionClass($name);

				if (in_array('Hubzero\Component\Router\RouterInterface', $reflection->getInterfaceNames()))
				{
					$this->componentRouters[$component] = new $name;
				}
			}
			else if (class_exists($name2))
			{
				$reflection = new ReflectionClass($name2);

				if (in_array('Hubzero\Component\Router\RouterInterface', $reflection->getInterfaceNames()))
				{
					$this->componentRouters[$component] = new $name2;
				}
			}

			if (!isset($this->componentRouters[$component]))
			{
				$this->componentRouters[$component] = new \Hubzero\Component\Router\Legacy($compname);
			}
		}

		return $this->componentRouters[$component];
	}
}
