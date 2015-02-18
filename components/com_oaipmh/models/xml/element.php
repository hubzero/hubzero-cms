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

use LogicException;
use DOMElement;

/**
 * XML Response Element
 */
class Element
{
	/**
	 * @var  DOMElement
	 */
	protected $current;

	/**
	 * @var  DomDocument
	 */
	protected $dom;

	/**
	 * Constructor
	 *
	 * @param   object  $element  DOMElement
	 * @return  void
	 * @throws  LogicException
	 */
	public function __construct(DOMElement $element)
	{
		if (null === $element->ownerDocument)
		{
			throw new LogicException(\JText::_('Owner document is not set'));
		}

		$this->current = $element;
		$this->dom = $element->ownerDocument;
	}

	/**
	 * Assigns $name to new XmlElement which holds pointer to current XML element
	 *
	 * @param   mixed   $name
	 * @return  object  $this
	 */
	public function reference(&$name)
	{
		$name = new self($this->current);

		return $this;
	}

	/**
	 * Close open element
	 *
	 * @return  object  $this
	 * @throws  LogicException
	 */
	public function end()
	{
		if (null === $this->current->parentNode)
		{
			throw new LogicException(\JText::_('Could not find parent node'));
		}

		$this->current = $this->current->parentNode;

		return $this;
	}

	/**
	 * Create a new element
	 *
	 * @param   string  $name
	 * @param   string  $value
	 * @param   string  $namespace
	 * @return  object  $this
	 */
	public function element($name, $value = null, $namespace = null)
	{
		$element = $namespace ? $this->dom->createElementNS($namespace, $name) : $this->dom->createElement($name);

		$this->current->appendChild($element);
		$this->current = $element;

		if (null !== $value)
		{
			$this->text($value);
		}

		return $this;
	}

	/**
	 * Set some text content
	 *
	 * @param   string  $content
	 * @return  object  $this
	 */
	public function text($content)
	{
		$this->current->appendChild($this->dom->createTextNode($content));

		return $this;
	}

	/**
	 * Set an attribute on an element
	 *
	 * @param   string  $name
	 * @param   string  $value
	 * @param   string  $namespace
	 * @return  object  $this
	 */
	public function attr($name, $value, $namespace = null)
	{
		null !== $namespace ? $this->current->setAttributeNS($namespace, $name, $value) : $this->current->setAttribute($name, $value);

		return $this;
	}

	/**
	 * Add a comment
	 *
	 * @param   string  $content
	 * @return  object  $this
	 */
	public function comment($content)
	{
		$this->current->appendChild($this->dom->createComment(htmlentities($content, ENT_QUOTES|ENT_XML1)));

		return $this;
	}

	/**
	 * Set content as CDATA
	 *
	 * @param   string  $content
	 * @return  object  $this
	 */
	public function cdata($content)
	{
		$this->current->appendChild($this->dom->createCDATASection($content));

		return $this;
	}
}
