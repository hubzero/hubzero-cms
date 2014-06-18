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
	 * Push CSS to the document
	 *
	 * @param   string  $stylesheet Stylesheet name (optional, uses component name if left blank)
	 * @param   string  $component  Component name
	 * @param   string  $type       Mime encoding type
	 * @param   string  $media      Media type that this stylesheet applies to
	 * @param   string  $attribs    Attributes to add to the link
	 * @return  void
	 */
	public function css($stylesheet = '', $module = null, $type = 'text/css', $media = null, $attribs = array())
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

		if ($module == 'system')
		{
			Assets::addSystemStylesheet($stylesheet);
			return $this;
		}

		if (substr($module, 0, strlen('mod_')) !== 'mod_')
		{
			$module = 'mod_' . $module;
		}

		Assets::addModuleStylesheet($module, $stylesheet, $type, $media, $attribs);

		return $this;
	}

	/**
	 * Push javascript to the document
	 *
	 * @param   string  $stylesheet Stylesheet name (optional, uses component name if left blank)
	 * @param   string  $component  Component name
	 * @param   string  $type       Mime encoding type
	 * @param   string  $media      Media type that this stylesheet applies to
	 * @param   string  $attribs    Attributes to add to the link
	 * @return  void
	 */
	public function js($script = '', $module = null, $type = "text/javascript", $defer = false, $async = false)
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

		if ($module == 'system')
		{
			Assets::addSystemScript($script);
			return $this;
		}

		if (substr($module, 0, strlen('mod_')) !== 'mod_')
		{
			$module = 'mod_' . $module;
		}

		Assets::addModuleScript($module, $script, $type, $defer, $async);

		return $this;
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

