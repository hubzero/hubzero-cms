<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Models\Adapters;

use Hubzero\Base\Obj;

/**
 * Abstract adapter class for a forum post link
 */
abstract class Base extends Obj
{
	/**
	 * Script name
	 *
	 * @var string
	 */
	protected $_base = 'index.php';

	/**
	 * URL segments
	 *
	 * @var string
	 */
	protected $_segments = array();

	/**
	 * Scope title
	 *
	 * @var string
	 */
	protected $_name = '';

	/**
	 * Constructor
	 *
	 * @param   integer  $scope_id  Scope ID (group, course, etc.)
	 * @return  void
	 */
	public function __construct($scope_id=0)
	{
	}

	/**
	 * Get the scope type
	 *
	 * @return  string
	 */
	public function name()
	{
		return $this->_name;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type    The type of link to return
	 * @param   mixed   $params  Optional string or associative array of params to append
	 * @return  string
	 */
	public function build($type='', $params=null)
	{
		return $this->_base;
	}

	/**
	 * Flatten array of segments into querystring
	 *
	 * @param   array   $segments  An associative array of querystring bits
	 * @return  string
	 */
	protected function _build(array $segments)
	{
		$bits = array();
		foreach ($segments as $key => $param)
		{
			if (!trim($param))
			{
				continue;
			}
			$bits[] = $key . '=' . $param;
		}
		return implode('&', $bits);
	}
}
