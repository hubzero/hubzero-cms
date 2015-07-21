<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\User;

use Hubzero\Base\Object;

/**
 * Logger model
 *
 * This is basically an aggregator class.  It helps us map our relational models
 * and namespaces in a similar fashion.
*/
class Logger extends Object
{
	/**
	 * User model
	 *
	 * @var  \Hubzero\User\User
	 **/
	private $user = null;

	/**
	 * Constructs a new user logger class
	 *
	 * @return  void
	 * @since   2.0.0
	 **/
	public function __construct($user)
	{
		$this->user = $user;
	}

	/**
	 * Defines a one to many relationship between users and auth log entries
	 *
	 * @return  \Hubzero\Database\Relationship\OneToMany
	 * @since   2.0.0
	 **/
	public function auth()
	{
		return $this->user->oneToMany('Hubzero\User\Log\Auth');
	}
}