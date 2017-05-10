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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Image\Mozify;
use Request;
use Config;
use Notify;

/**
 * Newsletter tools Controller
 */
class Tools extends AdminController
{
	/**
	 * Display Newsletter Tools
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		//set view vars
		$code     = (isset($this->code) && $this->code) ? $this->code : '';
		$preview  = (isset($this->preview) && $this->preview) ? $this->preview : '';
		$original = (isset($this->original) && $this->original) ? $this->original : '';

		// Output the HTML
		$this->view
			->setLayout('display')
			->set('code', $code)
			->set('preview', $preview)
			->set('original', $original)
			->display();
	}

	/**
	 * Mozify Image
	 *
	 * @return  void
	 */
	public function mozifyTask()
	{
		//get request vars
		$imageFile  = Request::getVar('image-file', '', 'files');
		$imageUrl   = Request::getVar('image-url', '', 'post');
		$mosaicSize = Request::getInt('mosaic-size', 5, 'post');

		//temp upload path
		$uploadPath = Config::get('tmp_path') . DS . 'newsletter' . DS . 'mozify';

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
				$this->setError(Lang::txt('COM_NEWSLETTER_TOOLS_NOT_VALID_IMAGE'));
				$this->displayTask();
				return;
			}

			//create path if doesnt exist
			if (!is_dir($uploadPath))
			{
				\Filesystem::makeDirectory($uploadPath);
			}

			//define image
			$image = $uploadPath . DS . $imageFile['name'];

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
		$hubzeroImageMozify = new Mozify($config);
		if (!$hubzeroImageMozify->getError())
		{
			$this->code     = $hubzeroImageMozify->mozify();
			$this->preview  = $hubzeroImageMozify->mosaic();
			$this->original = $image;
		}
		else
		{
			$this->code     = null;
			$this->preview  = null;
			$this->original = $image;

			Notify::error($hubzeroImageMozify->getError());
		}

		$this->displayTask();
	}
}