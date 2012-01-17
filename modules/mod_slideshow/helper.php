<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'modSlideshow'
 * 
 * Long description (if any) ...
 */
class modSlideshow
{

	/**
	 * Description for 'homedir'
	 * 
	 * @var string
	 */
	public $homedir = 'site/slideshow';

	/**
	 * Description for 'attributes'
	 * 
	 * @var array
	 */
	private $attributes = array();

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $params Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( $params )
	{
		$this->params = $params;
	}

	/**
	 * Short description for '__set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}

	/**
	 * Short description for '__get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}

	/**
	 * Short description for 'display'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function display()
	{
		$params = $this->params;
		ximport('Hubzero_Document');

		$image_dir = ($params->get('image_dir')) ? $params->get('image_dir') : 'site/slideshow' ;
		$alias = ($params->get('alias')) ? $params->get('alias') : '' ;
		$height = ($params->get('height')) ? $params->get('height') : '350' ;
		$width = ($params->get('width')) ? $params->get('width') : '600' ;
		$timerdelay = ($params->get('timerdelay')) ? $params->get('timerdelay') : '10000' ;
		$transitiontype = ($params->get('transitiontype')) ? $params->get('transitiontype') : 'fade' ;
		$random = ($params->get('random')) ? $params->get('random') : 0 ;
		$xmlPrefix = "slideshow-data";
		$noflash = ($params->get('stype')) ? $params->get('stype') : 0 ;
		$noflash_link = ($params->get('noflash_link')) ? $params->get('noflash_link') : '' ;

		$swffile = rtrim( Hubzero_Document::getModuleImage('mod_slideshow', 'banner'.$width.'x'.$height.'.swf'), '.swf');

		//$swffile = 'modules'.DS.'mod_slideshow'.DS.'images'.DS.'banner600x'.$height.'.swf';
		//Make sure the path doesn't start with a slash
		if (substr($image_dir, 0, 1) == DS) {
			$image_dir = substr($image_dir, 1, strlen($image_dir));
		}
		// Make sure the path doesn't end with a slash
		if (substr($image_dir, -1) == DS) {
			$image_dir = substr($image_dir, 0, strlen($image_dir) - 1);
		}

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		// check for directory
		if (!is_dir( JPATH_ROOT.DS.$image_dir )) {
			if (!JFolder::create( JPATH_ROOT.DS.$image_dir, 0777 )) {
				echo JText::_('failed to create image directory').' '.$image_dir;
				$noflash = 1;
			} else {
				// use default images for this time
				$image_dir = 'modules/mod_slideshow/images/images';
			}
		}

		$images = array();
		$files = JFolder::files(JPATH_ROOT.DS.$image_dir, '.', false, true, array());
		if (count($files)==0) {
			$image_dir = 'modules/mod_slideshow/images/images';
		}

		$noflash_file = 'modules/mod_slideshow/images/images/default_'.$width.'x'.$height.'.jpg';

		$d = @dir(JPATH_ROOT.DS.$image_dir);

		if ($d) {
			// fetch images
			while (false !== ($entry = $d->read()))
			{
				$img_file = $entry;
				if (is_file(JPATH_ROOT.DS.$image_dir.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (preg_match("#bmp|gif|jpg|png|swf#i", strtolower($img_file) )) {
						$images[] = $img_file;
					}
				}
			}

			$d->close();

			if (count($images) > 0) {
				// pick a random image  to display if flash doesn't work
				$noflash_file = $image_dir.DS.$images[array_rand($images)];

				if ($random) {
					// shuffle array
					shuffle($images);
				}

				// start xml output
				if (!$noflash) {
					$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
					$xml.= " <slideshow>\n";
					$xml.= " <timerdelay>".$timerdelay."</timerdelay>\n";
					$xml.= " <transition>".$transitiontype."</transition>\n";
					for ($i=0, $n=count( $images ); $i < $n; $i++)
					{
						if (is_file(JPATH_ROOT.DS.$image_dir.DS.$images[$i])) {
							$xml.= " <image src='".htmlspecialchars($image_dir.DS.$images[$i])."'  />\n";
						}
					}
					$xml.= " </slideshow>\n";

					$xmlpath = JPATH_ROOT.DS.$this->homedir.DS.$xmlPrefix;
					$xmlpath.= $alias ? '-'.$alias : '';
					$xmlpath.= '.xml';

					$fh = fopen($xmlpath, "w");
					fwrite($fh,utf8_encode($xml));
					fclose($fh);
				}
			} else {
				$noflash = 1;
			}
		} else {
			$noflash = 1;
		}

		if (!$noflash) {
			$document =& JFactory::getDocument();
			$document->addScript('modules/mod_slideshow/mod_slideshow.js');
			$document->addScriptDeclaration('HUB.ModSlideshow.src="'.$swffile.'"; HUB.ModSlideshow.alias="'.$alias.'"; HUB.ModSlideshow.height="'.$height.'"; HUB.ModSlideshow.width="'.$width.'"');
		}

		$this->width = $width;
		$this->height = $height;
		$this->noflash_link = $noflash_link;
		$this->noflash_file = $noflash_file;
	}
}
