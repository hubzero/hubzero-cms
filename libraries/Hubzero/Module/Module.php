<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Module;

use Hubzero\Base\Object;
use Hubzero\Document\Assets;

/**
 * Base class for modules
 */
class Module extends Object
{
	/**
     * Callback for escaping.
     *
     * @var string
     */
	protected $_escape = 'htmlspecialchars';

	 /**
     * Charset to use in escaping mechanisms; defaults to urf8 (UTF-8)
     *
     * @var string
     */
	protected $_charset = 'UTF-8';

	/**
	 * JRegistry
	 *
	 * @var object
	 */
	public $params = null;

	/**
	 * Database row
	 *
	 * @var object
	 */
	public $module = null;

	/**
	 * Constructor
	 *
	 * @param   object $params JParameter/JRegistry
	 * @param   object $module Database row
	 * @return  void
	 */
	public function __construct($params, $module)
	{
		$this->params = $params;
		$this->module = $module;
	}

	/**
     * Escapes a value for output in a view script.
     *
     * If escaping mechanism is one of htmlspecialchars or htmlentities, uses
     * {@link $_encoding} setting.
     *
     * @param   mixed $var The output to escape.
     * @return  mixed The escaped value.
     */
	public function escape($var)
	{
		if (in_array($this->_escape, array('htmlspecialchars', 'htmlentities')))
		{
			return call_user_func($this->_escape, $var, ENT_COMPAT, $this->_charset);
		}

		return call_user_func($this->_escape, $var);
	}

	/**
     * Sets the _escape() callback.
     *
     * @param  mixed $spec The callback for _escape() to use.
     */
	public function setEscape($spec)
	{
		$this->_escape = $spec;
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
	 * @param   string  $module     Module name
	 * @return  object
	 */
	public function css($stylesheet = '', $module = null)
	{
		if (!$module)
		{
			$module = $this->module->module;
		}

		if ($module === true || strstr($stylesheet, '{') || strstr($stylesheet, '@'))
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

		if ($module == 'system')
		{
			Assets::addSystemStylesheet($stylesheet, $dir);
			return $this;
		}

		if (substr($module, 0, strlen('mod_')) !== 'mod_')
		{
			$module = 'mod_' . $module;
		}

		Assets::addModuleStylesheet($module, $stylesheet, $dir);

		return $this;
	}

	/**
	 * Push javascript to the document
	 *
	 * @param   string  $stylesheet Stylesheet name (optional, uses component name if left blank)
	 * @param   string  $module     Module name
	 * @return  object
	 */
	public function js($script = '', $module = null)
	{
		if (!$module)
		{
			$module = $this->module->module;
		}

		if ($module === true || strstr($script, '(') || strstr($script, ';'))
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

		if ($module == 'system')
		{
			Assets::addSystemScript($script);
			return $this;
		}

		if (substr($module, 0, strlen('mod_')) !== 'mod_')
		{
			$module = 'mod_' . $module;
		}

		Assets::addModuleScript($module, $script, $dir);

		return $this;
	}

	/**
	 * Get the path to an image
	 *
	 * @param   string  $image   Image name
	 * @param   string  $module  Module name
	 * @return  string
	 */
	public function img($image, $component = null)
	{
		if (!$module)
		{
			$module = $this->module->module;
		}

		$dir = $this->_assetDir($image, 'img');
		if ($dir == '/')
		{
			return rtrim(\JURI::base(true), '/') . $dir . $image;
		}

		if ($module == 'system')
		{
			return Assets::getSystemImage($image);
		}

		if (substr($module, 0, strlen('mod_')) !== 'mod_')
		{
			$module = 'mod_' . $module;
		}

		return Assets::getModuleImage($module, $image, $dir);
	}

	/**
	 * Display module
	 *
	 * @return  void
	 */
	public function display()
	{
		require(\JModuleHelper::getLayoutPath($this->module->module));
	}
}

