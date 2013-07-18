<?php
/**
 * @package		Joomla.Site
 * @subpackage	Application
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
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

		//Set the route
		$uri->setPath(trim($path , '/'));

		$vars += parent::parse($uri);

		/* START: HUBzero Extensions Follow to force registration and email confirmation */
		$juser = &JFactory::getUser();

		if (!$juser->get('guest'))
		{
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
					$session->set('linkaccount', false);
					return $vars;
				}

				if ($uri->getPath() != 'legal/terms')
				{
					$originalVars = $vars;
					$vars = array();

					if ($juser->get('tmp_user')) // joomla tmp users
					{
						$vars['option'] = 'com_register';
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
							$vars['option'] = 'com_user';
							$vars['view']   = 'link';
						}
						else
						{
							$vars['option'] = 'com_register';
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

			$xprofile = &Hubzero_Factory::getProfile();

			if (is_object($xprofile) && ($xprofile->get('emailConfirmed') != 1) && ($xprofile->get('emailConfirmed') != 3))
			{
				if ($vars['option'] == 'com_user')
				{
					if (($vars['view'] == 'logout') || ($vars['task'] == 'logout'))
						return $vars;
				}
				else if ($uri->getPath() == 'legal/terms')
				{
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

			$badpassword = $session->get('badpassword',false);
			$expiredpassword = $session->get('expiredpassword',false);

			if ($badpassword || $expiredpassword) {
				if ($vars['option'] == 'com_members' && $vars['task'] == 'changepassword') {
					return $vars;
				}

				if ($vars['option'] == 'com_user' && ($vars['view'] == 'logout' || $vars['task'] == 'logout' || JRequest::getWord('task') == 'logout')) {
					return $vars;
				}

				if ($vars['option'] == 'com_support' && $vars['task'] == 'save') {
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
			if(is_object($item)) {
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
				if(isset($item->language)){
					$item->language = trim($item->language);
				}
				$length = strlen($item->route); //get the length of the route
				if ($length > 0 && JString::strpos($route_lowercase.'/', $item->route.'/') === 0 && $item->type != 'menulink' && (!$app->getLanguageFilter() || $item->language == '*' || $item->language == $lang_tag)) {
					/* START: HUBzero Extension to handle external url menu items differently */
					if ($item->type == 'url') {

						// If menu route exactly matches url route,
						// redirect (if necessary) to menu link
						if (trim($item->route,"/") == trim($route,"/")) {
							if (trim($item->route,"/") != trim($item->link,"/")) {
								$app->redirect($item->link);
							}
						}

						// @FIXME: START FROM HUBZERO J1.5, NOT SURE WHAT TO DO WITH IT
						// Pass local URLs through, but record Itemid
						//if (strpos($item->route, "://") === false) {
						//	$vars['Itemid'] = $item->id;
						//	break;
						//}
						// @FIXME: END FROM HUBZERO J1.5, NOT SURE WHAT TO DO WITH IT */						
					}
					/* END: HUBzero Extension to handle external url menu items differently */

					// We have exact item for this language
					if ($item->language == $lang_tag) {
						$found = $item;
						break;
					}
					// Or let's remember an item for all languages
					elseif (!$found) {
						$found = $item;
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
			}

			$vars['Itemid'] = $found->id;
			$vars['option'] = $found->component;
		}

		/* START: HUBzero Extension to parse com_content component specially */
		if (empty($vars['option'])) {
			$vars = $this->_parseContentRoute(explode('/',ltrim($route,"/")));
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
				$search = $param->get('search','ysearch');
				if (empty($search)) {
					$search = 'ysearch';
				}
				$segments[0] = $search;
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

			if (file_exists($path) && count($segments)) {
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

				require_once $path;
				$function = substr($component, 4).'ParseRoute';
				$function = str_replace(array("-", "."), "", $function);
				$vars =  $function($segments);

				$this->setVars($vars);
			}
		} else {
			/* START: HUBzero Extension to check redirection table if otherwise unable to match URL to content */
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
			/* END: HUBzero Extension to check redirection table if otherwise unable to match URL to content */
			//Set active menu item

			if ($item = $menu->getActive()) {
				$vars = $item->query;
			}
		}

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
		// Get the route
		$route = $uri->getPath();

		// Get the query data
		$query = $uri->getQuery(true);

		if (!isset($query['option'])) {
			/* START: HUBzero Extension to handle section, category, alias routing of com_content pages */
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
			/* END: HUBzero Extension to handle section, category, alias routing of com_content pages */
			return;
		}

		$app	= JApplication::getInstance('site');
		$menu	= $app->getMenu();

		/*
		 * Build the component route
		 */
		$component	= preg_replace('/[^A-Z0-9_\.-]/i', '', $query['option']);
		$tmp		= '';

		// Use the component routing handler if it exists
		$path = JPATH_SITE . '/components/' . $component . '/router.php';

		// Use the custom routing handler if it exists
		if (file_exists($path) && !empty($query)) {
			require_once $path;
			$function	= substr($component, 4).'BuildRoute';
			$function   = str_replace(array("-", "."), "", $function);
			$parts		= $function($query);

			// encode the route segments
			if ($component != 'com_search') {
				// Cheep fix on searches
				/* START: HUBzero Extension to fix joomla break ':' in urls in com_wiki/com_topics (others?) */
				/*
				$parts = $this->_encodeSegments($parts);
				 */
				if ($component == "com_content") {
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
			$tmp	= ($result != "") ? $result : '';
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
	 * @param	   array &$segments Parameter description (if any) ...
	 * @return	   array Return description (if any) ...
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
						"#__content.alias=" . $db->Quote($alias) . " AND ";

				if (!empty($category))
					$query .= "#__content.catid=#__categories.id AND " . "#__categories.alias=" . $db->Quote($category) . " AND " .
					"#__content.sectionid=#__sections.id AND " . "#__sections.alias=" . $db->Quote($section) . "";
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

		if ($count == 3)
		{
			if (is_numeric($segments[2]))
				$query .= " AND #__content.id=" . $db->Quote($segments[2]) . " ";
			else
				$query .= " AND #__content.alias=" . $db->Quote($segments[2]) . " ";

			if (is_numeric($segments[1]))
				$query .= " AND #__content.catid=" . $db->Quote($segments[1]) . " ";
			else
				$query .= " AND #__categories.alias=" . $db->Quote($segments[1]) . " ";

			if (is_numeric($segments[0]))
				$query .= " AND #__content.sectionid=" . $db->Quote($segments[0]) . " ";
			else
				$query .= " AND #__sections.alias=" . $db->Quote($segments[0]) . " ";

			$query .= " AND #__content.state='1' LIMIT 1;";
		}
		else if ($count == 2)
		{
			if (!empty($id)) {
				$query = "SELECT #__content.id from `#__content`, `#__categories`, `#__sections` WHERE " .
						"#__content.alias=" . $db->Quote($segments[1]) . " AND " .
						"#__content.catid=#__categories.id AND " .
						"#__categories.alias=" . $db->Quote($segments[0]) . " AND " .
						"#__categories.section=#__sections.id AND " .
						"#__sections.id=(SELECT sectionid FROM `#__content` WHERE id='" . $id . "') AND #__content.state='1' LIMIT 1;";
			} else {
				$query = "SELECT #__content.id from `#__content`, `#__categories`, `#__sections` WHERE " .
						"#__content.alias=" . $db->Quote($segments[1]) . " AND " .
						"#__content.catid=#__categories.id AND " .
						"#__categories.alias=" . $db->Quote($segments[0]) . " AND " .
						"#__categories.section=#__sections.id AND " .
						"#__sections.alias=" . $db->Quote($segments[0]) . " AND #__content.state='1' LIMIT 1;";
			}
		}
		else if ($count == 1 && 0)
		{
			$query = "SELECT #__content.id from `#__content`, `#__categories`, `#__sections` WHERE " .
					"#__content.alias=" . $db->Quote($segments[0]) . " AND " .
					"#__content.catid=(SELECT catid FROM `#__content` WHERE id='" . $id ."') AND #__content.state='1' LIMIT 1;";
		}
		else if ($count == 1)
		{
			$page = $segments[0];
			$category = $segments[0];
			$section = $segments[0];

			$query = "SELECT #__content.id from `#__content`, `#__categories`, `#__sections` WHERE " .
					"#__content.alias=" . $db->Quote($page) . " AND " .
					"(" .
					"(#__content.catid=#__categories.id AND " . "#__categories.alias=" . $db->Quote($category) . " AND " .
					"#__content.sectionid=#__sections.id AND " . "#__sections.alias=" . $db->Quote($section) . ")" .
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
					"#__content.alias=" . $db->Quote($page) . " AND " .
					"#__content.catid=#__categories.id AND " .
					"#__categories.alias=" . $db->Quote($category) . " AND " .
					"#__categories.section=#__sections.id AND " .
					"#__sections.alias=" . $db->Quote($section) . " AND #__content.state='1' LIMIT 1;";
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
