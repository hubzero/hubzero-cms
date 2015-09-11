<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Answers\Models;

use Hubzero\Base\Model;
use Hubzero\User\Profile;
use Hubzero\Config\Registry;
use Component;
use Lang;
use Date;

/**
 * Base class for Answers models to extend
 */
class Base extends Model
{
	/**
	 * Hubzero\User\Profile
	 *
	 * @var object
	 */
	protected $_creator = NULL;

	/**
	 * Registry
	 *
	 * @var object
	 */
	protected $_config = NULL;

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string $as What format to return
	 * @return  string
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get('created');
			break;
		}
	}

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire object
	 *
	 * @param   string $property Property to retrieve
	 * @param   mixed  $default  Default value if property not set
	 * @return  mixed
	 */
	public function creator($property=null, $default=null)
	{
		if (!($this->_creator instanceof Profile))
		{
			$this->_creator = Profile::getInstance($this->get('created_by'));
			if (!$this->_creator)
			{
				$this->_creator = new Profile();
			}
		}
		if ($property)
		{
			$property = ($property == 'id' ? 'uidNumber' : $property);
			return $this->_creator->get($property, $default);
		}
		return $this->_creator;
	}

	/**
	 * Was the entry reported?
	 *
	 * @return  boolean True if reported, False if not
	 */
	public function isReported()
	{
		if ($this->get('state') == self::APP_STATE_FLAGGED)
		{
			return true;
		}
		return false;
	}

	/**
	 * Get a configuration value
	 * If no key is passed, it returns the configuration object
	 *
	 * @param   string $key     Config property to retrieve
	 * @param   mixed  $default Default value if key not found
	 * @return  mixed
	 */
	public function config($key=null, $default=null)
	{
		if (!($this->_config instanceof Registry))
		{
			$this->_config = Component::params('com_answers');
		}
		if ($key)
		{
			if ($key == 'banking' && $this->_config->get('banking', -1) == -1)
			{
				$this->_config->set('banking', Component::params('com_members')->get('bankAccounts'));
			}
			return $this->_config->get($key, $default);
		}
		return $this->_config;
	}

	/**
	 * Check a user's authorization
	 *
	 * @param   string  $action Action to check
	 * @return  boolean True if authorized, false if not
	 */
	public function access($action='view', $item='entry')
	{
		return $this->config('access-' . strtolower($action) . '-' . $item);
	}
}

