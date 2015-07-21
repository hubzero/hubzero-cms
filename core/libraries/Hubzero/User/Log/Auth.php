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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\User\Log;

use Hubzero\Database\Relational;

/**
 * User authentication log database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Auth extends Relational
{
	/**
	 * The table to which the class pertains
	 *
	 * @var  string
	 **/
	protected $table = '#__users_log_auth';

	/**
	 * Automatically fillable fields
	 *
	 * @var array
	 **/
	public $initiate = [
		'logged',
		'ip'
	];

	/**
	 * Generates automatic owned logged date/time
	 *
	 * @param   array  $data the data being saved
	 * @return  string
	 **/
	public function automaticLogged($data)
	{
		return \Date::of()->toSql();
	}

	/**
	 * Generates automatic source ip
	 *
	 * @param   array  $data the data being saved
	 * @return  string
	 **/
	public function automaticIp($data)
	{
		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
	}
}