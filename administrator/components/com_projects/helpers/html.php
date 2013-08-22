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

if (!defined("n")) {
	define("t","\t");
	define("n","\n");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class ProjectsHtml 
{
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'."\n";
	}
	
	//-----------
	
	public function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'."\n";
	}

	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}
	
	//-----------
	
	public function getThumbSrc( $id, $alias, $picname = '', $config ) {
			
		$src = '';
		$dir = $alias;	
		
		$webdir = $config->get('imagepath', '/site/projects');
		if (substr($webdir, 0, 1) != DS) {
			$webdir = DS.$webdir;
		}
		if (substr($webdir, -1, 1) == DS) {
			$webdir = substr($webdir, 0, (strlen($webdir) - 1));
		}
		$path   = $webdir.DS.$dir.DS.'images';
	
		if($picname) {
			$thumb = ProjectsHtml::createThumbName($picname);
			$src = $thumb && file_exists( JPATH_ROOT.$path.DS.$thumb ) ? $path.DS.$thumb :  '';
		}
		if(!$src) {
			$src = $config->get('defaultpic');
		}
		
		return $src;
	}
	
	//-----------

	public function createThumbName( $image=null, $tn='_thumb' )
	{
		if (!$image) {
			$image = $this->image;
		}
		if (!$image) {
			$this->setError( JText::_('No image set.') );
			return false;
		}
		
		$image = explode('.',$image);
		$n = count($image);
		if($n > 1) {
			$image[$n-2] .= $tn;
			$end = array_pop($image);
			$image[] = $end;
			$thumb = implode('.',$image);
		}
		else {
			// No extension
			$thumb = $image[0];
			$thumb .= $tn;
		}	
		return $thumb;
	}
	
	//----------------------------------------------------------
	// Date/time management
	//----------------------------------------------------------
	
	public function timeAgo($timestamp) 
	{
		$timestamp = Hubzero_View_Helper_Html::mkt($timestamp);
		$text = Hubzero_View_Helper_Html::timeAgoo($timestamp);
		
		$parts = explode(' ',$text);

		$text  = $parts[0].' '.$parts[1];
		if($text == '0 seconds') {
			$text = JText::_('COM_PROJECTS_JUST_A_MOMENT');
		}

		return $text;
	}
	
	//----------------------------------------------------------
	// File management
	//----------------------------------------------------------
	
	public function getFileAttribs( $path, $base_path = '', $get = '', $prefix = JPATH_ROOT )
	{
		// Return nothing if no path provided
		if (!$path) {
			return '';
		}
		
		if ($base_path) {
			// Strip any trailing slash
			if (substr($base_path, -1) == DS) { 
				$base_path = substr($base_path, 0, strlen($base_path) - 1);
			}
			// Ensure a starting slash
			if (substr($base_path, 0, 1) != DS) { 
				$base_path = DS.$base_path;
			}
		}
		
		// Ensure a starting slash
		if (substr($path, 0, 1) != DS) { 
			$path = DS.$path;
		}
		if (substr($path, 0, strlen($base_path)) == $base_path) {
			// Do nothing
		} else {
			$path = $base_path.$path;
		}
		$path = $prefix.$path;

		$file_name_arr = explode(DS,$path);
	    $type = end($file_name_arr);
	
		if($get == 'ext') {
			$ext = explode('.',$type);
			$ext = end($ext);
			return strtoupper($ext);
		}
	
		$fs = '';
		
		// Get the file size if the file exist
		if (file_exists( $path )) {
			$fs = filesize( $path );
		}
		if($get == 'size') {
			$fs = ProjectsHtml::formatSize($fs);
			return ($fs) ? $fs : '';
		}
	}
	
	//-----------

	public function formatSize($file_size, $round = 0) 
	{
		if ($file_size >= 1073741824) {
			$file_size = round(($file_size / 1073741824 * 100), $round) / 100 . 'GB';
		} elseif ($file_size >= 1048576) {
			$file_size = round(($file_size / 1048576 * 100), $round) / 100 . 'MB';
		} elseif ($file_size >= 1024) {
			$file_size = round(($file_size / 1024 * 100) / 100, $round) . 'KB';
		} else {
			$file_size = $file_size . 'b';
		}

		return $file_size;
	}

	//-----------

	public function convertSize($file_size, $from = 'b', $to = 'GB', $round = 0) 
	{
		$file_size = str_replace(' ', '', $file_size);

		if($from == 'b') {
			if ($to == 'GB') {
				$file_size = round(($file_size / 1073741824 * 100), $round) / 100;
			} elseif ($to == 'MB') {
				$file_size = round(($file_size / 1048576 * 100), $round) / 100 ;
			} elseif ($to == 'KB') {
				$file_size = round(($file_size / 1024 * 100) / 100, $round);
			} 
		}
		else if($from == 'GB') {
			if ($to == 'b') {
				$file_size = $file_size * 1073741824;
			} 
			if ($to == 'KB') {
				$file_size = $file_size * 1048576;
			}
			if ($to == 'MB') {
				$file_size = $file_size * 1024;
			}
		}

		return $file_size;
	}
	
	//----------------------------------------------------------
	// Form <select> builders
	//----------------------------------------------------------

	//-------------------------------------------------------------
	// Media manager functions
	//-------------------------------------------------------------

	//-----------
}
