<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Image\Mozify;
use Request;
use Config;
use Notify;
use Lang;

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
		$imageFile  = Request::getArray('image-file', '', 'files');
		$imageUrl   = Request::getString('image-url', '', 'post');
		$mosaicSize = Request::getInt('mosaic-size', 5, 'post');

		//temp upload path
		$uploadPath = Config::get('tmp_path') . DS . 'newsletter' . DS . 'mozify';

		//url regex
		$UrlPtn = "(?:https?:|mailto:|ftp:|gopher:|news:|file:)" . "(?:[^ |\\/\"\']*\\/)*[^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_]";

		//make sure we have a valid url if we passed one
		if ($imageUrl != '' && !preg_match("/$UrlPtn/", $imageUrl))
		{
			Notify::error(Lang::txt('Image Url is not valid.'));
			return $this->displayTask();
		}

		//do we have a file upload or just an image url
		if (isset($imageFile) && $imageFile['tmp_name'] != '')
		{
			//make sure file is image
			$info = pathinfo($imageFile['name']);
			if (!in_array($info['extension'], array('png','jpg','jpeg','bmp','gif', 'tiff')))
			{
				Notify::error(Lang::txt('COM_NEWSLETTER_TOOLS_NOT_VALID_IMAGE'));
				return $this->displayTask();
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
