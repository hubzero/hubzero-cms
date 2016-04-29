<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Support plugin class for CAPTCHA
 */
class plgSupportCaptcha extends \Hubzero\Plugin\Plugin
{
	/**
	 * If the user is a verified, logged-in user
	 * @var	boolean
	 */
	private $_verified = false;

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
	 * Pseudo-randomized sequences for lists
	 * @var	array
	 */
	private $_sequence = array(
		array(0, 2, 4, 1, 3),
		array(1, 2, 0, 3, 4),
		array(2, 0, 4, 1, 3),
		array(3, 2, 1, 0, 4),
		array(4, 1, 2, 3, 0),
		array(3, 4, 2, 1, 0),
		array(0, 3, 4, 2, 1),
		array(2, 4, 1, 3, 0),
		array(4, 2, 3, 0, 1),
		array(2, 0, 3, 1, 4)
	);

	/**
	 * Text-based CAPTCHA questions
	 * @var	array
	 */
	private $_questions = array(
		array(
			'type'     => 'math',
			'question' => 'PLG_SUPPORT_TEXTCAPTCHA_MATH',
			'answer'   => '@dynamic'
		),
		array(
			'type'     => 'list',
			'question' => 'PLG_SUPPORT_TEXTCAPTCHA_WHAT_DAY_AFTER_TUESDAY',
			'options'  => array(
				'WEDNESDAY',
				'MONDAY',
				'THURSDAY',
				'TUESDAY',
				'FRIDAY'
			),
			'answer'   => 'WEDNESDAY'
		),
		array(
			'type'     => 'text',
			'question' => 'PLG_SUPPORT_TEXTCAPTCHA_EYE_ANKLE_ARM',
			'answer'   => 'PLG_SUPPORT_TEXTCAPTCHA_EYE_ANKLE_ARM_ANSWER'
		),
		array(
			'type'     => 'list',
			'question' => 'PLG_SUPPORT_TEXTCAPTCHA_SUN_RISES',
			'options'  => array(
				'PLG_SUPPORT_TEXTCAPTCHA_SUN_RISES_EAST',
				'PLG_SUPPORT_TEXTCAPTCHA_SUN_RISES_NIGHT',
				'PLG_SUPPORT_TEXTCAPTCHA_SUN_RISES_SOUTH',
				'PLG_SUPPORT_TEXTCAPTCHA_SUN_RISES_WEST',
				'PLG_SUPPORT_TEXTCAPTCHA_SUN_RISES_CHICKENS'
			),
			'answer'   => 'PLG_SUPPORT_TEXTCAPTCHA_SUN_RISES_EAST'
		),
		array(
			'type'     => 'list',
			'question' => 'PLG_SUPPORT_TEXTCAPTCHA_PYRAMIDS',
			'options'  => array(
				'PLG_SUPPORT_TEXTCAPTCHA_PYRAMIDS_EGYPT',
				'PLG_SUPPORT_TEXTCAPTCHA_PYRAMIDS_ROCKS',
				'PLG_SUPPORT_TEXTCAPTCHA_PYRAMIDS_JAPAN',
				'PLG_SUPPORT_TEXTCAPTCHA_PYRAMIDS_NORTH_POLE',
				'PLG_SUPPORT_TEXTCAPTCHA_PYRAMIDS_CANADA'
			),
			'answer'   => 'PLG_SUPPORT_TEXTCAPTCHA_PYRAMIDS_EGYPT'
		),
		array(
			'type'     => 'list',
			'question' => 'PLG_SUPPORT_TEXTCAPTCHA_MOON',
			'options'  => array(
				'PLG_SUPPORT_TEXTCAPTCHA_MOON_SKY',
				'PLG_SUPPORT_TEXTCAPTCHA_MOON_AUTOMOBILES',
				'PLG_SUPPORT_TEXTCAPTCHA_MOON_CLOSET',
				'PLG_SUPPORT_TEXTCAPTCHA_MOON_MOVIES',
				'PLG_SUPPORT_TEXTCAPTCHA_MOON_DRAWER'
			),
			'answer'   => 'PLG_SUPPORT_TEXTCAPTCHA_MOON_SKY'
		),
		array(
			'type'     => 'text',
			'question' => 'PLG_SUPPORT_TEXTCAPTCHA_ICE',
			'answer'   => 'PLG_SUPPORT_TEXTCAPTCHA_ICE_ANSWER'
		),
		array(
			'type'     => 'text',
			'question' => 'PLG_SUPPORT_TEXTCAPTCHA_BODY_PART',
			'answer'   => 'PLG_SUPPORT_TEXTCAPTCHA_BODY_PART_ANSWER'
		),
		array(
			'type'     => 'list',
			'question' => 'PLG_SUPPORT_TEXTCAPTCHA_SHARKS',
			'options'  => array(
				'PLG_SUPPORT_TEXTCAPTCHA_SHARKS_SEA',
				'PLG_SUPPORT_TEXTCAPTCHA_SHARKS_APRICOTS',
				'PLG_SUPPORT_TEXTCAPTCHA_SHARKS_GREENHOUSES',
				'PLG_SUPPORT_TEXTCAPTCHA_SHARKS_BARRELS',
				'PLG_SUPPORT_TEXTCAPTCHA_SHARKS_HOUSE'
			),
			'answer'   => 'PLG_SUPPORT_TEXTCAPTCHA_SHARKS_SEA'
		),
		array(
			'type'     => 'text',
			'question' => 'PLG_SUPPORT_TEXTCAPTCHA_NIGHT',
			'answer'   => 'PLG_SUPPORT_TEXTCAPTCHA_NIGHT_ANSWER'
		),
		array(
			'type'     => 'text',
			'question' => 'PLG_SUPPORT_TEXTCAPTCHA_COLOR',
			'answer'   => 'PLG_SUPPORT_TEXTCAPTCHA_COLOR_ANSWER'
		),
		array(
			'type'     => 'text',
			'question' => 'PLG_SUPPORT_TEXTCAPTCHA_CAPITAL_OF_FRANCE',
			'answer'   => 'PLG_SUPPORT_TEXTCAPTCHA_CAPITAL_OF_FRANCE_ANSWER'
		),
		array(
			'type'     => 'list',
			'question' => 'PLG_SUPPORT_TEXTCAPTCHA_MICKEY',
			'options'  => array(
				'PLG_SUPPORT_TEXTCAPTCHA_MICKEY_MOUSE',
				'PLG_SUPPORT_TEXTCAPTCHA_MICKEY_TEAPOT',
				'PLG_SUPPORT_TEXTCAPTCHA_MICKEY_SUITCASE',
				'PLG_SUPPORT_TEXTCAPTCHA_MICKEY_PENGUIN',
				'PLG_SUPPORT_TEXTCAPTCHA_MICKEY_CHICKEN'
			),
			'answer'   => 'PLG_SUPPORT_TEXTCAPTCHA_MICKEY_MOUSE'
		)
	);

	/**
	 * Constructor
	 *
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function plgSupportCaptcha(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();

		if (!User::isGuest())
		{
			if (User::get('activation') == 1 || User::get('activation') == 3)
			{
				$this->_verified = true;
			}
		}
	}

	/**
	 * Displays either an image or text-based CAPTCHA for module forms
	 *
	 * @return string
	 */
	public function onGetModuleCaptcha()
	{
		return $this->onGetCaptcha('mod');
	}

	/**
	 * Displays either an image or text-based CAPTCHA for component forms
	 *
	 * @return string
	 */
	public function onGetComponentCaptcha()
	{
		return $this->onGetCaptcha('com');
	}

	/**
	 * Displays either an image or text-based CAPTCHA
	 *
	 * @return string
	 */
	public function onGetCaptcha($ext='com')
	{
		if ($this->_verified)
		{
			return;
		}

		$ext = strtolower($ext);

		if ($ext != 'com' && $ext != 'mod')
		{
			return;
		}

		$type = $this->params->get($ext . 'Captcha', 'image');
		$type = '_generate' . ucfirst(strtolower($type)) . 'Captcha';

		// Return the HTML
		return $this->$type();
	}

	/**
	 * Checks for a CAPTCHA response and Calls the CAPTCHA validity check
	 *
	 * @return boolean True if valid CAPTCHA response
	 */
	public function onValidateCaptcha()
	{
		if ($this->_verified)
		{
			return true;
		}

		$captcha = Request::getVar('captcha', array());
		$task = Request::getVar('task', '');

		if (!isset($captcha['instance']) || !isset($captcha['answer']))
		{
			return false;
		}

		if (!isset($captcha['krhash']))
		{
			$captcha['krhash'] = null;
		}

		if ($captcha['instance'] < 0 || trim($captcha['answer']) == '')
		{
			return false;
		}

		if ($task && $task != 'logout'
		 && $this->_confirm(trim($captcha['answer']), trim($captcha['instance']), trim($captcha['krhash'])))
		{
			return true;
		}

		return false;
	}

	/**
	 * Randomly chooses a text-based CAPTCHA to display
	 *
	 * @return string
	 */
	private function _generateTextCaptcha()
	{
		if (!isset($GLOBALS['totalCaptchas']))
		{
			$GLOBALS['totalCaptchas'] = -1;
		}

		$GLOBALS['totalCaptchas']++;

		$nqs = count($this->_questions);
		$use = rand(0, $nqs-1);
		$question = $this->_questions[$use];

		$html  = '<label for="captcha-answer">' . "\n";
		$html .= Lang::txt('PLG_SUPPORT_TEXTCAPTCHA_PLEASE_ANSWER') . ' <span class="required">' . Lang::txt('PLG_SUPPORT_TEXTCAPTCHA_REQUIRED') . '</span><br />' . "\n";
		switch ($question['type'])
		{
			case 'math':
				// Generate a CAPTCHA
				$problem = array();
				$problem['operand1'] = rand(0, 10);
				$problem['operand2'] = rand(0, 10);
				$key = $problem['operand1'] + $problem['operand2'];

				$html .= "\t" . Lang::txt($question['question'], $problem['operand1'], $problem['operand2']);
				$html .= "\t" . '<input type="text" name="captcha[answer]" id="captcha-answer" value="" size="3" class="option" />' . "\n";
			break;

			case 'list':
				$key = strtolower(Lang::txt($question['answer']));
				$numseq = count($this->_sequence);
				$set = rand(0, $numseq-1);

				$html .= "\t" . Lang::txt($question['question']) . "\n";
				$html .= "\t" . '<select name="captcha[answer]" id="captcha-answer">' . "\n";
				$html .= "\t\t" . '<option value="">' . Lang::txt('PLG_SUPPORT_TEXTCAPTCHA_SELECT_ANSWER') . '</option>' . "\n";
				foreach ($this->_sequence[$set] as $row)
				{
					$html .= "\t\t" . '<option value="' . Lang::txt($question['options'][$row]) . '">' . Lang::txt($question['options'][$row]) . '</option>' . "\n";
				}
				$html .= "\t" . '</select>' . "\n";
			break;

			case 'text':
			default:
				$key = strtolower(Lang::txt($question['answer']));

				$html .= "\t" . Lang::txt($question['question']) . "\n";
				$html .= "\t" . '<input type="text" name="captcha[answer]" id="captcha-answer" value="" />' . "\n";
			break;
		}

		$html .= '</label>' . "\n";
		$html .= "\t" . '<input type="hidden" name="captcha[krhash]" id="captcha-krhash" value="' . $this->_generateHash($key, date('j')) . '" />' . "\n";
		//$html .= "\t" . '<input type="hidden" name="captcha[type]" id="captcha-type" value="text" />' . "\n";

		App::get('session')->set('securiy_code' . Request::getVar('instanceNo', $GLOBALS['totalCaptchas']), $this->_generateHash($key, date('j')));

		if ($this->_verified)
		{
			$html  = '<input type="hidden" name="captcha[answer]" id="captcha-answer" value="' . $key . '" />' . "\n";
		}

		$html .= '<input type="hidden" name="captcha[instance]" id="captcha-instance" value="' . $GLOBALS['totalCaptchas'] . '" />' . "\n";

		return $html;
	}

	/**
	 * Generates a hash
	 *
	 * @param  string $input Text to use as base for the hash
	 * @param  string $day   Day of the week
	 * @return string
	 */
	private function _generateHash($input, $day)
	{
		// Add date:
		$input .= Config::get('secret') . $day . date('ny');

		// Get MD5 and reverse it
		$enc = strrev(md5($input));

		// Get only a few chars out of the string
		$enc = substr($enc, 26, 1) . substr($enc, 10, 1) . substr($enc, 23, 1) . substr($enc, 3, 1) . substr($enc, 19, 1);

		return $enc;
	}

	/**
	 * Displays either a CAPTCHA image or form field
	 *
	 * @return string
	 */
	private function _generateImageCaptcha()
	{
		$showCaptcha = Request::getVar('showCaptcha', '');
		if ($showCaptcha)
		{
			return $this->_displayImage();
		}
		return $this->_getImageCaptchaHtml();
	}

	/**
	 * Displays a CAPTCHA image
	 *
	 * @return boolean
	 */
	private function _displayImage()
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
	 * @param  string  $word       Supplied CAPTCHA response to check
	 * @param  string  $instanceNo CAPTCHA instance number
	 * @return boolean True if valid
	 */
	private function _confirm($word, $instanceNo='', $hash='')
	{
		$word = $this->_generateHash(strtolower($word), date('j'));

		$securiy_code = App::get('session')->get('securiy_code' . $instanceNo);

		if ($hash && $word == $hash)
		{
			return true;
		}
		else if ($word == $securiy_code)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Displays a form field and image
	 *
	 * @return string
	 */
	private function _getImageCaptchaHtml()
	{
		if (!isset($GLOBALS['totalCaptchas']))
		{
			$GLOBALS['totalCaptchas'] = -1;
		}

		$GLOBALS['totalCaptchas']++;

		$view = $this->view('default', 'image')
					->set('task', Request::getVar('task', ''))
					->set('controller', Request::getVar('controller', ''))
					->set('option', Request::getVar('option', ''))
					->set('total', $GLOBALS['totalCaptchas']);

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
		if (!isset($GLOBALS['totalCaptchas']))
		{
			$GLOBALS['totalCaptchas'] = -1;
		}

		$GLOBALS['totalCaptchas']++;

		$alphabet = "0123456789abcdefghijklmnopqrstuvwxyz";
		$allowed_symbols = "23456789abcdeghkmnpqsuvxyz";

		$length = 5;
		$width = 120;
		$height = 60;
		$fluctuation_amplitude = 5;
		$no_spaces = true;

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
					$this->keystring .= $allowed_symbols{mt_rand(0, strlen($allowed_symbols)-1)};
				}
				if (!preg_match('/cp|cb|ck|c6|c9|rn|rm|mm|co|do|cl|db|qp|qb|dp|ww/', $this->keystring))
				{
					break;
				}
			}

			$font_file = $font_file = __DIR__ . DS . 'assets' . DS . 'fonts' . DS . 'adlibBT.png';
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
										if ($color+$opacity < 190) {
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
			imagegif ($img2);
		}
		else if (function_exists('imagepng'))
		{
			header('Content-Type: image/x-png');
			imagepng($img2);
		}
		$security_code = $this->_generateHash($this->keystring, date('j'));

		//Set the session to store the security code
		App::get('session')->set('securiy_code' . Request::getVar('instanceNo', $GLOBALS['totalCaptchas']), $security_code);
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
		$security_code = str_replace(array("0", "O", "o"), array("p"), substr($md5_hash, 15, 5));
		$security_code = $this->_generateHash($security_code, date('j'));

		// Set the session to store the security code
		App::get('session')->set('securiy_code' . (Request::getVar('instanceNo') + 0), $security_code);

		$width  = 120;
		$height = 40;
		$image  = imagecreate($width, $height);
		$this->_setColors();
		$foreground_color = $this->_hexToRgb($this->_textColor);
		$background_color = $this->_hexToRgb($this->_bgColor);

		// We are making three colors, white, black and gray
		$white = imagecolorallocate($image, $foreground_color[0], $foreground_color[1], $foreground_color[2]);
		$black = imagecolorallocate($image, $background_color[0], $background_color[1], $background_color[2]);
		$grey  = imagecolorallocate($image, 204, 204, 204);

		// Make the background black
		imagefill($image, 0, 0, $black);

		$size = 10;
		$this->ly = (int)(2.4 * $size);
		$x = 20;
		for ($i=0; $i<strlen($security_code); $i++)
		{
			$angle = rand(-45,45);
			$y = intval(rand((int)($size * 1.5), (int)($this->ly - ($size / 7))));

			@imagettftext($image, $size, $angle, $x + (int)($size / 15), $y, $white, __DIR__ . DS . 'assets' . DS . 'fonts' . DS . 'adlibBT.TTF', $security_code[$i]);
			$x += ($size *2);
		}

		header('Content-type: image/png');
		imagepng($image);
	}

	/**
	 * Converts HEX color to RGB
	 *
	 * @param  string $hex Hex value to convert
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
	 * @param  string $r R color to convert
	 * @param  string $g G color to convert
	 * @param  string $b B color to convert
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
