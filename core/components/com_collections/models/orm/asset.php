<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models\Orm;

use Hubzero\Database\Relational;
use Component;
use Filesystem;
use Lang;
use Date;

/**
 * Collection item asset model
 */
class Asset extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'collections';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'ordering';

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
		'filename' => 'notempty',
		'item_id'  => 'positive|nonzero',
		'type'     => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by',
		'ordering'
	);

	/**
	 * Fields to be parsed
	 *
	 * @var  array
	 */
	protected $parsed = array(
		'description'
	);

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
	 * Generates automatic ordering field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  integer
	 */
	public function automaticOrdering($data)
	{
		if (!isset($data['ordering']))
		{
			$last = self::all()
				->select('ordering')
				->whereEquals('item_id', $data['item_id'])
				->order('ordering', 'desc')
				->row();

			$data['ordering'] = $last->ordering + 1;
		}

		return $data['ordering'];
	}

	/**
	 * Generates automatic type field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  integer
	 */
	public function automaticType($data)
	{
		$allowed = array('file', 'link');

		if (!isset($data['type']) || !$data['type'] || !in_array($data['type'], $allowed))
		{
			$data['type'] = 'file';
		}

		return $data['type'];
	}

	/**
	 * Return a formatted timestamp for created date
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		$as = strtolower($as);

		if ($as == 'date')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		return $this->get('created');
	}

	/**
	 * Creator profile
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Get represented item
	 *
	 * @return  object
	 */
	public function item()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Item');
	}

	/**
	 * Get the path to the file space
	 *
	 * @return  string
	 */
	public function filespace()
	{
		static $path;

		if (!$path)
		{
			$path = PATH_APP . DS . trim(Component::params('com_collections')->get('filepath', '/site/collections'), DS);
		}

		return $path;
	}

	/**
	 * Is an asset an image?
	 *
	 * @return  boolean  True if image, false if not
	 */
	public function isImage()
	{
		$dot = strrpos($this->get('filename'), '.') + 1;

		$ext = substr($this->get('filename'), $dot);
		$ext = strtolower($ext);

		if (in_array($ext, array('jpg', 'jpe', 'jpeg', 'gif', 'png')))
		{
			return true;
		}

		return false;
	}

	/**
	 * Is an asset a link?
	 *
	 * @return  boolean  True if image, false if not
	 */
	public function isLink()
	{
		return ($this->get('type') == 'link');
	}

	/**
	 * Is an asset an external link?
	 *
	 * @return  boolean  True if image, false if not
	 */
	public function isExternalLink()
	{
		if ($this->isLink())
		{
			$UrlPtn  = "(?:https?:|mailto:|ftp:|gopher:|news:|file:)"
					 . "(?:[^ |\\/\"\']*\\/)*[^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_]";

			if (preg_match("/$UrlPtn/", $this->get('filename')))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Does the file exist?
	 *
	 * @return  boolean  True if image, false if not
	 */
	public function exists()
	{
		$path  = $this->filespace() . DS . $this->get('item_id') . DS;
		$path .= ltrim($this->get('filename'), DS);

		return file_exists($path);
	}

	/**
	 * Is the file an image?
	 *
	 * @return  boolean
	 */
	public function size()
	{
		if (is_null($this->size))
		{
			$this->size = 0;

			if ($this->exists())
			{
				$path  = $this->filespace() . DS . $this->get('item_id') . DS;
				$path .= ltrim($this->get('filename'), DS);

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
		if (is_null($this->_dimensions))
		{
			$this->_dimensions = array(0, 0);

			if ($this->isImage() && $this->exists())
			{
				$this->_dimensions = getimagesize($this->path());
			}
		}

		return $this->_dimensions;
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
	 * Download URL
	 *
	 * @param   string  $size
	 * @return  string
	 */
	public function link($size = 'original')
	{
		$path  = $this->filespace() . DS . $this->get('item_id') . DS;
		$path .= ltrim($this->file($size), DS);

		return with(new \Hubzero\Content\Moderator($path, 'public'))->getUrl();
	}

	/**
	 * Mark a record as trashed and rename the file
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function trash()
	{
		if ($this->get('type') == 'file')
		{
			$ext  = Filesystem::extension($this->get('filename'));
			$path = $this->filespace() . DS . $this->get('item_id') . DS;

			$file  = $path . $this->get('filename');
			$trash = Filesystem::name($this->get('filename')) . uniqid('_d') . '.' . $ext;

			if (file_exists($file))
			{
				if (!Filesystem::move($file, $path . $trash))
				{
					$this->addError(Lang::txt('COM_COLLECTIONS_ERROR_UNABLE_TO_RENAME_FILE'));
					return false;
				}
			}

			$this->set('filename', $trash);
		}

		$this->set('state', self::STATE_DELETED);

		return parent::save();
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Can't delete what doesn't exist
		if ($this->isNew())
		{
			return true;
		}

		// Remove associated files
		if ($this->get('type') == 'file')
		{
			$ext  = Filesystem::extension($this->get('filename'));
			$path = $this->filespace() . DS . $this->get('item_id') . DS;

			$files = array(
				// The file
				'orign' => $path . $this->get('filename'),
				// Medium sized (if an image)
				'mediu' => $path . Filesystem::name($this->get('filename')) . '_m.' . $ext,
				// Thumbnail (if an image)
				'thumb' => $path . Filesystem::name($this->get('filename')) . '_t.' . $ext//,
				// Thumbnail (if an image)
				// A previously "trashed" file
				//'trash' => $path . Filesystem::name($this->get('filename')) . uniqid('_d') . '.' . $ext;
			);

			foreach ($files as $file)
			{
				if (file_exists($file))
				{
					if (!Filesystem::delete($file))
					{
						$this->addError(Lang::txt('COM_COLLECTIONS_ERROR_UNABLE_TO_DELETE_FILE'));
						return false;
					}
				}
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}
}
