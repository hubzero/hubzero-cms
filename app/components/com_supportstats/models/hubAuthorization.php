<?php

namespace Components\Supportstats\Models;

require_once Component::path('com_supportstats') . '/helpers/hubAuthorizationFactory.php';
require_once Component::path('com_supportstats') . '/helpers/urlHelper.php';

use Components\Supportstats\Helpers\HubAuthorizationFactory;
use Components\Supportstats\Helpers\UrlHelper;
use Hubzero\Database\Relational;
use Request;
use Date;

class HubAuthorization extends Relational
{

	protected static $_stateFieldName = 'api_request_state';
	protected static $_escapedStateFieldName = 'escaped_api_request_state';
	protected $table = 'jos_supportstats_hub_authorizations';

	public function isValid()
	{
		return !$this->isNew() &&
			$this->get('access_token') &&
			($this->get('access_token_expiration') > Date::of('now'));
	}

	public function isRefreshable()
	{
		return $this->get('refresh_token_expiration') > Date::of('now');
	}

	public function saveAccessToken($accessTokenData)
	{
		$accessTokenLifetime = $accessTokenData['expires_in'];
		$refreshTokenLifetime = $this->_getRefreshTokenLifetime($accessTokenData);
		$accessTokenExpiration = Date::of('now')->add("{$accessTokenLifetime} seconds")->toSql();
		$refreshTokenExpiration = Date::of('now')->add("{$refreshTokenLifetime} seconds")->toSql();

		HubAuthorizationFactory::update($this, array(
				'access_token' => $accessTokenData['access_token'],
				'access_token_expiration' => $accessTokenExpiration,
				'refresh_token' => $accessTokenData['refresh_token'],
				'refresh_token_expiration' => $refreshTokenExpiration,
				'token_type' => $accessTokenData['token_type']
			)
		);
	}

	protected function _getRefreshTokenLifetime($accessTokenData)
	{
		$refreshTokenLifetime = 7200;
		$refreshTokenLifetimeKey = 'refresh_token_expires_in';

		if (array_key_exists($refreshTokenLifetimeKey, $accessTokenData))
		{
			$refreshTokenLifetime = $accessTokenData[$refreshTokenLifetimeKey];
		}

		return $refreshTokenLifetime;
	}

	public function setApiRequestState($clientSecret)
	{
		$apiRequestState = $this->_generateApiRequestState($clientSecret);
		$apiRequestState = UrlHelper::encryptParamValue($apiRequestState, $clientSecret);
		$this->_saveApiRequestState($apiRequestState);
	}

	protected function _generateApiRequestState($clientSecret)
	{
		$hubName = $this->getHub()->get('name');
		return  $hubName . '-' . rand();
	}

	protected function _saveApiRequestState($apiRequestState)
	{
		$escapedApiRequestState = self::_escapeApiRequestState($apiRequestState);

		HubAuthorizationFactory::update($this, array(
				self::$_escapedStateFieldName => $escapedApiRequestState,
				self::$_stateFieldName => $apiRequestState
			)
		);
	}

	protected function _escapeApiRequestState($apiRequestState)
	{
		return Request::getCmd(null, $apiRequestState);
	}

	public function getHub()
	{
		return $this->belongsToOne('Hub')
			->row();
	}

	public function getUser()
	{
		return $this->belongsToOne('Hubzero\User\User')
			->row();
	}

	public static function stateIsValid($escapedState)
	{
		$instance = self::_oneByEscapedState($escapedState);
		return !$instance->isNew();
	}

	public static function forCurrentUser()
	{
		$currentUserId = User::getInstance()->get('id');

		return self::forUser($currentUserId);
	}

	protected static function forUser($userId)
	{
		return self::blank()
			->whereEquals('user_id', $userId);
	}

	public static function oneByState($state)
	{
		return self::_oneByEscapedState($state);
	}

	protected static function _oneByEscapedState($escapedState)
	{
		$db = self::_getDb();
		$escapedState = $db->escape($escapedState);

		return self::blank()
			->whereEquals(self::$_escapedStateFieldName, $escapedState)
			->row();
	}

	protected static function _getDb()
	{
		return App::get('db');
	}

}
