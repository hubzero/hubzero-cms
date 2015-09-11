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

namespace Components\Developer\Models;

use Components\Developer\Tables;
use Hubzero\Base\Model;
use Hubzero\Base\ItemList;

// include application models
require_once __DIR__ . '/api/application.php';
require_once __DIR__ . '/api/authorizationcode.php';
require_once __DIR__ . '/api/accesstoken.php';
require_once __DIR__ . '/api/refreshtoken.php';

/**
 * Core developer model
 */
class Developer extends Model
{
	/**
	 * Container for cached data. Minimizes
	 * the number of queries made.
	 *
	 * @var  array
	 */
	private $_cache = array(
		'apps.count'          => null,
		'apps.list'           => null,
		'accesstokens.count'  => null,
		'accesstokens.list'   => null,
		'refreshtokens.count' => null,
		'refreshtokens.list'  => null
	);

	/**
	 * Get applications
	 * 
	 * @param   string  $rtrn     Data to return
	 * @param   array   $filters  Filters to apply
	 * @param   boolean $clear    Reset internal cache?
	 * @return  mixed
	 */
	public function applications($rtrn='list', $filters=array(), $clear=false)
	{
		$tbl = new Tables\Api\Application($this->_db);

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['apps.count']))
				{
					$this->_cache['apps.count'] = (int) $tbl->count($filters);
				}
				return $this->_cache['apps.count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_cache['apps.list'] instanceof ItemList))
				{
					if ($results = $tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Api\Application($result);
						}
					}

					$this->_cache['apps.list'] = new ItemList($results);
				}
				return $this->_cache['apps.list'];
			break;
		}
	}

	/** 
	 * Return application by Id
	 * 
	 * @param   integer  $id  Application Id
	 * @return  object   Developer Application Model
	 */
	public function application($id)
	{
		return new Api\Application($id);
	}

	/**
	 * Get list of developer tokens (access tokens)
	 * 
	 * @param   string  $rtrn     Data to return
	 * @param   array   $filters  Filters to apply
	 * @param   boolean $clear    Reset internal cache?
	 * @return  mixed
	 */
	public function accessTokens($rtrn='list', $filters=array(), $clear=false)
	{
		$tbl = new Tables\Api\AccessToken($this->_db);

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['accesstokens.count']))
				{
					$this->_cache['accesstokens.count'] = (int) $tbl->count($filters);
				}
				return $this->_cache['accesstokens.count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_cache['accesstokens.list'] instanceof ItemList))
				{
					if ($results = $tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Api\AccessToken($result);
						}
					}

					$this->_cache['accesstokens.list'] = new ItemList($results);
				}
				return $this->_cache['accesstokens.list'];
			break;
		}
	}

	/** 
	 * Return Access Token by Id
	 * 
	 * @param   integer  $id  Application Id
	 * @return  object   Developer Token Model
	 */
	public function accessToken($id)
	{
		return new Api\AccessToken($id);
	}

	/**
	 * Get list of developer tokens (refresh tokens)
	 * 
	 * @param   string  $rtrn     Data to return
	 * @param   array   $filters  Filters to apply
	 * @param   boolean $clear    Reset internal cache?
	 * @return  mixed
	 */
	public function refreshTokens($rtrn='list', $filters=array(), $clear=false)
	{
		$tbl = new Tables\Api\RefreshToken($this->_db);

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['refreshtokens.count']))
				{
					$this->_cache['refreshtokens.count'] = (int) $tbl->count($filters);
				}
				return $this->_cache['refreshtokens.count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_cache['refreshtokens.list'] instanceof ItemList))
				{
					if ($results = $tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Api\RefreshToken($result);
						}
					}

					$this->_cache['refreshtokens.list'] = new ItemList($results);
				}
				return $this->_cache['refreshtokens.list'];
			break;
		}
	}

	/** 
	 * Return Refresh Token by Id
	 * 
	 * @param   integer  $id  Refresh Token row Id
	 * @return  object   Refresh Token Model
	 */
	public function refreshToken($id)
	{
		return new Api\RefreshToken($id);
	}
}