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
