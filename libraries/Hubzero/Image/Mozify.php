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

namespace Hubzero\Image;

/**
 * Helper class for Converting Image to Table Mosaic
 */
class Mozify
{
	private $imageAlt        = null;
	private $imageUrl        = null;
	private $imageWidth      = null;
	private $imageHeight     = null;
	private $mosaicSize      = 5;
	private $cssClassName    = 'hz_mozify_';
	private $counter         = 1;

	/**
	 * Image Mozifyier Constructor
	 *
	 * @param    $config    Array of config values
	 */
	public function __construct($config = array())
	{
		//we must have image url to do anything
		if (isset($config['imageUrl']) && $config['imageUrl'] != '')
		{
			//set image url
			$this->setImageUrl( $config['imageUrl'] );

			//set alt text if we have it
			if (isset($config['imageAlt']))
			{
				$this->setImageAlt( $config['imageAlt'] );
			}

			//set mosaic size if we have it
			if (isset($config['mosaicSize']))
			{
				$this->setMosaicSize( $config['mosaicSize'] );
			}

			//set mosaic size if we have it
			if (isset($config['cssClassName']))
			{
				$this->setCssClassName( $config['cssClassName'] );
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

		//build return html
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
	 */
	public function getImageUrl()
	{
		return $this->imageUrl;
	}

	/**
	 * Mutator Method to set Image Url
	 */
	public function setImageUrl($imageUrl = '')
	{
		$this->imageUrl = $imageUrl;
		list($this->imageWidth, $this->imageHeight) = getimagesize($this->imageUrl);
	}

	/**
	 * Accessor Method to get Image Alt Text
	 */
	public function getImageAlt()
	{
		return $this->imageAlt;
	}

	/**
	 * Mutator Method to set Image Alt Text
	 */
	public function setImageAlt($imageAlt = '')
	{
		$this->imageAlt = $imageAlt;
	}

	/**
	 * Accessor Method to get Mosaic Size
	 */
	public function getMosaicSize()
	{
		return $this->mosaicSize;
	}

	/**
	 * Mutator Method to set Mosaic Size
	 */
	public function setMosaicSize($mosaicSize = '')
	{
		$this->mosaicSize = $mosaicSize;
	}

	/**
	 * Accessor Method to get CSS Class Name
	 */
	public function getCssClassName()
	{
		return $this->cssClassName . $this->counter;
	}

	/**
	 * Mutator Method to set CSS Class Name
	 *
	 * @param  string $cssClassName
	 * @return void
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
		$mosHack = '<!--[if mso]><style>.' . $class . '{ display:none !important }</style><table cellpadding="0" cellspacing="0" style="display:block;float:none;" align=""><tr><td>';
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
		$replacement = '<table width="' . $this->imageWidth . '" cellspacing="0" cellpadding="0" border="0" align="" moz="3" style="display:block;float:none" class="' . $class . '">';
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
		for ( $y = 0; $y < $this->imageHeight; $y+=$this->mosaicSize)
		{
			$mosaic .= '<tr>';
			for ($x = 0; $x < $this->imageWidth; $x+=$this->mosaicSize)
			{
				$color = imagecolorat($resource, $x, $y);
				$rgba = imagecolorsforindex($resource, $color);
				//$rgba['alpha'] = $rgba['alpha'];
				$color_string = $this->_rgb2hex( $rgba );
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