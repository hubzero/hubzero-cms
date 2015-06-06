<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Encryption;

/**
 * Encryption key object
 */
class Key
{
	/**
	 * The private key.
	 *
	 * @var  string
	 */
	public $private;

	/**
	 * The public key.
	 *
	 * @var  string
	 */
	public $public;

	/**
	 * The key type.
	 *
	 * @var  string
	 */
	public $type;

	/**
	 * Constructor.
	 *
	 * @param   string  $type     The key type.
	 * @param   string  $private  The private key.
	 * @param   string  $public   The public key.
	 * @return  void
	 */
	public function __construct($type, $private = null, $public = null)
	{
		// Set the key type.
		$this->type = (string) $type;

		// Set the optional public/private key strings.
		$this->private = isset($private) ? (string) $private : null;
		$this->public  = isset($public)  ? (string) $public  : null;
	}

	/**
	 * Magic method to return some protected property values.
	 *
	 * @param   string  $name  The name of the property to return.
	 * @return  mixed
	 */
	/*public function __get($name)
	{
		if ($name == 'type')
		{
			return $this->type;
		}

		throw new InvalidArgumentException('Cannot access property ' . __CLASS__ . '::' . $name);
	}*/
}
