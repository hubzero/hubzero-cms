<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

