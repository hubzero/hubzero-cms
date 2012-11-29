<?php
/**
 * @package		HUBzero CMS
 * @author		Christopher Smoak <csmoak@purdue.edu>
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

class NewsletterController extends Hubzero_Controller
{

	protected function displayTask()
	{
		//instantiate objects
		$database 		=& JFactory::getDBO();
		$document 		=& JFactory::getDocument();
		$application 	=& JFactory::getApplication(); 
		
		//set page title
		$document->setTitle("Newsletters");
		$this->view->title = "Newsletters";
		
		//push css to view
		$this->_getStyles();
		
		//add item to pathway
		$application->getPathway()->addItem( "Newsletters",
			'index.php?option=' . $this->_option
		);
		
		//instantiate campaign object
		$nc = new NewsletterCampaign( $database );
		
		//get request vars
		$id = JRequest::getVar("id", 0);
		
		//do we have an id
		if($id)
		{   
			$campaign = (array) $nc->getCampaign( $id );
		}
		else
		{
			$campaign = (array) $nc->getCurrentCampaign();
		}
		
		//
		$application->getPathway()->addItem( $campaign['name'],
			'index.php?option=' . $this->_option . '&id' . $campaign['id']
		);
		
		//build newsletter
		$this->view->newsletter = $nc->buildnewsletter( $campaign );
		
		//get list of all campaigns so we can view old newsletters
		$this->view->campaigns = $nc->getCampaign();
		
		// Output HTML
		if($this->getError()) 
			$this->view->setError($this->getError());
		$this->view->display();
	}
}
?>