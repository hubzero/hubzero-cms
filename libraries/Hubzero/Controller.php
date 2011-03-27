<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
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

jimport('joomla.application.component.view');

class Hubzero_Controller extends JObject
{	
	protected $_name = NULL;
	protected $_data = array();
	protected $_task = NULL;

	//-----------
	
	public function __construct( $config=array() )
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';
		
		// Set the controller name
		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
		// Set the component name
		$this->_option = 'com_'.$this->_name;

		$this->juser = JFactory::getUser();
		$this->database = JFactory::getDBO();
		$this->config = JComponentHelper::getParams( $this->_option );
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
	
	//----------------------------------------------------------
	// Public methods
	//----------------------------------------------------------
	
	public function execute()
	{
	}
	
	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}
	
	//----------------------------------------------------------
	// Protected methods
	//----------------------------------------------------------
	
	protected function _getStyles($option='', $stylesheet='') 
	{
		ximport('Hubzero_Document');
		$option = ($option) ? $option : $this->_option;
		Hubzero_Document::addComponentStylesheet($option, $stylesheet);
	}

	//-----------
	
	protected function _getScripts($script='', $option='')
	{
		$document =& JFactory::getDocument();
		
		$option = ($option) ? $option : $this->_option;
		$script = ($script) ? $script : $this->_name;

		if (is_file(JPATH_ROOT.DS.'components'.DS.$option.DS.$script.'.js')) {
			$document->addScript('components'.DS.$option.DS.$script.'.js');
		}
	}
	
	//-----------

	protected function _buildPathway() 
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option='.$this->_option
			);
		}
		if ($this->_task) {
			$pathway->addItem(
				JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)),
				'index.php?option='.$this->_option.'&task='.$this->_task
			);
		}
	}
	
	//-----------
	
	protected function _buildTitle() 
	{
		$title = JText::_(strtoupper($this->_option));
		if ($this->_task) {
			$title .= ': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));
		}
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
	}
	
	//----------------------------------------------------------
	// Authorization checks
	//----------------------------------------------------------
	
	protected function _authorize()
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) {
			return false;
		}
		
		// Check if they're a site admin (from Joomla)
		if ($this->juser->authorize($this->_option, 'manage')) {
			return true;
		}

		return false;
	}
}

