<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Resources helper class for HTML
 */
class ResourcesHtml
{
	/**
	 * Display a message and go back tot he previous page
	 *
	 * @param      string $msg Message
	 * @return     string Javascript
	 */
	public static function alert($msg)
	{
		return "<script type=\"text/javascript\"> alert('" . $msg . "'); window.history.go(-1); </script>\n";
	}

	/**
	 * Display a key for various resource statuses
	 *
	 * @return     void
	 */
	public static function statusKey()
	{
		?>
			<p><?php echo JText::_('Published status: (click icon above to toggle state)'); ?></p>
			<ul class="key">
				<li class="draftinternal"><span>draft (internal)</span> = <?php echo JText::_('Draft (internal production)'); ?></li>
				<li class="draftexternal"><span>draft (external)</span> = <?php echo JText::_('Draft (user created)'); ?></li>
				<li class="submitted"><span>new</span> = <?php echo JText::_('New, awaiting approval'); ?></li>
				<li class="pending"><span>pending</span> = <?php echo JText::_('Published, but is Coming'); ?></li>
				<li class="published"><span>current</span> = <?php echo JText::_('Published and is Current'); ?></li>
				<li class="expired"><span>finished</span> = <?php echo JText::_('Published, but has Finished'); ?></li>
				<li class="unpublished"><span>unpublished</span> = <?php echo JText::_('Unpublished'); ?></li>
				<li class="deleted"><span>deleted</span> = <?php echo JText::_('Delete/Removed'); ?></li>
			</ul>
		<?php
	}

	/**
	 * Format an ID by prepending 0
	 *
	 * @param      integer $someid ID to format
	 * @return     integer
	 */
	public static function niceidformat($someid)
	{
		while (strlen($someid) < 5)
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}

	/**
	 * Build the path to resource files from the creation date
	 *
	 * @param      string  $date Resource creation date
	 * @param      integer $id   Resource ID
	 * @param      string  $base Base path to prepend
	 * @return     string
	 */
	public static function build_path($date, $id, $base='')
	{
		if ($date && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $date, $regs))
		{
			$date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}
		if ($date)
		{
			$dir_year  = date('Y', $date);
			$dir_month = date('m', $date);
		}
		else
		{
			$dir_year  = date('Y');
			$dir_month = date('m');
		}
		$dir_id = ResourcesHtml::niceidformat($id);

		$path = $base . DS . $dir_year . DS . $dir_month . DS . $dir_id;

		//return $base . DS . $dir_id;
		return $path;
	}

	/**
	 * Short description for 'writeRating'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $rating Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function writeRating($rating)
	{
		switch ($rating)
		{
			case 0.5: $class = ' half';      break;
			case 1:   $class = ' one';       break;
			case 1.5: $class = ' onehalf';   break;
			case 2:   $class = ' two';       break;
			case 2.5: $class = ' twohalf';   break;
			case 3:   $class = ' three';     break;
			case 3.5: $class = ' threehalf'; break;
			case 4:   $class = ' four';      break;
			case 4.5: $class = ' fourhalf';  break;
			case 5:   $class = ' five';      break;
			case 0:
			default:  $class = ' none';      break;
		}

		return '<p class="avgrating'.$class.'"><span>Rating: '.$rating.' out of 5 stars</span></p>';
	}

	/**
	 * Generate a select access list
	 *
	 * @param      array  $as    Access levels
	 * @param      string $value Value to select
	 * @return     string HTML
	 */
	public static function selectAccess($as, $value, $name = 'access')
	{
		$as = explode(',',$as);
		$html  = '<select name="' . $name . '" id="field-' . str_replace(array('[',']'), '', $name) . '">' . "\n";
		for ($i=0, $n=count($as); $i < $n; $i++)
		{
			$html .= "\t" . '<option value="' . $i . '"';
			if ($value == $i)
			{
				$html .= ' selected="selected"';
			}
			$html .= '>' . trim($as[$i]) . '</option>' . "\n";
		}
		$html .= '</select>' . "\n";
		return $html;
	}

	/**
	 * Generate a select list for groups
	 *
	 * @param      array  $groups Groups to populate list
	 * @param      string $value  Value to select
	 * @return     string HTML
	 */
	public static function selectGroup($groups, $value, $name = 'group_owner', $class = '')
	{
		$html  = '<select class="'.$class.'" name="'.$name.'" id="field-' . str_replace(array('[',']'), '', $name) . '"';
		if (!$groups)
		{
			$html .= ' disabled="disabled"';
		}
		$html .= '>' . "\n";
		$html .= ' <option value="">' . JText::_('Select group ...') . '</option>' . "\n";
		if ($groups)
		{
			foreach ($groups as $group)
			{
				$html .= ' <option value="' . $group->cn . '"';
				if ($value == $group->cn)
				{
					$html .= ' selected="selected"';
				}
				$html .= '>' . stripslashes($group->description) . '</option>' . "\n";
			}
		}
		$html .= '</select>' . "\n";
		return $html;
	}

	/**
	 * Generate a section select list
	 *
	 * @param      string  $name  Name of the field
	 * @param      array   $array Values to populate list
	 * @param      integer $value Value to select
	 * @param      string  $class Class name of field
	 * @param      string  $id    ID of field
	 * @return     string HTML
	 */
	public static function selectSection($name, $array, $value, $class='', $id)
	{
		$html  = '<select name="' . $name . '" id="' . $name . '" onchange="return listItemTask(\'cb' . $id . '\',\'regroup\')"';
		$html .= ($class) ? ' class="' . $class . '">' . "\n" : '>' . "\n";
		$html .= ' <option value="0"';
		$html .= ($id == $value || $value == 0) ? ' selected="selected"' : '';
		$html .= '>' . JText::_('[ none ]') . '</option>' . "\n";
		foreach ($array as $anode)
		{
			$selected = ($anode->id == $value || $anode->type == $value)
					  ? ' selected="selected"'
					  : '';
			$html .= ' <option value="' . $anode->id . '"' . $selected . '>' . $anode->type . '</option>' . "\n";
		}
		$html .= '</select>' . "\n";
		return $html;
	}

	/**
	 * Generate a type select list
	 *
	 * @param      array  $arr      Values to populate list
	 * @param      string $name     Name of the field
	 * @param      mixed  $value    Value to select
	 * @param      string $shownone Show a 'none' option?
	 * @param      string $class    Class name of field
	 * @param      string $js       Scripts to add to field
	 * @param      string $skip     ITems to skip
	 * @return     string HTML
	 */
	public static function selectType($arr, $name, $value='', $shownone='', $class='', $js='', $skip='')
	{
		$html  = '<select name="' . $name . '" id="' . $name . '"' . $js;
		$html .= ($class) ? ' class="' . $class . '">' . "\n" : '>' . "\n";
		if ($shownone != '')
		{
			$html .= "\t" . '<option value=""';
			$html .= ($value == 0 || $value == '') ? ' selected="selected"' : '';
			$html .= '>' . $shownone . '</option>' . "\n";
		}
		if ($skip)
		{
			$skips = explode(',', $skip);
		}
		else
		{
			$skips = array();
		}
		foreach ($arr as $anode)
		{
			if (!in_array($anode->id, $skips))
			{
				$selected = ($value && ($anode->id == $value || $anode->type == $value))
					  ? ' selected="selected"'
					  : '';
				$html .= "\t" . '<option value="' . $anode->id . '"' . $selected . '>' . stripslashes($anode->type) . '</option>' . "\n";
			}
		}
		$html .= '</select>' . "\n";
		return $html;
	}

	/**
	 * Convert a date to a path
	 *
	 * @param      string $date Date to convert (0000-00-00 00:00:00)
	 * @return     string
	 */
	public static function dateToPath($date)
	{
		if ($date && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $date, $regs))
		{
			$date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}
		$dir_year  = date('Y', $date);
		$dir_month = date('m', $date);
		return $dir_year . DS . $dir_month;
	}
}

