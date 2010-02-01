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

jimport('joomla.application.component.controller');

class ToolsController extends JController
{
/*	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;

*/
	//-----------

	private function _getStyles($option='') 
	{
		ximport('xdocument');
		$option = ($option) ? $option : $this->_option;
		XDocument::addComponentStylesheet($option);
	}

	//-----------

	private function _getScripts()
	{
		$document =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.$this->_name.'.js')) {
			$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
		}
	}
	/**
	 * Method to display the view
	 *
	 * @access    public
	*/

	function display()
	{
		$this->_getStyles('com_tools');
		
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
		
		if ($view == 'accessdenied') {
			include_once( JPATH_ROOT.DS.'components'.DS.$this->_option.DS.'accessdenied.html.php' );
			ToolsViewAccessDenied::display();
		} else if ($view == 'quotaexceeded') {
			include_once( JPATH_ROOT.DS.'components'.DS.$this->_option.DS.'quotaexceeded.html.php' );
			ToolsViewQuotaExceeded::display();
		} else if ($view == 'image') {
        		ximport('xdocument');

		        $xhub  = & XFactory::getHub();
		        $image = JPATH_SITE . XDocument::getComponentImage('com_projects', 'forge.png', 1);

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
?>
