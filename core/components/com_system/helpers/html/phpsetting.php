<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Utility class working with phpsetting
 */
class ComponentsSystemHelpersHtmlPhpsetting
{
	/**
	 * Method to generate a boolean message for a value
	 *
	 * @param   boolean  $val  Is the value set?
	 * @return  string   html code
	 */
	public static function boolean($val)
	{
		return ($val ? '<span class="state on"><span>' . Lang::txt('JON') : '<span class="state off"><span>' . Lang::txt('JOFF')) . '</span></span>';
	}

	/**
	 * Method to generate a boolean message for a value
	 *
	 * @param   boolean  $val Is the value set?
	 * @return  string   html code
	 */
	public static function set($val)
	{
		return ($val ? '<span class="state yes"><span>' . Lang::txt('JYES') : '<span class="state no"><span>' . Lang::txt('JNO')) . '</span></span>';
	}

	/**
	 * Method to generate a string message for a value
	 *
	 * @param   string  $val  A php ini value
	 * @return  string  html code
	 */
	public static function string($val)
	{
		return (empty($val) ? Lang::txt('JNONE') : $val);
	}

	/**
	 * Method to generate an integer from a value
	 *
	 * @param   string   $val  A php ini value
	 * @return  integer
	 */
	public static function integer($val)
	{
		return intval($val);
	}
}
