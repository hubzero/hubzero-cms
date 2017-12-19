<?php

namespace Components\Supportstats\Models;

require_once Component::path('com_supportstats') . '/helpers/clientApiConfigHelper.php';
require_once Component::path('com_supportstats') . '/helpers/hubConfigHelper.php';
require_once Component::path('com_supportstats') . '/helpers/arrayHelper.php';
require_once Component::path('com_supportstats') . '/helpers/apiHelper.php';
require_once Component::path('com_supportstats') . '/helpers/urlHelper.php';

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

		$endpoint = '/v2.0/support/outstandingtickets';
		$response = $this->_fetchFromApi($endpoint);
		$this->_setOutstandingTicketData($response);

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

		$endpoint = '/support/list';
		$response = $this->_fetchFromApi($endpoint, $params);
		$tickets = $response['tickets'];

		foreach ($tickets as &$ticket)
		{
			$ticket['hub'] = $this;
		}

		return $tickets;
	}

	protected function _fetchFromApi($endpoint, $params = array())
	{
		$baseUrl = $this->get('api_url') . $endpoint;
		$combinedParams = array_merge($this->_getApiAccessParams(), $params);
		$fullUrl = UrlHelper::appendToUrl($baseUrl, $combinedParams);
		$response = $this->_sendApiRequest('get', $fullUrl);

		return $response;
	}

	protected function _getApiAccessParams()
	{
		$apiAccessParams = array(
			'access_token' =>	$this->_getAccessToken()
		);

		return $apiAccessParams;
	}

	protected function _getAccessToken()
	{
		if (!isset($this->accessToken))
		{
			$this->accessToken = HubConfigHelper::getAccessToken($this->name);
		}

		return $this->accessToken;
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

	protected function _sendApiRequest($method, $url, $params = array())
	{
		$response = ApiHelper::$method($url, $params);

		return $response;
	}

	protected function _getAccessTokenParams($params)
	{
		return array_merge(array(
			'client_id' => $this->_getClientId(),
			'redirect_uri' => ClientApiConfigHelper::getRedirectUri(),
		), $params);
	}

	public function getMemberUrl($memberId)
	{
		$memberUrl = $this->get('base_url') . "/members/$memberId";

		return $memberUrl;
	}

}
