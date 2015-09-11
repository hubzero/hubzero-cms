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

namespace Components\Oaipmh\Models\Xml;

use DOMDocument;

require_once(__DIR__ . DS . 'element.php');

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
