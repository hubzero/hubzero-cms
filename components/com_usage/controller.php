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
	
	private function getTask()
	{
		$task = JRequest::getVar( 'task', 'overview' );
		$this->_task = $task;
		return $task;
	}
	
	//-----------
	
	public function execute()
	{
		$this->getTask();

		if ($this->getError()) {
			echo UsageHTML::error($this->getError());
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
	
	private function getStyles() 
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet($this->_option);
	}

	//-----------
	
	private function getScripts()
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
		$this->getStyles();
		$this->getScripts();
		
		// Build the page title
		$title  = JText::_(strtoupper($this->_name));
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		
		// Establish a connection to the usage database
		$udb =& UsageHelper::getUDBO();
		if (!is_object($udb)) {
			$html  = UsageHtml::div( UsageHtml::hed(2, JText::_(strtoupper($this->_name)).': '.JText::_('USAGE_'.strtoupper($this->_task))), 'full', 'content-header');
			$html .= UsageHtml::div( UsageHtml::error( JText::_('Unable to connect to usage database.') ), 'main section' );
			echo $html;
			return;
		}
		
		// Get plugins
		JPluginHelper::importPlugin( 'usage' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Trigger the functions that return the areas we'll be using
		$cats = $dispatcher->trigger( 'onUsageAreas', array() );
		
		// Build the page title
		$title .= ($this->_task) ? ': '.JText::_('USAGE_'.strtoupper($this->_task)) : '';
		
		// Set the page title
		$document->setTitle( $title );
		
		// Set the pathway
		if ($this->_task) {
			$pathway->addItem(JText::_('USAGE_'.strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);
		}
		
		// Get the sections
		$sections = $dispatcher->trigger( 'onUsageDisplay', array(
				$this->_option, 
				$this->_task, 
				$udb, 
				$months, 
				$monthsReverse, 
				$enddate
			)
		);
		
		// Output HTML
		echo UsageHtml::view( $this->_option, $cats, $sections, $this->_task, $title, $no_html );
	}
}
?>