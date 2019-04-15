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

namespace Components\Citations\Download;

/**
 * Abstract class for citations download
 */
abstract class Downloadable
{
	/**
	 * Mime type
	 *
	 * @var string
	 */
	protected $_mime = '';

	/**
	 * File extension
	 *
	 * @var string
	 */
	protected $_extension = '';

	/**
	 * Set the mime type
	 *
	 * @param      string $mime Value to set
	 * @return     void
	 */
	public function setMimeType($mime)
	{
		$this->_mime = trim($mime);
	}

	/**
	 * Get the mime type
	 *
	 * @return     string
	 */
	public function getMimeType()
	{
		return $this->_mime;
	}

	/**
	 * Set the file extension
	 *
	 * @param      string $ext Value to set
	 * @return     void
	 */
	public function setExtension($ext)
	{
		$this->_extension = trim($ext);
	}

	/**
	 * Get the file extension
	 *
	 * @return     string
	 */
	public function getExtension()
	{
		return $this->_extension;
	}

	/**
	 * Format the file
	 *
	 * @return     string
	 */
	public function format($row)
	{
		return '';
	}
}
