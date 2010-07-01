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

ximport('Hubzero_Controller');

class WishlistController extends Hubzero_Controller
{
	public function execute()
	{
		// Load the component config
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;
		
		$this->_task = JRequest::getVar( 'task', '' );
		
		switch ($this->_task) 
		{
			default: $this->wishes(); break;
		}
	}	
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	public function wishes()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'wishes') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}
}
