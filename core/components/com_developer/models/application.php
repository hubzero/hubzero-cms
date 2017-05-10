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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\Validate;
use Session;
use Lang;

include_once __DIR__ . DS . 'accesstoken.php';
include_once __DIR__ . DS . 'refreshtoken.php';
include_once __DIR__ . DS . 'authorizationcode.php';
include_once __DIR__ . DS . 'application' . DS . 'member.php';

/**
 * Develper mdoel for an application
 */
class Application extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'developer';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'created';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'name'  => 'notempty',
		'description' => 'notempty',
		'entry_id' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by',
		'grant_types',
		'client_id',
		'client_secret'
	);

	/**
	 * Load entry by client_id
	 * 
	 * @param   string  $client_id
	 * @return  object
	 */
	public static function oneByClientId($client_id)
	{
		$code = self::all()
			->whereEquals('client_id', $client_id)
			->row();

		return $code;
	}

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 **/
	public function setup()
	{
		$this->addRule('redirect_uri', function($data)
		{
			if (!isset($data['redirect_uri']) || !$data['redirect_uri'])
			{
				return Lang::txt('COM_DEVELOPER_API_APPLICATION_MISSING_REDIRECT_URI');
			}

			$uris = array_map('trim', explode(PHP_EOL, $data['redirect_uri']));

			// must have one
			if (empty($uris))
			{
				return Lang::txt('COM_DEVELOPER_API_APPLICATION_MISSING_REDIRECT_URI');
			}

			// validate each one
			$invalid = array();
			foreach ($uris as $uri)
			{
				if (!Validate::url($uri))
				{

					$invalid[] = $uri;
				}
			}

			// if we have any invalid URIs lets inform the user
			if (!empty($invalid))
			{
				return Lang::txt('COM_DEVELOPER_API_APPLICATION_INVALID_REDIRECT_URI', implode('<br />', $invalid));
			}

			return false;
		});
	}

	/**
	 * Is this application the hub account?
	 * 
	 * @return  bool
	 */
	public function isHubAccount()
	{
		return (bool) $this->get('hub_account');
	}

	/**
	 * Is this application published
	 * 
	 * @return  bool
	 */
	public function isPublished()
	{
		return $this->get('state') == self::STATE_PUBLISHED;
	}

	/**
	 * Is this application published
	 * 
	 * @return  bool
	 */
	public function isUnpublished()
	{
		return $this->get('state') == self::STATE_UNPUBLISHED;
	}

	/**
	 * Is this application published
	 * 
	 * @return  bool
	 */
	public function isDeleted()
	{
		return $this->get('state') == self::STATE_DELETED;
	}

	/**
	 * Defines a belongs to one relationship between entry and user
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Generates automatic grant_types field value
	 *
	 * @param   array   $data  The data being saved
	 * @return  string
	 */
	public function automaticGrantTypes($data)
	{
		if (!isset($data['hub_account']) || !$data['hub_account'])
		{
			// Allow the 3 main grantypes
			// 
			// authorization code = 3 legged oauth
			// password           = users username/password
			// refresh_token      = allow refreshing of access_tokens to require less logins
			$data['grant_types'] = 'authorization_code password refresh_token';
		}

		return $data['grant_types'];
	}

	/**
	 * Generates automatic client_id field value
	 *
	 * @param   array   $data  The data being saved
	 * @return  string
	 */
	public function automaticClientId($data)
	{
		if (!isset($data['client_id']) || !$data['client_id'])
		{
			$data['client_id'] = md5(uniqid($this->get('created_by'), true));
		}

		return $data['client_id'];
	}

	/**
	 * Generates automatic client_secret field value
	 *
	 * @param   array   $data  The data being saved
	 * @return  string
	 */
	public function automaticClientSecret($data)
	{
		if (!isset($data['client_secret']) || !$data['client_secret'])
		{
			if (!$this->get('client_id'))
			{
				$this->set('client_id', $this->automaticClientId($data));
			}
			$data['client_secret'] = sha1($this->get('client_id'));
		}

		return $data['client_secret'];
	}

	/**
	 * Create new client secret
	 * 
	 * @return  string
	 */
	public function newClientSecret()
	{
		$data = array();

		$this->set('client_id', $this->automaticClientId($data));

		$data['client_id'] = $this->get('client_id');

		return $this->automaticClientSecret($data);
	}

	/**
	 * Get the team members
	 *
	 * @return  object
	 */
	public function team()
	{
		return $this->oneToMany('Components\Developer\Models\Application\Member', 'application_id');
	}

	/**
	 * Save record
	 * 
	 * @return  bool
	 */
	public function save()
	{
		// check to make sure its not the hub account
		if ($this->get('state') == self::STATE_DELETED)
		{
			if (!$this->revokeAccessTokens())
			{
				return false;
			}

			if (!$this->revokeRefreshTokens())
			{
				return false;
			}

			if (!$this->revokeAuthorizationCodes())
			{
				return false;
			}
		}

		return parent::save();
	}

	/**
	 * Delete record and associated data
	 * 
	 * @return  bool
	 */
	public function destroy()
	{
		// check to make sure its not the hub account
		if ($this->get('hub_account') == 1)
		{
			$this->setError('Unable to delete the hub account.');
			return false;
		}

		if (!$this->revokeAccessTokens())
		{
			return false;
		}

		if (!$this->revokeRefreshTokens())
		{
			return false;
		}

		if (!$this->revokeAuthorizationCodes())
		{
			return false;
		}

		foreach ($this->team()->rows() as $member)
		{
			if (!$member->destroy())
			{
				$this->addError($member->getError());
				return false;
			}
		}

		return parent::destroy();
	}

	/**
	 * Get a list of application access tokens
	 * 
	 * @return  object  List of AccessToken objects
	 */
	public function accessTokens()
	{
		return $this->oneToMany('Accesstoken', 'application_id');
	}

	/**
	 * Revoke access tokens
	 * 
	 * @return  bool
	 */
	public function revokeAccessTokens()
	{
		foreach ($this->accessTokens()->rows() as $token)
		{
			if (!$token->destroy())
			{
				$this->addError($token->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Get a list of application refresh tokens
	 * 
	 * @return  object  List of Refreshtoken objects
	 */
	public function refreshTokens()
	{
		return $this->oneToMany('Refreshtoken', 'application_id');
	}

	/**
	 * Revoke refresh tokens
	 * 
	 * @return  bool
	 */
	public function revokeRefreshTokens()
	{
		foreach ($this->refreshTokens()->rows() as $token)
		{
			if (!$token->destroy())
			{
				$this->addError($token->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Get a list of application authorization codes
	 * 
	 * @return  object
	 */
	public function authorizationCodes()
	{
		return $this->oneToMany('Authorizationcode', 'application_id');
	}

	/**
	 * Revoke authorization codes
	 * 
	 * @return  void
	 */
	public function revokeAuthorizationCodes()
	{
		foreach ($this->authorizationCodes()->rows() as $code)
		{
			if (!$code->destroy())
			{
				$this->addError($code->getError());
				return false;
			}
		}

		return true;
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
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What format to return
	 * @return  string
	 */
	public function created($as='')
	{
		$as = strtolower($as);

		if ($as == 'date')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		if ($as)
		{
			return Date::of($this->get('created'))->toLocal($as);
		}

		return $this->get('created');
	}

	/**
	 * Get number of users using application
	 * 
	 * @return  int
	 */
	public function users()
	{
		return $this->accessTokens()->total();
	}
}
