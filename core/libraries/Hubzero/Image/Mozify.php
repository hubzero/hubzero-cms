<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Image;

use Hubzero\Base\Obj;

/**
 * Helper class for Converting Image to Table Mosaic
 */
class Mozify extends Obj
{
	/**
	 * Image alt text
	 *
	 * @var  string
	 */
	private $imageAlt        = null;

	/**
	 * Image URL
	 *
	 * @var  string
	 */
	private $imageUrl        = null;

	/**
	 * Image width
	 *
	 * @var  integer
	 */
	private $imageWidth      = null;

	/**
	 * Image height
	 *
	 * @var  integer
	 */
	private $imageHeight     = null;

	/**
	 * Mosaix size
	 *
	 * @var  integer
	 */
	private $mosaicSize      = 5;

	/**
	 * CSS class prefix
	 *
	 * @var  string
	 */
	private $cssClassName    = 'hz_mozify_';

	/**
	 * Internal coutner
	 *
	 * @var  integer
	 */
	private $counter         = 1;

	/**
	 * Image Mozifyier Constructor
	 *
	 * @param   array  $config  Array of config values
	 * @return  void
	 */
	public function __construct($config = array())
	{
		//we must have image url to do anything
		if (isset($config['imageUrl']) && $config['imageUrl'] != '')
		{
			//set image url
			$this->setImageUrl($config['imageUrl']);

			//set alt text if we have it
			if (isset($config['imageAlt']))
			{
				$this->setImageAlt($config['imageAlt']);
			}

			//set mosaic size if we have it
			if (isset($config['mosaicSize']))
			{
				$this->setMosaicSize($config['mosaicSize']);
			}

			//set mosaic size if we have it
			if (isset($config['cssClassName']))
			{
				$this->setCssClassName($config['cssClassName']);
			}
		}
	}

	/**
	 * Mozify!
	 *
	 * @return  string
	 */
	public function mozify()
	{
		if ($this->imageUrl == '')
		{
			return;
		}

		$html  = $this->_mozifyCss() . PHP_EOL;
		$html .= $this->_mozifyStartMsoHack() . PHP_EOL;
		$html .= $this->_mozifyImageReplacement() . PHP_EOL;
		$html .= $this->_mozifyMosaic() . PHP_EOL;
		$html .= $this->_mozifyEndWrapper() . PHP_EOL;
		$html .= $this->_mozifyEndMsoHack() . PHP_EOL;

		$this->counter++;

		return $html;
	}

	/**
	 * Convert an image to a mosaic
	 *
	 * @return  string
	 */
	public function mosaic()
	{
		if ($this->imageUrl == '')
		{
			return;
		}

		return $this->_mozifyMosaic();
	}

	/**
	 * Accessor Method to get Image Url
	 *
	 * @return  string
	 */
	public function getImageUrl()
	{
		return $this->imageUrl;
	}

	/**
	 * Mutator Method to set Image Url
	 *
	 * @param   string  $imageUrl
	 * @return  void
	 */
	public function setImageUrl($imageUrl = '')
	{
		$this->imageUrl = $imageUrl;
		$imageSizes = @getimagesize($this->imageUrl);
		if ($imageSizes)
		{
			list($this->imageWidth, $this->imageHeight) = $imageSizes;
		}
		else
		{
			$this->setError('Unable to get details of image');
		}
	}

	/**
	 * Accessor Method to get Image Alt Text
	 *
	 * @return  string
	 */
	public function getImageAlt()
	{
		return $this->imageAlt;
	}

	/**
	 * Mutator Method to set Image Alt Text
	 *
	 * @param   string  $imageAlt
	 * @return  void
	 */
	public function setImageAlt($imageAlt = '')
	{
		$this->imageAlt = $imageAlt;
	}

	/**
	 * Accessor Method to get Mosaic Size
	 *
	 * @return  string
	 */
	public function getMosaicSize()
	{
		return $this->mosaicSize;
	}

	/**
	 * Mutator Method to set Mosaic Size
	 *
	 * @param   string  $mosaicSize
	 * @return  void
	 */
	public function setMosaicSize($mosaicSize = '')
	{
		$this->mosaicSize = $mosaicSize;
	}

	/**
	 * Accessor Method to get CSS Class Name
	 *
	 * @return  string
	 */
	public function getCssClassName()
	{
		return $this->cssClassName . $this->counter;
	}

	/**
	 * Mutator Method to set CSS Class Name
	 *
	 * @param   string  $cssClassName
	 * @return  void
	 */
	public function setCssClassName($cssClassName = '')
	{
		$this->cssClassName = $cssClassName;
	}

	/**
	 * Generate CSS needed for mozify
	 *
	 * @return  string
	 */
	private function _mozifyCss()
	{
		//get the class
		$class = $this->getCssClassName();

		//build css needed for mozify
		$css  = '<style type="text/css">';
		$css .= '.ExternalClass .ecxhm1_3 { width:' . $this->imageWidth . 'px !important; height:' . $this->imageHeight . 'px !important; float:none !important }';
		$css .= '.ExternalClass .ecxhm2_3 { display:none !important }';
		$css .= '.' . $class . ' td b { width:1px; height:1px; font-size:1px }';
		$css .= '.' . $class . '{ -webkit-text-size-adjust: none }';
		$css .= '</style>';
		return $css;
	}

	/**
	 * Output the start of the MSO hack
	 *
	 * @return  string
	 */
	private function _mozifyStartMsoHack()
	{
		//get the class
		$class = $this->getCssClassName();

		//build mso hack
		$mosHack  = '<!--[if mso]><style>.' . $class . '{ display:none !important }</style><table cellpadding="0" cellspacing="0" style="display:block;float:none;" align=""><tr><td>';
		$mosHack .= '<img src="' . $this->imageUrl . '" alt="'.$this->imageAlt.'" style="display:block;" moz="3" valid="true" height="' . $this->imageHeight . '" width="' . $this->imageWidth . '"></td></tr></table><style type="text/css">/*<![endif]-->';
		return $mosHack;
	}

	/**
	 * Output the end of the MSO hack
	 *
	 * @return  string
	 */
	private function _mozifyEndMsoHack()
	{
		$msoHack = '<!--[if mso]>*/</style><![endif]-->';
		return $msoHack;
	}

	/**
	 * Image replacement
	 *
	 * @return  string
	 */
	private function _mozifyImageReplacement()
	{
		//get the class
		$class = $this->getCssClassName();

		//build replacement html
		$replacement  = '<table width="' . $this->imageWidth . '" cellspacing="0" cellpadding="0" border="0" align="" moz="3" style="display:block;float:none" class="' . $class . '">';
		$replacement .= '<tbody>';
		$replacement .= '<tr>';
		$replacement .= '<td style="padding:0px 0px 0px 0px;" class="' . $class . '">';
		$replacement .= '<div class="' . $class . '" style="width:0px;height:0px;overflow:visible;float:left;position:absolute">';
		$replacement .= '<table cellspacing="0" cellpadding="0" class="' . $class . '">';
		$replacement .= '<tbody>';
		$replacement .= '<tr>';
		$replacement .= '<td background="' . $this->imageUrl . '"><div class="' . $class . '" style="width:' . $this->imageWidth . 'px;height:' . $this->imageHeight . 'px"></div></td>';
		$replacement .= '</tr>';
		$replacement .= '</tbody>';
		$replacement .= '</table>';
		$replacement .= '</div>';
		return $replacement;
	}

	/**
	 * Create a mosaic
	 *
	 * @return  string
	 */
	private function _mozifyMosaic()
	{
		//get image resource
		$resource = imagecreatefromstring(file_get_contents($this->imageUrl));

		//get the class
		$class = $this->getCssClassName();

		//build mosaic html
		$mosaic  = '<table width="' . $this->imageWidth . '" height="' . $this->imageHeight . '" cellspacing="0" cellpadding="0" border="0" bgcolor="#fefefe" class="' . $class . '">';
		$mosaic .= '<tbody>';
		for ($y = 0; $y < $this->imageHeight; $y+=$this->mosaicSize)
		{
			$mosaic .= '<tr>';
			for ($x = 0; $x < $this->imageWidth; $x+=$this->mosaicSize)
			{
				$color = imagecolorat($resource, $x, $y);
				$rgba = imagecolorsforindex($resource, $color);
				//$rgba['alpha'] = $rgba['alpha'];
				$color_string = $this->_rgb2hex($rgba);
				$mosaic .= '<td width="' . $this->mosaicSize . '" bgcolor="' . $color_string . '"><b></b></td>' . PHP_EOL;
			}
			$mosaic .= '</tr>' . PHP_EOL;
		}
		$mosaic .= '</tbody>';
		$mosaic .= '</table>';
		return $mosaic;
	}

	/**
	 * Cinvert an RGB value to hex
	 *
	 * @param   array   $rgb
	 * @return  string
	 */
	private function _rgb2hex(array $rgb)
	{
		if (isset($rgb['alpha']))
		{
			unset($rgb['alpha']);
		}
		$out = "";
		foreach ($rgb as $c)
		{
			$hex = base_convert($c, 10, 16);
			$out .= ($c < 16) ? ("0" . $hex) : $hex;
		}
		return '#' . strtoupper($out);
	}

	/**
	 * Output the end of the wrapper
	 *
	 * @return  string
	 */
	private function _mozifyEndWrapper()
	{
		$wrapper  = '</td>';
		$wrapper .= '</tr>';
		$wrapper .= '</tbody>';
		$wrapper .= '</table>';
		return $wrapper;
	}
}
