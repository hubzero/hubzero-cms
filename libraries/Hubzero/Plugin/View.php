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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Plugin;

use Hubzero\View\View as AbstractView;
use Hubzero\Document\Assets;
use ReflectionClass;
use Exception;

/**
 * Base class for a plugin View
 */
class View extends AbstractView
{
	/**
	 * Layout name
	 *
	 * @var		string
	 */
	protected $_layout = 'default';

	/**
	 * Layout extension
	 *
	 * @var		string
	 */
	protected $_layoutExt = 'php';

	/**
	 * Folder
	 *
	 * @var	string
	 */
	protected $_folder = null;

	/**
	 * Folder
	 *
	 * @var	string
	 */
	protected $_element = null;

	/**
	 * Constructor
	 *
	 * @return   void
	 */
	public function __construct($config = array())
	{
		//set the view name
		if (empty($this->_folder))
		{
			if (array_key_exists('folder', $config))
			{
				$this->_folder = $config['folder'];
			}
			else
			{
				$this->_folder = $this->getFolder();
			}
		}

		//set the view name
		if (empty($this->_element))
		{
			if (array_key_exists('element', $config))
			{
				$this->_element = $config['element'];
			}
			else
			{
				$this->_element = $this->getElement();
			}
		}

		// Set the view name
		if (empty($this->_name))
		{
			if (array_key_exists('name', $config))
			{
				$this->_name = $config['name'];
			}
			else
			{
				$this->_name = $this->getName();
			}
		}

		// Set the charset (used by the variable escaping functions)
		if (array_key_exists('charset', $config))
		{
			$this->_charset = $config['charset'];
		}

		// User-defined escaping callback
		if (array_key_exists('escape', $config))
		{
			$this->setEscape($config['escape']);
		}

		// Set a base path for use by the view
		if (array_key_exists('base_path', $config))
		{
			$this->_basePath = $config['base_path'];
		}
		else
		{
			$this->_basePath = JPATH_PLUGINS . DS . $this->_folder . DS . $this->_element;
		}

		// Set the default template search path
		if (array_key_exists('template_path', $config))
		{
			// User-defined dirs
			$this->_setPath('template', $config['template_path']);
		}
		else
		{
			$this->_setPath('template', $this->_basePath . '/views/' . $this->getName() . '/tmpl');
		}

		// Set the default helper search path
		if (array_key_exists('helper_path', $config))
		{
			// User-defined dirs
			$this->_setPath('helper', $config['helper_path']);
		}
		else
		{
			$this->_setPath('helper', $this->_basePath . '/helpers');
		}

		// Set the layout
		if (array_key_exists('layout', $config))
		{
			$this->setLayout($config['layout']);
		}
		else
		{
			$this->setLayout('default');
		}

		$this->baseurl = \JURI::base(true);
	}

	/**
	 * Method to get the plugin folder
	 *
	 * The model name by default parsed using the classname, or it can be set
	 * by passing a $config['folder'] in the class constructor
	 *
	 * @return    string The name of the model
	 */
	public function getFolder()
	{
		$folder = $this->_folder;

		if (empty($folder))
		{
			$r = new ReflectionClass($this);
			if ($r->inNamespace())
			{
				$bits = explode('\\', __NAMESPACE__);

				// Should match either:
				//   Plugins\Folder\Element
				//   Components\Folder\Plugins\Element
				$folder = strtolower($bits[1]);
			}
			else
			{
				throw new Exception(\JText::_('JLIB_APPLICATION_ERROR_VIEW_GET_NAME'), 500);
			}
		}

		return $folder;
	}

	/**
	 * Method to get the plugin folder
	 *
	 * The model name by default parsed using the classname, or it can be set
	 * by passing a $config['folder'] in the class constructor
	 *
	 * @return    string The name of the model
	 */
	public function getElement()
	{
		$element = $this->_element;

		if (empty($element))
		{
			$r = new ReflectionClass($this);
			if ($r->inNamespace())
			{
				$bits = explode('\\', __NAMESPACE__);

				// Should match either:
				//   Plugins\Folder\Element
				//   Components\Folder\Plugins\Element
				$element = strtolower($bits[2]);

				if (strtolower($bits[0]) == 'components')
				{
					$element = strtolower($bits[3]);
				}
			}
			else
			{
				throw new Exception(\JText::_('JLIB_APPLICATION_ERROR_VIEW_GET_NAME'), 500);
			}
		}

		return $element;
	}

	/**
	* Sets an entire array of search paths for templates or resources.
	*
	* @param string $type The type of path to set, typically 'template'.
	* @param string|array $path The new set of search paths.  If null or false, resets to the current directory only.
	*/
	protected function _setPath($type, $path)
	{
		// clear out the prior search dirs
		$this->_path[$type] = array();

		// actually add the user-specified directories
		$this->_addPath($type, $path);

		// always add the fallback directories as last resort
		switch (strtolower($type))
		{
			case 'template':
			{
				$app = \JFactory::getApplication();

				$option = 'plg_' . $this->_folder . '_' . $this->_element;
				$option = preg_replace('/[^A-Z0-9_\.-]/i', '', $option);

				// set the alternative template search dir
				if (isset($app))
				{
					$this->_addPath(
						'template',
						JPATH_BASE . DS . 'templates' . DS . $app->getTemplate() . DS . 'html' . DS . $option . DS . $this->getName()
					);
				}
			}	break;
		}
	}

	/**
	 * Determine the asset directory
	 *
	 * @param   string $path    File path
	 * @param   string $default Default directory
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
	 * @param   string  $stylesheet Stylesheet name (optional, uses component name if left blank)
	 * @param   string  $folder     Plugin type
	 * @param   string  $element    Plugin name
	 * @return  void
	 */
	public function css($stylesheet = '', $folder = null, $element = null)
	{
		if (!$folder)
		{
			$folder = $this->_folder;
		}
		if (!$element)
		{
			$element = $this->_element;
		}

		if ($folder === true || strstr($stylesheet, '{') || strstr($stylesheet, '@'))
		{
			\JFactory::getDocument()->addStyleDeclaration($stylesheet);
			return $this;
		}

		if ($stylesheet && substr($stylesheet, -4) != '.css')
		{
			$stylesheet .= '.css';
		}

		$dir = $this->_assetDir($stylesheet, 'css');
		if ($dir == '/')
		{
			Assets::addStylesheet($dir . $stylesheet);
			return $this;
		}

		if ($folder == 'system')
		{
			Assets::addSystemStylesheet($stylesheet, $dir);
			return $this;
		}

		if (substr($folder, 0, strlen('com_')) == 'com_')
		{
			Assets::addComponentStylesheet($folder, $stylesheet, $dir);
		}

		Assets::addPluginStylesheet($folder, $element, $stylesheet, $dir);
		return $this;
	}

	/**
	 * Push javascript to the document
	 *
	 * @param   string  $stylesheet Stylesheet name (optional, uses component name if left blank)
	 * @param   string  $folder     Plugin type
	 * @param   string  $element    Plugin name
	 * @return  void
	 */
	public function js($script = '', $folder = null, $element = null)
	{
		if (!$folder)
		{
			$folder = $this->_folder;
		}
		if (!$element)
		{
			$element = $this->_element;
		}

		if ($folder === true || strstr($script, '(') || strstr($script, ';'))
		{
			\JFactory::getDocument()->addScriptDeclaration($script);
			return $this;
		}

		$dir = $this->_assetDir($script, 'js');
		if ($dir == '/')
		{
			Assets::addScript($dir . $script);
			return $this;
		}

		if ($folder == 'system')
		{
			Assets::addSystemScript($script, $dir);
			return $this;
		}

		if (substr($folder, 0, strlen('com_')) == 'com_')
		{
			Assets::addComponentScript($folder, $script, $dir);
		}

		Assets::addPluginScript($folder, $element, $script, $dir);
		return $this;
	}

	/**
	 * Get the path to an image
	 *
	 * @param   string  $image      Image name
	 * @param   string  $folder     Plugin type
	 * @param   string  $element    Plugin name
	 * @return  string
	 */
	public function img($image, $folder = null, $element = null)
	{
		if (!$folder)
		{
			$folder = $this->_folder;
		}
		if (!$element)
		{
			$element = $this->_element;
		}

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
	 * @param   string $layout View layout
	 * @param   string $name   View name
	 * @return	object
	 */
	public function view($layout, $name=null)
	{
		// If we were passed only a view model, just render it.
		if ($layout instanceof AbstractView)
		{
			return $layout;
		}

		$view = new self(array(
			'folder'  => $this->_folder,
			'element' => $this->_element,
			'name'    => ($name ? $name : $this->_name),
			'layout'  => $layout
		));
		$view->set('folder', $this->_folder)
		     ->set('element', $this->_element);

		return $view;
	}
}
