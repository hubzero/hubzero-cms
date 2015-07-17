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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 * @since     Class available since release 2.0.0
 */

namespace Components\Time\Models;

use Hubzero\Database\Relational;

/**
 * Tasks database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Task extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'time';

	/**
	 * Default order by for fetch
	 *
	 * @var  string
	 **/
	public $orderBy = 'name';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 **/
	protected $rules = [
		'name'   => 'notempty',
		'hub_id' => 'notempty'
	];

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 * @since   2.0.0
	 **/
	public function setup()
	{
		$this->addRule('end_date', function($data)
		{
			return $data['end_date'] >= $data['start_date'] ? false : 'The task cannot end before it begins';
		});
	}

	/**
	 * Defines a one to many relationship with records
	 *
	 * @return  \Hubzero\Database\Relationship\OneToMany
	 * @since   2.0.0
	 **/
	public function records()
	{
		return $this->oneToMany('Record');
	}

	/**
	 * Defines the inverse relationship between a task and a hub
	 *
	 * @return  \Hubzero\Database\Relationship\BelongsToOne
	 * @since   2.0.0
	 **/
	public function hub()
	{
		return $this->belongsToOne('Hub');
	}

	/**
	 * Defines a belongs to one relationship between task and liaison
	 *
	 * @return  \Hubzero\Database\Relationship\BelongsToOne
	 * @since   2.0.0
	 **/
	public function liaison()
	{
		return $this->belongsToOne('Hubzero\User\User', 'liaison_id');
	}

	/**
	 * Defines a belongs to one relationship between task and assignee
	 *
	 * @return  \Hubzero\Database\Relationship\BelongsToOne
	 * @since   2.0.0
	 **/
	public function assignee()
	{
		return $this->belongsToOne('Hubzero\User\User', 'assignee_id');
	}

	/**
	 * Returns only the active tasks
	 *
	 * @return  \Hubzero\Database\Row version row object
	 * @since   2.0.0
	 **/
	public function helperAreActive()
	{
		return $this->whereEquals('active', 1);
	}
}