<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
			throw new LogicException(Lang::txt('Owner document is not set'));
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
			throw new LogicException(Lang::txt('Could not find parent node'));
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
