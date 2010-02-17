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

class UsageController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	
	//-----------
	
	public function __construct( $config=array() )
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';
		
		//Set the controller name
		if (empty( $this->_name ))
		{
			if (isset($config['name']))  {
				$this->_name = $config['name'];
			}
			else
			{
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
		$this->_option = 'com_'.$this->_name;
	}
	
	//-----------
	
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}
	
	//-----------
	
	public function execute()
	{
		$this->_task = JRequest::getVar( 'task', 'overview' );

		if ($this->getError()) {
			JError::raiseError( 500, $this->getError() );
		} else {
			$this->view();
		}
	}

	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message );
		}
	}
	
	//-----------
	
	private function _getStyles() 
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet($this->_option);
	}

	//-----------
	
	private function _getScripts()
	{
		$document =& JFactory::getDocument();
		if (is_file('components'.DS.$this->_option.DS.$this->_name.'.js')) {
			$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
		}
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------
	
	protected function view()
	{
		// Set some common variables
		$thisyear = date("Y");
		$months = array('01' => 'Jan', 
						'02' => 'Feb', 
						'03' => 'Mar', 
						'04' => 'Apr', 
						'05' => 'May', 
						'06' => 'Jun', 
						'07' => 'Jul', 
						'08' => 'Aug', 
						'09' => 'Sep', 
						'10' => 'Oct', 
						'11' => 'Nov', 
						'12' => 'Dec'
					);
		$monthsReverse = array_reverse($months, TRUE);
		
		// Incoming
		$enddate = JRequest::getVar('selectedPeriod', 0, 'post');
		$no_html = JRequest::getVar('no_html',0);
		
		// Push some scripts and styles to the tmeplate
		$this->_getStyles();
		$this->_getScripts();
		
		// Establish a connection to the usage database
		$udb =& UsageHelper::getUDBO();
		if (!is_object($udb)) {
			JError::raiseError( 500, JText::_('COM_USAGE_ERROR_CONNECTING_TO_DATABASE') );
			return;
		}
		
		$view = new JView( array('name'=>'results') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->no_html = $no_html;
		
		// Get plugins
		JPluginHelper::importPlugin( 'usage' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Trigger the functions that return the areas we'll be using
		$view->cats = $dispatcher->trigger( 'onUsageAreas', array() );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_option)),'index.php?option='.$this->_option);
		}
		
		if ($this->_task) {
			$pathway->addItem(JText::_('PLG_'.strtoupper($this->_name).'_'.strtoupper($this->_task)),'index.php?option='.$this->_option.'&task='.$this->_task);
		}
		
		// Get the sections
		$view->sections = $dispatcher->trigger( 'onUsageDisplay', array(
				$this->_option, 
				$this->_task, 
				$udb, 
				$months, 
				$monthsReverse, 
				$enddate
			)
		);
		
		// Build the page title
		$title  = JText::_(strtoupper($this->_option));
		$title .= ($this->_task) ? ': '.JText::_('PLG_'.strtoupper($this->_name).'_'.strtoupper($this->_task)) : '';
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Output HTML
		$view->title = $title;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
}
?>