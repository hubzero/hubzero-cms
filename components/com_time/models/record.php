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
 * Records database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Record extends \Hubzero\Database\Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'time';

	/**
	 * Default order dir for fetch
	 *
	 * @var string
	 **/
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		'time'    => 'positive|nonzero',
		'task_id' => 'notempty'
	);

	/**
	 * Defines the inverse relationship between a record and a task
	 *
	 * @return \Hubzero\Database\Relationship\belongsToOne
	 * @author 
	 **/
	public function task()
	{
		return $this->belongsToOne('Task');
	}

	/**
	 * Defines a one to one relationship between record and hub user
	 *
	 * @return \Hubzero\Database\Relationship\oneToOne
	 * @since  1.3.2
	 **/
	public function user()
	{
		return $this->oneToOne('Hubzero\User\User');
	}

	/**
	 * Defines a one to many relationship between record and proxies
	 *
	 * @return \Hubzero\Database\Relationship\oneToMany
	 * @since  1.3.2
	 **/
	public function proxies()
	{
		return $this->oneToMany('Proxy', 'user_id', 'user_id');
	}

	/**
	 * Compares the current user to the model user
	 *
	 * @return bool
	 * @since  1.3.2
	 **/
	public function helperIsMine()
	{
		return $this->isCreator('user_id');
	}

	/**
	 * Checks if the current user is a proxy for the record owner
	 *
	 * @return bool
	 * @since  1.3.2
	 **/
	public function helperICanProxy()
	{
		return in_array(\JFactory::getUser()->get('id'), $this->proxies()->rows()->fieldsByKey('proxy_id'));
	}

	/**
	 * Pulls out hours from time field
	 *
	 * @return int
	 * @since  1.3.2
	 **/
	public function transformHours()
	{
		if (strpos($this->time, '.') !== false)
		{
			$parts = explode('.', $this->time);
			return $parts[0];
		}
		else
		{
			return $this->time;
		}
	}

	/**
	 * Pulls out minutes from time field
	 *
	 * @return int
	 * @since  1.3.2
	 **/
	public function transformMinutes()
	{
		if (strpos($this->time, '.') !== false)
		{
			$parts = explode('.', $this->time);
			return $parts[1];
		}
		else
		{
			return 0;
		}
	}
}