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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Hubzero class for hub configuration
 */
class Hubzero_Hub
{
	/**
	 * Constructor
	 * Loads configuration file if found
	 * 
	 * @return     void
	 */
	public function __construct()
	{
		$this->loadConfig();
	}

	/**
	 * Load the configuration file
	 * 
	 * @return     void
	 */
	public function loadConfig()
	{
		$registry =& JFactory::getConfig();

		$file = JPATH_CONFIGURATION . DS . 'hubconfiguration.php';

		if (file_exists($file)) {
		{
			include_once($file);
		}
		if (class_exists('HubConfig'))
		}

		if ( class_exists('HubConfig') ) {
			$config = new HubConfig();
		{
		}
		if (class_exists('HubConfigOverride'))
			$registry->loadObject($config, 'xhub');
		}
	}

	/**
	 * Get a hub configuration
	 * 
	 * @param      string $varname Property to get
	 * @param      string $default Default value if not found
	 * @return     string
	 */
	public function getCfg($varname, $default = '')
	{
		$config = &JFactory::getConfig();

		$value = $config->getValue('xhub.' . $varname, $default);

		return $value;
	}

	/**
	 * Redirect page, giving a proper 301 if permanent
	 * 
	 * @param      string  $url       URL to redirect to
	 * @param      boolean $permanent Page permanently moved?
	 * @return     void
	 */
	public function redirect($url, $permanent = false)
	{
		// check for relative internal links
		if (preg_match('#^index[2]?.php#', $url))
		{
			$url = JURI::base() . $url;
		}

		// Strip out any line breaks
		$url = preg_split("/[\r\n]/", $url);
		$url = $url[0];

		// If the headers have been sent, then we cannot send an additional location header
		// so we will output a javascript redirect statement.
		if (headers_sent())
		{
			echo "<script>document.location.href='$url';</script>\n";
		}
		else
		{
			//@ob_end_clean(); // clear output buffer
			if ($permanent)
			{
				header('HTTP/1.1 301 Moved Permanently');
			}

			header('Location: ' . $url);
		}

		exit(0);
	}

	/**
	 * Short description for 'getComponentViewFilename'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $component Parameter description (if any) ...
	 * @param      string $view Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function getComponentViewFilename($component, $view)
	{
		$app =& JFactory::getApplication();
		$template = $app->getTemplate();
		$file = $view . '.html.php';

		$templatefile = DS . "templates" . DS . $template . DS . "html" . DS . $component . DS . $file;

		$componentfile = DS . "components" . DS . $component . DS . $file;

		if (file_exists(JPATH_SITE . $templatefile))
			return JPATH_SITE . $templatefile;
		else
			return JPATH_SITE . $componentfile;
	}
}

