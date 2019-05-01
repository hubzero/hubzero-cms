<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
