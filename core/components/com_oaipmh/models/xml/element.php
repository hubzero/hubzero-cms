<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Oaipmh\Models\Xml;

use LogicException;
use DOMElement;
use Hubzero\Facades\Lang;

/**
 * XML Response Element
 */
class Element
{
    /**
     * The element being built, or the document itself at the top
     *
     * @var  \DOMNode
     */
    protected $current;

    /**
     * @var  \DOMDocument
     */
    protected $dom;

    /**
     * Constructor
     *
     * @param   \DOMElement $element  DOMElement
     * @return  void
     * @throws  LogicException
     */
    public function __construct(DOMElement $element)
    {
        if (null === $element->ownerDocument) {
            throw new LogicException(Lang::txt('Owner document is not set'));
        }

        $this->current = $element;
        $this->dom = $element->ownerDocument;
    }

    /**
     * Assigns $name to new XmlElement which holds pointer to current XML element
     *
     * @param   mixed   $name
     * @return  $this  $this
     */
    public function reference(&$name)
    {
        // At the top the current node is the document; reference its root element
        $element = $this->current instanceof \DOMElement ? $this->current : $this->dom->documentElement;
        if ($element === null) {
            throw new \RuntimeException('The document has no root element to reference');
        }
        $name = new self($element);

        return $this;
    }

    /**
     * Close open element
     *
     * @return  $this  $this
     * @throws  LogicException
     */
    public function end()
    {
        if (null === $this->current->parentNode) {
            throw new LogicException(Lang::txt('Could not find parent node'));
        }

        $this->current = $this->current->parentNode;

        return $this;
    }

    /**
     * Create a new element
     *
     * @param   string  $name
     * @param   string|null  $value
     * @param   string|null  $namespace
     * @return  $this  $this
     */
    public function element($name, $value = null, $namespace = null)
    {
        $element = $namespace ? $this->dom->createElementNS($namespace, $name) : $this->dom->createElement($name);

        $this->current->appendChild($element);
        $this->current = $element;

        if (null !== $value) {
            $this->text($value);
        }

        return $this;
    }

    /**
     * Set some text content
     *
     * @param   string  $content
     * @return  $this  $this
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
     * @param   string|null  $namespace
     * @return  $this  $this
     */
    public function attr($name, $value, $namespace = null)
    {
        if (!($this->current instanceof DOMElement)) {
            throw new LogicException('An attribute can only be set on an element');
        }

        if (null !== $namespace) {
            $this->current->setAttributeNS($namespace, $name, $value);
        } else {
            $this->current->setAttribute($name, $value);
        }

        return $this;
    }

    /**
     * Add a comment
     *
     * @param   string  $content
     * @return  $this  $this
     */
    public function comment($content)
    {
        $this->current->appendChild($this->dom->createComment(htmlentities($content, ENT_QUOTES | ENT_XML1)));

        return $this;
    }

    /**
     * Set content as CDATA
     *
     * @param   string  $content
     * @return  $this  $this
     */
    public function cdata($content)
    {
        $this->current->appendChild($this->dom->createCDATASection($content));

        return $this;
    }
}
