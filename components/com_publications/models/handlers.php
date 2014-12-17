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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once(dirname(__FILE__) . DS . 'attachment.php');
include_once(dirname(__FILE__) . DS . 'handler.php');

include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
	. DS . 'com_publications' . DS . 'tables' . DS . 'handler.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
	. DS . 'com_publications' . DS . 'tables' . DS . 'handlerassoc.php');

/**
 * Publications handlers class
 *
 */
class PublicationsModelHandlers extends JObject
{
	/**
	 * JDatabase
	 *
	 * @var object
	 */
	public $_db   		= NULL;

	/**
	* @var    array  Loaded elements
	*/
	protected $_types 	= array();

	/**
	* @var    array  Directories, where attachment types can be stored
	*/
	protected $_path 	= array();

	/**
	 * Constructor
	 *
	 * @param      object  &$db      	 JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		$this->_db 		= $db;
		$this->_path[] 	= dirname(__FILE__) . DS . 'handlers';
	}

	/**
	 * Show handler selection
	 *
	 * @return object
	 */
	public function showHandlers($pub, $elementid, $handlers, $handler, $attachments)
	{
		$html = '';
		// We have a forced handler
		if ($handler)
		{
			if (!is_object($handler))
			{
				$handler = $this->ini($handler);
			}
			$html  = '<div class="handler-controls">';
			$html .= $this->drawSelectedHandler($handler);
			$html .= '</div>';
		}
		elseif ($handlers)
		{
			// TEMP
			return false;

			// Load needed objects
			$obj = new PublicationHandler($this->_db);

			// Get all available handlers
			$all = $obj->getHandlers($pub->version_id, $elementid);

			// Get applicable handlers
			if (!$all)
			{
				return;
			}
			$i = 0;
			$html = '<div class="handler-controls">';
			foreach ($all as $item)
			{
				$handler  = $this->ini($item->name);
				if ($relevant = self::isRelevant($handler, $attachments) || $item->assigned)
				{
					$html .= $this->drawHandlerChoice($handler, $item->assigned, $relevant);
					$i++;
				}
			}
			$html.= '</div>';

			// No applicable hanlders?
			if ($i == 0)
			{
				return;
			}
		}

		return $html;
	}

	/**
	 * Check if handler applies to selection
	 *
	 * @return  void
	 */
	public function isRelevant( $handler, $attachments )
	{
		// Get handler configs
		$configs = $handler->get('_configs');
		if (!$configs)
		{
			$configs = $handler->getConfig();
		}

		// Attachments are needed
		if (!$attachments || empty($attachments))
		{
			return false;
		}

		// Check allowed formats
		if (!self::checkAllowed($attachments, $configs->params->allowed_ext))
		{
			return false;
		}

		// Check required formats
		if (!self::checkRequired($attachments, $configs->params->required_ext))
		{
			return false;
		}

		return true;
	}

	/**
	 * Check for allowed formats
	 *
	 * @return  object
	 */
	public function checkAllowed( $attachments, $formats = array() )
	{
		if (empty($attachments))
		{
			return true;
		}

		if (empty($formats))
		{
			return true;
		}

		foreach ($attachments as $attach)
		{
			$file = isset($attach->path) ? $attach->path : $attach;
			$ext = explode('.', $file);
			$ext = end($ext);

			if ($ext && !in_array(strtolower($ext), $formats))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Check for required formats
	 *
	 * @return  object
	 */
	public function checkRequired( $attachments, $formats = array() )
	{
		if (empty($attachments))
		{
			return true;
		}

		if (empty($formats))
		{
			return true;
		}

		$i = 0;
		foreach ($attachments as $attach)
		{
			$file = isset($attach->path) ? $attach->path : $attach;
			$ext = explode('.', $file);
			$ext = end($ext);

			if ($ext && in_array(strtolower($ext), $formats))
			{
				$i++;
			}
		}

		if ($i < count($formats))
		{
			return false;
		}

		return true;
	}

	/**
	 * Side controls for handler
	 *
	 * @return  void
	 */
	public function drawSelectedHandler($handler, $assigned = NULL)
	{
		$configs = $handler->get('_configs');
		if (!$configs)
		{
			$configs = $handler->getConfig();
		}
		$html = '<div class="handlertype-' . $handler->get('_name') . '">';
		$html.= '<h3>' . JText::_('Presentation') . ': ' . $configs->label . '</h3>';
		$html.= '<p>' . $configs->about . '</p>';
		$html.= '</div>';

		return $html;
	}

	/**
	 * Side controls for handler
	 *
	 * @return  void
	 */
	public function drawHandlerChoice($handler, $assigned = NULL, $relevant = true)
	{
		$configs = $handler->get('_configs');
		if (!$configs)
		{
			$configs = $handler->getConfig();
		}
		$html = '<div class="handlertype-' . $handler->get('_name') . '">';
		$html.= '<h3>' . $configs->label . '</h3>';
		$html.= '</div>';

		return $html;
	}


	/**
	 * Initialize
	 *
	 * @return object
	 */
	public function ini($name)
	{
		// Load
		$handler = $this->loadHandler($name);

		if ($handler === false)
		{
			return false;
		}

		// Load config
		$handler->getConfig();

		return $handler;

	}

	/**
	 * Loads a handler
	 *
	 * @return  object
	 */
	public function loadHandler( $name, $new = false )
	{
		$signature = md5($name);

		if ((isset($this->_types[$signature])
			&& !($this->_types[$signature] instanceof __PHP_Incomplete_Class))
			&& $new === false)
		{
			return	$this->_types[$signature];
		}

		$elementClass = 'PublicationsModelHandler' . ucfirst($name);
		if (!class_exists($elementClass))
		{
			if (isset($this->_path))
			{
				$dirs = $this->_path;
			}
			else
			{
				$dirs = array();
			}

			$file = JFilterInput::getInstance()->clean(str_replace('_', DS, $name).'.php', 'path');

			jimport('joomla.filesystem.path');
			if ($elementFile = JPath::find($dirs, $file))
			{
				include_once $elementFile;
			}
			else
			{
				$false = false;
				return $false;
			}
		}

		if (!class_exists($elementClass))
		{
			$false = false;
			return $false;
		}

		$this->_types[$signature] = new $elementClass($this);
		return $this->_types[$signature];
	}

	/**
	 * Get params for the handler
	 *
	 * @return  void
	 */
	public function parseConfig($name, $configs = array())
	{
		// Load config from db
		$obj = new PublicationHandler($this->_db);
		$savedConfig = $obj->getConfig($name);

		if ($savedConfig)
		{
			foreach ($configs as $configName => $configValue)
			{
				if ($configName == 'params')
				{
					foreach ($configValue as $paramName => $paramValue)
					{
						$configs['params'][$paramName] = isset($savedConfig['params'][$paramName]) && $savedConfig['params'][$paramName] ? $savedConfig['params'][$paramName] : $paramValue;
					}
				}
				else
				{
					$configs[$configName] = isset($savedConfig[$configName]) && $savedConfig[$configName] ? $savedConfig[$configName] : $configValue;
				}
			}
		}

		return $configs;
	}
}
