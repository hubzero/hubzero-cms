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

namespace Hubzero\Database\Tests\Mock;

use Hubzero\Database\Relational;

/**
 * Post mock model
 *
 * @uses \Hubzero\Database\Relational
 */
class Post extends Relational
{
	/**
	 * Belongs to one relationship with user
	 *
	 * @return \Hubzero\Database\Relationship\BelongToOne
	 **/
	public function user()
	{
		// Be explicit, otherwise it will find the User facade
		return $this->belongsToOne('Hubzero\Database\Tests\Mock\User');
	}

	/**
	 * Many to many relationship with tags
	 *
	 * @return \Hubzero\Database\Relationship\ManyToMany
	 **/
	public function tags()
	{
		return $this->manyToMany('Tag');
	}
}