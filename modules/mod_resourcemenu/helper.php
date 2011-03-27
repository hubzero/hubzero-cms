<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
