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

//-------------------------------------------------------------

class modResourceMenu
{
	private $params;

	//-----------

	public function __construct( $params ) 
	{
		$this->params = $params;
	}

	//-----------

	private function xHubTags( $ctext ) 
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
						$text = $this->xHubTagsModules($tag[2]);
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
	
	private function xHubTagsModules($options)
	{
	    global $mainframe;

	    $regex = "/position\s*=\s*(\"|&quot;)([^\"]+)(\"|&quot;)/i";

		if (!preg_match($regex, $options, $position))
	        return "";

	    $regex = "/style\s*=\s*(\"|&quot;)([^\"]+)(\"|&quot;)/i";

		if (!preg_match($regex, $options, $style))
	        $style[2] = "-2";

	    ximport('xmodule');

	    return XModuleHelper::renderModules($position[2],$style[2]);
	}

	//-----------

	public function display()
	{
		$database =& JFactory::getDBO();
		
		// Get the module parameters
		$params =& $this->params;
		$moduleid = $params->get('moduleid');
		$moduleclass = $params->get('moduleclass');
		$text = $params->get('content');
		
		// Build the HTML
		$html  = "\t".'<div id="'.$moduleid.'" class="'.$moduleclass.'">'."\n";
		$html .= $this->xHubTags( $text );
		$html .= "\t".'</div>'."\n";
		
		$this->html = $html;
		
		ximport('xdocument');
		XDocument::addModuleStylesheet('mod_resourcemenu');
		
		$jdocument =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.'/modules/mod_resourcemenu/mod_resourcemenu.js')) {
			$jdocument->addScript('/modules/mod_resourcemenu/mod_resourcemenu.js');
		}
	}
}

//-------------------------------------------------------------

$modresourcemenu = new modResourceMenu( $params );
$modresourcemenu->display();

require( JModuleHelper::getLayoutPath('mod_resourcemenu') );
?>