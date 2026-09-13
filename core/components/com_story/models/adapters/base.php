<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Models\Adapters;

use Hubzero\Base\Obj;

/**
 * Where a story lives, and therefore what its links look like
 *
 * Site-wide today. The adapter exists from the start because a story feed
 * per research group is the likeliest reason a hub adopts this at all, and
 * retrofitting scope onto a populated component is miserable.
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
	 * Scope name
	 *
	 * @var  string
	 */
	protected $_name = '';

	/**
	 * Constructor
	 *
	 * @param   integer  $scopeId
	 * @return  void
	 */
	public function __construct($scopeId = 0)
	{
	}

	/**
	 * The scope this adapter speaks for
	 *
	 * @return  string
	 */
	public function name()
	{
		return $this->_name;
	}

	/**
	 * Build a link
	 *
	 * @param   string  $type
	 * @param   mixed   $params
	 * @return  string
	 */
	public function build($type = '', $params = null)
	{
		return $this->_base;
	}

	/**
	 * Flatten segments into a query string
	 *
	 * @param   array  $segments
	 * @return  string
	 */
	protected function _build(array $segments)
	{
		$bits = array();

		foreach ($segments as $key => $param)
		{
			if (!strlen(trim((string) $param)))
			{
				continue;
			}

			$bits[] = $key . '=' . $param;
		}

		return implode('&', $bits);
	}
}
