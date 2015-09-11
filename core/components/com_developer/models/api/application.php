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

use Components\Developer\Tables;
use Hubzero\Base\Model;
use Hubzero\Base\ItemList;
use Hubzero\User\Profile;
use Hubzero\Utility\String;
use Session;
use User;
use Date;
use Lang;

require_once __DIR__ . DS . 'application' . DS . 'team.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'api' . DS . 'application.php';

/**
 * Developer api model class
 */
class Application extends Model
{
	/**
	 * Table name
	 *
	 * @var  string
	 */
	protected $_tbl_name = '\\Components\\Developer\\Tables\\Api\\Application';

	/**
	 * Check if the created_by user ID matches the passed in ID
	 *
	 * @param   integer  $userid  User ID
	 * @return  boolean
	 */
	public function belongsToUserWithId($userid = null)
	{
		if ($userid == null)
		{
			$userid = User::get('id');
		}

		if ($this->get('created_by') == null)
		{
			$this->set('created_by', User::get('id'));
		}

		return $this->get('created_by') == $userid;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What format to return
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

			case 'relative':
				return Date::of($this->get('created'))->relative();
			break;

			default:
				if ($as)
				{
					return Date::of($this->get('created'))->toLocal($as);
				}
				return $this->get('created');
			break;
		}
	}

	/**
	 * Return application creator
	 * 
	 * @return  object  Created by profile
	 */
	public function creator($key = '')
	{
		if (!$profile = Profile::getInstance($this->get('created_by')))
		{
			return 'System User';
		}

		return ($key != '') ? $profile->get($key) : $profile;
	}

	/**
	 * Application description
	 * 
	 * @param   integer  $shorten
	 * @return  string
	 */
	public function description($shorten = 0, $options = array())
	{
		// get the description 
		$content = $this->get('description', null);

		// shorten if necessary
		if ($shorten)
		{
			$content = String::truncate($content, $shorten, $options);
		}

		return $content;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type  The type of link to return
	 * @return  string
	 */
	public function link($type='')
	{
		static $base;

		if (!isset($base))
		{
			$base = 'index.php?option=com_developer&controller=applications';
		}

		$link = $base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link .= '&task=edit&id=' . $this->get('id');
			break;

			case 'delete':
				$link .= '&task=delete&id=' . $this->get('id') . '&' . Session::getFormToken() . '=1';
			break;

			case 'revoke':
				$link .= '&task=revoke&id=' . $this->get('id') . '&' . Session::getFormToken() . '=1';
			break;

			case 'revokeall':
				$link .= '&task=revokeall&id=' . $this->get('id') . '&' . Session::getFormToken() . '=1';
			break;

			case 'stats':
				$link .= '&task=stats&id=' . $this->get('id');
			break;

			case 'team':
				$link .= '&task=team&id=' . $this->get('id');
			break;

			case 'view':
			case 'permalink':
			default:
				$link .= '&id=' . $this->get('id');
			break;
		}

		return $link;
	}

	/**
	 * Get Application Team Members
	 * 
	 * @param   integer  $uidNumber
	 * @return  array
	 */
	public function team($uidNumber = null)
	{
		// new team model
		$team = new Application\Team();

		// filters array
		$filters = array('application_id' => $this->get('id'));

		// do we want a specific team member
		if ($uidNumber != null)
		{
			$filters['uidNumber'] = $uidNumber;
			return $team->members('list', $filters)->first();
		}

		// get members
		return $team->members('list', $filters);
	}

	/**
	 * Override delete method to delete other suff
	 * 
	 * @return  voolean
	 */
	public function delete()
	{
		// check to make sure its not the hub account
		if ($this->get('hub_account') == 1)
		{
			$this->setError('Unable to delete the hub account.');
			return false;
		}

		// delete access tokens
		$this->revokeAccessTokens();

		// delete refresh tokens
		$this->revokeRefreshTokens();

		// delete authorization codes
		$this->revokeAuthorizationCodes();

		// set the application state
		$this->set('state', 2);
		if (!$this->store(false))
		{
			return false;
		}
		return true;
	}

	/**
	 * Get number of users using application
	 * 
	 * @return int
	 */
	public function users()
	{
		return $this->accessTokens()->count();
	}

	/**
	 * Load By Client ID
	 *
	 * @param   integer  $client_id
	 * @return  object
	 */
	public function loadByClientid($client_id)
	{
		$client = $this->_tbl->loadByClientid($client_id);
		return new self($client);
	}

	/**
	 * Is this application the hub account?
	 * 
	 * @return  boolean  Hub account test
	 */
	public function isHubAccount()
	{
		return (bool) $this->get('hub_account');
	}

	/**
	 * Create new client secret
	 * 
	 * @return  array  New client id/secret
	 */
	public function newClientSecret()
	{
		list($clientId, $clientSecret) = $this->_tbl->generateUniqueClientIdAndSecret();

		return $clientSecret;
	}

	/**
	 * Get a list of application access tokens
	 * 
	 * @param   array   $filters
	 * @return  object  List of AccessToken objects
	 */
	public function accessTokens($filters = array())
	{
		$tbl = new Tables\Api\AccessToken($this->_db);

		$filters = array_merge($filters, array(
			'application_id' => $this->get('id')
		));

		if ($results = $tbl->find($filters))
		{
			foreach ($results as $key => $result)
			{
				$results[$key] = new AccessToken($result);
			}
		}

		return new ItemList($results);
	}

	/**
	 * Get a list of application refresh tokens
	 * 
	 * @return  object  List of RefreshToken objects
	 */
	public function refreshTokens()
	{
		$tbl = new Tables\Api\RefreshToken($this->_db);

		$filters = array(
			'application_id' => $this->get('id')
		);

		if ($results = $tbl->find($filters))
		{
			foreach ($results as $key => $result)
			{
				$results[$key] = new RefreshToken($result);
			}
		}

		return new ItemList($results);
	}

	/**
	 * Get authorization codes
	 * 
	 * @return  void
	 */
	public function authorizationCodes()
	{
		//
	}

	/**
	 * Revoke access tokens
	 * 
	 * @return  void
	 */
	public function revokeAccessTokens()
	{
		foreach ($this->accessTokens() as $token)
		{
			$token->delete();
		}
	}

	/**
	 * Revoke refresh tokens
	 * 
	 * @return  void
	 */
	public function revokeRefreshTokens()
	{
		foreach ($this->refreshTokens() as $token)
		{
			$token->delete();
		}
	}

	/**
	 * Revoke authorization codes
	 * 
	 * @return  void
	 */
	public function revokeAuthorizationCodes()
	{
		//
	}
}