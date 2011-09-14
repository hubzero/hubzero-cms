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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * @see JView
 */
jimport('joomla.application.component.view');

/**
 * @package		HUBzero                                  CMS
 * @author		Shawn                                     Rice <zooley@purdue.edu>
 * @copyright	Copyright                               2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */
class Hubzero_Controller extends JObject
{
	/**
	 * Container for component messages
	 * @var		array
	 */
	public $componentMessageQueue = array();

	/**
	 * The name of the component derived from the controller class name
	 * @var		string
	 */
	protected $_name = NULL;

	/**
	 * Container for storing overloaded data
	 * @var		array
	 */
	protected $_data = array();

	/**
	 * The task the component is to perform
	 * @var		string
	 */
	protected $_task = NULL;

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array	$config		Optional configurations to be used
	 * @return	void
	 */
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

		// Clear component messages - for cross component messages
		$this->getComponentMessage();
	}

	/**
	 * Method to set an overloaded variable to the component
	 *
	 * @param	string	$property	Name of overloaded variable to add
	 * @param	mixed	$value 		Value of the overloaded variable
	 * @return	void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Method to get an overloaded variable of the component
	 *
	 * @param	string	$property	Name of overloaded variable to retrieve
	 * @return	mixed 	Value of the overloaded variable
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}

	/**
	 * Overloadable method
	 *
	 * @return	void
	 */
	public function execute()
	{
	}

	/**
	 * Method to redirect the application to a new URL and optionally include a message
	 *
	 * @return	void
	 */
	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect($this->_redirect, $this->_message, $this->_messageType);
		}
	}

	/**
	 * Method to add a message to the component message que
	 *
	 * @param	string	$message	The message to add
	 * @param	string	$type		The type of message to add
	 * @return	void
	 */
	public function addComponentMessage($message, $type='message')
	{
		//if message is somthing
		if ($message != '') {
			$this->componentMessageQueue[] = array('message' => $message, 'type' => strtolower($type), 'option' => $this->_option);
		}

		$session =& JFactory::getSession();
		$session->set('component.message.queue', $this->componentMessageQueue);
	}

	/**
	 * Method to get component messages
	 *
	 * @return	array
	 */
	public function getComponentMessage()
	{
		if (!count($this->componentMessageQueue)) {
			$session =& JFactory::getSession();
			$componentMessage = $session->get('component.message.queue');
			if (count($componentMessage)) {
				$this->componentMessageQueue = $componentMessage;
				$session->set('component.message.queue', null);
			}
		}

		foreach ($this->componentMessageQueue as $k => $cmq) {
			if ($cmq['option'] != $this->_option) {
				$this->componentMessageQueue[$k] = array();
			}
		}

		return $this->componentMessageQueue;
	}

	/**
	 * Method to add stylesheets to the document.
	 * Defaults to current component and stylesheet name the same as component.
	 *
	 * @param	string	$option 	Component name to load stylesheet from
	 * @param	string	$script 	Name of the stylesheet to load
	 * @return	void
	 */
	protected function _getStyles($option='', $stylesheet='')
	{
		ximport('Hubzero_Document');
		$option = ($option) ? $option : $this->_option;
		Hubzero_Document::addComponentStylesheet($option, $stylesheet);
	}

	/**
	 * Method to add scripts to the document.
	 * Defaults to current component and script name the same as component.
	 *
	 * @param	string	$script 	Name of the script to load
	 * @param	string	$option 	Component name to load script from
	 * @return	void
	 */
	protected function _getScripts($script='', $option='')
	{
		$document =& JFactory::getDocument();

		$option = ($option) ? $option : $this->_option;
		$script = ($script) ? $script : $this->_name;

		if (is_file(JPATH_ROOT.DS.'components'.DS.$option.DS.$script.'.js')) {
			$document->addScript('components'.DS.$option.DS.$script.'.js');
		}
	}

	/**
	 * Method to set the document path
	 *
	 * @return	void
	 */
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

	/**
	 * Method to build and set the document title
	 *
	 * @return	void
	 */
	protected function _buildTitle()
	{
		$title = JText::_(strtoupper($this->_option));
		if ($this->_task) {
			$title .= ': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));
		}
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
	}

	/**
	 * Method to check admin access permission
	 *
	 * @return	boolean	True on success
	 */
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

