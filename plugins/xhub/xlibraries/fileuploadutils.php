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

//----------------------------------------------------------
// Misc Resource utility functions
//----------------------------------------------------------

class FileUploadUtils
{
	public function make_path( $base, $path='', $mode=0777 ) 
	{
		if (file_exists( $base . $path )) {
		    return true;
		}
		$path = str_replace( '\\', DS, $path );
		$path = str_replace( '//', DS, $path );
		$parts = explode( DS, $base );
	
		$n = count( $parts );
		if ($n < 1) {
		    return mkdir( $base, $mode );
		} else {
			for ($i = 0; $i < $n; $i++) 
			{
			    $path .= $parts[$i] . DS;
				if (!file_exists( $path )) {
			        if (!mkdir( $path, $mode )) {
			            return false;
					}
				}
			}
			return true;
		}
	}

	//-----------

	public function delete_dir( $dir ) 
	{
		$current_dir = opendir( $dir );
		while ($entryname = readdir( $current_dir )) 
		{
			if ($entryname != '.' and $entryname != '..') {
				if (is_dir( $dir . $entryname )) {
					FileUploadUtils::delete_dir( $dir . $entryname );
				} else {
					unlink( $dir . $entryname );
				}
			}
		}
		closedir( $current_dir );
		return rmdir( $dir );
	}

	//-----------

	/*function empty_dir($dir, $DeleteMe)
	{
		if(!$dh = @opendir($dir)) return;
		while (false !== ($obj = readdir($dh))) 
		{
			if($obj=='.' || $obj=='..') continue;
			if(!@unlink($dir.'/'.$obj)) FileUploadUtils::empty_dir($dir.'/'.$obj, true);
		}
		if($DeleteMe) {
			closedir($dh);
			@rmdir($dir);
		}
	}*/

	//-----------

	public function delete_file( $file ) 
	{
		if (is_file( $file )) {
			unlink( $file );
		} elseif (is_dir( $file )) {
			return FileUploadUtils::delete_dir( $file );
		}
		return;
	}

	//-----------

	public function is_empty( $dir )
	{
		$file_dir = opendir($dir);
		while ($file = readdir($file_dir)) 
		{
			if ($file != '..' and $file != '.' and $file != '.DS_Store' and is_file($dir.DS.$file)) {
				$empty = false;
			} else {
				$empty = true;
			}
		}
		return $empty;
	}

	//-----------

	public function formatsize($file_size) 
	{
		if ($file_size >= 1073741824) {
			$file_size = round($file_size / 1073741824 * 100) / 100 . 'Gb';
		} elseif ($file_size >= 1048576) {
			$file_size = round($file_size / 1048576 * 100) / 100 . 'Mb';
		} elseif ($file_size >= 1024) {
			$file_size = round($file_size / 1024 * 100) / 100 . 'Kb';
		} else {
			$file_size = $file_size . 'b';
		}
		return $file_size;
	}

	//-----------

	public function niceidformat($someid) 
	{
		$negative = ($someid < 0);

		if ($negative)
			$someid = $someid * -1;

		while (strlen($someid) < 5) 
		{
			$someid = 0 . "$someid";
		}

		if ($negative)
			$someid = 'n' . $someid;

		return $someid;
	}
	
	//-----------
	
	public function build_path( $date='', $id, $base )
	{
		if ($date && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs )) {
			$date = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		if ($date) {
			$dir_year  = date('Y', $date);
			$dir_month = date('m', $date);
		} else {
			$dir_year  = date('Y');
			$dir_month = date('m');
		}
		$dir_id = FileUploadUtils::niceidformat( $id );
		
		$path = $base.DS.$dir_year.DS.$dir_month.DS.$dir_id;
	
		return $path;
	}
}
?>
