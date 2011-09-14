<?php
/**
 * HUBzero CMS
 *
 * Copyright 2008-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth
 * @copyright Copyright 2008-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Short description for 'InfrastructureController'
 * 
 * Long description (if any) ...
 */
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

