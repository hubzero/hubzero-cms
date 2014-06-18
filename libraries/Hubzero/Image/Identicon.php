<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author    Benjamin Laugueux <benjamin@yzalis.com>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   MIT
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
 *   $identicon = new Identicon();
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
 */
class Identicon
{
	/**
	 * @var string
	 */
	private $hash;

	/**
	 * @var integer
	 */
	private $color;

	/**
	 * @var integer
	 */
	private $size;

	/**
	 * @var integer
	 */
	private $pixelRatio;

	/**
	 * @var array
	 */
	private $arrayOfSquare = array();

	/**
	 * Set the image size
	 *
	 * @param integer $size
	 * @return Identicon
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
	 * @return integer
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * Generate a hash fron the original string
	 *
	 * @param string $string
	 *
	 * @return Identicon
	 */
	public function setString($string)
	{
		if (null === $string)
		{
			throw new Exception('The string cannot be null.');
		}

		$this->hash = md5($string);

		$this->convertHashToArrayOfBoolean();

		return $this;
	}

	/**
	 * Get the identicon string hash
	 *
	 * @return string
	 */
	public function getHash()
	{
		return $this->hash;
	}

	/**
	 * Convert the hash into an multidimensionnal array of boolean
	 *
	 * @return Identicon
	 */
	private function convertHashToArrayOfBoolean()
	{
		preg_match_all('/(\w)(\w)/', $this->hash, $chars);

		foreach ($chars[1] as $i => $char)
		{
			if ($i % 3 == 0)
			{
				$this->arrayOfSquare[$i/3][0] = $this->convertHexaToBoolean($char);
				$this->arrayOfSquare[$i/3][4] = $this->convertHexaToBoolean($char);
			}
			elseif ($i % 3 == 1)
			{
				$this->arrayOfSquare[$i/3][1] = $this->convertHexaToBoolean($char);
				$this->arrayOfSquare[$i/3][3] = $this->convertHexaToBoolean($char);
			}
			else
			{
				$this->arrayOfSquare[$i/3][2] = $this->convertHexaToBoolean($char);
			}
			ksort($this->arrayOfSquare[$i/3]);
		}

		$this->color[0] = hexdec(array_pop($chars[1]))*16;
		$this->color[1] = hexdec(array_pop($chars[1]))*16;
		$this->color[2] = hexdec(array_pop($chars[1]))*16;

		return $this;
	}

	/**
	 * Convert an heaxecimal number into a boolean
	 *
	 * @param string $hexa
	 * @return boolean
	 */
	private function convertHexaToBoolean($hexa)
	{
		return (bool) intval(round(hexdec($hexa)/10));
	}

	/**
	 * Get arrayOfSquare
	 *
	 * @return array
	 */
	public function getArrayOfSquare()
	{
		return $this->arrayOfSquare;
	}


	/**
	 * Generate the Identicon image
	 *
	 * @param string  $string
	 * @param integer $size
	 * @param string $hexaColor
	 */
	private function generateImage($string, $size, $color)
	{
		$this->setString($string);
		$this->setSize($size);

		// prepare the image
		$image = imagecreatetruecolor($this->pixelRatio * 5, $this->pixelRatio * 5);
		$background = imagecolorallocate($image, 0, 0, 0);
		imagecolortransparent($image, $background);

		// prepage the color
		if (null !== $color)
		{
			$this->setColor($color);
		}
		$color = imagecolorallocate($image, $this->color[0], $this->color[1], $this->color[2]);

		// draw the content
		foreach ($this->arrayOfSquare as $lineKey => $lineValue)
		{
			foreach ($lineValue as $colKey => $colValue)
			{
				if (true === $colValue)
				{
					imagefilledrectangle($image, $colKey * $this->pixelRatio, $lineKey * $this->pixelRatio, ($colKey + 1) * $this->pixelRatio, ($lineKey + 1) * $this->pixelRatio, $color);
				}
			}
		}

		imagepng($image);
	}

	/**
	 * Set the image color
	 *
	 * @param string|array $color The color in hexa (6 chars) or rgb array
	 * @return Identicon
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
			if (false !== strpos($color, '#'))
			{
				$color = substr($color, 1);
			}
			$this->color[0] = hexdec(substr($color, 0, 2));
			$this->color[1] = hexdec(substr($color, 2, 2));
			$this->color[2] = hexdec(substr($color, 4, 2));
		}

		return $this;
	}

	/**
	 * Get the color
	 *
	 * @return arrray
	 */
	public function getColor()
	{
		return $this->color;
	}

	/**
	 * Display an Identicon image
	 *
	 * @param string  $string
	 * @param integer $size
	 * @param string $hexaColor
	 */
	public function displayImage($string, $size = 64, $hexaColor = null)
	{
		header("Content-Type: image/png");
		$this->generateImage($string, $size, $hexaColor);
	}

	/**
	 * Get an Identicon PNG image data
	 *
	 * @param string  $string
	 * @param integer $size
	 * @param string $hexaColor
	 * @return string
	 */
	public function getImageData($string, $size = 64, $hexaColor = null)
	{
		ob_start();
		$this->generateImage($string, $size, $hexaColor);
		$imageData = ob_get_contents();
		ob_end_clean();

		return $imageData;
	}

	/**
	 * Get an Identicon PNG image data
	 *
	 * @param string  $string
	 * @param integer $size
	 * @param string $hexaColor
	 * @return string
	 */
	public function getImageDataUri($string, $size = 64, $hexaColor = null)
	{
		return sprintf('data:image/png;base64,%s', base64_encode($this->getImageData($string, $size, $hexaColor)));
	}
}
