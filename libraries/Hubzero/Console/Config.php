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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Console;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Console configuration class
 **/
class Config
{
	/**
	 * Parsed config vars
	 *
	 * @var array
	 **/
	private $config = array();

	/**
	 * Constructor
	 *
	 * Parse for muse configuration file
	 *
	 * @return void
	 **/
	public function __construct()
	{
		$home = getenv('HOME');
		$path = $home . DS . '.muse';

		if (is_file($path))
		{
			$this->config = parse_ini_file($path);
		}
	}

	/**
	 * Function to return config var
	 *
	 * @param  (string) $key
	 * @param  (mixed) $default
	 * @return (string) $value
	 **/
	public static function get($key, $default=false)
	{
		static $instance;

		if (!isset($instance))
		{
			$instance = new self();
		}

		return (isset($instance->config[$key])) ? $instance->config[$key] : $default;
	}
}