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
 * Short description for 'FeaturesHtml'
 * 
 * Long description (if any) ...
 */
class FeaturesHtml
{

	/**
	 * Short description for 'niceidformat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $someid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function niceidformat($someid)
	{
		$pre = '';
		if ($someid < 0) 
		{
			$pre = 'n';
			$someid = abs($someid);
		}
		while (strlen($someid) < 5)
		{
			$someid = 0 . "$someid";
		}
		return $pre . $someid;
	}

	/**
	 * Short description for 'thumb'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $thumb Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function thumb($thumb)
	{
		$image = explode('.', $thumb);
		$n = count($image);
		$image[$n-2] .= '_thumb';
		$end = array_pop($image);
		$image[] = $end;
		$thumb = implode('.', $image);

		return $thumb;
	}

	/**
	 * Short description for 'getImage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $path Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function getImage($path)
	{
		$d = @dir(JPATH_ROOT . $path);

		$images = array();

		if ($d) 
		{
			while (false !== ($entry = $d->read()))
			{
				$imgFile = $entry;
				if (is_file(JPATH_ROOT . $path . DS . $imgFile) && substr($entry, 0, 1) != '.' && strtolower($entry) !== 'index.html') 
				{
					if (preg_match("#bmp|gif|jpg|png#i", $imgFile)) 
					{
						$images[] = $imgFile;
					}
				}
			}

			$d->close();
		}

		$b = 0;
		if ($images) 
		{
			foreach ($images as $ima)
			{
				$bits = explode('.', $ima);
				$type = array_pop($bits);
				$img = implode('.', $bits);

				if ($img == 'thumb') 
				{
					return $ima;
				}
			}
		}
	}

	/**
	 * Short description for 'getToolImage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $path Parameter description (if any) ...
	 * @param      integer $versionid Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function getToolImage($path, $versionid=0)
	{
		// Get contribtool parameters
		$tconfig =& JComponentHelper::getParams('com_tools');
		$allowversions = $tconfig->get('screenshot_edit');

		if ($versionid && $allowversions) 
		{
			// Add version directory
			//$path .= DS.$versionid;
		}

		$d = @dir(JPATH_ROOT.$path);

		$images = array();
		$tns = array();
		$all = array();
		$ordering = array();
		$html = '';

		if ($d) 
		{
			while (false !== ($entry = $d->read()))
			{
				$img_file = $entry;
				if (is_file(JPATH_ROOT.$path . DS . $img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') 
				{
					if (preg_match("#bmp|gif|jpg|png#i", $img_file)) 
					{
						$images[] = $img_file;
					}
				}
			}

			$d->close();
		}

		$b = 0;
		if ($images) 
		{
			foreach ($images as $ima)
			{
				$bits = explode('.', $ima);
				$type = array_pop($bits);
				$img = implode('.', $bits);

				if ($img == 'thumb') 
				{
					return $ima;
				}
			}
		}
	}

	/**
	 * Short description for 'thumbnail'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $pic Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function thumbnail($pic)
	{
		$pic = explode('.', $pic);
		$n = count($pic);
		$pic[$n-2] .= '-tn';
		$end = array_pop($pic);

		$pic[] = 'gif';
		$tn = implode('.', $pic);
		return $tn;
	}

	/**
	 * Short description for 'build_path'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $date Parameter description (if any) ...
	 * @param      unknown $id Parameter description (if any) ...
	 * @param      string $base Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function build_path($date, $id, $base='')
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
		$dir_id = FeaturesHtml::niceidformat($id);

		return $base . DS . $dir_year . DS . $dir_month . DS . $dir_id;
	}

	/**
	 * Short description for 'mkt'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $stime Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function mkt($stime)
	{
		if ($stime && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $stime, $regs)) 
		{
			$stime = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}
		return $stime;
	}

	/**
	 * Short description for 'timeAgoo'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      number $timestamp Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function timeAgoo($timestamp)
	{
		// Store the current time
		$current_time = time();

		// Determine the difference, between the time now and the timestamp
		$difference = $current_time - $timestamp;

		// Set the periods of time
		$periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');

		// Set the number of seconds per period
		$lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

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
			$periods[$val] .= 's';
		}

		// Return text
		$text = sprintf("%d %s ", $number, $periods[$val]);

		// Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
		if (($val >= 1) && (($current_time - $new_time) > 0)) 
		{
			$text .= FeaturesHtml::timeAgoo($new_time);
		}

		return $text;
	}

	/**
	 * Short description for 'timeAgo'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $timestamp Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function timeAgo($timestamp)
	{
		$text = FeaturesHtml::timeAgoo($timestamp);

		$parts = explode(' ',$text);

		$text  = $parts[0] . ' '.  $parts[1];
		$text .= ($parts[2]) ? ' ' . $parts[2] . ' ' . $parts[3] : '';
		return $text;
	}

	/**
	 * Short description for 'getContributorImage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $id Parameter description (if any) ...
	 * @param      unknown $database Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function getContributorImage($id, $database)
	{
		$thumb = '';

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'helper.php');
		$helper = new ResourcesHelper($id, $database);
		$ids = $helper->getContributorIDs();
		if (count($ids) > 0) 
		{
			$uid = $ids[0];
		} 
		else 
		{
			return $thumb;
		}

		// Load some needed libraries
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'profile.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'association.php');

		// Load the member profile
		$row = new MembersProfile($database);
		$row->load($uid);

		// Do they have a picture?
		if (isset($row->picture) && $row->picture != '') 
		{
			$config =& JComponentHelper::getParams('com_members');

			// Yes - so build the path to it
			$thumb  = DS . trim($config->get('webpath'), DS) . DS . FeaturesHtml::niceidformat($row->uidNumber) . DS . $row->picture;

			// No - use default picture
			if (is_file(JPATH_ROOT . $thumb)) 
			{
				// Build a thumbnail filename based off the picture name
				$thumb = FeaturesHtml::thumb($thumb);

				if (!is_file(JPATH_ROOT . $thumb)) 
				{
					// Create a thumbnail image
					include_once(JPATH_ROOT . DS . 'components' . DS . 'com_members' . DS . 'helpers' . DS . 'imghandler.php');
					$ih = new MembersImgHandler();
					$ih->set('image', $row->picture);
					$ih->set('path', JPATH_ROOT . DS . trim($config->get('webpath'), DS) . DS . FeaturesHtml::niceidformat($row->uidNumber) . DS);
					$ih->set('maxWidth', 50);
					$ih->set('maxHeight', 50);
					$ih->set('cropratio', '1:1');
					$ih->set('outputName', $ih->createThumbName());
					if (!$ih->process()) 
					{
						echo '<!-- Error: '. $ih->getError() .' -->';
					}
				}
			}
		}

		return $thumb;
	}
}

