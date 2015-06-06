<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Plugin;

use Hubzero\Document\Assets;
use Hubzero\Config\Registry;
use Hubzero\Base\Object;

/**
 * Base class for plugins to extend
 */
class Plugin extends Object
{
	use \Hubzero\Base\Traits\AssetAware;

	/**
	 * Event object to observe.
	 *
	 * @var    object
	 * @since  11.3
	 */
	protected $_subject = null;

	/**
	 * A Registry object holding the parameters for the plugin
	 *
	 * @var  Registry
	 */
	public $params = null;

	/**
	 * The name of the plugin
	 *
	 * @var  string
	 */
	protected $_name = null;

	/**
	 * The plugin type
	 *
	 * @var  string
	 */
	protected $_type = null;

	/**
	 * Container for component messages
	 *
	 * @var  array
	 */
	public $pluginMessageQueue = array();

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = false;

	/**
	 * Constructor
	 *
	 * @param   object  $subject  Event dispatcher
	 * @param   array   $config   Optional configurations to be used
	 * @return  void
	 */
	public function __construct($subject, $config)
	{
		// Set the subject to observe
		$this->_subject = &$subject;

		// Get the parameters.
		if (isset($config['params']))
		{
			$this->params = $config['params'];
		}

		if (!($this->params instanceof Registry))
		{
			$this->params = new Registry($this->params);
		}

		// Get the plugin name.
		if (isset($config['name']))
		{
			$this->_name = $config['name'];
		}

		// Get the plugin type.
		if (isset($config['type']))
		{
			$this->_type = $config['type'];
		}

		// @TODO: Remove this
		$this->option = (isset($config['type']) ? 'com_' . $config['type'] : 'com_' . $this->_type);

		// Load the language files if needed.
		if ($this->_autoloadLanguage)
		{
			$this->loadLanguage();
		}
	}

	/**
	 * Loads the plugin language file
	 *
	 * @param   string   $extension  The extension for which a language file should be loaded
	 * @param   string   $basePath   The basepath to use
	 * @return  boolean  True, if the file has successfully loaded.
	 */
	public function loadLanguage($extension = '', $basePath = JPATH_ADMINISTRATOR)
	{
		if (empty($extension))
		{
			$extension = 'plg_' . $this->_type . '_' . $this->_name;
		}

		$lang = \App::get('language');
		return $lang->load(strtolower($extension), $basePath, null, false, true)
			|| $lang->load(strtolower($extension), PATH_CORE . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true)
			|| $lang->load(strtolower($extension), PATH_APP . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true);
	}

	/**
	 * Redirect
	 *
	 * @param   string  $url      The url to redirect to
	 * @param   string  $msg      A message to display
	 * @param   string  $msgType  Message type [error, success, warning]
	 * @return  void
	 */
	public function redirect($url, $msg='', $msgType='')
	{
		if ($url)
		{
			$url = str_replace('&amp;', '&', $url);

			//preserve plugin messages after redirect
			if (count($this->pluginMessageQueue))
			{
				\App::get('session')->set('plugin.message.queue', $this->pluginMessageQueue);
			}

			\App::redirect($url, $msg, $msgType);
		}
	}

	/**
	 * Method to add a message to the component message que
	 *
	 * @param   string  $message  The message to add
	 * @param   string  $type     The type of message to add
	 * @return  object
	 */
	public function addPluginMessage($message, $type='message')
	{
		if (!count($this->pluginMessageQueue))
		{
			$session = \App::get('session');
			$pluginMessage = $session->get('plugin.message.queue');
			if (count($pluginMessage))
			{
				$this->pluginMessageQueue = $pluginMessage;
				$session->set('plugin.message.queue', null);
			}
		}

		//if message is somthing
		if ($message != '')
		{
			$this->pluginMessageQueue[] = array(
				'message' => $message,
				'type'    => strtolower($type),
				'option'  => $this->option,
				'plugin'  => $this->_name
			);
		}

		return $this;
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
			$session = \App::get('session');
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
	 * @param   string  $name    Plugin name
	 * @param   string  $folder  Plugin folder
	 * @return	object
	 */
	public static function getParams($name, $folder)
	{
		$database = \JFactory::getDBO();

		// load the params from databse
		$sql = "SELECT params FROM `#__extensions` WHERE folder=" . $database->quote($folder) . " AND element=" . $database->quote($name) . " AND enabled=1";
		$database->setQuery($sql);
		$result = $database->loadResult();

		// params object
		$params = new Registry($result);
		return $params;
	}

	/**
	 * Create a plugin view and return it
	 *
	 * @param   string  $layout  View layout
	 * @param   string  $name    View name
	 * @return  object
	 */
	public function view($layout='default', $name='')
	{
		$view = new View(array(
			'folder'  => $this->_type,
			'element' => $this->_name,
			'name'    => ($name   ?: $this->_name),
			'layout'  => ($layout ?: 'default')
		));
		return $view;
	}
}

