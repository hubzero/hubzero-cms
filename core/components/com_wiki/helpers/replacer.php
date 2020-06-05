<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
