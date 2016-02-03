<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Helpers;

use Filesystem;
use Config;
use User;
use Date;
use Lang;

/**
 * Tools helper class for misc. HTML
 */
class Html
{
	/**
	 * Short description for 'error'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $msg Parameter description (if any) ...
	 * @param      string $tag Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function error($msg, $tag='p')
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'."\n";
	}

	/**
	 * Turn datetime 0000-00-00 00:00:00 to timestamp
	 *
	 * @param      string $stime Datetime to convert
	 * @return     number
	 */
	public static function mkt($stime)
	{
		if ($stime && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $stime, $regs))
		{
			$stime = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}
		return $stime;
	}

	/**
	 * Calculate how long ago a date was
	 *
	 * @param      number $timestamp Date to convert
	 * @return     string
	 */
	public static function timeAgoo($timestamp)
	{
		// Store the current time
		$current_time = strtotime(Date::of('now'));

		// Determine the difference, between the time now and the timestamp
		$difference = $current_time - $timestamp;

		// Set the periods of time
		$periods = array(
			Lang::txt('COM_TOOLS_SECOND'),
			Lang::txt('COM_TOOLS_MINUTE'),
			Lang::txt('COM_TOOLS_HOUR'),
			Lang::txt('COM_TOOLS_DAY'),
			Lang::txt('COM_TOOLS_WEEK'),
			Lang::txt('COM_TOOLS_MONTH'),
			Lang::txt('COM_TOOLS_YEAR'),
			Lang::txt('COM_TOOLS_DECADE')
		);

		// Set the number of seconds per period
		$lengths = array(
			1, // second
			60, // minute
			3600, // hour
			86400, // day
			604800, //week
			2630880, // month
			31570560, // year
			315705600  // decade
		);

		// Determine which period we should use, based on the number of seconds lapsed.
		// If the difference divided by the seconds is more than 1, we use that. Eg 1 year / 1 decade = 0.1, so we move on
		// Go from decades backwards to seconds
		for ($val = sizeof($lengths) - 1; ($val >= 0) && (($number = $difference / $lengths[$val]) <= 1); $val--);

		// Ensure the script has found a match
		if ($val < 0)
		{
			$val = 0;
		}

		// Determine the minor value, to recurse through
		$new_time = $current_time - ($difference % $lengths[$val]);

		// Set the current value to be floored
		$number = floor($number);

		// If required create a plural
		if ($number != 1)
		{
			$periods = array(
				Lang::txt('COM_TOOLS_SECONDS'),
				Lang::txt('COM_TOOLS_MINUTES'),
				Lang::txt('COM_TOOLS_HOURS'),
				Lang::txt('COM_TOOLS_DAYS'),
				Lang::txt('COM_TOOLS_WEEKS'),
				Lang::txt('COM_TOOLS_MONTHS'),
				Lang::txt('COM_TOOLS_YEARS'),
				Lang::txt('COM_TOOLS_DECADES')
			);
		}

		// Return text
		$text = sprintf("%d %s ", $number, $periods[$val]);

		// Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
		if (($val >= 1) && (($current_time - $new_time) > 0))
		{
			$text .= self::timeAgoo($new_time);
		}

		return $text;
	}

	/**
	 * Calculate how long ago a date was and put it in human readable format
	 *
	 * @param      number $timestamp Date to convert
	 * @return     string
	 */
	public static function timeAgo($timestamp)
	{
		if (strstr($timestamp, '-'))
		{
			$timestamp = self::mkt($timestamp);
		}
		$text = self::timeAgoo($timestamp);

		$parts = explode(' ', $text);

		$text  = $parts[0] . ' ' . $parts[1];

		return $text;
	}

	/**
	 * Short description for 'formSelect'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $name Parameter description (if any) ...
	 * @param      string $idname Parameter description (if any) ...
	 * @param      array $array Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @param      string $class Parameter description (if any) ...
	 * @param      string $jscall Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function formSelect($name, $idname, $array, $value, $class='', $jscall='')
	{
		$out  = '<select name="'.$name.'" id="'.$idname.'"';
		$out .= ($class)  ? ' class="'.$class.'"'           : ''."";
		$out .= ($jscall) ? ' onChange="'.$jscall.'">'."\n" : '>'."\n";
		foreach ($array as $avalue => $alabel)
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="'.$avalue.'"'.$selected.'>'.$alabel.'</option>'."\n";
		}
		$out .= '</select>'."\n";
		return $out;
	}

	/**
	 * Short description for 'primaryButton'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $class Parameter description (if any) ...
	 * @param      string $href Parameter description (if any) ...
	 * @param      string $msg Parameter description (if any) ...
	 * @param      string $xtra Parameter description (if any) ...
	 * @param      string $title Parameter description (if any) ...
	 * @param      string $action Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function primaryButton($class, $href, $msg, $xtra='', $title='', $action='')
	{
		//$title = str_replace('"', '&quot;', $title);
		$html  = '<span id="test-document"><a class="'.$class.'" style="padding:0.1em 1em 0 1em;"  href="'.$href.'" title="'.htmlentities($title).'" '.$action.'>'.$msg.'</a>';
		$html .= $xtra;
		$html .= '</span>'."\n";
		return $html;
	}

	/**
	 * Short description for 'getNumofTools'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $status Parameter description (if any) ...
	 * @param      string $toolnum Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function getNumofTools($status, $toolnum='')
	{
		// get hub parameters
		$sitename = Config::get('sitename');

		$toolnum = ($status['state']!=9) ? Lang::txt('COM_TOOLS_THIS_TOOL').'  ': '';
		if (!$status['published'] && self::toolActive($status['state']))
		{
			$toolnum .= Lang::txt('COM_TOOLS_IS_ONE_OF').' '.$status['ntoolsdev'].' '.strtolower(Lang::txt('COM_TOOLS_TOOLS')). ' '.strtolower(Lang::txt('COM_TOOLS_UNDER_DEVELOPMENT')).' '.Lang::txt('COM_TOOLS_ON').' '.$sitename;
		}
		else if ($status['published'] && self::toolActive($status['state']))
		{
			$toolnum .= Lang::txt('COM_TOOLS_IS_ONE_OF').' '.$status['ntools_published'].' '.strtolower(Lang::txt('COM_TOOLS_TOOLS')). ' '.strtolower(Lang::txt('COM_TOOLS_PUBLISHED')).' '.Lang::txt('COM_TOOLS_ON').' '.$sitename;
		}
		else if ($status['state']==8)
		{
			$toolnum .= Lang::txt('COM_TOOLS_WAS_ONCE_PUBLISHED').' '.Lang::txt('COM_TOOLS_ON').' '.$sitename.' '.Lang::txt('COM_TOOLS_NOW_RETIRED');
		}

		return $toolnum;
	}

	/**
	 * Short description for 'toolActive'
	 *
	 * Long description (if any) ...
	 *
	 * @param      integer $stateNum Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public static function toolActive($stateNum)
	{
		if ($stateNum == 8 || $stateNum == 9)
		{
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'toolWIP'
	 *
	 * Long description (if any) ...
	 *
	 * @param      integer $stateNum Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public static function toolWIP($stateNum)
	{
		if ($stateNum == 2 || $stateNum == 9 || $stateNum == 1)
		{
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'toolEstablished'
	 *
	 * Long description (if any) ...
	 *
	 * @param      integer $stateNum Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public static function toolEstablished($stateNum)
	{
		if ($stateNum == 1 || $stateNum == 9)
		{
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'getStatusClass'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $statusNum Parameter description (if any) ...
	 * @param      string &$statusClass Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function getStatusClass($statusNum, &$statusClass)
	{
		switch ($statusNum)
		{
			case 7:  $statusClass = '_closed';    break;
			case 8:  $statusClass = '_abandoned'; break;
			case 9:  $statusClass = '_abandoned'; break;
			default: $statusClass = '';
		}

		return $statusClass;
	}

	/**
	 * Short description for 'getStatusName'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $statusNum Parameter description (if any) ...
	 * @param      unknown &$statusName Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public static function getStatusName($statusNum, &$statusName)
	{
		switch ($statusNum)
		{
			case 1: $statusName = Lang::txt('COM_TOOLS_REGISTERED'); break;
			case 2: $statusName = Lang::txt('COM_TOOLS_CREATED');    break;
			case 3: $statusName = Lang::txt('COM_TOOLS_UPLOADED');   break;
			case 4: $statusName = Lang::txt('COM_TOOLS_INSTALLED');  break;
			case 5: $statusName = Lang::txt('COM_TOOLS_UPDATED');    break;
			case 6: $statusName = Lang::txt('COM_TOOLS_APPROVED');   break;
			case 7: $statusName = Lang::txt('COM_TOOLS_PUBLISHED');  break;
			case 8: $statusName = Lang::txt('COM_TOOLS_RETIRED');    break;
			case 9: $statusName = Lang::txt('COM_TOOLS_ABANDONED');  break;
		}

		return $statusName;
	}

	/**
	 * Short description for 'getStatusNum'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $statusName Parameter description (if any) ...
	 * @param      integer $statusNum Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public static function getStatusNum($statusName, $statusNum=1)
	{
		switch (strtolower($statusName))
		{
			case 'registered': $statusNum = 1; break;
			case 'created':    $statusNum = 2; break;
			case 'uploaded':   $statusNum = 3; break;
			case 'installed':  $statusNum = 4; break;
			case 'updated':    $statusNum = 5; break;
			case 'approved':   $statusNum = 6; break;
			case 'published':  $statusNum = 7; break;
			case 'retired':    $statusNum = 8; break;
			case 'abandoned': $statusNum = 9; break;
		}
		return $statusNum;
	}

	/**
	 * Short description for 'getPriority'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $int Parameter description (if any) ...
	 * @param      string $priority Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function getPriority($int, $priority='')
	{
		switch ($int)
		{
			case 1:  $priority = 'critical'; break;
			case 2:  $priority = 'high';     break;
			case 3:  $priority = 'normal';   break;
			case 4:  $priority = 'low';      break;
			case 5:  $priority = 'lowest';   break;
			default: $priority = 'normal';   break;
		}
		return $priority;
	}

	/**
	 * Short description for 'getDevTeam'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $members Parameter description (if any) ...
	 * @param      integer $obj Parameter description (if any) ...
	 * @param      string $team Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function getDevTeam($members, $obj = 1, $team='')
	{
		if ($members && count($members) > 0)
		{
			foreach ($members as $member)
			{
				$uid = ($obj) ? $member->uidNumber : $member;
				$user = User::getInstance($uid);
				if (is_object($user))
				{
					$login = $user->get('username');
				}
				else
				{
					$login = Lang::txt('COM_TOOLS_UNKNOWN');
				}
				$team .= ($member != end($members)) ? $login . ', ' : $login;
			}
		}
		else
		{
			$team .= Lang::txt('COM_TOOLS_NA');
		}
		return $team;
	}

	/**
	 * Turn a list of groups into a comma-separated string
	 *
	 * @param   array    $groups  List of groups
	 * @param   integer  $obj     Incoming data are objects?
	 * @param   string   $list    String to append to
	 * @return  string
	 */
	public static function getGroups($groups, $obj = 1, $list='')
	{
		if (is_array($groups) || $groups instanceof Traversable)
		{
			foreach ($groups as $group)
			{
				$cn = ($obj) ? $group->cn : $group;
				$list .= ($group != end($groups)) ? $cn . ', ' : $cn;
			}
		}
		return $list;
	}

	/**
	 * Short description for 'getToolAccess'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $access Parameter description (if any) ...
	 * @param      array $groups Parameter description (if any) ...
	 * @param      string $toolaccess Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function getToolAccess($access, $groups, $toolaccess='')
	{
		switch ($access)
		{
			case '@GROUP':
				if (count($groups)>0)
				{
					$toolaccess = Lang::txt('COM_TOOLS_RESTRICTED') . ' ' . Lang::txt('COM_TOOLS_TO') . ' ' . Lang::txt('COM_TOOLS_GROUP_OR_GROUPS') . ' ';
					foreach ($groups as $group)
					{
						$toolaccess .= ($group != end($groups)) ? $group->cn . ', ' : $group->cn;
					}
				}
				else
				{
					$toolaccess = Lang::txt('COM_TOOLS_RESTRICTED') . ' ' . Lang::txt('COM_TOOLS_TO') . ' ' . Lang::txt('COM_TOOLS_UNSPECIFIED') . ' ' . Lang::txt('COM_TOOLS_GROUP_OR_GROUPS');
				}
			break;

			case '@US': $toolaccess = Lang::txt('COM_TOOLS_TOOLACCESS_US'); break;
			case '@PU': $toolaccess = Lang::txt('COM_TOOLS_TOOLACCESS_PU'); break;
			case '@D1': $toolaccess = Lang::txt('COM_TOOLS_TOOLACCESS_D1'); break;
			default:    $toolaccess = Lang::txt('COM_TOOLS_ACCESS_OPEN');   break;
		}

		return $toolaccess;
	}

	/**
	 * Short description for 'getCodeAccess'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $access Parameter description (if any) ...
	 * @param      string $codeaccess Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function getCodeAccess($access, $codeaccess = '')
	{
		switch ($access)
		{
			case '@OPEN': $codeaccess = Lang::txt('COM_TOOLS_OPEN_SOURCE');   break;
			case '@DEV':  $codeaccess = Lang::txt('COM_TOOLS_CLOSED_SOURCE'); break;
			default:      $codeaccess = Lang::txt('COM_TOOLS_UNSPECIFIED');   break;
		}

		return $codeaccess;
	}

	/**
	 * Short description for 'getWikiAccess'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $access Parameter description (if any) ...
	 * @param      string $wikiaccess Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function getWikiAccess($access, $wikiaccess = '')
	{
		switch ($access)
		{
			case '@OPEN': $wikiaccess = Lang::txt('COM_TOOLS_ACCESS_OPEN');       break;
			case '@DEV':  $wikiaccess = Lang::txt('COM_TOOLS_ACCESS_RESTRICTED'); break;
			default:      $wikiaccess = Lang::txt('COM_TOOLS_UNSPECIFIED');       break;
		}

		return $wikiaccess;
	}

	/**
	 * Short description for 'writeApproval'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $active_stage Parameter description (if any) ...
	 * @return     void
	 */
	public static function writeApproval($active_stage)
	{
		//$stages = array(Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_CONFIRM_VERSION'),Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_CONFIRM_LICENSE'), Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_APPEND_NOTES'), Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_CONFIRM_APPROVE'));
		$stages = array(Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_CONFIRM_VERSION'),Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_CONFIRM_LICENSE'), Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_CONFIRM_APPROVE'));
		$key = array_keys($stages, $active_stage);

		$html = "\t\t".'<div class="clear"></div>'."\n";
		$html .= "\t\t".'<ol id="steps">'."\n";
		$html .= "\t\t".' <li>'.Lang::txt('COM_TOOLS_CONTRIBTOOL_APPROVE_PUBLICATION').':</li>'."\n";

		for ($i=0, $n=count($stages); $i < $n; $i++)
		{
			$html .= "\t\t".' <li';

			if (strtolower($active_stage) == strtolower($stages[$i])) {
				$html .= ' class="active"';

			}
			else if (count($key) == 0 or $i > $key[0]) {
				$html .= ' class="future"';
			}

			$html .= '>';
			$html .= $stages[$i];
			$html .= '</li>'."\n";
		}
		$html .= "\t\t".'</ol>'."\n";
		$html .= "\t\t".'<div class="clear"></div>'."\n";

		echo $html;
	}

	/**
	 * Short description for 'selectAccess'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $as Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function selectAccess($as, $value)
	{
		$html  = '<select name="access">';
		for ($i=0, $n=count($as); $i < $n; $i++)
		{
			if ($as[$i] != 'Registered' && $as[$i] != 'Special')
			{
				$html .= '<option value="' . $i . '"';
				if ($value == $i)
				{
					$html .= ' selected="selected"';
				}
				$html .= '>' . Lang::txt('COM_TOOLS_ACCESS_' . strtoupper($as[$i])) . '</option>';
			}
		}
		$html .= '</select>';
		return $html;
	}

	/**
	 * Short description for 'selectGroup'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $groups Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function selectGroup($groups, $value)
	{
		$html  = '<select name="group_owner">'."\n";
		$html .= '<option value="">'.Lang::txt('COM_TOOLS_SELECT_GROUP').'</option>'."\n";
		foreach ($groups as $group)
		{
			$html .= '<option value="'.$group->cn.'"';
			if ($value == $group->cn)
			{
				$html .= ' selected="selected"';
			}
			$html .= '>'.$group->description .'</option>'."\n";
		}
		$html .= '</select>'."\n";
		return $html;
	}

	/**
	 * Short description for 'writeNotesArea'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $notes Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @param      integer $edititem Parameter description (if any) ...
	 * @param      integer $addnew Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function writeNotesArea($notes, $option, $type='', $edititem = 0, $addnew = 1)
	{

		$out ='';
		$i = 0;
		if (count($notes) > 0)
		{
			$out .= '<ul class="features">'."\n";
			for ($i=0, $n=count($notes); $i < $n; $i++)
			{
				$note = $notes[$i];
				$out .= ' <li>'."\n";
				$out .= '  <span><span>'.Lang::txt('COM_TOOLS_EDIT').'</span></span>'."\n";
				$out .= $note->note;
				$out .= ' </li>'."\n";
			}
			$out .= '</ul>'."\n";
		}

		if ($addnew)
		{
			$out .= self::addNoteArea($i, $option, $type);
		}

		return $out;
	}

	/**
	 * Short description for 'addNoteArea'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $i Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function addNoteArea($i, $option, $type = 'item')
	{
		$out  = '';
		$out .= '<label>'."\n";
		$out .= ' <span class="selectgroup editnote">'."\n";
		$out .= '   <textarea name="'.$type.'[]" id="'.$type.$i.'"  rows="6" cols="35"></textarea>'."\n";
		$out .= '   <span class="extras"><span></span></span>'."\n";
		$out .= ' </span>'."\n";
		$out .= '</label>'."\n";

		return $out;
	}

	/**
	 * Short description for 'parseTag'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $text Parameter description (if any) ...
	 * @param      string $tag Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	/*public static function parseTag($text, $tag)
	{
		preg_match("#<nb:".$tag.">(.*?)</nb:".$tag.">#s", $text, $matches);
		if (count($matches) > 0) {
			$match = $matches[0];
			$match = str_replace('<nb:'.$tag.'>','',$match);
			$match = str_replace('</nb:'.$tag.'>','',$match);
		} else {
			$match = '';
		}
		return $match;
	}*/

	/**
	 * Short description for 'txt_unpee'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $pee Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public static function txt_unpee($pee)
	{
		$pee = str_replace("\t", '', $pee);
		$pee = str_replace('</p><p>', '', $pee);
		$pee = str_replace('<p>', '', $pee);
		$pee = str_replace('</p>', "\n", $pee);
		$pee = str_replace('<br />', '', $pee);
		$pee = trim($pee);
		return $pee;
	}

	/**
	 * Short description for 'niceidformat'
	 *
	 * Long description (if any) ...
	 *
	 * @param      integer $someid Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	/*public static function niceidformat($someid)
	{
		while (strlen($someid) < 5)
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}*/

	/**
	 * Get the file attributes (type, size) of a file
	 *
	 * @param      string $path      Path to get file info fore
	 * @param      string $base_path Base path to prepend to $path
	 * @return     string
	 */
	public static function getFileAttribs($path, $base_path='')
	{
		// Return nothing if no path provided
		if (!$path)
		{
			return '';
		}

		if ($base_path)
		{
			// Strip any trailing slash
			$base_path = DS . trim($base_path, DS);
		}

		// Ensure a starting slash
		$path = DS . trim($path, DS);

		// Does the beginning of the path match the base path?
		if (substr($path, 0, strlen($base_path)) != $base_path)
		{
			$path = $base_path . $path;
		}
		$path = PATH_APP . $path;

		$type = strtoupper(Filesystem::extension($path));

		$fs = '';

		// Get the file size if the file exist
		if (file_exists($path))
		{
			$fs = filesize($path);
		}

		$html  = $type;
		if ($fs)
		{
			switch ($type)
			{
				case 'HTM':
				case 'HTML':
				case 'PHP':
				case 'ASF':
				case 'SWF': $fs = ''; break;
				default:
					$fs = \Hubzero\Utility\Number::formatBytes($fs);
				break;
			}

			$html .= ($fs) ? ', ' . $fs : '';
		}

		return $html;
	}
}
