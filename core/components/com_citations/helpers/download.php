<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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

		return (isset($this->_formatter[$format])) ? $this->_formatter[$format] : null;
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
			$cls = '\\Components\\Citations\\Download\\' . $format;

			if (is_file(dirname(__DIR__) . DS . 'download' . DS . strtolower($format) . '.php'))
			{
				include_once dirname(__DIR__) . DS . 'download' . DS . strtolower($format) . '.php';
			}
			if (!class_exists($cls))
			{
				throw new Exception(Lang::txt('Download format unavailable.'), 500);
			}

			$formatter = new $cls();

			if (!$formatter instanceof Downloadable)
			{
				throw new Exception(Lang::txt('Invalid download formatter specified.'), 500);
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
