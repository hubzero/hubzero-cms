<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Models\Adapters;

require_once __DIR__ . DS . 'base.php';

/**
 * Stories published to the hub as a whole
 */
class Site extends Base
{
	/**
	 * Scope name
	 *
	 * @var  string
	 */
	protected $_name = 'site';

	/**
	 * Build a link
	 *
	 * @param   string  $type
	 * @param   mixed   $params
	 * @return  string
	 */
	public function build($type = '', $params = null)
	{
		$segments = array('option' => 'com_story');

		if (is_array($params))
		{
			$segments = array_merge($segments, $params);
		}

		return $this->_base . '?' . $this->_build($segments);
	}
}
