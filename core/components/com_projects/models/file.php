<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models;

use Hubzero\Base\Obj;
use Components\Projects\Helpers;
use Filesystem;
use stdClass;
use Route;
use Date;

/**
 * Project File model
 */
class File extends Obj
{
	/**
	 * Container for properties
	 *
	 * @var  array
	 */
	private $_data = array();

	/**
	 * Constructor
	 *
	 * @param   string  $localPath
	 * @param   string  $repoPath
	 * @return  void
	 */
	public function __construct($localPath = null, $repoPath = null)
	{
		$this->set('localPath', $localPath); // Path to item within repo

		$fullPath = trim($repoPath, DS) . DS . trim($localPath, DS);
		$fullPath = trim($fullPath, DS);
		if ($fullPath)
		{
			$this->set('fullPath', DS . $fullPath); // Full server path to item
		}

		// Set defaults
		$this->defaults();
	}

	/**
	 * Check if a property is set
	 *
	 * @param   string   $property  Name of property to set
	 * @return  boolean  True if set
	 */
	public function __isset($property)
	{
		return isset($this->_data[$property]);
	}

	/**
	 * Set a property
	 *
	 * @param   string  $property  Name of property to set
	 * @param   mixed   $value     Value to set property to
	 * @return  void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Unset a property
	 *
	 * @param   string  $property  Name of property to set
	 * @return  void
	 */
	public function clear($property)
	{
		if (isset($this->_data[$property]))
		{
			unset($this->_data[$property]);
		}
	}

	/**
	 * Get a property
	 *
	 * @param   string  $property  Name of property to retrieve
	 * @return  mixed
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property]))
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Return all properties
	 *
	 * @return  array
	 */
	public function getData()
	{
		return $this->_data;
	}

	/**
	 * Check if file exists
	 *
	 * @return  boolean
	 */
	public function exists()
	{
		return $this->get('fullPath') && file_exists($this->get('fullPath'));
	}

	/**
	 * Build basic metadata object
	 *
	 * @return  void
	 */
	public function defaults()
	{
		$this->set('name', basename($this->get('localPath')));

		// Directory path within repo
		if (dirname($this->get('localPath')) !== '.')
		{
			$this->set('dirname', dirname($this->get('localPath')));
		}

		if ($this->exists() && is_dir($this->get('fullPath')))
		{
			$this->set('type', 'folder');
		}
		else
		{
			$this->set('type', 'file');
			$this->set('ext', Helpers\Html::getFileExtension($this->get('localPath')));
		}

		if ($this->exists())
		{
			$this->set('date', Date::of(filemtime($this->get('fullPath'))));
		}
	}

	/**
	 * Get file contents
	 *
	 * @return  mixed
	 */
	public function contents()
	{
		if ($this->exists())
		{
			return file_get_contents($this->get('fullPath'));
		}

		return null;
	}

	/**
	 * Get preview image
	 *
	 * @param   object  $model
	 * @param   string  $hash
	 * @param   string  $get
	 * @param   string  $render
	 * @param   string  $hashed
	 * @return  mixed
	 */
	public function getPreview($model, $hash = '', $get = 'name', $render = '', $hashed = null)
	{
		if (!($model instanceof Project))
		{
			return false;
		}

		$image = null;

		if (!$hashed)
		{
			$hash = $hash ? $hash : $this->get('hash');
			$hash = $hash ? substr($hash, 0, 10) : '';

			// Determine name and size
			switch ($render)
			{
				case 'medium':
					$hashed = md5($this->get('name') . '-' . $hash) . '.png';
					$maxWidth  = 600;
					$maxHeight = 600;
					break;

				case 'thumb':
					$hashed = $hash ? Helpers\Html::createThumbName($this->get('name'), '-' . $hash, 'png') : null;
					$maxWidth  = 80;
					$maxHeight = 80;
					break;

				default:
					$hashed = $hash ? Helpers\Html::createThumbName($this->get('name'), '-' . $hash . '-thumb', 'png') : null;
					$maxWidth  = 180;
					$maxHeight = 180;
					break;
			}
		}

		// Target directory
		$target  = PATH_APP . DS . trim($model->config()->get('imagepath', '/site/projects'), DS);
		$target .= DS . strtolower($model->get('alias')) . DS . 'preview';

		$remoteThumb = null;
		if ($this->get('remoteId') && $this->get('modified'))
		{
			$remoteThumb = substr($this->get('remoteId'), 0, 20) . '_' . strtotime($this->get('modified')) . '.png';
		}

		if ($hashed && is_file($target . DS . $hashed))
		{
			// First check locally generated thumbnail
			$image = $target . DS . $hashed;
		}
		elseif ($remoteThumb && is_file($target . DS . $remoteThumb))
		{
			// Check remotely generated thumbnail
			$image = $target . DS . $remoteThumb;

			// Copy this over as local thumb
			if ($hashed && Filesystem::copy($target . DS . $remoteThumb, $target . DS . $hashed))
			{
				Filesystem::delete($target . DS . $remoteThumb);
			}
		}
		else
		{
			// Generate thumbnail locally
			if (!file_exists( $target ))
			{
				Filesystem::makeDirectory( $target, 0755, true, true);
			}

			// Make sure it's an image file
			if (!$this->isImage() || !is_file($this->get('fullPath')))
			{
				return false;
			}

			if (!Filesystem::copy($this->get('fullPath'), $target . DS . $hashed))
			{
				return false;
			}

			// Resize the image if necessary
			$hi = new \Hubzero\Image\Processor($target . DS . $hashed);
			$square = ($render == 'thumb') ? true : false;
			$hi->resize($maxWidth, false, false, $square);
			$hi->save($target . DS . $hashed);
			$image = $target . DS . $hashed;
		}

		// Return image
		if ($get == 'localPath')
		{
			return str_replace(PATH_APP, '', $image);
		}
		elseif ($get == 'fullPath')
		{
			return $image;
		}
		elseif ($get == 'url')
		{
			return Route::url('index.php?option=com_projects&alias=' . $model->get('alias') . '&controller=media&media=' . urlencode(basename($image)));
		}

		return basename($image);
	}

	/**
	 * Get file size
	 *
	 * @param   bool   $formatted
	 * @return  mixed
	 */
	public function getSize($formatted = false)
	{
		if (!$this->get('size'))
		{
			$this->setSize();
		}

		return $formatted ? $this->get('formattedSize') : $this->get('size');
	}

	/**
	 * Set file size
	 *
	 * @param   integer  $size
	 * @return  integer
	 */
	public function setSize($size = null)
	{
		if (intval($size) > 0)
		{
			$this->set('size', $size);
		}
		if ($this->get('size'))
		{
			// Already set
			return $this->get('size');
		}

		// Get size for local
		if ($this->exists())
		{
			$this->set('size', Filesystem::size($this->get('fullPath')));
		}

		// Formatted size
		$this->set('formattedSize', \Hubzero\Utility\Number::formatBytes($this->get('size')));

		return $this->get('size');
	}

	/**
	 * Get mime type
	 *
	 * @return  string
	 */
	public function getMimeType()
	{
		if (!$this->get('mimeType'))
		{
			$this->setMimeType();
		}

		return $this->get('mimeType');
	}

	/**
	 * Set mime type
	 *
	 * @return  void
	 */
	public function setMimeType()
	{
		if (!$this->get('mimeType'))
		{
			$this->set('mimeType', Filesystem::mimetype($this->get('fullPath')));
		}
	}

	/**
	 * Set md5Hash
	 *
	 * @return  void
	 */
	public function setMd5Hash()
	{
		if (is_file($this->get('fullPath')))
		{
			$this->set('md5Hash', hash_file('md5', $this->get('fullPath')));
		}
	}

	/**
	 * Set md5Hash
	 *
	 * @return  string
	 */
	public function getMd5Hash()
	{
		if (!$this->get('md5Hash'))
		{
			$this->setMd5Hash();
		}
		return $this->get('md5Hash');
	}

	/**
	 * Is binary?
	 *
	 * @return  mixed
	 */
	public function isBinary()
	{
		return Helpers\Html::isBinary($this->get('fullPath'));
	}

	/**
	 * Is image?
	 *
	 * @return  bool
	 */
	public function isImage()
	{
		$mime = $this->getMimeType();
		return strpos($mime, 'image/') !== false ? true : false;
	}

	/**
	 * Can image thumbnail be generated?
	 *
	 * @return  bool
	 */
	public function isSupportedImage()
	{
		$mime = $this->getMimeType();
		if (in_array($mime, array('image/jpeg', 'image/gif', 'image/png')))
		{
			return true;
		}
		return false;
	}

	/**
	 * Get item parent directories
	 *
	 * @return  mixed
	 */
	public function getParents()
	{
		if ($this->get('parents'))
		{
			return $this->get('parents');
		}
		else
		{
			return $this->setParents();
		}
	}

	/**
	 * Set item parents
	 *
	 * @return  mixed
	 */
	public function setParents()
	{
		if (!$this->get('dirname'))
		{
			return false;
		}
		if ($this->get('parents'))
		{
			return $this->get('parents');
		}

		$parents = new stdClass;
		$dirParts = explode('/', $this->get('dirname'));

		$i = 1;
		$collect = '';

		foreach ($dirParts as $part)
		{
			if (!trim($part))
			{
				break;
			}
			$collect .= DS . $part;
			$parents->$i = trim($collect, DS);
			$i++;
		}

		$this->set('parents', $parents);
		return $parents;
	}

	/**
	 * Build file metadata object for a folder
	 *
	 * @return  void
	 */
	public function setFolder()
	{
		$fullPath = str_replace($this->get('localPath'), '', $this->get('fullPath'));

		// Folder metadata
		$this->set('type', 'folder');
		$this->set('name', basename($this->get('dirname')));
		$this->set('localPath', $this->get('dirname'));

		$this->set('fullPath', $fullPath . $this->get('localPath'));

		$dirname = dirname($this->get('dirname')) == '.'
				? null : dirname($this->get('dirname'));
		$this->set('dirname', $dirname);
		$this->setParents();

		$this->clear('ext');
		$this->setIcon('folder');
	}

	/**
	 * Fix up some mimetypes
	 *
	 * @param   string  $mimeType
	 * @return  string
	 */
	protected function _fixUpMimeType($mimeType = null)
	{
		if ($this->get('ext'))
		{
			switch (strtolower($this->get('ext')))
			{
				case 'key':
					$mimeType = 'application/x-iwork-keynote-sffkey';
					break;

				case 'ods':
					$mimeType = 'application/vnd.oasis.opendocument.spreadsheet';
					break;

				case 'wmf':
					$mimeType = 'application/x-msmetafile';
					break;

				case 'tex':
					$mimeType = 'application/x-tex';
					break;
			}
		}

		return $mimeType;
	}

	/**
	 * Get file icon image
	 *
	 * @param   boolean  $basename
	 * @return  string
	 */
	public function getIcon($basename = false)
	{
		if (!$this->get('icon'))
		{
			$this->setIcon($this->get('ext'), $basename);
		}
		return $this->get('icon');
	}

	/**
	 * Set file icon image
	 *
	 * @param   string   $ext
	 * @param   boolean  $basename
	 * @param   string   $icon
	 * @return  string
	 */
	public function setIcon($ext = null, $basename = false, $icon = '')
	{
		if ($this->get('icon') && $this->get('ext') == $ext)
		{
			return $this->get('icon');
		}
		if ($icon)
		{
			$this->set('icon', $icon);
			return $this->get('icon');
		}
		if ($this->get('type') == 'folder')
		{
			$ext = 'folder';
		}

		$ext = $ext ? $ext : $this->get('ext');
		$icon = self::getIconImage($ext, $basename);

		$this->set('icon', $icon);
	}

	/**
	 * Draw icon
	 *
	 * @param   string  $ext
	 * @return  string  HTML
	 */
	public static function drawIcon($ext = '')
	{
		$icon = self::getIconImage($ext);
		return '<img class="file-type' . ($ext ? ' file-type-' . $ext : '') . '" src="' . $icon . '" alt="' . $ext . '" />';
	}

	/**
	 * Get file icon image
	 *
	 * @param   string   $ext
	 * @param   boolean  $basename
	 * @param   string   $icon
	 * @return  string
	 */
	public static function getIconImage($ext, $basename = false, $icon = '')
	{
		$ext = strtolower($ext);

		$icon = \Html::asset('image', 'assets/filetypes/' . $ext . '.svg', '', null, true, true);
		if (!$icon)
		{
			$icon = \Html::asset('image', 'assets/filetypes/file.svg', '', null, true, true);
		}

		return $basename ? basename($icon) : $icon;
	}

	/**
	 * Get folder structure level
	 *
	 * @param   string   $dirPath
	 * @return  integer
	 */
	public function getDirLevel($dirPath = '')
	{
		$dirPath = trim($dirPath);
		$dirPath = trim($dirPath, '/');
		if (!$dirPath)
		{
			return 0;
		}
		$dirParts = explode('/', $dirPath);
		return count($dirParts);
	}
}
