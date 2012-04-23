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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


class Hubzero_Image
{   
	private $source 		= NULL;
	private $resource 		= NULL;
	private $image_type 	= IMAGETYPE_PNG;
	private $exif_data 		= NULL;
	private $config			= array();
	private $errors 		= array();
	
	//-----
	
	public function __construct( $image_source = null, $config = array() )
	{
		$this->source = $image_source;
		$this->config = $config;
		
		if(!$this->checkPackageRequirements('gd'))
			return false;
		
		if(!is_null($this->source) && is_file($this->source))
		{   
			$this->openImage();
		}
	}
	
	//-----
	
	public function setImageType($type)
	{
		if($type)
		{
			$this->image_type = $type;
		}
		
		if($this->image_type == IMAGETYPE_PNG)
		{
			imagesavealpha($this->resource, true);
			imagealphablending($this->resource, false);
		}
	}
	
	//-----
	
	private function checkPackageRequirements( $package = '' )
	{
		if($package == '')
			return false;
		
		$installed_exts = get_loaded_extensions();
		
		if(!in_array($package, $installed_exts))
		{   
			$this->errors[] = "[ERROR] You are missing the required PHP package {$package}.";
			return false;
		}
		   
		return true;
	}
	
	//-----
	
	private function openImage()
	{
		$image_atts = getimagesize($this->source);
		
		switch( $image_atts['mime'] )
		{   
			case 'image/jpeg':
				$this->image_type = IMAGETYPE_JPEG;
				$this->resource = imagecreatefromjpeg($this->source);
				break;
			case 'image/gif':
				$this->image_type = IMAGETYPE_GIF;
				$this->resource = imagecreatefromgif($this->source);
				break;
			case 'image/png':
				$this->image_type = IMAGETYPE_PNG;
				$this->resource = imagecreatefrompng($this->source);
				break;
		}
		
		if($this->image_type == IMAGETYPE_PNG)
		{
			imagesavealpha($this->resource, true);
			imagealphablending($this->resource, false);
		} 
		
		if(isset($this->config['auto_rotate']) && $this->config['auto_rotate'] == true)
		{
			$this->autoRotate();
		}
	}
	
	
	/*----------------------------------------------------
		Image Rotation
	----------------------------------------------------*/
	
	public function autoRotate()
	{
		if(!$this->checkPackageRequirements('exif'))
		{                
			$this->errors[] = "You need the PHP exif library installed to rotate image based on Exif Orientation value.";
			return false;
		}
		
		if($this->image_type == IMAGETYPE_JPEG)
		{
			$this->exif_data = exif_read_data($this->source);
			if(isset($this->exif_data['Orientation']))
			{
				switch($this->exif_data['Orientation'])
				{  
					case 2:		$this->flip(true, false);						break;
					case 3:		$this->rotate(180);								break;
					case 4:		$this->flip(false, true);						break;
					case 5:		$this->rotate(270); 
								$this->flip(true, false);						break;
					case 6:		$this->rotate(270);								break;
					case 7:		$this-rotate(90);
								$this->flip(true, false);						break;
					case 8:		$this->rotate(90);								break;
				}
			}
		}
	}
	
	//-----  
	
	public function rotate( $rotation = 0, $background = 0 )
	{
		$resource = imagerotate($this->resource, $rotation, $background);
		imagedestroy($this->resource);
		$this->resource = $resource;
	} 
	 
	
	/*----------------------------------------------------
		Image Flip
	----------------------------------------------------*/

	public function flip( $flip_horizontal, $flip_vertical = false )
	{
		$resource = $this->resource;
		$width = imagesx($resource);
		$height = imagesy($resource);
		$new_resource = imagecreatetruecolor($width, $height);
		
        for ($x=0 ; $x<$width ; $x++)
		{
			for ($y=0 ; $y<$height ; $y++)
			{
				if($flip_horizontal && $flip_vertical) 
				{
					imagecopy($new_resource, $resource, $width-$x-1, $height-$y-1, $x, $y, 1, 1);
				}
				else if ($flip_horizontal) 
				{
					imagecopy($new_resource, $resource, $width-$x-1, $y, $x, $y, 1, 1);
				}
				else if ($flip_vertical) 
				{
					imagecopy($new_resource, $resource, $x, $height-$y-1, $x, $y, 1, 1);
				}
			}
		}

		$this->resource = $new_resource;
		imagedestroy($resource);
		
	}
	
	
	/*----------------------------------------------------
		Image Crop
	----------------------------------------------------*/
	
	public function crop( $top, $right = 0, $bottom = 0, $left = 0)
	{
		$width  = imagesx($this->resource);
		$height  = imagesy($this->resource);
		$new_width = $width - ($left + $right);
		$new_height = $height - ($top + $bottom);
		
		$resource = imagecreatetruecolor( $new_width, $new_height );
		imagecopy($resource, $this->resource, 0, 0, $left, $top, $new_width, $new_height );

		imagedestroy($this->resource);
		$this->resource = $resource;
	}   
	
	
	/*----------------------------------------------------
		Image Resize
	----------------------------------------------------*/
	
	public function resize( $new_dimension, $use_height = false, $squared = false, $resample = true )
	{
		$percent 	= false;
		$width 		= imagesx( $this->resource );
		$height 	= imagesy( $this->resource );
		$w 			= $width;
		$h 			= $height;
		$x 			= 0;
		$y 			= 0;
		
		if( ($new_dimension > $width && !$use_height) || ($new_dimension > $height && $use_height) )
		{
			return;
		}

		if( $new_dimension < 1 )
		{
			$percent = $new_dimension;
		}
		elseif( substr($new_dimension, -1) == '%' )
		{
			$percent = (substr($new_dimension, 0, -1) / 100);
		}

		if( $percent !== false )
		{    
			$new_dimension = $use_height ? ($height * $percent) : ($width * $percent);
			$new_dimension = round($new_dimension);
		}

		if( $squared )
		{
			$new_w = $new_dimension;
			$new_h = $new_dimension;
			if( !$use_height )
			{
				$x = ceil(($width - $height) / 2);
				$w = $height;
				$h = $height;
			}
			else
			{
				$y = ceil(($height - $width) / 2);
				$w = $width;
				$h = $width;
			}
		}
		else
		{
			if( !$use_height )
			{
				$new_w = $new_dimension;
				$new_h = floor($height * ($new_w / $width));
			}
			else
			{
				$new_h = $new_dimension;
				$new_w = floor($width * ($new_h / $height));
			}
		}
		
		$resource = imagecreatetruecolor($new_w,$new_h);
		
		if( $resample )
		{
			imagecopyresampled($resource, $this->resource, 0, 0, $x, $y, $new_w, $new_h, $w, $h);
		}
		else
		{
			imagecopyresized($resource, $this->resource, 0, 0, $x, $y, $new_w, $new_h, $w, $h);
		}
		
		imagedestroy($this->resource);
		$this->resource = $resource;
	}
	
	
	/*----------------------------------------------------
		Image Geo Location Data
	----------------------------------------------------*/
	
	public function getGeoLocation()
	{
		
		if(!$this->checkPackageRequirements('exif'))
		{                
			$this->errors[] = "You need the PHP exif library installed to rotate image based on Exif Orientation value.";
			return false;
		}
		
		$this->exif_data = exif_read_data($this->source);
		
		if(isset($this->exif_data['GPSLatitude']))
		{
			$lat = $this->exif_data['GPSLatitude'];
			$lat_dir = $this->exif_data['GPSLatitudeRef'];
			$long = $this->exif_data['GPSLongitude'];
			$long_dir = $this->exif_data['GPSLongitudeRef'];
		
			$latitude = $this->geo_single_fracs2dec($lat);
			$longitude = $this->geo_single_fracs2dec($long);
			$latitude_formatted = $this->geo_pretty_fracs2dec($lat) . $lat_dir;
			$longitude_formatted = $this->geo_pretty_fracs2dec($long) . $long_dir;
		
			if ($lat_dir == 'S') 
				$latitude *= -1; 
		
			if ($long_dir == 'W') 
				$longitude *= -1;
			
			$geo = array(
				'latitude' => $latitude,
				'longitude' => $longitude,
				'latitude_formatted' => $latitude_formatted,
				'longitude_formatted' => $longitude_formatted
			);
		}
		else
		{
			$geo = array();
		}
		
		return $geo;
	}
	
	//-----
	
	private function geo_frac2dec($str) 
	{
		list( $n, $d ) = explode( '/', $str );
		
		if ( !empty($d) )
			return $n / $d;
			
		return $str;
	}
	
	//-----
	
	private function geo_pretty_fracs2dec($fracs) 
	{
		return	$this->geo_frac2dec($fracs[0]) . '&deg; ' . $this->geo_frac2dec($fracs[1]) . '&prime; ' . $this->geo_frac2dec($fracs[2]) . '&Prime; ';
	}
	
	//-----
	
	private function geo_single_fracs2dec($fracs) 
	{
		return	$this->geo_frac2dec($fracs[0]) + $this->geo_frac2dec($fracs[1]) / 60 + $this->geo_frac2dec($fracs[2]) / 3600;
	}
	
	
	/*----------------------------------------------------
		Image Output
	----------------------------------------------------*/
	
	public function display()
	{
		$image_atts = getimagesize($this->source);
		header('Content-type: ' . $image_atts['mime']);
		$this->output(null); 
	}
	    
	//-----
	
	public function save( $save_path = null, $make_paths = false )
	{
		$path = $this->source;    
		
		if(!is_null($save_path))
		{   
			$info = pathinfo($save_path);
			
			if($make_paths)
			{
				JFolder::create($info['dirname'], 0770);
			}
			
			if(!is_dir($info['dirname']) && $make_paths == false)
			{
				$this->errors[] = "You must supply a valid path or allow save function to create recursive path";
				return;
			}
			
			$path = $save_path;
		} 
		
		$this->output($path);
	}   
	
	//-----
	
	private function output( $save_path )
	{
		switch($this->image_type)
		{
			case IMAGETYPE_PNG:		imagepng($this->resource, $save_path);		break;
			case IMAGETYPE_GIF:		imagegif($this->resource, $save_path);		break;
			case IMAGETYPE_JPEG:	imagejpeg($this->resource, $save_path);		break;
		}
		//exit();
	}

	//-----
}	