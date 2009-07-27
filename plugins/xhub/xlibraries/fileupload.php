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
// Resources upload class. 
// Handles uploading of files using HTTP protocol.
//----------------------------------------------------------

class FileUpload
{
    var $file_name;
    var $temp_file_name;
    var $upload_dir;
    var $max_file_size;
    var $ext_array;
    var $err;

	//-----------

    function validate_extension()
    {
		$valid_extension = false;
    
		$extension = strtolower(strrchr($this->file_name,'.'));
		$ext_count = count($this->ext_array);
    
		if (!$this->file_name) {
			return false;
		} else {
			if (!$this->ext_array) {
				return true;
			} else {
                foreach ($this->ext_array as $value) 
				{
					$first_char = substr($value,0,1);
					if ($first_char <> '.') {
						$extensions[] = '.'.strtolower($value);
					} else {
						$extensions[] = strtolower($value);
					}
                }

				foreach ($extensions as $value) 
				{
					if ($value == $extension) {
						$valid_extension = true;
					}
				}

				if ($valid_extension) {
					return true;
				} else {
					$this->err = 'Extension not permited';
					return false;
				}
			}
		}
	}
    
	//-----------
	
    function validate_size()
    {
		$temp_file_name = trim($this->temp_file_name);
		$max_file_size = trim($this->max_file_size);
    
		if ($temp_file_name) {
			$size = filesize($temp_file_name);
			if ($size > $max_file_size) {
				$this->err = 'Size exceeds';
				return false;
			} else {
				return true;
			}
		} else {
			$this->err = 'Size exceeds';
			return false;
		}    
    }
    
	//-----------
	
    function existing_file()
    {
        $file_name = trim($this->file_name);
        $upload_dir = $this->get_upload_directory();
    
        if ($upload_dir == 'ERROR') {
            $this->err='directory problem';
            return true;
        } else {
            $file = $upload_dir . $file_name;
            if (file_exists($file)) {
                $this->err='File exists';
                return true;
            } else {
                return false;
            }
        }    
    } 
    
	//-----------
	
    function if_exists_rename($file_name = NULL)
    {
        if (!$file_name) $filename=$this->file_name;
        
        $upload_dir = $this->get_upload_directory();
    
        if ($upload_dir == 'ERROR') {
            $this->err='directory problem';
            return true;
        } else {
            $file = $upload_dir . $file_name;
            if (file_exists($file)) {
                $this->file_name = date("Ymd-gi").'_'.$this->file_name;
            } 
        }    
    } 
    
	//-----------
	
    function get_upload_directory() {

	$upload_dir = trim($this->upload_dir);

	if ($upload_dir) {
		$ud_len = strlen($upload_dir);
		$last_slash = substr($upload_dir,$ud_len-1,1);
		if ($last_slash <> '/') {
			$upload_dir = $upload_dir.'/';
		} else {
			$upload_dir = $upload_dir;
		}
    
		$handle = @opendir($upload_dir);

		if ($handle) {
			$upload_dir = $upload_dir;
			closedir($handle);
		} else {
		die($upload_dir);
			$upload_dir = 'ERROR';
		}
        } else {
            $upload_dir = 'ERROR';
        }

        return $upload_dir;
    }
	
	//-----------
    
    function get_file_size()
    {
		$temp_file_name = trim($this->temp_file_name);
		$kb = 1024;
		$mb = 1024 * $kb;
		$gb = 1024 * $mb;
		$tb = 1024 * $gb;

		if ($temp_file_name) {
			$size = filesize($temp_file_name);
			if ($size < $kb) {
				$file_size = "$size Bytes";
			} elseif ($size < $mb) {
				$final = round($size/$kb,2);
				$file_size = "$final KB";
			} elseif ($size < $gb) {
				$final = round($size/$mb,2);
				$file_size = "$final MB";
			} elseif($size < $tb) {
				$final = round($size/$gb,2);
				$file_size = "$final GB";
			} else {
				$final = round($size/$tb,2);
				$file_size = "$final TB";
			}
		} else {
			$file_size = 'ERROR';
		}
		return $file_size;
    } 

	//-----------
	
	function upload_file_no_validation()
	{
		$temp_file_name = trim($this->temp_file_name);
		$file_name = trim(strtolower($this->file_name));
		$upload_dir = $this->get_upload_directory();
		$file_size = $this->get_file_size();
    
		if (($upload_dir == 'ERROR')) 
			$this->err='directory problem';
		else 
		{
			if (is_uploaded_file($temp_file_name)) 
			{
				if (move_uploaded_file($temp_file_name,$upload_dir . $file_name)) 
				{
					$origmask = @umask(0);
					$result = chmod( $upload_dir . $file_name, 0660 ); 
					@umask($origmask);

					if ($result)
						return true;

					$this->err='Failed to change file permissions';
				} 
			} 
		}

		return false;
	}

	//-----------
	
	function upload_file_with_validation()
	{
		$temp_file_name = trim($this->temp_file_name);
		$file_name  = trim(strtolower($this->file_name));
		$upload_dir = $this->get_upload_directory();
		$file_size  = $this->get_file_size();
        
		$valid_size = $this->validate_size();
		$valid_ext  = $this->validate_extension();
    
		if (($upload_dir == 'ERROR')) {
			$this->err='directory problem';
			return false;
		} elseif ((((!$valid_size) OR (!$valid_ext)))) {
			$this->err .= ' - Error uploading';
			return false;
		} else {
			if (is_uploaded_file($temp_file_name)) {
				if (move_uploaded_file( $temp_file_name, $upload_dir . $file_name )) {
					$origmask = @umask(0);
					if (chmod( $upload_dir . $file_name, 0660 )) {
						@umask($origmask);
						return true;
					} else {
						@umask($origmask);
						$this->err='Failed to change file permissions';
						return false;
					}
                		} else {
                    			return false;
                		}
            		} else {
                		return false;
            		}
        	}
    	}
}
?>
