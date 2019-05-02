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

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;
use Hubzero\User\Group;
use Filesystem;
use FilesystemIterator;
use DirectoryIterator;


/**
 * macro class for displaying an image slider
 */
class Carousel extends Macro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = "Creates a slider with the images passed in.";
		$txt['html'] = '<p>Creates a slider with the images passed in. Enter uploaded image names seperated by commas.</p>
						<p>Examples:</p>
						<ul>
							<li><code>[[Carousel(images=image1.jpg;image2.gif;image3.png)]]</code></li>
							<li><code>[[Carousel(images=image1.jpg;image2.gif;image3.png, timeout=3000)]]</code></li>
							<li><code>[[Carousel(images=image1.jpg;image2.gif;image3.png, timeout=3000, height=100%, width=100%)]]</code></li>

						</ul>';

		return $txt['html'];
	}

  protected function getArgs()
	{
		//get the args passed in
		return explode(',', $this->args);
	}
	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		//get the args passed in
		$args = $this->getArgs();

		// args will be null if the macro is called without parenthesis.
		if (!$args)
		{
				return;
		}

		$images = $this->_getImages($args);
		$timeout = $this->_getTimeout($args);
		$height = $this->_getHeight($args);
		$width = $this->_getWidth($args);


		//generate a unique id for the slider
		$id = uniqid();

		// null base url for now
		$base_url = '';

		// needed objects
		$db     = \App::get('db');
		$option = \Request::getCmd('option');
		$config = \Component::params($option);

		// define a base url
		switch ($option)
		{
			case 'com_groups':
				$cn = \Request::getString('cn');
				$group = Group::getInstance($cn);

				$base_url  = DS . trim($config->get('uploadpath', 'site/groups'), DS) . DS;
				$base_url .= $group->get('gidNumber') . DS . 'uploads';
			break;

			case 'com_resources':
				require_once \Component::path('com_resources') . '/models/entry.php';

				$row = \Components\Resources\Models\Entry::oneOrNew($this->pageid);

				$base_url  = DS . trim($config->get('uploadpath', 'site/resources'), DS) . DS;
				$base_url .= trim($row->relativePath(), DS) . DS . 'media';
			break;
		}


		//array for checked slides
		$final_slides = array();

		//check each passed in slide
		foreach ($images as $slide)
		{
			//check to see if image is external
			if (strpos($slide, 'http') === false)
			{
				$slide = trim($slide);

				//check if internal file actually exists
				if (is_file(PATH_APP . $base_url . DS . $slide))
				{
					$final_slides[] = 'app' . $base_url . DS . $slide;
				}

        //If a directory is taken as the input argument,it will get into the directory and render the images
				else
				{
					$path =  'app' . $base_url . DS . $slide;
					$imgpath = Filesystem::listContents($path, $filter = '.', $recursive = false, $full = false, $exclude = array('.svn', '.git', 'CVS', '.DS_Store', '__MACOSX'));
				  foreach($imgpath as $img){

						foreach($img as $key => $value){
              if($key==='path'){
                  //Used to check if it's an image file
									if(preg_match("/\.(bmp|gif|jpg|jpe|jpeg|png)$/i",$value))
									 {
								     $imgaddr = $path . $value;
                     $final_slides[] = $imgaddr;
							     }
								}
							}
						}

				}

      }
			else
			{
				$headers = get_headers($slide);
				if (strpos($headers[0], "OK") !== false)
				{
					$final_slides[] = $slide;
				}
			}
		}

		$html  = '';
		$html .= '<div class="wiki_slider">';
			$html .= '<div id="slider_' . $id . '", style="height: ' . $height . ';width:' . $width . '">';
			foreach ($final_slides as $fs)
			{
				$html .= '<img src="' . $fs . '" alt="" />';
      }
			$html .= '</div>';
			$html .= '<div class="wiki_slider_pager" id="slider_' . $id . '_pager"></div>';
		$html .= '</div>';

		$base = rtrim(str_replace(PATH_ROOT, '', __DIR__));

		\Document::addStyleSheet($base . DS . 'assets' . DS . 'carousel' . DS . 'css' . DS . 'carousel.css');
		\Document::addScript($base . DS . 'assets' . DS . 'carousel' . DS . 'js' . DS . 'carousel.js');
		\Document::addScriptDeclaration('
			var $jQ = jQuery.noConflict();

			$jQ(function() {
				$jQ("#slider_' . $id . '").cycle({
					fx: \'scrollHorz\',
					timeout: ' . $timeout . ',
					pager: \'#slider_' . $id . '_pager\'
				});
			});
		');

		return $html;
	}

	private function _getImages(&$args)
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/images=([\S;]*)/', $arg, $matches))
			{
				$image = array_map('trim' , explode(';', (isset($matches[1])) ? $matches[1] : ''));
				unset($args[$k]);
				return $image;
			}
		}

		return false;
	}

	private function _getTimeout(&$args, $default = "3000")
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/timeout=([\w;]*)/', $arg, $matches))
			{
				$timeout = (isset($matches[1]) ? $matches[1] : '');
				unset($args[$k]);
				return $timeout;
			}
		}

		return $default;
	}


	private function _getHeight(&$args, $default = "100%")
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/height=([\S;]*)/', $arg, $matches))
			{
				$imgHeight = (isset($matches[1]) ? $matches[1] : '');
				unset($args[$k]);
				return $imgHeight;
			}
		}

		return $default;
	}

	private function _getWidth(&$args, $default = "100%")
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/width=([\S;]*)/', $arg, $matches))
			{
				$imgWidth = (isset($matches[1]) ? $matches[1] : '');
				unset($args[$k]);
				return $imgWidth;
			}
		}

		return $default;
	}

}
