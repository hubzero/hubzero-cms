<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Type;

use Hubzero\Document\Base;

/**
 * XML document class for parsing and displaying an XML page
 *
 * Inspired by Joomla's JDocumentXml class
 */
class Xml extends Base
{
	/**
	 * Document name
	 *
	 * @var  string
	 */
	protected $name = 'hubzero';

	/**
	 * Class constructor
	 *
	 * @param   array  $options  Associative array of options
	 * @return  void
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);

		// set mime type
		$this->_mime = 'application/xml';

		// set document type
		$this->_type = 'xml';
	}

	/**
	 * Render the document.
	 *
	 * @param   boolean  $cache   If true, cache the output
	 * @param   array    $params  Associative array of attributes
	 * @return  The rendered data
	 */
	public function render($cache = false, $params = array())
	{
		$this->setMimeEncoding($this->_mime);

		parent::render();

		\App::get('response')->headers->set('Content-disposition', 'inline; filename="' . $this->getName() . '.xml"', true);

		return $this->getBuffer();
	}

	/**
	 * Returns the document name
	 *
	 * @return  string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Sets the document name
	 *
	 * @param   string  $name  Document name
	 * @return  object  instance of $this to allow chaining
	 */
	public function setName($name)
	{
		$this->name = (string) $name;

		return $this;
	}
}
