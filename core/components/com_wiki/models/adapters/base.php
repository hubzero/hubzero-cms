<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Models\Adapters;

use Hubzero\Base\Obj;

/**
 * Abstract adapter class for a forum post link
 */
abstract class Base extends Obj
{
	/**
	 * Script name
	 *
	 * @var  string
	 */
	protected $_base = 'index.php';

	/**
	 * URL segments
	 *
	 * @var  array
	 */
	protected $_segments = array();

	/**
	 * Scope ID
	 *
	 * @var  integer
	 */
	protected $_scope_id = 0;

	/**
	 * Constructor
	 *
	 * @param   string   $pagename
	 * @param   string   $path
	 * @param   integer  $scope_id
	 * @return  void
	 */
	public function __construct($pagename=null, $path=null, $scope_id=0)
	{
		$pagename = ($path ? $path . '/' : '') . $pagename;

		$this->_segments['pagename'] = $pagename;

		$this->_scope_id = $scope_id;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type    The type of link to return
	 * @param   mixed   $params  Optional string or associative array of params to append
	 * @return  string
	 */
	public function link($type='', $params=null)
	{
		return $this->_base;
	}

	/**
	 * Flatten array of segments into querystring
	 *
	 * @param   array  $segments  An associative array of querystring bits
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
	 * Get an array of routing inputs
	 *
	 * @param   string  $task
	 * @return  array
	 */
	public function routing($task='save')
	{
		return array(
			'task' => $task
		);
	}

	/**
	 * Get permissions for a user
	 *
	 * @param   object  $page
	 * @return  boolean
	 */
	public function authorise($page)
	{
		return true;
	}
}
