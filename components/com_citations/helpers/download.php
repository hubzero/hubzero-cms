<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Citations\Helpers;

use Components\Citations\Download\Downloadable;
use Exception;

/**
 * Citations class for downloading a citation of a specific file type
 */
class Download
{
	/**
	 * Download formt
	 *
	 * @var string
	 */
	protected $_format = 'Bibtex';

	/**
	 * Citation object
	 *
	 * @var object
	 */
	protected $_reference = null;

	/**
	 * List of formatters
	 *
	 * @var array
	 */
	protected $_formatters = array();

	/**
	 * Mime type
	 *
	 * @var string
	 */
	protected $_mime = '';

	/**
	 * File extension
	 *
	 * @var  string
	 */
	protected $_extension = '';

	/**
	 * Constructor
	 *
	 * @param   object  $reference
	 * @param   string  $format
	 * @return  void
	 */
	public function __construct($reference=null, $format='Bibtex')
	{
		$this->setFormat($format);
		$this->setReference($reference);
	}

	/**
	 * Set the format
	 *
	 * @param   string  $format
	 * @return  void
	 */
	public function setFormat($format)
	{
		$this->_format = ucfirst(trim(strtolower($format)));
	}

	/**
	 * Get the format
	 *
	 * @return  string
	 */
	public function getFormat()
	{
		return $this->_format;
	}

	/**
	 * Set the reference
	 *
	 * @param   object  $reference
	 * @return  void
	 */
	public function setReference($reference)
	{
		$this->_reference = $reference;
	}

	/**
	 * Get the reference
	 *
	 * @return  object
	 */
	public function getReference()
	{
		return $this->_reference;
	}

	/**
	 * Set the mime type
	 *
	 * @param   string  $mime
	 * @return  void
	 */
	public function setMimeType($mime)
	{
		$this->_mime = $mime;
	}

	/**
	 * Get the mime type
	 *
	 * @return  string
	 */
	public function getMimeType()
	{
		return $this->_mime;
	}

	/**
	 * Set the extension
	 *
	 * @param   string  $extension
	 * @return  void
	 */
	public function setExtension($extension)
	{
		$this->_extension = $extension;
	}

	/**
	 * Get the extension
	 *
	 * @return  string
	 */
	public function getExtension()
	{
		return $this->_extension;
	}

	/**
	 * Set formatter for specified format
	 *
	 * @param   object  $formatter
	 * @param   string  $format
	 * @return  void
	 */
	public function setFormatter($formatter, $format='')
	{
		$format = ($format) ? $format : $this->_format;

		$this->_formatter[$format] = $formatter;
	}

	/**
	 * Get the formatter object, if set
	 *
	 * @param   string  $format  Format to get
	 * @return  mixed
	 */
	public function getFormatter($format='')
	{
		$format = ($format) ? $format : $this->_format;

		return (isset($this->_formatter[$format])) ? $this->_formatter[$format] : NULL;
	}

	/**
	 * Format a record
	 *
	 * @param   object  $reference  Record to format
	 * @return  string
	 */
	public function formatReference($reference=null)
	{
		if (!$reference)
		{
			$reference = $this->getReference();
		}

		if (!$reference || (!is_array($reference) && !is_object($reference)))
		{
			return '';
		}

		$format = $this->getFormat();

		$formatter = $this->getFormatter($format);

		if (!$formatter || !is_object($formatter))
		{
			$cls = '\\components\\Citations\\Download\\' . $format;

			if (is_file(dirname(__DIR__) . DS . 'download' . DS . strtolower($format) . '.php'))
			{
				include_once(dirname(__DIR__) . DS . 'download' . DS . strtolower($format) . '.php');
			}
			if (!class_exists($cls))
			{
				throw new Exception(\JText::_('Download format unavailable.'), 500);
			}

			$formatter = new $cls();

			if (!$formatter instanceof Downloadable)
			{
				throw new Exception(\JText::_('Invalid download formatter specified.'), 500);
			}

			$this->setFormatter($formatter, $format);
			$this->setExtension($formatter->getExtension());
			$this->setMimeType($formatter->getMimeType());
		}
		else
		{
			$this->setExtension($formatter->getExtension());
			$this->setMimeType($formatter->getMimeType());
		}

		return $formatter->format($reference);
	}
}

