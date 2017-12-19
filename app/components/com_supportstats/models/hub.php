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
 * @author    Anthony Fuentes <fuentesa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Supportstats\Models;

require_once Component::path('com_supportstats') . '/helpers/hubConfigHelper.php';
require_once Component::path('com_supportstats') . '/helpers/arrayHelper.php';
require_once Component::path('com_supportstats') . '/helpers/apiHelper.php';
require_once Component::path('com_supportstats') . '/helpers/urlHelper.php';

use Components\Supportstats\Helpers\HubConfigHelper;
use Components\Supportstats\Helpers\ArrayHelper;
use Components\Supportstats\Helpers\ApiHelper;
use Components\Supportstats\Helpers\UrlHelper;
use Hubzero\Database\Relational;

/**
 * Serves as Hub entity
 */
class Hub extends Relational
{
	/**
	 * Table holding hub records
	 *
	 * @var string
	 */
	protected $table = 'jos_supportstats_hubs';
	/**
	 * Virtual attributes stored on model instances
	 *
	 * @var array
	 */
	protected $_virtualAttributes = array();

	/**
	 * Universal getter method
	 *
	 * @param   string	$key			Variable name
	 * @param   mixed		$default	Value to return in case attribute is not set
	 * @return  mixed
	 */
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

	/**
	 * Gets hub's outstanding tickets
	 *
	 * @return  array
	 */
	public function fetchOutstandingTickets()
	{
		$tickets = array();

		$endpoint = '/v2.0/support/outstandingtickets';
		$response = $this->_fetchFromApi($endpoint);
		$this->_setOutstandingTicketData($response);

		return $tickets;
	}

	/**
	 * Sets outstanding tickets virtual attribute
	 *
	 * @param   array		$outstandingTicketsData	Outstanding tickets data
	 * @return  null
	 */
	protected function _setOutstandingTicketData($outstandingTicketData)
	{
		$ticketsByCriterion = $outstandingTicketData['tickets'];
		$criteria = $outstandingTicketData['criteria'];

		$ticketsByDescription = $this->_mapTicketsToCriterionDescription($ticketsByCriterion, $criteria);

		$this->_setVirtualAttribute('outstanding_tickets', $ticketsByDescription);
	}

	/**
	 * Sets virtual attribute on hub instance
	 *
	 * @param   string	$name		Attribute name
	 * @param   string	$data		Attribute data
	 * @return  null
	 */
	protected function _setVirtualAttribute($name, $data)
	{
		$this->_virtualAttributes[$name] = $data;
	}

	/**
	 * Maps outstanding tickets to description of violated criterion
	 *
	 * @param   array	$ticketsByCriterion		Outstanding tickets mapped by criterion ID
	 * @param   array	$criteria							Criteria data
	 * @return  array
	 */
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

	/**
	 * Retrieves data from the hub's given API endpoint
	 *
	 * @param   string	$endpoint		API endpoint to send request to
	 * @param   array		$params			Query params to append to URL
	 * @return  array
	 */
	protected function _fetchFromApi($endpoint, $params = array())
	{
		$baseUrl = $this->get('api_url') . $endpoint;
		$combinedParams = array_merge($this->_getApiAccessParams(), $params);
		$fullUrl = UrlHelper::appendToUrl($baseUrl, $combinedParams);
		$response = $this->_sendApiRequest('get', $fullUrl);

		return $response;
	}

	/**
	 * Gets API access data
	 *
	 * @return  array
	 */
	protected function _getApiAccessParams()
	{
		$apiAccessParams = array(
			'access_token' =>	$this->_getAccessToken()
		);

		return $apiAccessParams;
	}

	/**
	 * Gets API access token
	 *
	 * @return string
	 */
	protected function _getAccessToken()
	{
		if (!isset($this->accessToken))
		{
			$this->accessToken = HubConfigHelper::getAccessToken($this->name);
		}

		return $this->accessToken;
	}

	/**
	 * Sends API request
	 *
	 * @param  string	$method		Request method to use
	 * @param  string	$url			URL to send request to
	 * @param  string	$params		Data to send with request
	 * @return array
	 */
	protected function _sendApiRequest($method, $url, $params = array())
	{
		$response = ApiHelper::$method($url, $params);

		return $response;
	}

	/**
	 * Builds URL for given user's home page
	 *
	 * @param  integer	$memberId		User's ID
	 * @return string
	 */
	public function getMemberUrl($memberId)
	{
		$memberUrl = $this->get('base_url') . "/members/$memberId";

		return $memberUrl;
	}

}
