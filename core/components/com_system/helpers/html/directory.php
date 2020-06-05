<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Utility class working with directory
 */
class ComponentsSystemHelpersHtmlDirectory
{
	/**
	 * Method to generate a (un)writable message for directory
	 *
	 * @param   boolean  $writable  is the directory writable?
	 * @return  string   html code
	 */
	public static function writable($writable)
	{
		if ($writable)
		{
			return '<span class="writable">' . Lang::txt('COM_SYSTEM_INFO_WRITABLE') . '</span>';
		}
		else
		{
			return '<span class="unwritable">' . Lang::txt('COM_SYSTEM_INFO_UNWRITABLE') . '</span>';
		}
	}

	/**
	 * Method to generate a message for a directory
	 *
	 * @param   string   $dir      the directory
	 * @param   boolean  $message  the message
	 * @param   boolean  $visible  is the $dir visible?
	 * @return  string   html code
	 */
	public static function message($dir, $message, $visible=true)
	{
		if ($visible)
		{
			$output = $dir;
		}
		else
		{
			$output ='';
		}
		if (empty($message))
		{
			return $output;
		}
		else
		{
			return $output . ' <strong>' . Lang::txt($message) . '</strong>';
		}
	}
}
