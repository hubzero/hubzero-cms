<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Utility class working with system
 */
class ComponentsSystemHelpersHtmlSystem
{
	/**
	 * Method to generate a string message for a value
	 *
	 * @param   string  $val  A php ini value
	 * @return  string  html code
	 */
	public static function server($val)
	{
		return (empty($val) ? Lang::txt('COM_SYSTEM_INFO_NA') : $val);
	}
}
