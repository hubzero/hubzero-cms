<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Session\Storage;

use Hubzero\Filesystem\Filesystem;
use Hubzero\Filesystem\Adapter\Local;
use Hubzero\Session\Store;
use Exception;

/**
 * File session storage handler
 *
 * Inspired by Joomla's JSessionStorageFile class
 */
class File extends Store
{
	/**
	 * The filesystem instance.
	 *
	 * @var  object
	 */
	protected $files;

	/**
	 * The path where sessions should be stored.
	 *
	 * @var  string
	 */
	protected $path;

	/**
	 * Constructor
	 *
	 * @param   array  $options  Optional parameters.
	 * @return  void
	 */
	public function __construct($options = array())
	{
		if (!isset($options['session_path']))
		{
			$options['session_path'] = PATH_APP . DS . 'sessions';
		}

		if (!isset($options['filesystem']) || !($options['filesystem'] instanceof Filesystem))
		{
			$adapter = new Local($app['config']->get('virus_scanner', "clamscan -i --no-summary --block-encrypted"));
			$options['filesystem'] = new Filesystem($adapter);
		}

		$this->path  = $this->cleanPath($options['session_path']);
		$this->files = $options['filesystem'];

		if (!is_dir($this->path) || !is_readable($this->path) || !is_writable($this->path))
		{
			throw new Exception('Storage path should be directory with available read/write access.');
		}

		parent::__construct($options);
	}

	/**
	 * Read the data for a particular session identifier from the
	 * SessionHandler backend.
	 *
	 * @param   string  $id  The session identifier.
	 * @return  string  The session data.
	 */
	#[\ReturnTypeWillChange]
	public function read($id)
	{
		if ($this->files->exists($path = $this->path . DS . $id))
		{
			return $this->files->read($path);
		}

		return '';
	}

	/**
	 * Write session data to the SessionHandler backend.
	 *
	 * @param   string   $id    The session identifier.
	 * @param   string   $data  The session data.
	 * @return  boolean  True on success, false otherwise.
	 */
	#[\ReturnTypeWillChange]
	public function write($id, $data)
	{
		$this->files->write($this->path . DS . $id, $data, true);
	}

	/**
	 * Destroy the data for a particular session identifier in the
	 * SessionHandler backend.
	 *
	 * @param   string   $id  The session identifier.
	 * @return  boolean  True on success, false otherwise.
	 */
	#[\ReturnTypeWillChange]
	public function destroy($id)
	{
		$this->files->delete($this->path . DS . $id);
	}

	/**
	 * Garbage collect stale sessions from the SessionHandler backend.
	 *
	 * @param   int  $maxlifetime  The maximum age of a session.
	 * @return  boolean  True on success, false otherwise.
	 */
	#[\ReturnTypeWillChange]
	public function gc($maxlifetime = null)
	{
		$files = $this->files->listContents($this->path);

		$tm = time() - $maxlifetime;

		foreach ($files as $file)
		{
			if (!$file->isFile())
			{
				continue;
			}

			if ($file->getMTime() <= $tm)
			{
				$this->files->delete($file->getPathname());
			}
		}
	}

	/**
	 * Get single session data as an object
	 *
	 * @param   int  $id  Session Id
	 * @return  object
	 */
	public function session($id)
	{
		$session = new Object;
		$session->session_id = $id;
		$session->data       = $this->read($id);

		return $session;
	}

	/**
	 * Get list of all sessions
	 *
	 * @param   array  $filters
	 * @return  array
	 */
	public function all($filters = array())
	{
		$files = $this->files->listContents($this->path);

		$sessions = array();

		foreach ($files as $file)
		{
			if (!$file->isFile())
			{
				continue;
			}

			$session = new Object;
			$session->session_id = $file->getName();
			$session->data       = $this->files->read($file->getPathname());

			$sessions[] = $session;
		}

		return $sessions;
	}

	/**
	 * Strip additional / or \ in a path name
	 *
	 * @param   string  $path  The path to clean
	 * @param   string  $ds    Directory separator (optional)
	 * @return  string  The cleaned path
	 */
	protected function cleanPath($path, $ds = DIRECTORY_SEPARATOR)
	{
		$path = trim($path);

		// Remove double slashes and backslahses and convert
		// all slashes and backslashes to DIRECTORY_SEPARATOR
		return preg_replace('#[/\\\\]+#', $ds, $path);
	}
}
