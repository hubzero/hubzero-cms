<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;
use Filesystem;
use Component;

include_once(dirname(__FILE__) . DS . 'attachment.php');
include_once(dirname(__FILE__) . DS . 'handler.php');
include_once(dirname(__FILE__) . DS . 'editor.php');

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'handler.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'handlerassoc.php');

/**
 * Publications handlers class
 *
 */
class Handlers extends Obj
{
	/**
	 * Database
	 *
	 * @var object
	 */
	public $_db = null;

	/**
	 * Loaded elements
	 *
	 * @var  array
	 */
	protected $_types = array();

	/**
	 * Directories, where handlers can be stored
	 *
	 * @var  array
	 */
	protected $_path = array();

	/**
	 * Configs
	 *
	 * @var
	 */
	protected $_configs = null;

	/**
	 * Editor
	 *
	 * @var
	 */
	public $editor = null;

	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		$this->_db     = $db;
		$this->_path[] = dirname(__FILE__) . DS . 'handlers';
	}

	/**
	 * Show handler selection
	 *
	 * @param   object   $pub
	 * @param   integer  $elementid
	 * @param   array    $handlers
	 * @param   object   $handler
	 * @param   array    $attachments
	 * @param   object   $props
	 * @return  string
	 */
	public function showHandlers($pub, $elementid, $handlers, $handler, $attachments, $props = null)
	{
		$html = '';

		// TBD - Get handler configs from pub/type manifest
		$this->_configs = isset($pub->_curationModel->handlers) ? $pub->_curationModel->handlers : null;

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
			return '';

			// Load needed objects
			$obj = new \Components\Publications\Tables\Handler($this->_db);

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
				'base_path' => Component::path('com_publications') . DS . 'site',
				'name'      => 'handlers',
				'layout'    => '_header',
			));
			$html = $view->loadTemplate();

			// Go through available handlers and find those that apply
			foreach ($all as $item)
			{
				$handler  = $this->ini($item->name);
				if ($relevant = self::isRelevant($handler, $attachments) || $item->assigned)
				{
					$hview = new \Hubzero\Component\View(array(
						'base_path' => Component::path('com_publications') . DS . 'site',
						'name'      => 'handlers',
						'layout'    => '_choice',
					));
					$configs = $handler->get('_configs');
					if (!$configs)
					{
						$saved = $obj->getConfig($item->name, $item);
						$configs = $handler->getConfig($saved);
					}
					$hview->handler     = $handler;
					$hview->configs     = $configs;
					$hview->item        = $item;
					$hview->publication = $pub;
					$hview->props       = $props;
					$hview->relevant    = $relevant;
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
	 * @param   object   $handler
	 * @param   object   $pub
	 * @param   integer  $elementId
	 * @param   string   $action
	 * @return  void
	 */
	public function update($handler, $pub, $elementId = 0, $action = '')
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
	 * @param   object   $handler
	 * @param   object   $pub
	 * @param   integer  $elementId
	 * @return  object
	 */
	public function loadEditor($handler, $pub, $elementId = 0)
	{
		// Get handler configs
		$configs = $handler->get('_configs');
		if (!$configs)
		{
			$configs = $handler->getConfig();
		}

		// Start editor
		$editor = new \Components\Publications\Models\Editor($handler, $configs);

		// Make sure we have attachments
		if (!isset($pub->_attachments))
		{
			// Get attachments
			$pContent = new \Components\Publications\Tables\Attachment($this->_parent->_db);
			$pub->_attachments = $pContent->sortAttachments($pub->version_id);
		}

		// Sort out attachments for this element
		$attachments = $pub->_attachments;
		$attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : null;

		// Set editor properties
		$editor->set('pub', $pub);
		$editor->set('attachments', $attachments);
		$editor->set('elementId', $elementId);

		// Check if assigned and get association record
		$objAssoc = new \Components\Publications\Tables\HandlerAssoc($this->_db);
		$association = $objAssoc->getAssociation($pub->version_id, $elementId, $handler->get('_name'));
		$editor->set('assoc', $association);

		// Check status
		$editor->set('configured', $association && $association->params ? true : false);
		$editor->set('assigned', $association ? true : false);
		$editor->set('relevant', self::isRelevant($handler, $attachments));

		// Check for changes
		// TBD

		return $editor;
	}

	/**
	 * Check if handler applies to selection
	 *
	 * @param   object   $handler
	 * @param   array    $attachments
	 * @return  boolean
	 */
	public function isRelevant($handler, $attachments)
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
	 * @param   array    $attachments
	 * @param   object   $params
	 * @return  boolean
	 */
	public function checkAllowed($attachments, $params)
	{
		if (empty($attachments))
		{
			return true;
		}
		$formats  = $params->allowed_ext;
		$min      = $params->min_allowed;
		$max      = $params->max_allowed;
		$min      = $min ? $min : 1;
		$enforced = isset($params->enforced) ? $params->enforced : 0;

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
			$ext = \Components\Projects\Helpers\Html::getFileExtension($file);

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
	 * @param   array    $attachments
	 * @param   object   $params
	 * @return  boolean
	 */
	public function checkRequired($attachments, $params)
	{
		if (empty($attachments))
		{
			return true;
		}
		$formats = $params->required_ext;

		if (empty($formats))
		{
			return true;
		}

		$i = 0;
		foreach ($attachments as $attach)
		{
			$file = isset($attach->path) ? $attach->path : $attach;
			$ext = \Components\Projects\Helpers\Html::getFileExtension($file);

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
	 * @param   object   $handler
	 * @param   boolean  $assigned
	 * @return  string   HTML
	 */
	public function drawSelectedHandler($handler, $assigned = null)
	{
		$configs = $handler->get('_configs');
		if (!$configs)
		{
			$configs = $handler->getConfig();
		}

		$view = new \Hubzero\Component\View(array(
			'base_path' => Component::path('com_publications') . DS . 'site',
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
	 * @param   string  $name
	 * @return  object
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
	 * @param   string   $name
	 * @param   boolean  $new
	 * @return  object
	 */
	public function loadHandler($name, $new = false)
	{
		$signature = md5($name);

		if ((isset($this->_types[$signature])
			&& !($this->_types[$signature] instanceof __PHP_Incomplete_Class))
			&& $new === false)
		{
			return $this->_types[$signature];
		}

		$elementClass = '\Components\Publications\Models\Handlers\\' . ucfirst($name);
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

			$file = Filesystem::clean(str_replace('_', DS, $name).'.php', 'path');

			if ($elementFile = Filesystem::find($dirs, $file))
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
	 * @param   string  $name
	 * @param   array   $configs
	 * @param   array   $savedConfig
	 * @return  array
	 */
	public function parseConfig($name, $configs = array(), $savedConfig = array())
	{
		// Get custom config from db
		if (!$savedConfig  || empty($savedConfig))
		{
			$obj = new \Components\Publications\Tables\Handler($this->_db);
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
