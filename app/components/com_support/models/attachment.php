<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Models;

use Hubzero\Database\Relational;
use Filesystem;
use Component;
use Request;
use Route;
use Lang;

/**
 * Support ticket attachment model
 */
class Attachment extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	public $namespace = 'support';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

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
		//'ticket'   => 'positive|nonzero'
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
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'filename'
	);

	/**
	 * Diemnsions for file (must be an image)
	 *
	 * @var array
	 */
	private $_dimensions = null;

	/**
	 * Ensure no invalid characters
	 *
	 * @param   array  $data
	 * @return  string
	 */
	public function automaticFilename($data)
	{
		$data['filename'] = preg_replace("/[^A-Za-z0-9._]/i", '-', $data['filename']);

		return $data['filename'];
	}

	/**
	 * Get parent ticket
	 *
	 * @return  object
	 */
	public function ticket()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Ticket', 'ticket');
	}

	/**
	 * Get parent comment
	 *
	 * @return  object
	 */
	public function comment()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Comment', 'comment_id');
	}

	/**
	 * Defines a belongs to one relationship between comment and user
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
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
	 * Does the file exist on the server?
	 *
	 * @return  boolean
	 */
	public function hasFile()
	{
		return file_exists($this->path());
	}

	/**
	 * Get the file size
	 *
	 * @return  integer
	 */
	public function size()
	{
		if ($this->hasFile())
		{
			return filesize($this->path());
		}

		return 0;
	}

	/**
	 * Get image width
	 *
	 * @return  integer
	 */
	public function width()
	{
		if (!$this->_dimensions)
		{
			$this->_dimensions = $this->isImage() && $this->hasFile() ? getimagesize($this->path()) : array(0, 0);
		}

		return $this->_dimensions[0];
	}

	/**
	 * Get image height
	 *
	 * @return  integer
	 */
	public function height()
	{
		if (!$this->_dimensions)
		{
			$this->_dimensions = $this->isImage() && $this->hasFile() ? getimagesize($this->path()) : array(0, 0);
		}

		return $this->_dimensions[1];
	}

	/**
	 * Root file path
	 *
	 * @return  string
	 */
	public function rootPath()
	{
		return PATH_APP . DS . trim(Component::params('com_support')->get('webpath', '/site/tickets'), DS);
	}

	/**
	 * File path
	 *
	 * @return  string
	 */
	public function path()
	{
		//($this->get('comment_id') ? '/' . $this->get('comment_id') : '')
		return $this->rootPath() . '/' . $this->get('ticket') . '/' . $this->get('filename');
	}

	/**
	 * Delete record
	 *
	 * @return  boolean  True if successful, False if not
	 */
	public function destroy()
	{
		if ($this->hasFile())
		{
			if (!Filesystem::delete($this->path()))
			{
				$this->addError('Unable to delete file.');

				return false;
			}
		}

		return parent::destroy();
	}

	/**
	 * Load a record by comment ID and filename
	 *
	 * @param   integer  $comment_id
	 * @param   string   $filename
	 * @return  object
	 */
	public static function oneByComment($comment_id, $filename)
	{
		return self::all()
			->whereEquals('comment_id', (int)$comment_id)
			->whereEquals('filename', (string)$filename)
			->row();
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon type desired
	 *
	 * @param   string   $type      The type of link to return
	 * @param   boolean  $absolute  Get the URL absolute to the domain?
	 * @return  string
	 */
	public function link($type='', $absolute=false)
	{
		$link = 'index.php?option=com_support';

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'base':
			case 'component':
				return $this->_base;
			break;

			case 'filepath':
				return $this->path();
			break;

			case 'permalink':
			default:
				$link .= '&task=download&id=' . $this->get('id') . '&file=' . $this->get('filename');
			break;
		}

		if ($absolute)
		{
			$link = rtrim(Request::base(), '/') . '/' . trim(Route::url($link), '/');
		}

		return $link;
	}

	/**
	 * Take a file existing on the local filesystem and place it in a ticket
	 *
	 * @param   string   $currentfile
	 * @param   string   $filename
	 * @param   integer  $ticketid
	 * @return  string
	 */
	public function addFile($currentfile, $filename, $ticketid)
	{
		$config = Component::params('com_support');

		// Construct our file path for new file
		$path = $this->rootPath() . DS . $ticketid;

		// Build the path if it doesn't exist
		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->addError(Lang::txt('COM_SUPPORT_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return '';
			}
		}

		// Make the filename safe
		$filename = Filesystem::clean($filename);
		$filename = str_replace(' ', '_', $filename);
		$ext = strtolower(Filesystem::extension($filename));

		// Make sure that file is acceptable type
		if (!in_array($ext, explode(',', $config->get('file_ext'))))
		{
			$this->addError(Lang::txt('COM_SUPPORT_ERROR_INCORRECT_FILE_TYPE'));
			return Lang::txt('COM_SUPPORT_ERROR_INCORRECT_FILE_TYPE');
		}

		$newname = Filesystem::name($filename);
		while (file_exists($path . DS . $newname . '.' . $ext))
		{
			$newname .= rand(10, 99);
		}
		$newname = $newname . '.' . $ext;

		// We should ask the model if the name we generated is OK
		$data = array();
		$data['filename'] = $newname;
		$newname = $this->automaticFilename($data);

		$finalfile = $path . DS . $newname;

		// Perform the upload
		if (!Filesystem::upload($currentfile, $finalfile))
		{
			$this->addError(Lang::txt('COM_SUPPORT_ERROR_UPLOADING'));
			return '';
		}
		else
		{
			// Scan for viruses
			if (!Filesystem::isSafe($finalfile))
			{
				if (Filesystem::delete($finalfile))
				{
					$this->addError(Lang::txt('COM_SUPPORT_ERROR_FAILED_VIRUS_SCAN'));
					return Lang::txt('COM_SUPPORT_ERROR_FAILED_VIRUS_SCAN');
				}
			}

		}

		$this->set('filename', $newname);

		return '';
	}
}
