<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Oaipmh\Models\Xml;

use DOMDocument;

require_once __DIR__ . DS . 'element.php';

/**
 * XML Response Builder
 */
class Response extends Element
{
	/**
	 * @var  bool
	 */
	protected $formatOutput;

	/**
	 * Constructor
	 *
	 * @param   string   $version
	 * @param   string   $encoding
	 * @param   boolean  $formatOutput
	 * @return  void
	 */
	public function __construct($version = '1.0', $encoding = 'utf-8', $formatOutput = false)
	{
		$this->dom = new DOMDocument($version, $encoding);
		$this->formatOutput = (bool) $formatOutput;
		$this->current = $this->dom;
	}

	/**
	 * @param   boolean  $formatOutput
	 * @return  string
	 */
	public function getXml($formatOutput = null)
	{
		$this->dom->formatOutput = is_bool($formatOutput) ? $formatOutput : $this->formatOutput;

		return $this->dom->saveXML();
	}

	/**
	 * @param   string  $styles
	 * @return  object
	 */
	public function stylesheet($styles)
	{
		$xslt = $this->dom->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="' . $styles . '"');
		$this->dom->appendChild($xslt);

		return $this;
	}

	/**
	 * @param   string   $filename
	 * @param   boolean  $formatOutput
	 * @return  boolean
	 */
	public function save($filename, $formatOutput = null)
	{
		$this->dom->formatOutput = is_bool($formatOutput) ? $formatOutput : $this->formatOutput;

		return false !== $this->dom->save($filename);
	}
}
