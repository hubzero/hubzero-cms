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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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