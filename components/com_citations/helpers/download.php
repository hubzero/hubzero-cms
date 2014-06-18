<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Citations class for downloading a citation of a specific file type
 */
class CitationsDownload
{
	/**
	 * Description for '_format'
	 *
	 * @var string
	 */
	protected $_format = 'Bibtex';

	/**
	 * Description for '_reference'
	 *
	 * @var unknown
	 */
	protected $_reference = null;

	/**
	 * Description for '_formatters'
	 *
	 * @var array
	 */
	protected $_formatters = array();

	/**
	 * Description for '_mime'
	 *
	 * @var string
	 */
	protected $_mime = '';

	/**
	 * Description for '_extension'
	 *
	 * @var string
	 */
	protected $_extension = '';

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $reference Parameter description (if any) ...
	 * @param      string $format Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($reference=null, $format='Bibtex')
	{
		$this->setFormat($format);
		$this->setReference($reference);
	}

	/**
	 * Short description for 'setFormat'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $format Parameter description (if any) ...
	 * @return     void
	 */
	public function setFormat($format)
	{
		$this->_format = ucfirst(trim(strtolower($format)));
	}

	/**
	 * Short description for 'getFormat'
	 *
	 * Long description (if any) ...
	 *
	 * @return     string Return description (if any) ...
	 */
	public function getFormat()
	{
		return $this->_format;
	}

	/**
	 * Short description for 'setReference'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $reference Parameter description (if any) ...
	 * @return     void
	 */
	public function setReference($reference)
	{
		$this->_reference = $reference;
	}

	/**
	 * Short description for 'getReference'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function getReference()
	{
		return $this->_reference;
	}

	/**
	 * Short description for 'setMimeType'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $mime Parameter description (if any) ...
	 * @return     void
	 */
	public function setMimeType($mime)
	{
		$this->_mime = $mime;
	}

	/**
	 * Short description for 'getMimeType'
	 *
	 * Long description (if any) ...
	 *
	 * @return     string Return description (if any) ...
	 */
	public function getMimeType()
	{
		return $this->_mime;
	}

	/**
	 * Short description for 'setExtension'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $extension Parameter description (if any) ...
	 * @return     void
	 */
	public function setExtension($extension)
	{
		$this->_extension = $extension;
	}

	/**
	 * Short description for 'getExtension'
	 *
	 * Long description (if any) ...
	 *
	 * @return     string Return description (if any) ...
	 */
	public function getExtension()
	{
		return $this->_extension;
	}

	/**
	 * Short description for 'setFormatter'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $formatter Parameter description (if any) ...
	 * @param      string $format Parameter description (if any) ...
	 * @return     void
	 */
	public function setFormatter($formatter, $format='')
	{
		$format = ($format) ? $format : $this->_format;

		$this->_formatter[$format] = $formatter;
	}

	/**
	 * Short description for 'getFormatter'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $format Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getFormatter($format='')
	{
		$format = ($format) ? $format : $this->_format;

		return (isset($this->_formatter[$format])) ? $this->_formatter[$format] : NULL;
	}

	/**
	 * Format a record
	 *
	 * @param      object $reference Record to format
	 * @return     string
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
			$cls = 'CitationsDownload' . $format;

			if (is_file(JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'download' . DS . strtolower($format) . '.php'))
			{
				include_once(JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'download' . DS . strtolower($format) . '.php');
			}
			if (!class_exists($cls))
			{
				JError::raiseError(500, JText::_('Download format unavailable.'));
				return;
			}

			$formatter = new $cls();

			if (!$formatter instanceof CitationsDownloadAbstract)
			{
				JError::raiseError(500, JText::_('Invalid download formatter specified.'));
				return;
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

