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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class NewsletterControllerTools extends \Hubzero\Component\AdminController
{
	/**
	 * Display Newsletter Tools
	 *
	 * @return 	void
	 */
	public function displayTask()
	{
		//set layout
		$this->view->setLayout('display');

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		//set view vars
		$this->view->code     = ($this->code) ? $this->code : '';
		$this->view->preview  = ($this->preview) ? $this->preview : '';
		$this->view->original = ($this->original) ? $this->original : '';

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Mozify Image
	 *
	 * @return 	void
	 */
	public function mozifyTask()
	{
		//get request vars
		$imageFile  = JRequest::getVar('image-file','', 'files');
		$imageUrl   = JRequest::getVar('image-url','', 'post');
		$mosaicSize = JRequest::getInt('mosaic-size', 5, 'post');

		//temp upload path
		$uploadPath = JPATH_ROOT . DS . 'tmp' . DS . 'newsletter' . DS . 'mozify';

		//url regex
		$UrlPtn = "(?:https?:|mailto:|ftp:|gopher:|news:|file:)" . "(?:[^ |\\/\"\']*\\/)*[^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_]";

		//make sure we have a valid url if we passed one
		if ($imageUrl != '' && !preg_match("/$UrlPtn/", $imageUrl))
		{
			$this->setError('Image Url is not valid.');
			$this->displayTask();
			return;
		}

		//do we have a file upload or just an image url
		if (isset($imageFile) && $imageFile['tmp_name'] != '')
		{
			//make sure file is image
			$info = pathinfo($imageFile['name']);
			if (!in_array($info['extension'], array('png','jpg','jpeg','bmp','gif', 'tiff')))
			{
				$this->setError(JText::_('COM_NEWSLETTER_TOOLS_NOT_VALID_IMAGE'));
				$this->displayTask();
				return;
			}

			//import joomla filesystem lib
			jimport('joomla.filesystem.folder');

			//create path if doesnt exist
			if (!is_dir($uploadPath))
			{
				JFolder::create($uploadPath);
			}

			//define image
			$image = $uploadPath.DS.$imageFile['name'];

			//move uploaded file
			move_uploaded_file($imageFile['tmp_name'], $image);
		}
		else
		{
			$image = $imageUrl;
		}

		//config for mozify
		$config = array(
			'imageUrl'   => $image,
			'mosaicSize' => $mosaicSize
		);

		//instantiate new hubzero image mozify object
		$hubzeroImageMozify = new \Hubzero\Image\Mozify( $config );

		//return
		$this->code     = $hubzeroImageMozify->mozify();
		$this->preview  = $hubzeroImageMozify->mosaic();
		$this->original = $image;
		$this->displayTask();
		return;
	}
}