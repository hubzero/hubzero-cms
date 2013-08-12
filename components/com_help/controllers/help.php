<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//import Hubzero Controller Class
ximport('Hubzero_Controller');

//import filesystem library
jimport('joomla.filesystem.folder');

/**
 * Help controller class
 */
class HelpControllerHelp extends Hubzero_Controller
{
	/**
	 * Display Help Article Pages
	 * 
	 * @return     array
	 */
	public function displayTask()
	{
		//force help template
		JRequest::setVar('tmpl', 'help');
		
		//var to hold content
		$this->view->content = '';
		
		//get the page we are trying to access
		$page      = JRequest::getWord('page', 'index');
		$component = JRequest::getWord('component', 'com_help');
		$extension = JRequest::getWord('extension', '');
		
		//template override help page
		$templateHelpPage = JPATH_ROOT . DS . 'templates' . DS . JFactory::getApplication()->getTemplate() . DS .  'html' . DS . $component  . DS . 'help' . DS . JFactory::getLanguage()->getTag() . DS . $page . '.phtml';
		$templateHelpPageAlt = JPATH_ROOT . DS . 'templates' . DS . JFactory::getApplication()->getTemplate() . DS .  'html' . DS . 'plg_'.str_replace('com_', '', $component).'_'.$page . DS . 'help' . DS . JFactory::getLanguage()->getTag() . DS . 'index.phtml';
		
		//path to help page
		$helpPage    = JPATH_ROOT . DS . 'components' . DS . $component . DS . 'help' . DS . JFactory::getLanguage()->getTag() . DS . $page . '.phtml';
		$helpPageAlt = JPATH_ROOT . DS . 'plugins' . DS . str_replace('com_', '', $component) . DS . $page . DS . 'help' . DS . JFactory::getLanguage()->getTag() . DS . 'index.phtml';
		
		//if we have an extension
		if (isset($extension) && $extension != '')
		{
			$helpPage            = JPATH_ROOT . DS . 'plugins' . DS . str_replace('com_', '', $component) . DS . $extension . DS . 'help' . DS . JFactory::getLanguage()->getTag() . DS . $page . '.phtml';
			$templateHelpPageAlt = JPATH_ROOT . DS . 'templates' . DS . JFactory::getApplication()->getTemplate() . DS .  'html' . DS . 'plg_'.str_replace('com_', '', $component).'_'.$extension . DS . 'help' . DS . JFactory::getLanguage()->getTag() . DS . $page . '.phtml';
		}
		
		//store  final page
		$finalHelpPage = '';
		
		//determine path for help page, check template first
		if (file_exists($templateHelpPageAlt))
		{
			$finalHelpPage = $templateHelpPageAlt;
		}
		else if(file_exists($templateHelpPage))
		{
			$finalHelpPage = $templateHelpPage;
		}
		else if (file_exists($helpPage))
		{
			$finalHelpPage = $helpPage;
		}
		else if (file_exists($helpPageAlt))
		{
			$finalHelpPage = $helpPageAlt;
		}
		
		//if we have an existing pge
		if ($finalHelpPage != '')
		{
			ob_start();
			require_once( $finalHelpPage );
			$this->view->content = ob_get_contents();
			ob_end_clean();
		}
		else if (isset($component) && $component != '' && $page == 'index')
		{
			//get list of component pages
			$pages[] = $this->helpPagesForComponent( $component );
			
			//display page
			$this->view->content = $this->displayHelpPageIndexForPages( $pages, 'h2' );
		}
		else
		{
			//raise error to avoid security bug
			JError::raiseError( 404, JText::_('Help page not found.') );
		}
		
		//get file modified time
		$this->view->modified = filemtime($finalHelpPage);
		
		//display
		$this->view->display();
	}
	
	
	/**
	 * Get array of help pages for component
	 * 
	 * @param      $component    Component to get pages for
	 * @return     array
	 */
	private function helpPagesForComponent( $component )
	{
		//get component name from database
		$sql = "SELECT `name` FROM #__components WHERE `option`=" . $this->database->quote( $component ) . " AND `enabled`=1 AND `parent`=0";
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$sql = "SELECT `name` FROM `#__extensions` WHERE `type`=" . $this->database->quote( 'component' ) . " AND `element`=" . $this->database->quote( $component ) . " AND `enabled`=1";
		}
		$this->database->setQuery( $sql );
		$name = $this->database->loadResult();
	
		//make sure we have a component
		if ($name == '')
		{
			return array();
		}
		
		//path to help pages
		$helpPagesPath = JPATH_ROOT . DS . 'components' . DS . $component . DS . 'help' . DS . JFactory::getLanguage()->getTag();
		
		//make sure directory exists
		$pages = array();
		if (is_dir($helpPagesPath))
		{
			//get help pages for this component
			$pages = JFolder::files( $helpPagesPath , '.phtml' );
		}
		
		//return pages
		return array( 'name' => $name, 'option' => $component, 'pages' => $pages );
	}
	
	
	/**
	 * Get array of help pages for component
	 * 
	 * @param      $componentAndPages    Component info and corresponding help pages
	 * @param      $headingLevel         Leading level for component separation
	 * @return     array
	 */
	private function displayHelpPageIndexForPages( $componentAndPages, $headingLevel = 'h1' )
	{
		//var to hold content
		$content = '';
		
		//loop through each component and pages group passed in
		foreach ($componentAndPages as $component)
		{
			//build content to return
			$content .= "<".$headingLevel.">{$component['name']} Help</".$headingLevel.">";
			
			//make sure we have pages
			if (count($component['pages']) > 0)
			{
				$content .= '<p>' . JText::_('Below is a list of help pages for the "'.$component['name'].'" component, that might help answer any questions you might have.') . '</p>';
				$content .= '<ul>';
				foreach ($component['pages'] as $page)
				{
					$name = str_replace('.phtml', '', $page);
					$url  = JRoute::_('index.php?option=com_help&component='.str_replace('com_', '', $component['option']).'&page='.$name);
					$content .= '<li><a href="'.$url.'">' . ucwords(str_replace('_', ' ', $name)) .'</a></li>';
				}
				$content .= '</ul>';
			}
			else
			{
				$content .= "<p>Currently there are no help pages for this component.</p>";
			}
		}
		
		return $content;
	}
}