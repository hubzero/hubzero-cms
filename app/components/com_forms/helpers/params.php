<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/mockProxy.php";

use Components\Forms\Helpers\MockProxy;
use Hubzero\Utility\Arr;

class Params
{

	protected $_request, $_whitelist;

	/**
	 * Constructs Params instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_request = Arr::getValue($args, 'request', new MockProxy(['class' => 'Request']));
		$this->_whitelist = $args['whitelist'];
	}

	/**
	 * Retrieves array data from request
	 *
	 * @param    string   $key       Key to retrieve data
	 * @param    array    $default   Default values
	 * @return   array
	 */
	public function getArray($key, $default = [])
	{
		$params = $this->_request->getArray($key, $default);

		$filteredParams = Arr::filterKeys($params, $this->_whitelist);

		return $filteredParams;
	}

	/**
	 * Forwards requests to _request
	 *
	 * @param    string   $key       Key to retrieve datum
	 * @param    array    $default   Default value
	 * @return   mixed
	 */
	public function __call($name, $args)
	{
		$result = $this->_request->$name(...$args);

		return $result;
	}

}
