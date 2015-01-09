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
 * @since     Class available since release 1.3.2
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Tasks database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Task extends \Hubzero\Database\Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'time';

	/**
	 * Default order by for fetch
	 *
	 * @var string
	 **/
	public $orderBy = 'name';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		'name'   => 'notempty',
		'hub_id' => 'notempty'
	);

	/**
	 * Defines a one to many relationship with records
	 *
	 * @return \Hubzero\Database\Relationship\oneToMany
	 * @since  1.3.2
	 **/
	public function records()
	{
		return $this->oneToMany('Record');
	}

	/**
	 * Defines the inverse relationship between a task and a hub
	 *
	 * @return \Hubzero\Database\Relationship\belongsTo
	 * @author 
	 **/
	public function hub()
	{
		return $this->belongsToOne('Hub');
	}

	/**
	 * Defines a one to one relationship between task and liaison
	 *
	 * @return \Hubzero\Database\Relationship\oneToOne
	 * @since  1.3.2
	 **/
	public function liaison()
	{
		return $this->oneToOne('Hubzero\User\User', 'liaison_id');
	}

	/**
	 * Defines a one to one relationship between task and assignee
	 *
	 * @return \Hubzero\Database\Relationship\oneToOne
	 * @since  1.3.2
	 **/
	public function assignee()
	{
		return $this->oneToOne('Hubzero\User\User', 'assignee_id');
	}

	/**
	 * Returns only the active tasks
	 *
	 * @return \Hubzero\Database\Row version row object
	 * @since 1.3.2
	 **/
	public function helperAreActive()
	{
		return $this->whereEquals('active', 1);
	}
}