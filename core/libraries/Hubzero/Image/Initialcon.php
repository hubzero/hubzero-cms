<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Image;

use Exception;

/**
 * Identicon is a library which generate an identicon image based on a string.
 *
 * - Images are generated in PNG format with transparent background.
 * - The string can be an email, an IP address, a username, an ID or something else.
 *
 * Usage:
 *
 *   $identicon = new Initialcon();
 *
 *   // Generate image and display
 *   $identicon->displayImage('foo');
 *
 *   // Get image data
 *   $imageData = $identicon->getImageData('bar');
 *
 *   // Generate and get the base 64 image uri ready for integrate into an HTML img tag.
 *   $imageDataUri = $identicon->getImageDataUri('bar');
 *   <img src="<?php echo $imageDataUri; ?>" alt="bar Identicon" />
 *
 * Based on work by Benjamin Laugueux <benjamin@yzalis.com>
 */
class Initialcon
{
	/**
	 * @var  string
	 */
	private $hash;

	/**
	 * @var  string
	 */
	private $string;

	/**
	 * @var  integer
	 */
	private $color;

	/**
	 * @var  integer
	 */
	private $size;

	/**
	 * @var  string
	 */
	private $fontPath;

	/**
	 * @var  integer
	 */
	private $pixelRatio;

	/**
	 * Set the image size
	 *
	 * @param   integer  $size
	 * @return  object
	 */
	public function setSize($size)
	{
		$this->size = $size;
		$this->pixelRatio = round($size / 5);

		return $this;
	}

	/**
	 * Get the image size
	 *
	 * @return  integer
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * Generate a hash fron the original string
	 *
	 * @param   string  $string
	 * @return  object
	 */
	public function setString($string)
	{
		if (null === $string)
		{
			throw new Exception('The string cannot be null.');
		}

		$this->string = $string;
		$this->hash   = md5($string);

		$this->convertHash();

		return $this;
	}

	/**
	 * Get the identicon string hash
	 *
	 * @return  string
	 */
	public function getHash()
	{
		return $this->hash;
	}

	/**
	 * Convert the hash into an multidimensionnal array of boolean
	 *
	 * @return  object
	 */
	private function convertHash()
	{
		preg_match_all('/(\w)(\w)/', $this->hash, $chars);

		$this->color[0] = hexdec(array_pop($chars[1]))*16;
		$this->color[1] = hexdec(array_pop($chars[1]))*16;
		$this->color[2] = hexdec(array_pop($chars[1]))*16;

		return $this;
	}

	/**
	 * Generate the image
	 *
	 * @param   string   $string
	 * @param   integer  $size
	 * @param   string   $color
	 * @return  void
	 */
	private function generateImage($string, $size, $color)
	{
		$this->setString($string);
		$this->setSize($size);

		if ($this->fontPath === null)
		{
			$this->setFontPath(__DIR__ . '/fonts/OpenSans-Regular.ttf');
		}

		// Prepare the image
		$image = imagecreatetruecolor($this->pixelRatio * 5, $this->pixelRatio * 5);
		$background = imagecolorallocate($image, 0, 0, 0);

		// Prepage the color
		if (null !== $color)
		{
			$this->setColor($color);
		}
		$color = imagecolorallocate($image, $this->color[0], $this->color[1], $this->color[2]);
		imagefilledrectangle($image, 0, 0, $this->size, $this->size, $color);

		// Allocate A Color For The Text
		$white = imagecolorallocate($image, 255, 255, 255);

		$rnd = ceil($this->size / 20);

		$fontsize = round($this->size * 0.4); //ceil(($this->size / 3) + $rnd);

		$tb = imagettfbbox($fontsize, 0, $this->getFontPath(), $string);

		// Calculate x baseline
		/*if ($tb[0] >= -1)
		{
			$tb['x'] = abs($tb[0] + 1) * -1;
		}
		else
		{
			$tb['x'] = abs($tb[0] + 2);
		}

		// Calculate actual text width
		$tb['width'] = abs($tb[2] - $tb[0]);
		if ($tb[0] < -1)
		{
			$tb['width'] = abs($tb[2]) + abs($tb[0]) - 1;
		}

		// Calculate y baseline
		$tb['y'] = abs($tb[5] + 1);

		// Calculate actual text height
		$tb['height'] = abs($tb[7]) - abs($tb[1]);
		if ($tb[3] > 0)
		{
			$tb['height'] = abs($tb[7] - $tb[1]) - 1;
		}*/

		// Horizontally centr the text
		$x = ceil((($this->size - $tb[2]) / 2) - $rnd);
		//$x = ceil(($this->size - $tb['width']) / 2);

		// Vertically center the text
		$y = ceil(($this->size - $tb[7] - $rnd) / 2);
		//$y = ceil(($this->size - $tb['height']) / 2);
		//$y = $this->size - $y;

		// Print Text On Image
		imagettftext($image, $fontsize, 0, $x, $y, $white, $this->getFontPath(), $string);

		imagepng($image);
	}

	/**
	 * Set the image color
	 *
	 * @param   mixed   $color  The color in hexa (6 chars) or rgb array
	 * @return  object
	 */
	public function setColor($color)
	{
		if (is_array($color))
		{
			$this->color[0] = $color[0];
			$this->color[1] = $color[1];
			$this->color[2] = $color[2];
		}
		else
		{
			$color = ltrim($color, '#');

			$this->color[0] = hexdec(substr($color, 0, 2));
			$this->color[1] = hexdec(substr($color, 2, 2));
			$this->color[2] = hexdec(substr($color, 4, 2));
		}

		return $this;
	}

	/**
	 * Get the color
	 *
	 * @return  arrray
	 */
	public function getColor()
	{
		return $this->color;
	}

	/**
	 * Get the font path.
	 *
	 * @return  string
	 */
	public function getFontPath()
	{
		return realpath($this->fontPath);
	}

	/**
	 * Set the font path.
	 *
	 * @param   string  $path
	 * @return  object
	 */
	public function setFontPath($path)
	{
		$this->fontPath = $path;

		return $this;
	}

	/**
	 * Display an Identicon image
	 *
	 * @param   string   $string
	 * @param   integer  $size
	 * @param   string   $color
	 * @return  void
	 */
	public function displayImage($string, $size = 64, $color = null)
	{
		header("Content-Type: image/png");
		$this->generateImage($string, $size, $color);
	}

	/**
	 * Get an Identicon PNG image data
	 *
	 * @param   string   $string
	 * @param   integer  $size
	 * @param   string   $color
	 * @return  string
	 */
	public function getImageData($string, $size = 64, $color = null)
	{
		ob_start();
		$this->generateImage($string, $size, $color);
		$imageData = ob_get_contents();
		ob_end_clean();

		return $imageData;
	}

	/**
	 * Get an Identicon PNG image data
	 *
	 * @param   string   $string
	 * @param   integer  $size
	 * @param   string   $color
	 * @return  string
	 */
	public function getImageDataUri($string, $size = 64, $color = null)
	{
		return sprintf('data:image/png;base64,%s', base64_encode($this->getImageData($string, $size, $color)));
	}
}
