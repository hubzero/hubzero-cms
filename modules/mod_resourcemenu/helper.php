<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

class modResourceMenu
{
	private $attributes = array();

	//-----------

	public function __construct( $params ) 
	{
		$this->params = $params;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}

	//-----------

	private function _xHubTags( $ctext ) 
	{
		// Expression to search for
		$regex = "/\{xhub:\s*[^\}]*\}/i";

		// Find all instances of plugin and put in $matches
		$count = preg_match_all( $regex, $ctext, $matches );
		
		if ($count) {
			for ( $i=0; $i < $count; $i++ )
			{
				$regex = "/\{xhub:\s*([^\s]+)\s*(.*)/i";
				if ( preg_match($regex, $matches[0][$i], $tag) ) 
				{
					if ($tag[1] == 'module') {
						$text = $this->_xHubTagsModules($tag[2]);
					} else {
						$text = '';
					}
					$ctext = str_replace($matches[0][$i], $text, $ctext);
				}
			}
		}
		
		return $ctext;
	}
	
	//-----------
	
	private function _xHubTagsModules($options)
	{
	    global $mainframe;

	    $regex = "/position\s*=\s*(\"|&quot;)([^\"]+)(\"|&quot;)/i";

		if (!preg_match($regex, $options, $position))
	        return "";

	    $regex = "/style\s*=\s*(\"|&quot;)([^\"]+)(\"|&quot;)/i";

		if (!preg_match($regex, $options, $style))
	        $style[2] = "-2";

	    ximport('Hubzero_Module_Helper');

	    return Hubzero_Module_Helper::renderModules($position[2],$style[2]);
	}

	//-----------

	public function display()
	{
		// Get the module parameters
		$params =& $this->params;
		$this->moduleid = $params->get('moduleid');
		$this->moduleclass = $params->get('moduleclass');

		// Build the HTML
		$this->html = $this->_xHubTags( $params->get('content') );
		
		// Push some CSS to the tmeplate
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStylesheet('mod_resourcemenu');
		
		// Push some javascript to the tmeplate
		$jdocument =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.'/modules/mod_resourcemenu/mod_resourcemenu.js')) {
			$jdocument->addScript('/modules/mod_resourcemenu/mod_resourcemenu.js');
		}
	}
}