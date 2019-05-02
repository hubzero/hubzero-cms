<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\System\Helpers;

/**
 * HTML helper for system
 */
class Html
{
	/**
	 * Sortable table header in "scripts for this host" view
	 *
	 * @param  string  $key    Sort key
	 * @param  string  $name   Link name
	 * @param  string  $extra  Extra data to append to URL
	 * @param  string
	 */
	public static function sortheader($MYREQUEST, $MY_SELF_WO_SORT, $key, $name, $extra='')
	{
		if ($MYREQUEST['SORT1'] == $key)
		{
			$MYREQUEST['SORT2'] = $MYREQUEST['SORT2']=='A' ? 'D' : 'A';
		}

		return "<a class=\"sortable\" href=\"$MY_SELF_WO_SORT$extra&amp;SORT1=$key&amp;SORT2=" . $MYREQUEST['SORT2'] . "\">$name</a>";
	}

	/**
	 * Pretty printer for byte values
	 *
	 * @param   integer  $s  Byte value
	 * @return  string
	 */
	public static function bsize($s)
	{
		foreach (array('', 'K', 'M', 'G') as $i => $k)
		{
			if ($s < 1024)
			{
				break;
			}
			$s/=1024;
		}
		return sprintf("%5.1f %sBytes", $s, $k);
	}
}
