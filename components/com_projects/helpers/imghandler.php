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
 * @author    Shawn Rice <srice@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Image manipulation class
 */
class ProjectsImgHandler extends JObject
{	
	/**
	 * Description for 'path'
	 * 
	 * @var unknown
	 */
	var $path = NULL;

	/**
	 * Description for 'image'
	 * 
	 * @var unknown
	 */
	var $image = NULL;

	/**
	 * Description for 'maxWidth'
	 * 
	 * @var integer
	 */
	var $maxWidth = 186;

	/**
	 * Description for 'maxHeight'
	 * 
	 * @var integer
	 */
	var $maxHeight = 186;

	/**
	 * Description for 'cropratio'
	 * 
	 * @var unknown
	 */
	var $cropratio = NULL;

	/**
	 * Description for 'quality'
	 * 
	 * @var integer
	 */
	var $quality = 90;

	/**
	 * Description for 'color'
	 * 
	 * @var boolean
	 */
	var $color = false;
	
	/**
	 * Description for 'copyto'
	 * 
	 * @var string
	 */
	var $copyto = NULL;

	/**
	 * Description for 'overwrite'
	 * 
	 * @var boolean
	 */
	var $overwrite = true;
	
	/**
	 * Description for 'force'
	 * 
	 * @var boolean
	 */
	var $force = true;

	/**
	 * Description for 'outputName'
	 * 
	 * @var unknown
	 */
	var $outputName = NULL;

	/**
	 * Description for '_MEMORY_TO_ALLOCATE'
	 * 
	 * @var string
	 */
	var $_MEMORY_TO_ALLOCATE = '100M';

	/**
	 * Process an image
	 * 
	 * @return     boolean True if no errors
	 */	
	public function process() 
	{
		$docRoot 	= $this->path;
		$copyto 	= $this->copyto;
		$image 		= $this->image;
		$cropratio 	= $this->cropratio;
		$quality 	= $this->quality;
		$color 		= $this->color;
		$force 		= $this->force;
		
		// Make sure that the requested file is actually an image
		if (!$image) 
		{
			$this->setError( JText::_('No image set.') );
			return false;
		}
		
		// Make sure that the requested file is actually an image
		if (!$docRoot) 
		{
			$this->setError( JText::_('No image path set.') );
			return false;
		}
				
		// Strip the possible trailing slash off the document root
		//$docRoot = preg_replace('/\/$/', '', $docRoot);
		
		if (!is_file($docRoot . $image)) 
		{
			$this->setError( JText::_('File/path not found.') );
			return false;
		}
		
		// Get the size and MIME type of the requested image
		$size = GetImageSize($docRoot . $image);
		$mime = $size['mime'];

		// Make sure that the requested file is actually an image
		if (substr($mime, 0, 6) != 'image/') 
		{
			$this->setError( JText::_('File is not an image.') );
			return false;
		}
		
		$width  = $size[0];
		$height = $size[1];

		$maxWidth = $this->maxWidth;
		$maxHeight = $this->maxHeight;
		
		if ($maxWidth >= $width && $maxHeight >= $height && $force == false) 
		{
			return true;
		}
		
		if ($color) 
		{
			$color = preg_replace('/[^0-9a-fA-F]/', '', (string) $color);
		} 
		else 
		{
			$color = FALSE;
		}
		
		// Ratio cropping
		$offsetX = 0;
		$offsetY = 0;

		if ($cropratio) 
		{
			$cropRatio = explode(':', (string) $cropratio);
			if (count($cropRatio) == 2) 
			{
				$ratioComputed = $width / $height;
				$cropRatioComputed = (float) $cropRatio[0] / (float) $cropRatio[1];

				if ($ratioComputed < $cropRatioComputed) 
				{
					// Image is too tall so we will crop the top and bottom
					$origHeight	= $height;
					$height	= $width / $cropRatioComputed;
					$offsetY = ($origHeight - $height) / 2;
				} 
				else if ($ratioComputed > $cropRatioComputed) 
				{
					// Image is too wide so we will crop off the left and right sides
					$origWidth = $width;
					$width = $height * $cropRatioComputed;
					$offsetX = ($origWidth - $width) / 2;
				}
			}
		}

		// Setting up the ratios needed for resizing. We will compare these below to determine how to
		// resize the image (based on height or based on width)
		$xRatio	= $maxWidth / $width;
		$yRatio	= $maxHeight / $height;

		if ($xRatio * $height < $maxHeight) 
		{
			// Resize the image based on width
			$tnHeight = ceil($xRatio * $height);
			$tnWidth  = $maxWidth;
		} 
		else 
		{
			// Resize the image based on height
			$tnWidth  = ceil($yRatio * $width);
		 	$tnHeight = $maxHeight;
		}

		// Before we actually do any crazy resizing of the image, we want to make sure that we
		// haven't already done this one at these dimensions. To the cache!
		// Note, cache must be world-readable

		// We store our cached image filenames as a hash of the dimensions and the original filename
		$resizedImageSource	= $tnWidth . 'x' . $tnHeight . 'x' . $quality;
		if ($cropratio) 
		{
			$resizedImageSource	.= 'x' . (string) $cropratio;
		}
		$resizedImageSource .= '-' . $image;

		$resizedImage = $resizedImageSource; //md5($resizedImageSource);

		$resized = $docRoot . $resizedImage;

		// We don't want to run out of memory
		ini_set('memory_limit', $this->_MEMORY_TO_ALLOCATE);

		// Set up a blank canvas for our resized image (destination)
		$dst = imagecreatetruecolor($tnWidth, $tnHeight);

		// Set up the appropriate image handling functions based on the original image's mime type
		switch ($size['mime'])
		{
			case 'image/gif':
				// We will be converting GIFs to PNGs to avoid transparency issues when resizing GIFs
				// This is maybe not the ideal solution, but IE6 can suck it
				$creationFunction	= 'ImageCreateFromGif';
				$outputFunction		= 'ImagePng';
				$mime				= 'image/png'; // We need to convert GIFs to PNGs
				$doSharpen			= FALSE;
				$quality			= round(10 - ($quality / 10)); // We are converting the GIF to a PNG and PNG needs a compression level of 0 (no compression) through 9
			break;

			case 'image/x-png':
			case 'image/png':
				$creationFunction	= 'ImageCreateFromPng';
				$outputFunction		= 'ImagePng';
				$doSharpen			= FALSE;
				$quality			= round(10 - ($quality / 10)); // PNG needs a compression level of 0 (no compression) through 9
			break;

			default:
				$creationFunction	= 'ImageCreateFromJpeg';
				$outputFunction	 	= 'ImageJpeg';
				$doSharpen			= TRUE;
			break;
		}
		
		// Read in the original image
		$src = $creationFunction($docRoot . $image);

		if (in_array($size['mime'], array('image/gif', 'image/png'))) 
		{
			if (!$color) 
			{
				// If this is a GIF or a PNG, we need to set up transparency
				imagealphablending($dst, false);
				imagesavealpha($dst, true);
			} 
			else 
			{
				// Fill the background with the specified color for matting purposes
				if ($color[0] == '#') 
				{
					$color = substr($color, 1);
				}

				$background	= FALSE;

				if (strlen($color) == 6) 
				{
					$background	= imagecolorallocate($dst, hexdec($color[0].$color[1]), hexdec($color[2].$color[3]), hexdec($color[4].$color[5]));
				} 
				else if (strlen($color) == 3) 
				{
					$background	= imagecolorallocate($dst, hexdec($color[0].$color[0]), hexdec($color[1].$color[1]), hexdec($color[2].$color[2]));
				}
				if ($background) 
				{
					imagefill($dst, 0, 0, $background);
				}
			}
		}
		
		// Resample the original image into the resized canvas we set up earlier
		ImageCopyResampled($dst, $src, 0, 0, $offsetX, $offsetY, $tnWidth, $tnHeight, $width, $height);

		if ($doSharpen) 
		{
			// Sharpen the image based on two things:
			//	(1) the difference between the original size and the final size
			//	(2) the final size
			$sharpness = $this->findSharp($width, $tnWidth);

			$sharpenMatrix = array(
				array(-1, -2, -1),
				array(-2, $sharpness + 12, -2),
				array(-1, -2, -1)
			);
			$divisor = $sharpness;
			$offset  = 0;
			if (function_exists('imageconvolution')) 
			{
				imageconvolution($dst, $sharpenMatrix, $divisor, $offset);
			}
		}
		
		// Write the resized image to the cache
		$outputFunction($dst, $resized, $quality);
		
		// Yes - remove it
		$overwrite = $this->overwrite;
		if ($overwrite) {
			$outputName = $this->outputName;
			if ($outputName) {
				$image = $outputName;
			}
			
			jimport('joomla.filesystem.file');
			if (file_exists($resized)) 
			{
				if (file_exists($docRoot.$image)) 
				{
					if (!JFile::delete($docRoot.$image)) 
					{
						$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
						return false;
					}
				}
				if (!JFile::move($resized, $docRoot.$image)) 
				{
					$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
					return false;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Create a thumbnail name
	 * 
	 * @param      string $image Image name
	 * @param      string $tn    Thumbnail prefix
	 * @param      string $ext  
	 * @return     string
	 */
	public function createThumbName( $image=null, $tn='_thumb', $ext = '' )
	{
		if (!$image) 
		{
			$image = $this->image;
		}
		if (!$image) 
		{
			$this->setError( JText::_('No image set.') );
			return false;
		}
		
		$image = explode('.',$image);
		$n = count($image);
		
		if ($n > 1) 
		{
			$image[$n-2] .= $tn;
			$end = array_pop($image);
			if ($ext) 
			{
				$image[] = $ext;
			}
			else 
			{
				$image[] = $end;
			}
			
			$thumb = implode('.',$image);
		}
		else 
		{
			// No extension
			$thumb = $image[0];
			$thumb .= $tn;
			if ($ext) 
			{
				$thumb .= '.'.$ext;
			}
		}	
		return $thumb;
	}
	
	/**
	 * Append timestamp (or random string) to file name
	 * 
	 * @param      string $file
	 * @param      string $stamp
	 * @return     string
	 */
	public function appendTimeStamp ( $file = null, $stamp = '' )
	{
		if (!$file) 
		{
			$this->setError( JText::_('No filename set.') );
			return false;
		}
		if (!$stamp) 
		{
			$stamp = strtotime("now");
		}
		
		$file = explode('.',$file);
		$n = count($file);
		$file[$n-2] .= '_'.$stamp;
		$end = array_pop($file);
		$file[] = $end;
		$new = implode('.',$file);
		
		return $new;
	}
	
	/**
	 * Sharpen the image based on two things:
	 * 
	 * (1) the difference between the original size and the final size
	 * (2) the final size
	 * 
	 * @param      number $orig  Original size
	 * @param      number $final Final size
	 * @return     integer
	 */
	private function findSharp($orig, $final)
	{
		$final = $final * (750.0 / $orig);
		$a = 52;
		$b = -0.27810650887573124;
		$c = .00047337278106508946;
		
		$result = $a + $b * $final + $c * $final * $final;
		
		return max(round($result), 0);
	}
}
