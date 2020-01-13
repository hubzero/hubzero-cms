<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/hubRouter.php";

class ComponentRouter extends HubRouter
{

	protected $_baseSegment, $_urlBuilder;

	/**
	 * Constructs ComponentRouter instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_baseSegment = $args['base_segment'];

		parent::__construct();
	}

	/**
	 * Generates component URL based on given segments and parameters
	 *
	 * @param    array    $segments     URL segments
	 * @param    array    $parameters   URL parameters
	 * @return   string
	 */
	protected function _generateComponentUrl($segments, $parameters = [])
	{
		array_unshift($segments, $this->_baseSegment);

		$url = $this->_urlBuilder->generateUrl($segments, $parameters);

		return $url;
	}

}
