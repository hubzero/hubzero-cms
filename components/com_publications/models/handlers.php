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
include_once(dirname(__FILE__) . DS . 'editor.php');

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
	* @var    array  Directories, where handlers can be stored
	*/
	protected $_path 	= array();

	/**
	* Configs
	*
	* @var
	*/
	protected	$_configs 	= NULL;

	/**
	* Editor
	*
	* @var
	*/
	public	$editor = NULL;

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
	public function showHandlers($pub, $elementid, $handlers, $handler, $attachments, $props = NULL)
	{
		$html = '';

		// TBD - Get handler configs from pub/type manifest
		$this->_configs = isset($pub->_curationModel->handlers) ? $pub->_curationModel->handlers : NULL;

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

			// Header
			$view = new \Hubzero\Component\View(array(
				'base_path' => JPATH_ROOT . DS . 'components' . DS . 'com_publications',
				'name'   => 'handlers',
				'layout' => '_header',
			));
			$html = $view->loadTemplate();

			// Go through available handlers and find those that apply
			foreach ($all as $item)
			{
				$handler  = $this->ini($item->name);
				if ($relevant = self::isRelevant($handler, $attachments) || $item->assigned)
				{
					$hview = new \Hubzero\Component\View(array(
						'base_path' => JPATH_ROOT . DS . 'components' . DS . 'com_publications',
						'name'   => 'handlers',
						'layout' => '_choice',
					));
					$configs = $handler->get('_configs');
					if (!$configs)
					{
						$saved = $obj->getConfig($item->name, $item);
						$configs = $handler->getConfig($saved);
					}
					$hview->handler  	= $handler;
					$hview->configs  	= $configs;
					$hview->item	 	= $item;
					$hview->publication = $pub;
					$hview->props 		= $props;
					$hview->relevant	= $relevant;
					$html .= $hview->loadTemplate();
					$i++;
				}
			}

			// No applicable hanlders?
			if ($i == 0)
			{
				return;
			}
		}

		return $html;
	}

	/**
	 * Update handler status / perform handler action
	 *
	 * @return  void
	 */
	public function update( $handler, $pub, $elementId = 0, $action = '' )
	{
		if (!$action)
		{
			return false;
		}
		// TBD
		return;
	}

	/**
	 * Load content editor for handler
	 *
	 * @return  void
	 */
	public function loadEditor( $handler, $pub, $elementId = 0 )
	{
		// Get handler configs
		$configs = $handler->get('_configs');
		if (!$configs)
		{
			$configs = $handler->getConfig();
		}

		// Start editor
		$editor = new PublicationsModelEditor($handler, $configs);

		// Make sure we have attachments
		if (!isset($pub->_attachments))
		{
			// Get attachments
			$pContent = new PublicationAttachment( $this->_parent->_db );
			$pub->_attachments = $pContent->sortAttachments ( $pub->version_id );
		}

		// Sort out attachments for this element
		$attachments = $pub->_attachments;
		$attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : NULL;

		// Set editor properties
		$editor->set('pub', $pub);
		$editor->set('attachments', $attachments);
		$editor->set('elementId', $elementId);

		// Check if assigned and get association record
		$objAssoc = new PublicationHandlerAssoc($this->_db);
		$association = $objAssoc->getAssociation($pub->version_id, $elementId, $handler->get('_name'));
		$editor->set('assoc', $association);

		// Check status
		$editor->set('configured', $association && $association->params ? true : false );
		$editor->set('assigned', $association ? true : false);
		$editor->set('relevant', self::isRelevant($handler, $attachments));

		// Check for changes
		// TBD

		return $editor;
	}

	/**
	 * Check if handler applies to selection
	 *
	 * @return  boolean
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
		if (!self::checkAllowed($attachments, $configs->params))
		{
			return false;
		}
		// Check required formats
		if (!self::checkRequired($attachments, $configs->params))
		{
			return false;
		}
		// Any additional custom checks
		if (!$handler->checkRequired($attachments))
		{
			return false;
		}

		return true;
	}

	/**
	 * Check for allowed formats
	 *
	 * @return  boolean
	 */
	public function checkAllowed( $attachments, $params )
	{
		if (empty($attachments))
		{
			return true;
		}
		$formats 	= $params->allowed_ext;
		$min 		= $params->min_allowed;
		$max 		= $params->max_allowed;
		$min		= $min ? $min : 1;
		$enforced   = isset($params->enforced) ? $params->enforced : 0;

		if (empty($formats))
		{
			return true;
		}

		$i = 0;
		$b = 0;
		foreach ($attachments as $attach)
		{
			// Skip non-file attachments
			if ($attach->type != 'file')
			{
				continue;
			}
			$file = isset($attach->path) ? $attach->path : $attach;
			$ext = explode('.', $file);
			$ext = end($ext);

			if ($ext && in_array(strtolower($ext), $formats))
			{
				$i++;
			}
			else
			{
				$b++;
			}
		}
		if ($enforced == 1 && $b > 0)
		{
			return false;
		}

		return ($i >= $min && ($max && $i <= $max)) ? true : false;
	}

	/**
	 * Check for required formats
	 *
	 * @return  boolean
	 */
	public function checkRequired( $attachments, $params )
	{
		if (empty($attachments))
		{
			return true;
		}
		$formats 	= $params->required_ext;

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
	 * @return  string HTML
	 */
	public function drawSelectedHandler($handler, $assigned = NULL)
	{
		$configs = $handler->get('_configs');
		if (!$configs)
		{
			$configs = $handler->getConfig();
		}

		$view = new \Hubzero\Component\View(array(
			'base_path' => JPATH_ROOT . DS . 'components' . DS . 'com_publications',
			'name'   => 'handlers',
			'layout' => '_selected',
		));
		$view->handler  = $handler;
		$view->configs  = $configs;
		$view->assigned = $assigned;
		return $view->loadTemplate();
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
	public function parseConfig($name, $configs = array(), $savedConfig = array())
	{
		// Get custom config from db
		if (!$savedConfig  || empty($savedConfig))
		{
			$obj = new PublicationHandler($this->_db);
			$savedConfig = $obj->getConfig($name);
		}

		// Overwrite default config with custom
		if ($savedConfig && !empty($savedConfig))
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