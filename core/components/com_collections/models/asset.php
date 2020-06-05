<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models;

use Hubzero\Image\Processor;
use Filesystem;
use Lang;

require_once dirname(__DIR__) . DS . 'tables' . DS . 'asset.php';
require_once __DIR__ . DS . 'base.php';

/**
 * Collections model class for an Asset
 */
class Asset extends Base
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	public $_tbl_name = '\\Components\\Collections\\Tables\\Asset';

	/**
	 * File size
	 *
	 * @var  string
	 */
	protected $_size = null;

	/**
	 * Diemnsions for file (must be an image)
	 *
	 * @var  array
	 */
	protected $_dimensions = null;

	/**
	 * Constructor
	 *
	 * @param   mixed    $oid      ID, string, array, or object
	 * @param   integer  $item_id  ID of the item asset is attached
	 * @return  void
	 */
	public function __construct($oid=null, $item_id=null)
	{
		$this->_db = \App::get('db');

		$tbl = $this->_tbl_name;
		$this->_tbl = new $tbl($this->_db);

		if (is_numeric($oid) || is_string($oid))
		{
			$this->_tbl->load($oid, $item_id);
		}
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind($oid);
		}
	}

	/**
	 * Returns a reference to an asset object
	 *
	 * @param   mixed    $oid      ID, string, array, or object
	 * @param   integer  $item_id  ID of the item asset is attached
	 * @return  object
	 */
	static function &getInstance($oid=null, $item_id=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $oid . '_' . $item_id;
		}
		else if (is_object($oid))
		{
			$key = $oid->id . '_' . $item_id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'] . '_' . $item_id;
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid, $item_id);
		}

		return $instances[$key];
	}

	/**
	 * Is an asset an image?
	 *
	 * @return  boolean  True if image, false if not
	 */
	public function image()
	{
		$ext = strtolower(Filesystem::extension($this->get('filename')));

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
	 * Get the file size
	 *
	 * @return  boolean
	 */
	public function size()
	{
		if (is_null($this->_size))
		{
			$this->_size = 0;

			if ($this->exists())
			{
				$path  = $this->filespace() . DS . $this->get('item_id') . DS;
				$path .= ltrim($this->get('filename'), DS);

				$this->_size = filesize($path);
			}
		}

		return $this->_size;
	}

	/**
	 * File width and height
	 *
	 * @return  array
	 */
	public function dimensions()
	{
		if (!$this->_dimensions)
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
		$base = Request::base();
		$path  = $base . 'app/site/collections/' . $this->get('item_id') . DS;
		$path .= ltrim($this->file($size), DS);

		return $path;
	}

	/**
	 * Return the appropriate file name given a specified size
	 *
	 * @param   string  $size
	 * @return  string
	 */
	public function file($size = 'thumb')
	{
		if (!$this->image())
		{
			return $this->get('filename');
		}

		$path = $this->filespace() . DS . $this->get('item_id') . DS;
		$file = ltrim($this->get('filename'), DS);

		if (!file_exists($path . $file))
		{
			return $file;
		}

		switch ($size)
		{
			case 't':
			case 'tn':
			case 'thumb':
			case 'thumbnail':
				$ext   = Filesystem::extension($file);
				$thumb = Filesystem::name($file) . '_t.' . $ext;

				if (!file_exists($path . $thumb))
				{
					if (!$this->resize($path . $file, $path . $thumb, 400))
					{
						$thumb = $file;
					}
				}

				return $thumb;
			break;

			case 'm':
			case 'med':
			case 'medium':
				$ext   = Filesystem::extension($file);
				$thumb = Filesystem::name($file) . '_m.' . $ext;

				if (!file_exists($path . $thumb))
				{
					if (!$this->resize($path . $file, $path . $thumb, 1024))
					{
						$thumb = $file;
					}
				}

				return $thumb;
			break;

			case 'o':
			case 'orig':
			case 'original':
			default:
				return $file;
			break;
		}
	}

	/**
	 * Resize an image
	 *
	 * @param   string   $orig
	 * @param   string   $dest
	 * @param   integer  $size
	 * @return  boolean  True on success, false if errors
	 */
	private function resize($orig, $dest, $size)
	{
		if (!file_exists($dest))
		{
			list($originalWidth, $originalHeight) = getimagesize($orig);

			if ($originalWidth > $size || $originalHeight > $size)
			{
				$useHeight = ($originalHeight > $originalWidth) ? true : false;

				// Resize image
				$processor = new \Hubzero\Image\Processor($orig);
				if (!$processor->getErrors())
				{
					$processor->resize($size, $useHeight);
					if (!$processor->save($dest))
					{
						return false;
					}
				}
			}
			else
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Remove a record
	 *
	 * @return  boolean  True on success, false if errors
	 */
	public function remove()
	{
		if (!$this->_tbl->remove($this->get('id')))
		{
			$this->setError($this->_tbl->getError());
			return false;
		}
		return true;
	}

	/**
	 * Update content
	 *
	 * @param   string   $field   Field name
	 * @param   string   $before  Old value
	 * @param   string   $after   New value
	 * @return  boolean  True on success, false if errors
	 */
	public function update($field, $before, $after)
	{
		if (!$this->_tbl->updateField($field, $before, $after))
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		return true;
	}

	/**
	 * Store content
	 * Can be passed a boolean to turn off check() method
	 *
	 * @param   boolean $check Call check() method?
	 * @return  boolean True on success, false if errors
	 */
	public function store($check=true)
	{
		if ($this->get('_file'))
		{
			$path = $this->filespace() . DS . $this->get('item_id');

			if (!is_dir($path))
			{
				if (!Filesystem::makeDirectory($path))
				{
					$this->setError(Lang::txt('Error uploading. Unable to create path.'));
					return false;
				}
			}

			$file = $this->get('_file');

			// Make the filename safe
			$file['name'] = urldecode($files['name']);
			$file['name'] = Filesystem::clean($file['name']);
			$file['name'] = str_replace(' ', '_', $file['name']);

			// Upload new files
			if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
			{
				$this->setError(Lang::txt('ERROR_UPLOADING') . ': ' . $file['name']);
				return false;
			}

			$this->set('filename', $file['name']);

			// Generate a thumbnail
			$this->thumbnail();
		}

		return parent::store($check);
	}

	/**
	 * Update ordering
	 *
	 * @param   integer  $item_id  Item ID
	 * @return  boolean  True on success, false if errors
	 */
	public function reorder($item_id=0)
	{
		if (!$item_id)
		{
			$item_id = $this->get('item_id');
		}
		return $this->_tbl->reorder("item_id=" . $this->_db->quote(intval($item_id)));
	}

	/**
	 * Move to a new location
	 *
	 * @param   integer  $item_id  Item ID
	 * @return  boolean  True on success, false if errors
	 */
	public function move($item_id)
	{
		$curr = $this->filespace() . DS . $this->get('item_id') . DS;
		$dest = $this->filespace() . DS . $item_id . DS;

		$file = $this->get('filename');
		$ext  = Filesystem::extension($file);

		$files = array();
		$files['original'] = $file;
		$files['thumb']    = Filesystem::name($file) . '_t.' . $ext;
		$files['medium']   = Filesystem::name($file) . '_m.' . $ext;

		foreach ($files as $file)
		{
			if (!Filesystem::exists($curr . $file))
			{
				continue;
			}

			if (!Filesystem::move($curr . $file, $dest . $file))
			{
				return false;
			}
		}

		$this->set('item_id', $item_id);

		if (!$this->store())
		{
			return false;
		}

		return true;
	}
}
