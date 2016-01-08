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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem;

use Hubzero\Filesystem\Util\MimeType;

/**
 * File model
 */
class File extends Entity
{
	/**
	 * Creates a new object using a path as our starting point
	 *
	 * Don't forget, paths are relative to the adapter root
	 *
	 * @param   string  $path     The path to use to build a new entity from
	 * @param   object  $adapter  The filesystem adapter to use for interaction with the entity
	 * @return  static
	 **/
	public static function fromPath($path, $adapter = null)
	{
		// If the entity exists, grab its metadata
		if (isset($adapter) && $adapter->has($path))
		{
			$metadata = $adapter->getMetadata($path);
		}
		else
		{
			// Otherwise, we'll make our best guess at the appropriate data
			$path = trim($path, '/');

			// The minimum required data is a path and a type
			$metadata = [
				'type' => 'file',
				'path' => $path
			];
		}

		return self::getSpecialized($metadata, $adapter);
	}

	/**
	 * Checks to see if file is an image
	 *
	 * @return  bool
	 */
	public function isImage()
	{
		return strpos($this->getMimetype(), 'image/') !== false ? true : false;
	}

	/**
	 * Checks if file is binary
	 *
	 * @return  bool
	 */
	public function isBinary()
	{
		return substr($this->getMimetype(), 0, 4) == 'text' ? false : true;
	}

	/**
	 * Checks to see if entity can be expanded
	 *
	 * @return  bool
	 **/
	public function isExpandable()
	{
		$expandable = __NAMESPACE__ . '\\Type\\Expandable';
		return ($this instanceof $expandable);
	}

	/**
	 * Grabs the item name, without extension
	 *
	 * @return  string
	 **/
	public function getDisplayName()
	{
		return str_replace('.' . $this->getExtension(), '', $this->getName());
	}

	/**
	 * Gets the file mimetype
	 *
	 * @return  string
	 */
	public function getMimetype()
	{
		if (!isset($this->mimetype))
		{
			$this->mimetype = $this->hasAdapterOrFail()->adapter->getMimetype($this->getPath());
		}

		return $this->mimetype;
	}

	/**
	 * Grabs the item size
	 *
	 * @param   bool    $raw  Whether or not to return raw size (vs formatted size)
	 * @return  string|int
	 **/
	public function getSize($raw = false)
	{
		if (!isset($this->size))
		{
			$this->size = $this->hasAdapterOrFail()->adapter->getSize($this->getPath());
		}

		return ($raw) ? $this->size : \Hubzero\Utility\Number::formatBytes($this->size);
	}

	/**
	 * Grabs the entity extension
	 *
	 * @return  string
	 **/
	public function getExtension()
	{
		if (!isset($this->extension))
		{
			$bits = explode('.', $this->getName());
			$this->extension = end($bits);
		}

		return $this->extension;
	}

	/**
	 * Checks if current file has the given extension
	 *
	 * @param   string  $extension  The extension to compare against
	 * @return  bool
	 **/
	public function hasExtension($extension)
	{
		return $this->getExtension() == $extension;
	}

	/**
	 * Reads the file
	 *
	 * @return  string
	 **/
	public function read()
	{
		if (!isset($this->contents))
		{
			$this->contents = $this->hasAdapterOrFail()->adapter->read($this->getPath());
		}

		return (string) $this->contents;
	}

	/**
	 * Writes contents to the file
	 *
	 * @param   string  $contents  The contents to write to the file
	 * @return  bool
	 **/
	public function write($contents)
	{
		return $this->hasAdapterOrFail()->adapter->write($this->getPath(), $contents);
	}

	/**
	 * Updates the file
	 *
	 * @param   string  $contents  The contents to write to the file
	 * @return  bool
	 **/
	public function update($contents)
	{
		return $this->hasAdapterOrFail()->adapter->update($this->getPath(), $contents);
	}

	/**
	 * Updates or creates the file
	 *
	 * @param   string  $contents  The contents to write to the file
	 * @return  bool
	 **/
	public function put($contents)
	{
		return $this->hasAdapterOrFail()->adapter->put($this->getPath(), $contents);
	}

	/**
	 * Updates or creates the file using stream input
	 *
	 * @param   resource  $contents  The contents to write to the file
	 * @return  bool
	 **/
	public function putStream($contents)
	{
		return $this->hasAdapterOrFail()->adapter->putStream($this->getPath(), $contents);
	}

	/**
	 * Saves the file contents that are already set on the object
	 *
	 * @return  bool
	 **/
	public function save()
	{
		if (!isset($this->contents)) return false;

		// We can save a stream or string...so see which it is
		$method = (is_resource($this->contents) && get_resource_type($this->contents) == 'stream') ? 'putStream' : 'put';

		return $this->$method($this->contents);
	}

	/**
	 * Updates or creates the file
	 *
	 * @param   string  $contents  The contents to write to the file
	 * @return  bool
	 **/
	public function createOrUpdate($contents)
	{
		return $this->put($contents);
	}

	/**
	 * Reads the file
	 *
	 * @return  string
	 **/
	public function readAndDelete()
	{
		if (!isset($this->contents))
		{
			$this->contents = $this->hasAdapterOrFail()->adapter->readAndDelete($this->getPath());
		}
		else
		{
			// We've already read it...so just go ahead and delete rather than reading again
			$this->delete();
		}

		return $this->contents;
	}

	/**
	 * Deletes the file
	 *
	 * @return  bool
	 **/
	public function delete()
	{
		return $this->hasAdapterOrFail()->adapter->delete($this->getPath());
	}

	/**
	 * Serves up the file to the web
	 *
	 * @param   string  $as  What to serve the file as
	 * @return  bool
	 **/
	public function serve($as = null)
	{
		// Initiate a new content server
		$server = new \Hubzero\Content\Server();
		$server->disposition('attachment');
		$server->acceptranges(false);

		if (!$this->isLocal())
		{
			// Create a temp file and write to it
			$temp = tmpfile();
			fwrite($temp, $this->read());
			$server->filename(stream_get_meta_data($temp)['uri']);
		}
		else
		{
			$server->filename($this->getAbsolutePath());
		}

		$server->saveas($as ?: $this->getName());

		// Serve up the file
		$result = $server->serve();

		// Clean up after serving
		if (isset($temp) && is_resource($temp))
		{
			fclose($temp);
		}

		return $result;
	}
}