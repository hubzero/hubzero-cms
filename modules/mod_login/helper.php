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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Module class for displaying login box
 */
class modLoginHelper extends JObject
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
	 * Get the return url
	 * 
	 * @return string - base64 encoded string
	 */
	function getReturnURL($type)
	{
		if($itemid = $this->params->get($type))
		{  
			$menu =& JSite::getMenu();  
			$item = $menu->getItem($itemid);
			if ($item)
			{
				$url = JRoute::_($item->link.'&Itemid='.$itemid, false);
			}
			else
			{
			// stay on the same page
			$uri = JFactory::getURI();
			$url = $uri->toString(array('path', 'query', 'fragment'));
			}
				
		}
		else
		{
			// stay on the same page
			$uri = JFactory::getURI();
			$url = $uri->toString(array('path', 'query', 'fragment'));
		}

		return base64_encode($url);
	}

	/**
	 * Get type of action (logout or login) - not applicable in our scenario
	 * 
	 * @return string - logout or login
	 */
	function getType()
	{
		$user = & JFactory::getUser();
		return (!$user->get('guest')) ? 'logout' : 'login';
	}

	/**
	 * Display module content
	 * 
	 * @return     void
	 */
	function display()
	{
		$app      = JFactory::getApplication();
		$document = JFactory::getDocument();

		// Make sure we're using a secure connection
		if (!isset( $_SERVER['HTTPS'] ) || $_SERVER['HTTPS'] == 'off')
		{
			$app->redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			die('insecure connection and redirection failed');
		}

		// Get and add the js and extra css to the page
		$assets        = DS."components".DS."com_user".DS."assets".DS;
		$media         = DS."media".DS."system";
		$js            = $assets.DS."js".DS."login.jquery.js";
		$css           = $assets.DS."css".DS."login.css";
		$uniform_js    = $media.DS."js".DS."jquery.uniform.js";
		$uniform_css   = $media.DS."css".DS."uniform.css";
		$providers_css = $assets.DS."css".DS."providers.css";
		if(file_exists(JPATH_BASE . $js))
		{
			$document->addScript($js);
		}
		if(file_exists(JPATH_BASE . $css))
		{
			$document->addStyleSheet($css);
		}
		if(file_exists(JPATH_BASE . $uniform_js))
		{
			$document->addScript($uniform_js);
		}
		if(file_exists(JPATH_BASE . $uniform_css))
		{
			$document->addStyleSheet($uniform_css);
		}
		if(file_exists(JPATH_BASE . $providers_css))
		{
			$document->addStyleSheet($providers_css);
		}

		$type 	 = $this->getType();
		$return	 = $this->getReturnURL($type);
		$freturn = base64_encode($_SERVER['REQUEST_URI']);

		// If we have a return set with an authenticator in it, we're linking an existing account
		// Parse the return to retrive the authenticator, and remove it from the list below
		$auth = '';
		if($areturn = JRequest::getVar('return', null))
		{
			$areturn = base64_decode($areturn);
			$query   = parse_url($areturn);
			$query   = $query['query'];
			$query   = explode('&', $query);
			$auth    = '';
			foreach($query as $q)
			{
				$n = explode('=', $q);
				if($n[0] == 'authenticator')
				{
					$auth = $n[1];
				}
			}
		}

		// Figure out whether or not any of our third party auth plugins are turned on 
		// Don't include the 'hubzero' plugin, or the $auth plugin as described above
		$multiAuth      = false;
		$plugins        = JPluginHelper::getPlugin('authentication');
		$authenticators = array();

		foreach($plugins as $p)
		{
			if($p->name != 'hubzero' && $p->name != $auth)
			{
				$pparams = new JParameter($p->params);
				$display = $pparams->get('display_name', ucfirst($p->name));
				$authenticators[] = array('name' => $p->name, 'display' => $display);
				$multiAuth = true;
			}
		}

		// Set the return if we have it...
		$r = ($return) ? "&return={$return}" : '';

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
