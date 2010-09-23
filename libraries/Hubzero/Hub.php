<?php
/**
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


class Hubzero_Hub
{
	public function __construct()
	{
		$this->loadConfig();
	}
	
	//-----------

	public function loadConfig()
	{
		$registry =& JFactory::getConfig();
		$file = JPATH_CONFIGURATION . DS . 'hubconfiguration.php';

		if (file_exists($file))
			include_once($file);

		if ( class_exists('HubConfig') ) 
		{
			$config =& new HubConfig();
			$registry->loadObject($config, 'xhub');
		}

		$file = JPATH_CONFIGURATION . DS . 'hubconfiguration-local.php';

		if (file_exists($file))
			include_once($file);

		if ( class_exists('HubConfigOverride') ) 
		{
			$config =& new HubConfigOverride();
			$registry->loadObject($config, 'xhub');
		}
	}
	
	//-----------

	public function getCfg( $varname, $default = '' )
	{
		$config = &JFactory::getConfig();

		$value = $config->getValue('xhub.' . $varname, $default);

		return $value;
	}
	
	//-----------

	public function redirect($url, $permanent = false)
	{
		// check for relative internal links

		if (preg_match( '#^index[2]?.php#', $url )) 
		{
			$url = JURI::base() . $url;
		}

		// Strip out any line breaks

		$url = preg_split("/[\r\n]/", $url);
		$url = $url[0];

		/*
		 * If the headers have been sent, then we cannot send an additional location header
		 * so we will output a javascript redirect statement.
		 */

		if (headers_sent()) 
		{
			echo "<script>document.location.href='$url';</script>\n";
		} 
		else 
		{
			//@ob_end_clean(); // clear output buffer

			if ($permanent)
				header( 'HTTP/1.1 301 Moved Permanently' );

			header( 'Location: ' . $url );
		}

		exit(0);
	}
	
	//-----------

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
