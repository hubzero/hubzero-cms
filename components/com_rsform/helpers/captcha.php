<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class RSFormProCaptcha
{
	var $Size;
    var $Length;
    var $Type;
    var $CaptchaString;
    var $fontpath;
    var $fonts;
    var $data;

    function RSFormProCaptcha($componentId=0)
	{
		$this->data = RSFormProHelper::getComponentProperties($componentId);
		
		if (!isset($this->data['IMAGETYPE']))
			$this->data['IMAGETYPE'] = 'FREETYPE';
		if (!isset($this->data['LENGTH']))
			$this->data['LENGTH'] = 4;
		
		if ($this->data['IMAGETYPE'] == 'INVISIBLE')
			die();
		
		if (!function_exists('imagecreate'))
		{
			header('Location:'.JURI::root().'components/com_rsform/assets/images/nogd.gif');
			die();
		}
		
		header('Content-type: image/png');
		
	    $this->Length = $this->data['LENGTH'];
		$this->Size = is_numeric($this->data['SIZE']) && $this->data['SIZE'] > 0 ? $this->data['SIZE'] : 15;

	    $this->fontpath = JPATH_SITE.DS.'components'.DS.'com_rsform'.DS.'assets'.DS.'fonts';
	    $this->fonts    = $this->getFonts();
		
		if ($this->data['IMAGETYPE'] == 'FREETYPE')
		{
			if (!count($this->fonts))
			{
				$error = new RSFormProCaptchaError;
				$error->addError('No fonts available!');
				$error->displayError();
				die();
			}
		
			if (!function_exists('imagettftext'))
			{
				$error = new RSFormProCaptchaError;
				$error->addError('The function imagettftext does not exist.');
				$error->displayError();
				die();
			}
		}
		
	    $this->stringGenerate();
	    $this->makeCaptcha($componentId);
    }

    function getFonts()
	{
		jimport('joomla.filesystem.folder');
		return JFolder::files($this->fontpath, '\.ttf');
	}
	
    function getRandomFont()
	{
		return $this->fontpath.DS.$this->fonts[mt_rand(0, count($this->fonts) - 1)];
    }
    
	function stringGenerate()
	{
		if (!isset($this->data['TYPE']))
			$this->data['TYPE'] = 'ALPHANUMERIC';
			
    	switch ($this->data['TYPE'])
		{
    		case 'ALPHA': $CharPool = range('a','z'); break;
    		case 'NUMERIC': $CharPool = range('0','9'); break;
    		case 'ALPHANUMERIC': default: $CharPool = array_merge(range('0','9'),range('a','z')); break;
    	}
		$PoolLength = count($CharPool) - 1;

		for ($i = 0; $i < $this->Length; $i++)
			$this->CaptchaString .= $CharPool[mt_rand(0, $PoolLength)];
    }

    function makeCaptcha($componentId=0)
	{
		if (!isset($this->data['BACKGROUNDCOLOR']))
			$this->data['BACKGROUNDCOLOR'] = '#FFFFFF';
		if (!isset($this->data['TEXTCOLOR']))
			$this->data['TEXTCOLOR'] = '#000000';
			
		$imagelength = $this->Length * $this->Size + 10;
		$imageheight = $this->Size*1.6;
		$image       = imagecreate($imagelength, $imageheight);
		$usebgrcolor = sscanf($this->data['BACKGROUNDCOLOR'], '#%2x%2x%2x');
		$usestrcolor = sscanf($this->data['TEXTCOLOR'], '#%2x%2x%2x');

		$bgcolor     = imagecolorallocate($image, $usebgrcolor[0], $usebgrcolor[1], $usebgrcolor[2]);
		$stringcolor = imagecolorallocate($image, $usestrcolor[0], $usestrcolor[1], $usestrcolor[2]);

		if ($this->data['IMAGETYPE'] == 'FREETYPE')
			for ($i = 0; $i < strlen($this->CaptchaString); $i++)
			{
				imagettftext($image,$this->Size, mt_rand(-15,15), $i * $this->Size + 10,
						$imageheight/100*80,
						$stringcolor,
						$this->getRandomFont(),
						$this->CaptchaString{$i});
			}
		
		if ($this->data['IMAGETYPE'] == 'NOFREETYPE')
			imagestring($image, mt_rand(3,5), 10, 0,  $this->CaptchaString, $usestrcolor); 
		
		$this->addNoise($image, 2);
		imagepng($image);
		imagedestroy($image);
    }

	function addNoise(&$image, $runs = 30)
	{
		$w = imagesx($image);
		$h = imagesy($image);

		for ($n = 0; $n < $runs; $n++)
			for ($i = 1; $i <= $h; $i++)
			{
				$randcolor = imagecolorallocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
				imagesetpixel($image, mt_rand(1, $w), mt_rand(1, $h), $randcolor);
			}
    }
	
    function getCaptcha()
    {
		return $this->CaptchaString;
    }
}

class RSFormProCaptchaError
{
    var $errors = array();

    function addError($errormsg = '')
    {
        $this->errors[] = $errormsg;
    }

    function displayError()
    {
		$iheight     = count($this->errors) * 20 + 10;
		$iheight     = ($iheight < 70) ? 70 : $iheight;
		$image       = imagecreate(600, $iheight);
		$bgcolor     = imagecolorallocate($image, 255, 255, 255);
		$stringcolor = imagecolorallocate($image, 0, 0, 0);
		for ($i = 0; $i < count($this->errors); $i++)
		{
			$imx = ($i == 0) ? $i * 20 + 5 : $i * 20;
			imagestring($image, 5, 5, $imx, $this->errors[$i], $stringcolor);
		}
		imagepng($image);
		imagedestroy($image);
    }
}