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

jimport('joomla.plugin.plugin');

/**
 * Base class for plugins to extend
 */
class Plugin extends \JPlugin
{
	/**
	 * Container for component messages
	 * @var array
	 */
	public $pluginMessageQueue = array();

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = false;

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

		$this->option = (isset($config['type']) ? 'com_' . $config['type'] : 'com_' . $this->_type);

		// Load the language files if needed.
		if ($this->_autoloadLanguage)
		{
			$this->loadLanguage();
		}
	}

	/**
	 * Redirect
	 *
	 * @param   string $url     The url to redirect to
	 * @param   string $msg     A message to display
	 * @param   string $msgType Message type [error, success, warning]
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
				\JFactory::getSession()->set('plugin.message.queue', $this->pluginMessageQueue);
			}

			\JFactory::getApplication()->redirect($url, $msg, $msgType);
		}
	}

	/**
	 * Method to add a message to the component message que
	 *
	 * @param   string $message The message to add
	 * @param   string $type    The type of message to add
	 * @return  object
	 */
	public function addPluginMessage($message, $type='message')
	{
		if (!count($this->pluginMessageQueue))
		{
			$session = \JFactory::getSession();
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
			$session = \JFactory::getSession();
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
	 * @param   string $name   Plugin name
	 * @param   string $folder Plugin folder
	 * @return	object
	 */
	public static function getParams($name, $folder)
	{
		$database = \JFactory::getDBO();

		// load the params from databse
		$sql = "SELECT params FROM `#__extensions` WHERE folder=" . $database->quote($folder) . " AND element=" . $database->quote($name) . " AND enabled=1";
		$database->setQuery( $sql );
		$result = $database->loadResult();

		// params object
		$params = new \JRegistry($result);
		return $params;
	}

	/**
	 * Determine the asset directory
	 *
	 * @param   string  $path     File path
	 * @param   string  $default  Default directory
	 * @return  string
	 */
	private function _assetDir(&$path, $default='')
	{
		if (substr($path, 0, 2) == './')
		{
			$path = substr($path, 2);

			return '';
		}

		if (substr($path, 0, 1) == '/')
		{
			$path = substr($path, 1);

			return '/';
		}

		return $default;
	}

	/**
	 * Push CSS to the document
	 *
	 * @param   string  $stylesheet  Stylesheet name (optional, uses component name if left blank)
	 * @param   string  $folder      Plugin type
	 * @param   string  $element     Plugin name
	 * @return  object
	 */
	public function css($stylesheet = '', $folder = null, $element = null)
	{
		$folder  = $folder  ?: $this->_type;
		$element = $element ?: $this->_name;

		// Adding style declarations
		if ($folder === true || strstr($stylesheet, '{') || strstr($stylesheet, '@'))
		{
			\JFactory::getDocument()->addStyleDeclaration($stylesheet);
			return $this;
		}

		if ($stylesheet && substr($stylesheet, -4) != '.css')
		{
			$stylesheet .= '.css';
		}

		// Adding from an absolute path
		$dir = $this->_assetDir($stylesheet, 'css');
		if ($dir == '/')
		{
			Assets::addStylesheet($dir . $stylesheet);
			return $this;
		}

		// Adding a system stylesheet
		if ($folder == 'system')
		{
			Assets::addSystemStylesheet($stylesheet, $dir);
			return $this;
		}

		// Adding a component stylesheet
		if (substr($folder, 0, strlen('com_')) == 'com_')
		{
			Assets::addComponentStylesheet($folder, $stylesheet, $dir);
		}

		// Adding a plugin stylesheet
		Assets::addPluginStylesheet($folder, $element, $stylesheet, $dir);
		return $this;
	}

	/**
	 * Push javascript to the document
	 *
	 * @param   string  $stylesheet  Stylesheet name (optional, uses component name if left blank)
	 * @param   string  $folder      Plugin type
	 * @param   string  $element     Plugin name
	 * @return  object
	 */
	public function js($script = '', $folder = null, $element = null)
	{
		$folder  = $folder  ?: $this->_type;
		$element = $element ?: $this->_name;

		// Adding script declaration
		if ($folder === true || strstr($script, '(') || strstr($script, ';'))
		{
			\JFactory::getDocument()->addScriptDeclaration($script);
			return $this;
		}

		// Adding from an absolute path
		$dir = $this->_assetDir($script, 'js');
		if ($dir == '/')
		{
			Assets::addScript($dir . $script);
			return $this;
		}

		// Adding a system script
		if ($folder == 'system')
		{
			Assets::addSystemScript($script, $dir);
			return $this;
		}

		// Adding a component script
		if (substr($folder, 0, strlen('com_')) == 'com_')
		{
			Assets::addComponentScript($folder, $script, $dir);
		}

		// Adding a plugin script
		Assets::addPluginScript($folder, $element, $script, $dir);
		return $this;
	}

	/**
	 * Get the path to an image
	 *
	 * @param   string  $image    Image name
	 * @param   string  $folder   Plugin type
	 * @param   string  $element  Plugin name
	 * @return  string
	 */
	public function img($image, $folder = null, $element = null)
	{
		$folder  = $folder  ?: $this->_type;
		$element = $element ?: $this->_name;

		$dir = $this->_assetDir($image, 'img');
		if ($dir == '/')
		{
			return rtrim(\JURI::base(true), '/') . $dir . $image;
		}

		if ($folder == 'system')
		{
			return Assets::getSystemImage($image);
		}

		if (substr($folder, 0, strlen('com_')) == 'com_')
		{
			return Assets::getComponentImage($folder, $image, $dir);
		}

		return Assets::getPluginImage($folder, $element, $image, $dir);
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

