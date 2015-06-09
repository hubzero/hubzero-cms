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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Developer\Models\Api;

use Hubzero\Base\Model;
use Date;
use Lang;

require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'api' . DS . 'accesstoken.php';

/**
 * Access token model
 */
class AccessToken extends Model
{
	/**
	 * Table name
	 *
	 * @var  string
	 */
	protected $_tbl_name = '\\Components\\Developer\\Tables\\Api\\AccessToken';

	/**
	 * Return Instance of application for token
	 * 
	 * @return  object
	 */
	public function application()
	{
		return new Application($this->get('application_id'));
	}

	/**
	 * Return a formatted created timestamp
	 *
	 * @param   string  $as  What format to return
	 * @return  string
	 */
	public function created($as='')
	{
		return $this->timestamp('created', $as);
	}

	/**
	 * Return a formatted timestamp for expires
	 *
	 * @param   string  $as  What format to return
	 * @return  string
	 */
	public function expires($as='')
	{
		return $this->timestamp('expires', $as);
	}

	/**
	 * Load code details by code
	 * 
	 * @param   string  $accessToken
	 * @return  void
	 */
	public function loadByToken($accessToken)
	{
		$token = $this->_tbl->find(array(
			'access_token' => $accessToken,
			'limit'        => 1
		));
		
		return (!empty($token)) ? new self($token[0]) : null;
	}

	/**
	 * Reusable function for returning a formatted timestamp
	 *
	 * @param   string  $key  Key to format
	 * @param   string  $as   What format to return
	 * @return  string
	 */
	private function timestamp($key, $as = '')
	{
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get($key))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get($key))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			case 'relative':
				return Date::of($this->get($key))->relative();
			break;

			default:
				if ($as)
				{
					return Date::of($this->get($key))->toLocal($as);
				}
				return $this->get($key);
			break;
		}
	}

	/** 
	 * Expire token
	 * 
	 * @return  void
	 */
	public function expire()
	{
		$this->set('state', 2);
		$this->set('expires', Date::of('now')->toSql());
		if (!$this->store())
		{
			return false;
		}
		return true;
	}
}