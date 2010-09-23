<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth
 * @copyright	Copyright 2008-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2008-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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

jimport('joomla.application.component.controller');

class InfrastructureController extends JController
{
/*	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;

*/
	/**
	 * Method to display the view
	 *
	 * @access    public
	*/

	function display()
	{
		parent::display();
	}

/*
	private function getView()
	{
		$view = strtolower(JRequest::getVar('view', 'view'));
		$this->_view = $view;
		return $view;
	}
*/
	//-----------

/*
	public function execute()
	{
		$view = $this->getView();
		
		if ($view == 'image') {
        		ximport('Hubzero_Document');

		        $xhub  = & Hubzero_Factory::getHub();
		        $image = JPATH_SITE . Hubzero_Document::getComponentImage('com_projects', 'forge.png', 1);

		        if (is_readable($image)) {
               			ob_clean();
				header("Content-Type: image/png");
                		readfile($image);
                		ob_end_flush();
                		exit;
        		}
		}
	
	}
*/
}
