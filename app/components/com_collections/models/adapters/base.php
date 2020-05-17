<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models\Adapters;

use Hubzero\Base\Obj;

/**
 * Abstract adapter class for a blog entry link
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
	 * Constructor
	 *
	 * @param   integer  $scope_id S cope ID (group, course, etc.)
	 * @return  void
	 */
	public function __construct($scope_id=0)
	{
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
			$bits[] = $key . '=' . $param;
		}
		return implode('&', $bits);
	}

	/**
	 * Check if a user has access
	 *
	 * @param   integer  $user_id
	 * @return  boolean
	 */
	public function canAccess($user_id)
	{
		return true;
	}
}
