<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Hubzero\Document;

use Hubzero\Base\Object;

/**
 * Document class, provides an easy interface to parse and display a document
 *
 * Inspired by Joomla's JDocument class
 *
 * @todo  Rewrite all of this.
 */
class Base extends Object
{
	/**
	 * Document title
	 *
	 * @var  string
	 */
	public $title = '';

	/**
	 * Document description
	 *
	 * @var  string
	 */
	public $description = '';

	/**
	 * Document full URL
	 *
	 * @var  string
	 */
	public $link = '';

	/**
	 * Document base URL
	 *
	 * @var  string
	 */
	public $base = '';

	/**
	 * Contains the document language setting
	 *
	 * @var  string
	 */
	public $language = 'en-gb';

	/**
	 * Contains the document direction setting
	 *
	 * @var  string
	 */
	public $direction = 'ltr';

	/**
	 * Document generator
	 *
	 * @var  string
	 */
	public $_generator = 'HUBzero - The open source platform for scientific and educational collaboration';

	/**
	 * Document modified date
	 *
	 * @var  string
	 */
	public $_mdate = '';

	/**
	 * Tab string
	 *
	 * @var  string
	 */
	public $_tab = "\11";

	/**
	 * Contains the line end string
	 *
	 * @var  string
	 */
	public $_lineEnd = "\12";

	/**
	 * Contains the character encoding string
	 *
	 * @var  string
	 */
	public $_charset = 'utf-8';

	/**
	 * Document mime type
	 *
	 * @var  string
	 */
	public $_mime = '';

	/**
	 * Document namespace
	 *
	 * @var  string
	 */
	public $_namespace = '';

	/**
	 * Document profile
	 *
	 * @var  string
	 */
	public $_profile = '';

	/**
	 * Array of linked scripts
	 *
	 * @var  array
	 */
	public $_scripts = array();

	/**
	 * Array of scripts placed in the header
	 *
	 * @var  array
	 */
	public $_script = array();

	/**
	 * Array of linked style sheets
	 *
	 * @var  array
	 */
	public $_styleSheets = array();

	/**
	 * Array of included style declarations
	 *
	 * @var  array
	 */
	public $_style = array();

	/**
	 * Array of meta tags
	 *
	 * @var  array
	 */
	public $_metaTags = array();

	/**
	 * The rendering engine
	 *
	 * @var  object
	 */
	public $_engine = null;

	/**
	 * The document type
	 *
	 * @var  string
	 */
	public $_type = null;

	/**
	 * Array of buffered output
	 *
	 * @var  mixed (depends on the renderer)
	 */
	public static $_buffer = null;

	/**
	 * Class constructor.
	 *
	 * @param   array  $options  Associative array of options
	 * @return  void
	 */
	public function __construct($options = array())
	{
		parent::__construct();

		if (array_key_exists('lineend', $options))
		{
			$this->setLineEnd($options['lineend']);
		}

		if (array_key_exists('charset', $options))
		{
			$this->setCharset($options['charset']);
		}

		if (array_key_exists('language', $options))
		{
			$this->setLanguage($options['language']);
		}

		if (array_key_exists('direction', $options))
		{
			$this->setDirection($options['direction']);
		}

		if (array_key_exists('tab', $options))
		{
			$this->setTab($options['tab']);
		}

		if (array_key_exists('link', $options))
		{
			$this->setLink($options['link']);
		}

		if (array_key_exists('base', $options))
		{
			$this->setBase($options['base']);
		}
	}

	/**
	 * Get the contents of the document buffer
	 *
	 * @return  string  The contents of the document buffer
	 */
	public function getBuffer()
	{
		return self::$_buffer;
	}

	/**
	 * Set the contents of the document buffer
	 *
	 * @param   string  $content  The content to be set in the buffer.
	 * @param   array   $options  Array of optional elements.
	 * @return  object  Document instance of $this to allow chaining
	 */
	public function setBuffer($content, $options = array())
	{
		self::$_buffer = $content;

		return $this;
	}

	/**
	 * Gets a meta tag.
	 *
	 * @param   string   $name       Value of name or http-equiv tag
	 * @param   boolean  $httpEquiv  META type "http-equiv" defaults to null
	 * @return  string
	 */
	public function getMetaData($name, $httpEquiv = false)
	{
		$result = '';
		$name = strtolower($name);
		if ($name == 'generator')
		{
			$result = $this->getGenerator();
		}
		elseif ($name == 'description')
		{
			$result = $this->getDescription();
		}
		else
		{
			if ($httpEquiv == true)
			{
				$result = @$this->_metaTags['http-equiv'][$name];
			}
			else
			{
				$result = @$this->_metaTags['standard'][$name];
			}
		}

		return $result;
	}

	/**
	 * Sets or alters a meta tag.
	 *
	 * @param   string   $name        Value of name or http-equiv tag
	 * @param   string   $content     Value of the content tag
	 * @param   boolean  $http_equiv  META type "http-equiv" defaults to null
	 * @param   boolean  $sync        Should http-equiv="content-type" by synced with HTTP-header?
	 * @return  object   Document instance of $this to allow chaining
	 */
	public function setMetaData($name, $content, $http_equiv = false, $sync = true)
	{
		$name = strtolower($name);

		if ($name == 'generator')
		{
			$this->setGenerator($content);
		}
		elseif ($name == 'description')
		{
			$this->setDescription($content);
		}
		else
		{
			if ($http_equiv == true)
			{
				$this->_metaTags['http-equiv'][$name] = $content;

				// Syncing with HTTP-header
				if ($sync && strtolower($name) == 'content-type')
				{
					$this->setMimeEncoding($content, false);
				}
			}
			else
			{
				$this->_metaTags['standard'][] = array('name' => $name, 'content' => $content);
			}
		}

		return $this;
	}

	/**
	 * Adds a linked script to the page
	 *
	 * @param   string   $url    URL to the linked script
	 * @param   string   $type   Type of script. Defaults to 'text/javascript'
	 * @param   boolean  $defer  Adds the defer attribute.
	 * @param   boolean  $async  Adds the async attribute.
	 * @return  object   Document instance of $this to allow chaining
	 */
	public function addScript($url, $type = "text/javascript", $defer = false, $async = false)
	{
		$this->_scripts[$url]['mime'] = $type;
		$this->_scripts[$url]['defer'] = $defer;
		$this->_scripts[$url]['async'] = $async;

		return $this;
	}

	/**
	 * Adds a script to the page
	 *
	 * @param   string  $content  Script
	 * @param   string  $type     Scripting mime (defaults to 'text/javascript')
	 * @return  object  Document instance of $this to allow chaining
	 */
	public function addScriptDeclaration($content, $type = 'text/javascript')
	{
		if (!isset($this->_script[strtolower($type)]))
		{
			$this->_script[strtolower($type)] = array($content);
		}
		else
		{
			$this->_script[strtolower($type)][] = chr(13) . $content;
		}

		return $this;
	}

	/**
	 * Adds a linked stylesheet to the page
	 *
	 * @param   string  $url      URL to the linked style sheet
	 * @param   string  $type     Mime encoding type
	 * @param   string  $media    Media type that this stylesheet applies to
	 * @param   array   $attribs  Array of attributes
	 * @return  object  Document instance of $this to allow chaining
	 */
	public function addStyleSheet($url, $type = 'text/css', $media = null, $attribs = array())
	{
		$this->_styleSheets[$url]['mime'] = $type;
		$this->_styleSheets[$url]['media'] = $media;
		$this->_styleSheets[$url]['attribs'] = $attribs;

		return $this;
	}

	/**
	 * Adds a stylesheet declaration to the page
	 *
	 * @param   string  $content  Style declarations
	 * @param   string  $type     Type of stylesheet (defaults to 'text/css')
	 * @return  object  Document instance of $this to allow chaining
	 */
	public function addStyleDeclaration($content, $type = 'text/css')
	{
		if (!isset($this->_style[strtolower($type)]))
		{
			$this->_style[strtolower($type)] = $content;
		}
		else
		{
			$this->_style[strtolower($type)] .= chr(13) . $content;
		}

		return $this;
	}

	/**
	 * Sets the document charset
	 *
	 * @param   string  $type  Charset encoding string
	 * @return  object  Document instance of $this to allow chaining
	 */
	public function setCharset($type = 'utf-8')
	{
		$this->_charset = $type;

		return $this;
	}

	/**
	 * Returns the document charset encoding.
	 *
	 * @return  string
	 */
	public function getCharset()
	{
		return $this->_charset;
	}

	/**
	 * Sets the global document language declaration. Default is English (en-gb).
	 *
	 * @param   string  $lang  The language to be set
	 * @return  object  Document instance of $this to allow chaining
	 */
	public function setLanguage($lang = 'en-gb')
	{
		$this->language = strtolower($lang);

		return $this;
	}

	/**
	 * Returns the document language.
	 *
	 * @return  string
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * Sets the global document direction declaration. Default is left-to-right (ltr).
	 *
	 * @param   string  $dir  The language direction to be set
	 * @return  object  Document instance of $this to allow chaining
	 */
	public function setDirection($dir = 'ltr')
	{
		$this->direction = strtolower($dir);

		return $this;
	}

	/**
	 * Returns the document direction declaration.
	 *
	 * @return  string
	 */
	public function getDirection()
	{
		return $this->direction;
	}

	/**
	 * Sets the title of the document
	 *
	 * @param   string  $title  The title to be set
	 * @return  object  Document instance of $this to allow chaining
	 */
	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * Return the title of the document.
	 *
	 * @return  string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Sets the base URI of the document
	 *
	 * @param   string  $base  The base URI to be set
	 * @return  object  Document instance of $this to allow chaining
	 */
	public function setBase($base)
	{
		$this->base = $base;

		return $this;
	}

	/**
	 * Return the base URI of the document.
	 *
	 * @return  string
	 */
	public function getBase()
	{
		return $this->base;
	}

	/**
	 * Sets the description of the document
	 *
	 * @param   string  $description  The description to set
	 * @return  object  Document instance of $this to allow chaining
	 */
	public function setDescription($description)
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * Return the title of the page.
	 *
	 * @return  string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Sets the document link
	 *
	 * @param   string  $url  A url
	 * @return  object  Document instance of $this to allow chaining
	 */
	public function setLink($url)
	{
		$this->link = $url;

		return $this;
	}

	/**
	 * Returns the document base url
	 *
	 * @return  string
	 */
	public function getLink()
	{
		return $this->link;
	}

	/**
	 * Sets the document generator
	 *
	 * @param   string  $generator  The generator to be set
	 * @return  object  Document instance of $this to allow chaining
	 */
	public function setGenerator($generator)
	{
		$this->_generator = $generator;

		return $this;
	}

	/**
	 * Returns the document generator
	 *
	 * @return  string
	 */
	public function getGenerator()
	{
		return $this->_generator;
	}

	/**
	 * Sets the document modified date
	 *
	 * @param   string  $date  The date to be set
	 * @return  object  Document instance of $this to allow chaining
	 */
	public function setModifiedDate($date)
	{
		$this->_mdate = $date;

		return $this;
	}

	/**
	 * Returns the document modified date
	 *
	 * @return  string
	 */
	public function getModifiedDate()
	{
		return $this->_mdate;
	}

	/**
	 * Sets the document MIME encoding that is sent to the browser.
	 *
	 * @param   string   $type  The document type to be sent
	 * @param   boolean  $sync  Should the type be synced with HTML?
	 * @return  object   Document instance of $this to allow chaining
	 */
	public function setMimeEncoding($type = 'text/html', $sync = true)
	{
		$this->_mime = strtolower($type);

		// Syncing with meta-data
		if ($sync)
		{
			$this->setMetaData('content-type', $type, true, false);
		}

		return $this;
	}

	/**
	 * Return the document MIME encoding that is sent to the browser.
	 *
	 * @return  string
	 */
	public function getMimeEncoding()
	{
		return $this->_mime;
	}

	/**
	 * Sets the line end style to Windows, Mac, Unix or a custom string.
	 *
	 * @param   string  $style  "win", "mac", "unix" or custom string.
	 * @return  object  Document instance of $this to allow chaining
	 */
	public function setLineEnd($style)
	{
		switch ($style)
		{
			case 'win':
				$this->_lineEnd = "\15\12";
			break;

			case 'unix':
				$this->_lineEnd = "\12";
			break;

			case 'mac':
				$this->_lineEnd = "\15";
			break;

			default:
				$this->_lineEnd = $style;
		}

		return $this;
	}

	/**
	 * Returns the lineEnd
	 *
	 * @return  string
	 */
	public function _getLineEnd()
	{
		return $this->_lineEnd;
	}

	/**
	 * Sets the string used to indent HTML
	 *
	 * @param   string  $string  String used to indent ("\11", "\t", '  ', etc.).
	 * @return  object  Document instance of $this to allow chaining
	 */
	public function setTab($string)
	{
		$this->_tab = $string;

		return $this;
	}

	/**
	 * Returns a string containing the unit for indenting HTML
	 *
	 * @return  string
	 */
	public function _getTab()
	{
		return $this->_tab;
	}

	/**
	 * Load a renderer
	 *
	 * @param   string  $type  The renderer type
	 * @return  object  Object or null if class does not exist
	 */
	public function loadRenderer($type)
	{
		$class = __NAMESPACE__ . '\\Type\\' . ucfirst($this->_type) . '\\' . ucfirst($type);

		if (!class_exists($class))
		{
			throw new \InvalidArgumentException(\Lang::txt('Unable to load renderer class'), 500);
		}

		return new $class($this);
	}

	/**
	 * Parses the document and prepares the buffers
	 *
	 * @param   array   $params  The array of parameters
	 * @return  object  Document instance of $this to allow chaining
	 */
	public function parse($params = array())
	{
		return $this;
	}

	/**
	 * Outputs the document
	 *
	 * @param   boolean  $cache   If true, cache the output
	 * @param   array    $params  Associative array of attributes
	 * @return  The rendered data
	 */
	public function render($cache = false, $params = array())
	{
		if ($mdate = $this->getModifiedDate())
		{
			\App::get('response')->headers->set('Last-Modified', $mdate /* gmdate('D, d M Y H:i:s', time() + 900) . ' GMT' */);
		}

		\App::get('response')->headers->set('Content-Type', $this->_mime . ($this->_charset ? '; charset=' . $this->_charset : ''));
	}
}
