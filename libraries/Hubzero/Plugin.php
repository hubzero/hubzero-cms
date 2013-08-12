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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */
class Hubzero_Plugin extends JPlugin
{
	/**
	 * Container for component messages
	 * @var array
	 */
	public $pluginMessageQueue = array();

	/**
	 * URL to redirect to
	 * 
	 * @var string
	 */
	public $_redirect = NULL;

	/**
	 * Message to send
	 * 
	 * @var string
	 */
	public $_message = NULL;

	/**
	 * Message type [error, message, warning]
	 * 
	 * @var string
	 */
	public $_messageType = NULL;

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array	$config		Optional configurations to be used
	 * @return	void
	 */
	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);

		$this->option = 'com_' . $config['type'];
		$this->plugin = $config['name'];
	}

	/**
	 * Method to add a message to the component message que
	 *
	 * @param	string	$message	The message to add
	 * @return	void
	 */
	public function redirect($url, $msg='', $msgType='')
	{
		$url = ($url != '') ? $url : $this->_redirect;
		$url = str_replace('&amp;', '&', $url);

		$msg = ($msg) ? $msg : $this->_message;
		$msgType = ($msgType) ? $msgType : $this->_messageType;

		if ($url) 
		{
			$app =& JFactory::getApplication();
			$app->redirect($url, $msg, $msgType);
		}
	}

	/**
	 * Method to add a message to the component message que
	 *
	 * @param	string	$message	The message to add
	 * @param	string	$type		The type of message to add
	 * @return	void
	 */
	public function addPluginMessage($message, $type='message')
	{
		//if message is somthing
		if ($message != '') 
		{
			$this->pluginMessageQueue[] = array(
				'message' => $message, 
				'type'    => strtolower($type), 
				'option'  => $this->option, 
				'plugin'  => $this->plugin
			);
		}

		$session =& JFactory::getSession();
		$session->set('plugin.message.queue', $this->pluginMessageQueue);
	}

	/**
	 * Method to get component messages
	 *
	 * @return	array
	 */
	public function getPluginMessage()
	{
		if (!count($this->pluginMessageQueue)) 
		{
			$session =& JFactory::getSession();
			$pluginMessage = $session->get('plugin.message.queue');
			if (count($pluginMessage)) 
			{
				$this->pluginMessageQueue = $pluginMessage;
				$session->set('plugin.message.queue', null);
			}
		}

		foreach ($this->pluginMessageQueue as $k => $cmq) 
		{
			if ($cmq['option'] != $this->option) 
			{
				$this->pluginMessageQueue[$k] = array();
			}
		}

		return $this->pluginMessageQueue;
	}
	
	/**
	 * Method to get plugin params
	 *
	 * @return	array
	 */
	public function getParams( $name, $folder )
	{
		//vars
		$database =& JFactory::getDBO();
		$table = "#__plugins";
		$paramsClass = 'JParameter';
		$enabled = " published=1";
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$table = "#__extensions";
			$paramsClass = 'JRegistry';
			$enabled = " enabled=1";
		}

		//load the params from databse
		$sql = "SELECT params FROM {$table} WHERE folder='" . $folder . "' AND element='" . $name . "' AND " . $enabled;
		$database->setQuery( $sql );
		$result = $database->loadResult();

		//params object
		//return params object
		$params = new $paramsClass($result);
		return $params;
	}
}

