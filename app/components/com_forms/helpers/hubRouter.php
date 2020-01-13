<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/urlBuilder.php";

use Components\Forms\Helpers\UrlBuilder;

class HubRouter
{

	/**
	 * Constructs ComponentRouter instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_urlBuilder = new UrlBuilder();
	}

	/**
	 * Generates hub URL based on given segments and parameters
	 *
	 * @param    array    $segments   URL segments
	 * @param    array    $params     URL parameters
	 * @return   string
	 */
	protected function _generateHubUrl($segments, $params = [])
	{
		$url = $this->_urlBuilder->generateUrl($segments, $params);

		return $url;
	}

}
