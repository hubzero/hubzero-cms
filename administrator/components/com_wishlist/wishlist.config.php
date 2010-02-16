<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

//----------------------------------------------------------
// Wishlist configuration
//----------------------------------------------------------

class WishlistConfig
{
	public $paramaters = array();
	public $option = NULL;
	
	//-----------
	
	public function __construct( $option )
	{
		$database =& JFactory::getDBO();
		
		$database->setQuery( "SELECT params FROM #__components WHERE `option`='".$option."' AND parent=0 LIMIT 1" );
		$parameters = $database->loadResult();
		
		$params = array();
		if ($parameters) {
			$ps = explode("\n",$parameters);
			foreach ($ps as $p) 
			{
				$m = explode('=',$p);
				if (trim($m[0])) {
					$params[$m[0]] = (isset($m[1])) ? $m[1] : '';
				}
			}
		}
		
		$this->option = $option;
		$this->parameters = $params;
		
		if (!isset($this->parameters['group'])) {
			$this->parameters['group'] = '';
		}
		
	}
}
?>