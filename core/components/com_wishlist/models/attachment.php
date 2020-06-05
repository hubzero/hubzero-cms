<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Models;

use Hubzero\Database\Relational;
use Filesystem;
use Component;
use Route;
use Lang;

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
				/*if (!$this->get('category') || !$this->get('referenceid'))
				{
					$wish = Wish::oneOrNew($this->get('wish'));
					$wishlist = Wishlist::oneOrNew($wish->get('wishlist'));
					$this->set('category', $wishlist->get('category'));
					$this->set('referenceid', $wishlist->get('referenceid'));
				}
				return Route::url('index.php?option=com_wishlist&task=wish&category=' . $this->get('category') . '&rid=' . $this->get('referenceid') . '&wishid=' . $this->get('wish') . '&file=' . $this->get('filename'));
				*/
				return with(new \Hubzero\Content\Moderator($this->link('file')))->getUrl();
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
		$exts = array_values(array_filter($exts));

		if (!in_array($ext, $exts))
		{
			$this->setError(Lang::txt('COM_WISHLIST_ERROR_UPLOADING_INVALID_FILE', implode(', ', $exts)));
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
		if ($this->exists())
		{
			if (!Filesystem::delete($this->link('file')))
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

			if ($this->exists())
			{
				$this->size = filesize($this->link('file'));
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

			if ($this->isImage() && $this->exists())
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

	/**
	 * Check if the file exists
	 *
	 * @return  bool
	 */
	public function exists()
	{
		return file_exists($this->link('file'));
	}
}
