<?php

namespace Components\Supportstats\Models;

require_once Component::path('com_supportstats') . '/helpers/hubAuthorizationFactory.php';
require_once Component::path('com_supportstats') . '/helpers/clientApiConfigHelper.php';
require_once Component::path('com_supportstats') . '/helpers/hubConfigHelper.php';
require_once Component::path('com_supportstats') . '/helpers/arrayHelper.php';
require_once Component::path('com_supportstats') . '/helpers/apiHelper.php';
require_once Component::path('com_supportstats') . '/helpers/urlHelper.php';

use Components\Supportstats\Helpers\HubAuthorizationFactory;
use Components\Supportstats\Helpers\ClientApiConfigHelper;
use Components\Supportstats\Helpers\HubConfigHelper;
use Components\Supportstats\Helpers\ArrayHelper;
use Components\Supportstats\Helpers\ApiHelper;
use Components\Supportstats\Helpers\UrlHelper;
use Hubzero\Database\Relational;

class Hub extends Relational
{
	protected $table = 'jos_supportstats_hubs';
	protected $_virtualAttributes = array();
	public $_apiClientId, $_apiClientSecret;

	public function get($key, $default = null)
	{
		if (array_key_exists($key, $this->_virtualAttributes))
		{
			return $this->_virtualAttributes[$key];
		}
		else
		{
			return parent::get($key, $default);
		}
	}

	public function fetchOutstandingTickets()
	{
		$tickets = array();

		if ($this->authorizationIsRefreshable)
		{
			$endpoint = '/v2.0/support/outstandingtickets';
			$response = $this->_fetchFromApi($endpoint);
			$this->_setOutstandingTicketData($response);
		}

		return $tickets;
	}

	protected function _setOutstandingTicketData($outstandingTicketData)
	{
		$ticketsByCriterion = $outstandingTicketData['tickets'];
		$criteria = $outstandingTicketData['criteria'];

		$ticketsByDescription = $this->_mapTicketsToCriterionDescription($ticketsByCriterion, $criteria);

		$this->_setVirtualAttribute('outstanding_tickets', $ticketsByDescription);
	}

	protected function _setVirtualAttribute($name, $data)
	{
		$this->_virtualAttributes[$name] = $data;
	}

	public function _mapTicketsToCriterionDescription($ticketsByCriterion, $criteria)
	{
		$ticketsByDescription = array();

		foreach ($ticketsByCriterion as $criterionId => $tickets)
		{
			$criterionDescription = $criteria[$criterionId]['description'];
			$ticketsByDescription[$criterionDescription] = $tickets;
		}

		return $ticketsByDescription;
	}

	public function fetchTickets($params = array(
		'limit' => 25, 'limitstart' => 0, 'sort' => 'created', 'sort_Dir' => 'desc'
	))
	{
		$tickets = array();

		if ($this->authorizationIsRefreshable)
		{
			$endpoint = '/support/list';
			$response = $this->_fetchFromApi($endpoint, $params);
			$tickets = $response['tickets'];

			foreach ($tickets as &$ticket)
			{
				$ticket['hub'] = $this;
			}
		}

		return $tickets;
	}

	protected function _fetchFromApi($endpoint, $params = array())
	{
		$baseUrl = $this->get('api_url') . $endpoint;
		$combinedParams = array_merge($this->_getApiAccessParams(), $params);
		$fullUrl = UrlHelper::appendToUrl($baseUrl, $combinedParams);

		return $this->_sendApiRequest('get', $fullUrl);
	}

	protected function _getApiAccessParams()
	{
		$hubAuthorization = $this->getHubAuthorization();
		$apiAccessParams = array(
			'access_token' =>	$hubAuthorization->get('access_token')
		);

		return $apiAccessParams;
	}

	public function getAuthUrl()
	{
		$authUrl = $this->get('base_url') . '/developer/oauth/authorize';
		$authUrlParams = $this->_getAuthUrlParams();

		return UrlHelper::appendToUrl($authUrl, $authUrlParams);
	}

	protected function _getAuthUrlParams()
	{
		return array(
			'client_id' => $this->_getClientId(),
			'redirect_uri' => ClientApiConfigHelper::getRedirectUri(),
			'state' => $this->_getApiRequestState(),
			'response_type' => 'code'
		);
	}

	protected function _getApiRequestState()
	{
		$hubAuthorization = $this->getHubAuthorization();
		$apiClientSecret = $this->_getClientSecret();

		$hubAuthorization->setApiRequestState($apiClientSecret);

		return $hubAuthorization->get('api_request_state');
	}

	public function getHubAuthorization($userId = null)
	{
		if (!$userId)
		{
			$userId = User::getInstance()->get('id');
		}

		$hubAuthorization = $this->getHubAuthorizations()
			->whereEquals('user_id', $userId)
			->row();

		if ($hubAuthorization->isNew())
		{
			$hubAuthorization = $this->_createHubAuthorization($userId);
		}

		return $hubAuthorization;
	}

	protected function _createHubAuthorization($userId)
	{
		return HubAuthorizationFactory::create(
			array(
				'hub_id' => $this->get('id'),
				'user_id' => $userId
			)
		);
	}

	public function getHubAuthorizations()
	{
		return $this->oneToMany('HubAuthorization');
	}

	public function fetchAccessToken($code)
	{
		$requestParams = array(
			'code' => $code,
			'grant_type' => 'authorization_code'
		);

		return $this->_requestAccessToken($requestParams);
	}

	protected function _requestAccessToken($params)
	{
		$accessTokenUrl = $this->_getAccessTokenUrl();
		$accessTokenParams = $this->_getAccessTokenParams($params);

		$response = $this->_sendApiRequest('post', $accessTokenUrl, array(
			'json' =>	$accessTokenParams
		));

		return $response;
	}

	protected function _sendApiRequest($method, $url, $params = array())
	{
		if ($url !== $this->_getAccessTokenUrl())
		{
			$this->_refreshAccessToken();
		}

		return ApiHelper::$method($url, $params);
	}

	protected function _getAccessTokenUrl()
	{
		return $this->get('base_url') . '/developer/oauth/token';
	}

	protected function _refreshAccessToken()
	{
		$hubAuthorization = $this->getHubAuthorization();
		$refreshToken = $hubAuthorization->get('refresh_token');

		if (!$hubAuthorization->isValid())
		{
			$requestParams = array(
				'refresh_token' => $refreshToken,
				'client_secret' => $this->_getClientSecret(),
				'grant_type' => 'refresh_token'
			);
			$accessTokenData = $this->_requestAccessToken($requestParams);
			$hubAuthorization->saveAccessToken($accessTokenData);
		}
	}

	protected function _getAccessTokenParams($params)
	{
		return array_merge(array(
			'client_id' => $this->_getClientId(),
			'redirect_uri' => ClientApiConfigHelper::getRedirectUri(),
		), $params);
	}

	protected function _getClientId()
	{
		if (!isset($this->_apiClientId))
		{
			$this->_setApiCredentials();
		}

		return $this->_apiClientId;
	}

	protected function _getClientSecret()
	{
		if (!isset($this->_apiClientSecret))
		{
			$this->_setApiCredentials();
		}

		return $this->_apiClientSecret;
	}

	protected function _setApiCredentials()
	{
		HubConfigHelper::setApiCredentials($this);
	}

	public static function allWithAuthorization()
	{
		$hubs = self::all()->order('name', 'ASC')->rows();
		$usersHubAuthorizations = ArrayHelper::mapByAttribute(
			HubAuthorization::forCurrentUser(),
			'hub_id'
		);

		foreach ($hubs as $hub)
		{
			self::addAuthorizationStatus($hub, $usersHubAuthorizations);
		}

		return $hubs;
	}

	protected static function addAuthorizationStatus($hub, $usersHubAuthorizations)
	{
		$hubId = $hub->get('id');

		if (array_key_exists($hubId, $usersHubAuthorizations))
		{
			$hubAuthorization = $usersHubAuthorizations[$hubId];
			$hub->authorizationIsValid = $hubAuthorization->isValid();
			$hub->authorizationIsRefreshable = $hubAuthorization->isRefreshable();
		}
	}

	public function getMemberUrl($memberId)
	{
		$memberUrl = $this->get('base_url') . "/members/$memberId";

		return $memberUrl;
	}

}
