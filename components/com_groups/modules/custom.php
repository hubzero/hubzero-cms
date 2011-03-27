<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

Class CustomModule
{
	
	function __construct( $group )
	{
		//group object
		$this->group = $group;
		
	}
	
	//-----
	
	function onManageModules()
	{
		$mod = array(
			'name' => 'custom',
			'title' => 'Custom Content',
			'description' => 'The custom module allows for group manager to create a custom content block using wiki syntax.',
			'input_title' => "Custom Content: <span class=\"optional\">Optional</span>",
			'input' => "<textarea name=\"module[content]\" rows=\"15\">{{VALUE}}</textarea>"
		);
		
		return $mod;
	}
	
	//-----
	
	function render()
	{
		//option
		$option = 'com_groups';
		
		//get the config
		$config = JComponentHelper::getParams( $option );
		
		//var to hold content being returned
		$content  = '';
		
		//html wrapper for module 
		$content .= "<div class=\"group_module_custom\">";
		
		$wikiconfig = array(
			'option'   => $option,
			'scope'    => $this->group->get('cn').DS.'wiki',
			'pagename' => 'group',
			'pageid'   => $this->group->get('gidNumber'),
			'filepath' => $config->get('uploadpath'),
			'domain'   => $this->group->get('cn') 
		);
		
		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();
		
		//parse the wiki content
		$content .= $p->parse( "\n".stripslashes($this->content), $wikiconfig );
		
		//close wrapper
		$content .= "</div>";
		
		//return the content
		return $content;
	}
	
	//-----
}

?>

