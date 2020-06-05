<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Models;

use Hubzero\Database\Relational;
use Hubzero\Filesystem\Util;
use Filesystem;
use Component;

/**
 * Class for comment files (attachments)
 */
class Attachment extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'forum';

	/**
	 * Upload path
	 *
	 * @var  string
	 */
	protected $uploadDir = null;

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
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'parent'   => 'positive|nonzero',
		'post_id'  => 'positive|nonzero',
		'filename' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 **/
	public $initiate = array(
		'filename'
	);

	/**
	 * Defines a belongs to one relationship between category and post
	 *
	 * @return  object
	 */
	public function post()
	{
		return $this->belongsToOne('Post', 'post_id')->row();
	}

	/**
	 * Set the upload path
	 *
	 * @param   string  $path  Path to set to
	 * @return  object
	 */
	public function setUploadDir($path)
	{
		$path = str_replace(' ', '_', trim($path));
		$path = Util::normalizePath($path);

		$this->uploadDir = ($path ? $path : $this->uploadDir);

		return $this;
	}

	/**
	 * Get the upload path
	 *
	 * @return  string
	 */
	public function getUploadDir()
	{
		if (!$this->uploadDir)
		{
			$config = Component::params('com_forum');

			$this->uploadDir = PATH_APP . DS . trim($config->get('filepath', '/site/forum'), DS);
		}

		return $this->uploadDir;
	}

	/**
	 * Ensure no invalid characters
	 *
	 * @param   array  $data
	 * @return  string
	 */
	public function automaticFilename($data)
	{
		$data['filename'] = preg_replace("/[^A-Za-z0-9.]/i", '-', $data['filename']);

		return $data['filename'];
	}

	/**
	 * Ensure no conflicting file names by
	 * renaming the incoming file if the name
	 * already exists
	 *
	 * @param   array  $data
	 * @return  string
	 */
	public function uniqueFilename($data)
	{
		$filename = $data['filename'];
		$filename = preg_replace("/[^A-Za-z0-9.]/i", '-', $filename);

		$ext = strrchr($filename, '.');
		$prefix = substr($filename, 0, -strlen($ext));

		if (file_exists($this->getUploadDir() . DS . $data['parent'] . DS . $data['post_id'] . DS . $filename))
		{
			$i = 1;

			while (file_exists($this->getUploadDir() . DS . $data['parent'] . DS . $data['post_id'] . DS . $filename))
			{
				$filename = $prefix . ++$i . $ext;
			}
		}

		$data['filename'] = $filename;

		return $data['filename'];
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
			if (!Filesystem::delete($this->path()))
			{
				$this->addError(Lang::txt('Unable to delete file.'));

				return false;
			}
		}

		return parent::destroy();
	}

	/**
	 * Upload file
	 *
	 * @param   string  $name
	 * @param   string  $temp
	 * @return  bool
	 */
	public function upload($name, $temp)
	{
		$destination = $this->getUploadDir() . DS . $this->get('parent') . DS . $this->get('post_id');

		if (!is_dir($destination))
		{
			if (!Filesystem::makeDirectory($destination))
			{
				$this->addError(Lang::txt('COM_FORUM_UNABLE_TO_CREATE_UPLOAD_PATH') . ': ' . substr($destination, strlen(PATH_ROOT)));
				return false;
			}
		}

		// Make sure the name is unique
		$filename = $this->uniqueFilename(array(
			'filename' => $name,
			'parent'   => $this->get('parent'),
			'post_id'  => $this->get('post_id')
		));

		// Upload the file
		if (!Filesystem::upload($temp, $destination . DS . $filename))
		{
			$this->addError(Lang::txt('COM_FORUM_ERROR_UPLOADING'));
			return false;
		}

		// Make sure the file is safe
		if (!Filesystem::isSafe($destination . DS . $filename))
		{
			$this->setError(Lang::txt('COM_FORUM_ERROR_UPLOADING'));
			return false;
		}

		// Remove previous file
		if ($this->get('filename'))
		{
			if (!Filesystem::delete($destination . DS . $this->get('filename')))
			{
				$this->setError(Lang::txt('COM_FORUM_ERROR_UPLOADING'));
				return false;
			}
		}

		$this->set('filename', $filename);

		return true;
	}

	/**
	 * File path
	 *
	 * @return  string
	 */
	public function path()
	{
		return $this->getUploadDir() . DS . $this->get('parent') . DS . $this->get('post_id') . DS . $this->get('filename');
	}

	/**
	 * Download URL
	 *
	 * @return  string
	 */
	public function link()
	{
		return with(new \Hubzero\Content\Moderator($this->path(), 'public'))->getUrl();
	}

	/**
	 * Check if the file exists
	 *
	 * @return  bool
	 */
	public function exists()
	{
		return file_exists($this->path());
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
				$this->size = filesize($this->path());
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
				$this->dimensions = getimagesize($this->path());
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
	 * Load a record by thread ID and filename
	 *
	 * @param   integer  $thread_id
	 * @param   string   $filename
	 * @return  object
	 */
	public static function oneByThread($thread_id, $filename)
	{
		return self::all()
			->whereEquals('parent', (int)$thread_id)
			->whereEquals('filename', (string)$filename)
			->row();
	}

	/**
	 * Load a record by post ID
	 *
	 * @param   integer  $post_id
	 * @return  object
	 */
	public static function oneByPost($post_id)
	{
		return self::all()
			->whereEquals('post_id', (int)$post_id)
			->row();
	}
}
