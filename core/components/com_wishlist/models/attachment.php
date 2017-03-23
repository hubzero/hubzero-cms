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

namespace Components\Wishlist\Models;

use Hubzero\Database\Relational;
use Filesystem;
use Component;
use Route;

/**
 * Model class for a wish attachment
 */
class Attachment extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'wish';

	/**
	 * File size
	 *
	 * @var  string
	 */
	protected $size = null;

	/**
	 * Diemnsions for file (must be an image)
	 *
	 * @var  array
	 */
	protected $dimensions = null;

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'created';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'wish'     => 'nonzero',
		'filename' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Load a record by wish and filename
	 *
	 * @param   integer  $wish
	 * @param   string   $filename
	 * @return  object
	 */
	public static function oneByWishAndFile($wish, $filename)
	{
		return self::all()
			->whereEquals('wish', $wish)
			->whereEquals('filename', $filename)
			->row();
	}

	/**
	 * Defines a belongs to one relationship between task and liaison
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Defines a belongs to one relationship between attachment and wish
	 *
	 * @return  object
	 */
	public function wish()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Wish', 'wish');
	}

	/**
	 * Returns a link or path to the file
	 *
	 * @param   string  $type
	 * @return  string
	 */
	public function link($type)
	{
		$type = strtolower($type);

		switch ($type)
		{
			case 'download':
				if (!$this->get('category') || !$this->get('referenceid'))
				{
					$wish = Wish::oneOrNew($this->get('wish'));
					$wishlist = Wishlist::oneOrNew($wish->get('wishlist'));
					$this->set('category', $wishlist->get('category'));
					$this->set('referenceid', $wishlist->get('referenceid'));
				}
				return Route::url('index.php?option=com_wishlist&task=wish&category=' . $this->get('category') . '&rid=' . $this->get('referenceid') . '&wishid=' . $this->get('wish') . '&file=' . $this->get('filename'));
			break;

			case 'dir':
				$path = Component::params('com_wishlist')->get('webpath');
				return PATH_APP . DS . trim($path, '/') . DS . $this->get('wish');
			break;

			case 'file':
			case 'server':
				$path = Component::params('com_wishlist')->get('webpath');
				return PATH_APP . DS . trim($path, '/') . DS . $this->get('wish') . DS . ltrim($this->get('filename'), '/');
			break;
		}
	}

	/**
	 * Checks the file type and determines if it's in the
	 * whitelist of allowed extensions
	 *
	 * @return  boolean  True if allowed file type
	 */
	public function isAllowedType()
	{
		$ext = strtolower(Filesystem::extension($this->get('filename')));

		$exts = explode(',', Component::params('com_media')->get('upload_extensions', 'jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,wav,mp3,eps,ppt,pps,swf,tar,tex,gz'));
		$exts = array_map('trim', $exts);

		if (!in_array($ext, $exts))
		{
			return false;
		}

		return true;
	}

	/**
	 * Delete record
	 *
	 * @return  boolean  True if successful, False if not
	 */
	public function destroy()
	{
		$path = $this->link('file');

		if (file_exists($path))
		{
			if (!Filesystem::delete($path))
			{
				$this->addError('Unable to delete file.');

				return false;
			}
		}

		return parent::destroy();
	}

	/**
	 * Is the file an image?
	 *
	 * @return  boolean
	 */
	public function isImage()
	{
		return preg_match("/\.(bmp|gif|jpg|jpe|jpeg|png)$/i", $this->get('filename'));
	}

	/**
	 * Is the file an image?
	 *
	 * @return  boolean
	 */
	public function size()
	{
		if ($this->size === null)
		{
			$this->size = 0;

			$path = $this->link('file');

			if (file_exists($path))
			{
				$this->size = filesize($path);
			}
		}

		return $this->size;
	}

	/**
	 * File width and height
	 *
	 * @return  array
	 */
	public function dimensions()
	{
		if (!$this->dimensions)
		{
			$this->dimensions = array(0, 0);

			if ($this->isImage() && file_exists($this->link('file')))
			{
				$this->dimensions = getimagesize($this->link('file'));
			}
		}

		return $this->dimensions;
	}

	/**
	 * File width
	 *
	 * @return  integer
	 */
	public function width()
	{
		$dimensions = $this->dimensions();

		return $dimensions[0];
	}

	/**
	 * File height
	 *
	 * @return  integer
	 */
	public function height()
	{
		$dimensions = $this->dimensions();

		return $dimensions[1];
	}
}
