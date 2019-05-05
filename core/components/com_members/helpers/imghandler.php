<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Helpers;

use Hubzero\Base\Obj;
use Filesystem;
use Lang;

/**
 * Image manipulation class
 */
class ImgHandler extends Obj
{
	/**
	 * Description for 'path'
	 *
	 * @var unknown
	 */
	public $path = null;

	/**
	 * Description for 'image'
	 *
	 * @var unknown
	 */
	public $image = null;

	/**
	 * Description for 'maxWidth'
	 *
	 * @var integer
	 */
	public $maxWidth = 186;

	/**
	 * Description for 'maxHeight'
	 *
	 * @var integer
	 */
	public $maxHeight = 186;

	/**
	 * Description for 'cropratio'
	 *
	 * @var unknown
	 */
	public $cropratio = null;

	/**
	 * Description for 'quality'
	 *
	 * @var integer
	 */
	public $quality = 90;

	/**
	 * Description for 'color'
	 *
	 * @var boolean
	 */
	public $color = false;

	/**
	 * Description for 'overwrite'
	 *
	 * @var boolean
	 */
	public $overwrite = true;

	/**
	 * Description for 'outputName'
	 *
	 * @var unknown
	 */
	public $outputName = null;

	/**
	 * Description for '_MEMORY_TO_ALLOCATE'
	 *
	 * @var string
	 */
	public $_MEMORY_TO_ALLOCATE = '100M';

	/**
	 * Process an image
	 *
	 * @return     boolean True if no errors
	 */
	public function process()
	{
		$docRoot = $this->path;
		$image = $this->image;
		$cropratio = $this->cropratio;
		$quality = $this->quality;
		$color = $this->color;

		// Make sure that the requested file is actually an image
		if (!$image)
		{
			$this->setError(Lang::txt('No image set.'));
			return false;
		}

		// Make sure that the requested file is actually an image
		if (!$docRoot)
		{
			$this->setError(Lang::txt('No image path set.'));
			return false;
		}

		// Strip the possible trailing slash off the document root
		//$docRoot = preg_replace('/\/$/', '', $docRoot);

		if (!is_file($docRoot . $image))
		{
			$this->setError(Lang::txt('File/path not found.'));
			return false;
		}

		// Get the size and MIME type of the requested image
		$size = GetImageSize($docRoot . $image);
		$mime = $size['mime'];

		// Make sure that the requested file is actually an image
		if (substr($mime, 0, 6) != 'image/')
		{
			$this->setError(Lang::txt('File is not an image.'));
			return false;
		}

		$width  = $size[0];
		$height = $size[1];

		$maxWidth = $this->maxWidth;
		$maxHeight = $this->maxHeight;

		if ($maxWidth >= $width && $maxHeight >= $height)
		{
			return true;
		}

		if ($color)
		{
			$color = preg_replace('/[^0-9a-fA-F]/', '', (string) $color);
		}
		else
		{
			$color = false;
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
			$resizedImageSource .= 'x' . (string) $cropratio;
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
				$creationFunction = 'ImageCreateFromGif';
				$outputFunction   = 'ImagePng';
				$mime             = 'image/png'; // We need to convert GIFs to PNGs
				$doSharpen        = false;
				$quality          = round(10 - ($quality / 10)); // We are converting the GIF to a PNG and PNG needs a compression level of 0 (no compression) through 9
			break;

			case 'image/x-png':
			case 'image/png':
				$creationFunction = 'ImageCreateFromPng';
				$outputFunction   = 'ImagePng';
				$doSharpen        = false;
				$quality          = round(10 - ($quality / 10)); // PNG needs a compression level of 0 (no compression) through 9
			break;

			default:
				$creationFunction = 'ImageCreateFromJpeg';
				$outputFunction   = 'ImageJpeg';
				$doSharpen        = true;
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

				$background = false;

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
			//  (1) the difference between the original size and the final size
			//  (2) the final size
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
		if ($overwrite)
		{
			$outputName = $this->outputName;
			if ($outputName)
			{
				$image = $outputName;
			}

			if (file_exists($resized))
			{
				if (file_exists($docRoot . $image))
				{
					if (!Filesystem::delete($docRoot . $image))
					{
						$this->setError(Lang::txt('UNABLE_TO_DELETE_FILE'));
						return false;
					}
				}
				if (!Filesystem::move($resized, $docRoot . $image))
				{
					$this->setError(Lang::txt('UNABLE_TO_DELETE_FILE'));
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
	 * @return     string
	 */
	public function createThumbName($image=null, $tn='_thumb')
	{
		if (!$image)
		{
			$image = $this->image;
		}
		if (!$image)
		{
			$this->setError(Lang::txt('No image set.'));
			return false;
		}

		$ext = Filesystem::extension($image);

		return Filesystem::name($image) . $tn . '.' . $ext;
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
