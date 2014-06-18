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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * HUBzero plugin class for displaying image CAPTCHAs
 */
class plgHubzeroImagecaptcha extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Image background color
	 * @var	string
	 */
	private $_bgColor = '#000000';

	/**
	 * Text color
	 * @var	string
	 */
	private $_textColor = '#ff0000';

	/**
	 * Displays either a CAPTCHA image or form field
	 *
	 * @return string
	 */
	public function onGetCaptcha()
	{
		$showCaptcha = JRequest::getVar('showCaptcha', '');
		if ($showCaptcha)
		{
			return $this->_display();
		}
		return $this->_getCapthcaHtml();
	}

	/**
	 * Displays a CAPTCHA image
	 *
	 * @return boolean
	 */
	private function _display()
	{
		$imageFunction = '_createImage' . $this->params->get('imageFunction');
		$imageFunction = (!method_exists($this, $imageFunction)) ? '_createImageAdv' : $imageFunction;

		$this->$imageFunction();
		exit;

		return true;
	}

	/**
	 * Checks if a CAPTCHA response is valid
	 *
	 * @return boolean True if valid
	 */
	private function _confirm($word, $instanceNo='')
	{
		$currentSession = JFactory::getSession();

		$securiy_code = $currentSession->get('securiy_code' . $instanceNo);

		if ($word == $securiy_code  &&  ($word != ''))
		{
			return true;
		}

		return false;
	}

	/**
	 * Checks for a CAPTCHA response and Calls the CAPTCHA validity check
	 *
	 * @return boolean True if valid CAPTCHA response
	 */
	public function onValidateCaptcha()
	{
		$imgCatchaTxt     = strtolower(JRequest::getVar('imgCatchaTxt', ''));
		$imgCatchaTxtInst = JRequest::getVar('imgCatchaTxtInst', '');

		$option = JRequest::getVar('option');
		$task   = JRequest::getVar('task');

		if ($imgCatchaTxtInst == '' || $imgCatchaTxt == '')
		{
			return false;
		}

		if (isset($_REQUEST['task']) && $task != 'logout'
		 && isset($_REQUEST['imgCatchaTxt'])
		 && !$this->_confirm($imgCatchaTxt, $imgCatchaTxtInst))
		{
			return false;
		}

		return true;
	}

	/**
	 * Displays a form field and image
	 *
	 * @return string
	 */
	private function _getCapthcaHtml()
	{
		if (!isset($GLOBALS['totalCaptchas']))
		{
			$GLOBALS['totalCaptchas'] = -1;
		}

		$GLOBALS['totalCaptchas']++;

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'display'
			)
		);
		$view->task = JRequest::getVar('task', '');
		$view->option = JRequest::getVar('option', '');
		$view->total = $GLOBALS['totalCaptchas'];

		return $view->loadTemplate();
	}

	/**
	 * Sets some internal variables
	 *
	 * @return void
	 */
	private function _setColors()
	{
		$this->_bgColor   = $this->params->get('bgColor', $this->_bgColor);
		$this->_textColor = $this->params->get('textColor', $this->_textColor);
	}

	/**
	 * Creates a distorted image
	 *
	 * @return void
	 */
	private function _createImageAdv()
	{
		$alphabet = "0123456789abcdefghijklmnopqrstuvwxyz";
		$allowed_symbols = "23456789abcdeghkmnpqsuvxyz";

		$length = 5;
		$width = 120;
		$height = 60;
		$fluctuation_amplitude = 5;
		$no_spaces = false;

		$this->_setColors();

		$foreground_color = $this->_hexToRgb($this->_textColor);
		$background_color = $this->_hexToRgb($this->_bgColor);

		$jpeg_quality = 90;

		$alphabet_length = strlen($alphabet);

		do
		{
			// generating random keystring
			while (true)
			{
				$this->keystring = '';
				for ($i=0; $i<$length; $i++)
				{
					$this->keystring .= $allowed_symbols{mt_rand(0,strlen($allowed_symbols)-1)};
				}
				if (!preg_match('/cp|cb|ck|c6|c9|rn|rm|mm|co|do|cl|db|qp|qb|dp|ww/', $this->keystring))
				{
					break;
				}
			}

			$font_file = $font_file = dirname(__FILE__) . DS . 'adlibBT.png';
			$font = imagecreatefrompng($font_file);
			imagealphablending($font, true);
			$fontfile_width = imagesx($font);
			$fontfile_height = imagesy($font)-1;
			$font_metrics = array();
			$symbol = 0;
			$reading_symbol = false;

			// loading font
			for ($i=0; $i<$fontfile_width && $symbol<$alphabet_length; $i++)
			{
				$transparent = (imagecolorat($font, $i, 0) >> 24) == 127;

				if (!$reading_symbol && !$transparent)
				{
					$font_metrics[$alphabet{$symbol}] = array('start'=>$i);
					$reading_symbol = true;
					continue;
				}

				if ($reading_symbol && $transparent)
				{
					$font_metrics[$alphabet{$symbol}]['end'] = $i;
					$reading_symbol = false;
					$symbol++;
					continue;
				}
			}

			$img = imagecreatetruecolor($width, $height);
			imagealphablending($img, true);
			$white = imagecolorallocate($img, 255, 255, 255);
			$black = imagecolorallocate($img, 0, 0, 0);

			imagefilledrectangle($img, 0, 0, $width-1, $height-1, $white);

			// draw text
			$x = 1;
			for ($i=0; $i<$length; $i++)
			{
				$m = $font_metrics[$this->keystring{$i}];

				$y = mt_rand(-$fluctuation_amplitude, $fluctuation_amplitude)+($height-$fontfile_height)/2+2;

				if ($no_spaces)
				{
					$shift = 0;
					if ($i>0)
					{
						$shift = 10000;
						for ($sy=7; $sy<$fontfile_height-20; $sy+=1)
						{
							for ($sx=$m['start']-1; $sx<$m['end']; $sx+=1)
							{
								$rgb = imagecolorat($font, $sx, $sy);
								$opacity = $rgb>>24;
								if ($opacity < 127)
								{
									$left = $sx-$m['start']+$x;
									$py = $sy+$y;
									if ($py > $height)
									{
										break;
									}
									for ($px=min($left,$width-1); $px>$left-12 && $px>=0; $px-=1)
									{
										$color = imagecolorat($img, $px, $py) & 0xff;
										if ($color+$opacity < 190)
										{
											if ($shift > $left-$px)
											{
												$shift = $left-$px;
											}
											break;
										}
									}
									break;
								}
							}
						}
						if ($shift == 10000)
						{
							$shift = mt_rand(4,6);
						}

					}
				}
				else
				{
					$shift = 1;
				}
				imagecopy($img, $font, $x-$shift, $y, $m['start'], 1, $m['end']-$m['start'], $fontfile_height);
				$x += $m['end']-$m['start']-$shift;
			}
		}
		while ($x >= $width-10); // while not fit in canvas

		$center = $x/2;

		$img2 = imagecreatetruecolor($width, $height);
		$foreground = imagecolorallocate($img2, $foreground_color[0], $foreground_color[1], $foreground_color[2]);
		$background = imagecolorallocate($img2, $background_color[0], $background_color[1], $background_color[2]);
		imagefilledrectangle($img2, 0, 0, $width-1, $height-1, $background);
		imagefilledrectangle($img2, 0, $height, $width-1, $height+12, $foreground);

		// periods
		$rand1 = mt_rand(750000,1200000)/10000000;
		$rand2 = mt_rand(750000,1200000)/10000000;
		$rand3 = mt_rand(750000,1200000)/10000000;
		$rand4 = mt_rand(750000,1200000)/10000000;
		// phases
		$rand5 = mt_rand(0,31415926)/10000000;
		$rand6 = mt_rand(0,31415926)/10000000;
		$rand7 = mt_rand(0,31415926)/10000000;
		$rand8 = mt_rand(0,31415926)/10000000;
		// amplitudes
		$rand9 = mt_rand(330,420)/110;
		$rand10 = mt_rand(330,450)/110;

		// wave distortion
		for ($x=0; $x<$width; $x++)
		{
			for ($y=0; $y<$height; $y++)
			{
				$sx = $x+(sin($x*$rand1+$rand5)+sin($y*$rand3+$rand6))*$rand9-$width/2+$center+1;
				$sy = $y+(sin($x*$rand2+$rand7)+sin($y*$rand4+$rand8))*$rand10;

				if ($sx<0 || $sy<0 || $sx>=$width-1 || $sy>=$height-1)
				{
					continue;
				}
				else
				{
					$color = imagecolorat($img, $sx, $sy) & 0xFF;
					$color_x = imagecolorat($img, $sx+1, $sy) & 0xFF;
					$color_y = imagecolorat($img, $sx, $sy+1) & 0xFF;
					$color_xy = imagecolorat($img, $sx+1, $sy+1) & 0xFF;
				}

				if ($color==255 && $color_x==255 && $color_y==255 && $color_xy==255)
				{
					continue;
				}
				else if ($color==0 && $color_x==0 && $color_y==0 && $color_xy==0)
				{
					$newred = $foreground_color[0];
					$newgreen = $foreground_color[1];
					$newblue = $foreground_color[2];
				}
				else
				{
					$frsx = $sx-floor($sx);
					$frsy = $sy-floor($sy);
					$frsx1 = 1-$frsx;
					$frsy1 = 1-$frsy;

					$newcolor = (
						$color*$frsx1*$frsy1+
						$color_x*$frsx*$frsy1+
						$color_y*$frsx1*$frsy+
						$color_xy*$frsx*$frsy
					);

					if ($newcolor > 255)
					{
						$newcolor = 255;
					}
					$newcolor = $newcolor/255;
					$newcolor0 = 1-$newcolor;

					$newred = $newcolor0*$foreground_color[0]+$newcolor*$background_color[0];
					$newgreen = $newcolor0*$foreground_color[1]+$newcolor*$background_color[1];
					$newblue = $newcolor0*$foreground_color[2]+$newcolor*$background_color[2];
				}

				imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $newred, $newgreen, $newblue));
			}
		}

		ob_clean();
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', FALSE);
		header('Pragma: no-cache');

		if (function_exists('imagejpeg'))
		{
			header('Content-Type: image/jpeg');
			imagejpeg($img2, null, $jpeg_quality);
		}
		else if (function_exists('imagegif'))
		{
			header('Content-Type: image/gif');
			imagegif($img2);
		}
		else if (function_exists('imagepng'))
		{
			header('Content-Type: image/x-png');
			imagepng($img2);
		}
		$security_code = strtolower(str_replace(' ', '', trim($this->keystring)));

		//Set the session to store the security code
		$currentSession = JFactory::getSession();
		$currentSession->set('securiy_code' . (JRequest::getVar('instanceNo') + 0), $security_code);
		$width = 120;
		$height = 40;
	}

	/**
	 * Creates a plain letter image
	 *
	 * @return void
	 */
	private function _createImagePlain()
	{
		// Let's generate a totally random string using md5
		$md5_hash = md5(rand(0,999));

		// We don't need a 32 character long string so we trim it down to 5
		$security_code = str_replace(array("0","O","o"), array("p"), substr($md5_hash, 15, 5));

		// Set the session to store the security code
		$currentSession = JFactory::getSession();
		$currentSession->set('securiy_code' . (JRequest::getVar('instanceNo') + 0), $security_code);

		$width = 120;
		$height = 40;
		$image = imagecreate($width, $height);
		$this->_setColors();
		$foreground_color = $this->_hexToRgb($this->_textColor);
		$background_color = $this->_hexToRgb($this->_bgColor);

		// We are making three colors, white, black and gray
		$white = imagecolorallocate($image, $foreground_color[0], $foreground_color[1], $foreground_color[2]);
		$black = imagecolorallocate($image, $background_color[0], $background_color[1], $background_color[2]);
		$grey = imagecolorallocate($image, 204, 204, 204);

		// Make the background black
		imagefill($image, 0, 0, $black);

		$size = 10;
		$this->ly = (int)(2.4 * $size);
		$x = 20;
		for ($i=0; $i<strlen($security_code); $i++)
		{
			$angle = rand(-45,45);
			$y = intval(rand((int)($size * 1.5), (int)($this->ly - ($size / 7))));

			@imagettftext($image, $size, $angle, $x + (int)($size / 15), $y, $white, dirname(__FILE__) . DS . 'adlibBT.TTF', $security_code[$i]);
			$x += ($size *2);
		}

		ob_clean();
		header('Content-type: image/png');
		imagepng($image);
	}

	/**
	 * Converts HEX color to RGB
	 *
	 * @return string
	 */
	private function _hexToRgb($hex)
	{
		$hex = preg_replace("/#/", '', $hex);
		$color = array();

		if (strlen($hex) == 3)
		{
			$color['r'] = hexdec(substr($hex, 0, 1) . $r);
			$color['g'] = hexdec(substr($hex, 1, 1) . $g);
			$color['b'] = hexdec(substr($hex, 2, 1) . $b);
		}
		else if (strlen($hex) == 6)
		{
			$color['r'] = hexdec(substr($hex, 0, 2));
			$color['g'] = hexdec(substr($hex, 2, 2));
			$color['b'] = hexdec(substr($hex, 4, 2));
		}

		return array_values($color);
	}

	/**
	 * Converts RGB color to HEX
	 *
	 * @return string
	 */
	private function _rgbToHex($r, $g, $b)
	{
		$hex  = '#';
		$hex .= dechex($r);
		$hex .= dechex($g);
		$hex .= dechex($b);

		return $hex;
	}
}