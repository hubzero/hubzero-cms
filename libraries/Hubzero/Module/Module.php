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
	use \Hubzero\Base\Traits\AssetAware;

	/**
     * Callback for escaping.
     *
     * @var  string
     */
	protected $_escape = 'htmlspecialchars';

	 /**
     * Charset to use in escaping mechanisms; defaults to urf8 (UTF-8)
     *
     * @var  string
     */
	protected $_charset = 'UTF-8';

	/**
	 * JRegistry
	 *
	 * @var  object
	 */
	public $params = null;

	/**
	 * Database row
	 *
	 * @var  object
	 */
	public $module = null;

	/**
	 * Constructor
	 *
	 * @param   object  $params  JParameter/JRegistry
	 * @param   object  $module  Database row
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
     * @param   mixed  $var  The output to escape.
     * @return  mixed  The escaped value.
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
     * @param   mixed   $spec  The callback for _escape() to use.
     * @return  object
     */
	public function setEscape($spec)
	{
		$this->_escape = $spec;

		return $this;
	}

	/**
	 * Display module
	 *
	 * @return  void
	 */
	public function display()
	{
		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}

	/**
	 * Get the path of a layout for this module
	 *
	 * @param   string  $layout  The layout name
	 * @return  string
	 */
	public function getLayoutPath($layout='default')
	{
		return \JModuleHelper::getLayoutPath($this->module->module, $layout);
	}
}

