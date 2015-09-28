<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Hubzero\Session\Storage;

use Hubzero\Filesystem\Filesystem;
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
			$options['filesystem'] = new Filesystem;
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
	public function read($session_id)
	{
		if ($this->files->exists($path = $this->path . DS . $session_id))
		{
			return $this->files->get($path);
		}

		return '';
	}

	/**
	 * Write session data to the SessionHandler backend.
	 *
	 * @param   string   $session_id    The session identifier.
	 * @param   string   $session_data  The session data.
	 * @return  boolean  True on success, false otherwise.
	 */
	public function write($session_id, $session_data)
	{
		$this->files->put($this->path . DS . $session_id, $session_data, true);
	}

	/**
	 * Destroy the data for a particular session identifier in the
	 * SessionHandler backend.
	 *
	 * @param   string   $id  The session identifier.
	 * @return  boolean  True on success, false otherwise.
	 */
	public function destroy($session_id)
	{
		$this->files->delete($this->path . DS . $session_id);
	}

	/**
	 * Garbage collect stale sessions from the SessionHandler backend.
	 *
	 * @param   integer  $maxlifetime  The maximum age of a session.
	 * @return  boolean  True on success, false otherwise.
	 */
	public function gc($maxlifetime = null)
	{
		$files = $this->files->files($this->path);

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
	 * @param   integer  $session_id  Session Id 
	 * @return  object
	 */
	public function session($session_id)
	{
		$session = new Object;
		$session->session_id = $session_id;
		$session->data       = $this->read($session_id);

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
		$files = $this->files->files($this->path);

		$sessions = array();

		foreach ($files as $file)
		{
			if (!$file->isFile())
			{
				continue;
			}

			$session = new Object;
			$session->session_id = $file->getName();
			$session->data       = $this->files->get($file->getPathname());

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
