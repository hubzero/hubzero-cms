<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class FeaturesHtml 
{
	public function niceidformat($someid) 
	{
		$pre = '';
		if ($someid < 0) {
			$pre = 'n';
			$someid = abs($someid);
		}
		while (strlen($someid) < 5) 
		{
			$someid = 0 . "$someid";
		}
		return $pre.$someid;
	}
	
	//-----------

	public function thumb( $thumb ) 
	{
		$image = explode('.',$thumb);
		$n = count($image);
		$image[$n-2] .= '_thumb';
		$end = array_pop($image);
		$image[] = $end;
		$thumb = implode('.',$image);
		
		return $thumb;
	}
	
	//-----------
	
	public function getImage( $path ) 
	{
		$d = @dir(JPATH_ROOT.$path);

		$images = array();
		
		if ($d) {
			while (false !== ($entry = $d->read())) 
			{			
				$img_file = $entry; 
				if (is_file(JPATH_ROOT.$path.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (eregi( "bmp|gif|jpg|png", $img_file )) {
						$images[] = $img_file;
					}
				}
			}

			$d->close();
		}

		$b = 0;
		if ($images) {
			foreach ($images as $ima) 
			{
				$bits = explode('.',$ima);
				$type = array_pop($bits);
				$img = implode('.',$bits);
				
				if ($img == 'thumb') {
					return $ima;
				}
			}
		}
	}
	
	//-----------
	
	public function getToolImage( $path, $versionid=0 ) 
	{
		// Get contribtool parameters
		$tconfig =& JComponentHelper::getParams( 'com_contribtool' );
		$allowversions = $tconfig->get('screenshot_edit');
		
		if ($versionid && $allowversions) { 
			// Add version directory
			//$path .= DS.$versionid;
		}

		$d = @dir(JPATH_ROOT.$path);

		$images = array();
		$tns = array();
		$all = array();
		$ordering = array();
		$html = '';

		if ($d) {
			while (false !== ($entry = $d->read())) 
			{			
				$img_file = $entry; 
				if (is_file(JPATH_ROOT.$path.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (eregi( "bmp|gif|jpg|png", $img_file )) {
						$images[] = $img_file;
					}
				}
			}

			$d->close();
		}

		$b = 0;
		if ($images) {
			foreach ($images as $ima) 
			{
				$bits = explode('.',$ima);
				$type = array_pop($bits);
				$img = implode('.',$bits);
				
				if ($img == 'thumb') {
					return $ima;
				}
			}
		}
	}
	
	//-----------

	public function thumbnail($pic)
	{
		$pic = explode('.',$pic);
		$n = count($pic);
		$pic[$n-2] .= '-tn';
		$end = array_pop($pic);
		$pic[] = 'gif';
		$tn = implode('.',$pic);
		return $tn;
	}
	
	//-----------

	public function build_path( $date, $id, $base='' )
	{
		if ( $date && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs ) ) {
			$date = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		if ($date) {
			$dir_year  = date('Y', $date);
			$dir_month = date('m', $date);
		} else {
			$dir_year  = date('Y');
			$dir_month = date('m');
		}
		$dir_id = FeaturesHtml::niceidformat( $id );

		return $base.DS.$dir_year.DS.$dir_month.DS.$dir_id;
	}
	
	//-----------

	public function mkt($stime)
	{
		if ($stime && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $stime, $regs )) {
			$stime = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		return $stime;
	}
	
	//-----------

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
		if ($val < 0) $val = 0;

		// Determine the minor value, to recurse through
		$new_time = $current_time - ($difference % $lengths[$val]);

		// Set the current value to be floored
		$number = floor($number);

		// If required create a plural
		if ($number != 1) $periods[$val].= 's';

		// Return text
		$text = sprintf("%d %s ", $number, $periods[$val]);

		// Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
		if (($val >= 1) && (($current_time - $new_time) > 0)) {
			$text .= FeaturesHtml::timeAgoo($new_time);
		}

		return $text;
	}

	//-----------

	public function timeAgo($timestamp) 
	{
		$text = FeaturesHtml::timeAgoo($timestamp);

		$parts = explode(' ',$text);

		$text  = $parts[0].' '.$parts[1];
		$text .= ($parts[2]) ? ' '.$parts[2].' '.$parts[3] : '';
		return $text;
	}
	
	//-----------
	
	public function getContributorImage( $id, $database )
	{
		$thumb = '';
		
		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'helper.php');
		$helper = new ResourcesHelper( $id, $database );
		$ids = $helper->getContributorIDs();
		if (count($ids) > 0) {
			$uid = $ids[0];
		} else {
			return $thumb;
		}

		// Load some needed libraries
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'tables'.DS.'profile.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'tables'.DS.'association.php' );
		
		// Load the member profile
		$row = new MembersProfile( $database );
		$row->load( $uid );
		
		// Do they have a picture?
		if (isset($row->picture) && $row->picture != '') {
			$config =& JComponentHelper::getParams( 'com_members' );
			
			// Yes - so build the path to it
			$thumb  = $config->get('webpath');
			if (substr($thumb, 0, 1) != DS) {
				$thumb = DS.$thumb;
			}
			if (substr($thumb, -1, 1) == DS) {
				$thumb = substr($thumb, 0, (strlen($thumb) - 1));
			}
			$thumb .= DS.FeaturesHtml::niceidformat($row->uidNumber).DS.$row->picture;
			
			// No - use default picture
			if (is_file(JPATH_ROOT.$thumb)) {
				// Build a thumbnail filename based off the picture name
				$thumb = FeaturesHtml::thumb( $thumb );
				
				if (!is_file(JPATH_ROOT.$thumb)) {
					// Create a thumbnail image
					include_once( JPATH_ROOT.DS.'components'.DS.'com_members'.DS.'helpers'.DS.'imghandler.php' );
					$ih = new MembersImgHandler();
					$ih->set('image',$row->picture);
					$ih->set('path',JPATH_ROOT.$config->get('webpath').DS.FeaturesHtml::niceidformat($row->uidNumber).DS);
					$ih->set('maxWidth', 50);
					$ih->set('maxHeight', 50);
					$ih->set('cropratio', '1:1');
					$ih->set('outputName', $ih->createThumbName());
					if (!$ih->process()) {
						echo '<!-- Error: '. $ih->getError() .' -->';
					}
				}
			}
		}
		
		return $thumb;
	}
}
