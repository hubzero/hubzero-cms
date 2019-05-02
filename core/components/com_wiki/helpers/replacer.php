<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Base class for "replacers", objects used in preg_replace_callback() and
 * StringUtils::delimiterReplaceCallback()
 */
class Replacer
{
	/**
	 * Short description for 'cb'
	 *
	 * Long description (if any) ...
	 *
	 * @return     mixed Return description (if any) ...
	 */
	function cb()
	{
		return array(&$this, 'replace');
	}
}
