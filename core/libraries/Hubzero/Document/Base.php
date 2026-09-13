<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document;

use Hubzero\Base\Obj;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;

/**
 * Document class, provides an easy interface to parse and display a document
 *
 * Inspired by Joomla's JDocument class
 *
 * @todo  Rewrite all of this.
 */
class Base extends Obj
{
    /**
     * Document mime type (non-underscored alias)
     *
     * @var  string
     */
    public $mime = '';

    /**
     * Document type identifier (non-underscored alias)
     *
     * @var  string
     */
    public $type = null;

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
     * Directory the active template was found in
     *
     * The companion of baseurl and template, which address the same place as
     * a URL. Kept so that anything wanting to know whether the template
     * carries a file can ask the filesystem without rebuilding the path from
     * the template's name.
     *
     * @var  string
     */
    public $templatePath = null;

    /**
     * Name of the template the active one inherits from, where it does
     *
     * @var  string
     */
    public $templateParent = '';

    /**
     * Directory the parent template was found in
     *
     * @var  string
     */
    public $templateParentPath = '';

    /**
     * Base url of the parent template's root
     *
     * A child in app can inherit from a template in core, so the parent's
     * files are not addressed under the child's base url.
     *
     * @var  string
     */
    public $templateParentBase = '';

    /**
     * Document generator
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public $_generator = 'HUBzero - The open source platform for scientific and educational collaboration';

    /**
     * Document modified date
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public $_mdate = '';

    /**
     * Tab string
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public $_tab = "\11";

    /**
     * Contains the line end string
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public $_lineEnd = "\12";

    /**
     * Contains the character encoding string
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public $_charset = 'utf-8';

    /**
     * Document mime type
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public $_mime = '';

    /**
     * Document namespace
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public $_namespace = '';

    /**
     * Document profile
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public $_profile = '';

    /**
     * Array of linked scripts
     *
     * @var  array
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public $_scripts = array();

    /**
     * Array of scripts placed in the header
     *
     * @var  array
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public $_script = array();

    /**
     * Array of linked style sheets
     *
     * @var  array
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public $_styleSheets = array();

    /**
     * Array of included style declarations
     *
     * @var  array
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public $_style = array();

    /**
     * Array of meta tags
     *
     * @var  array
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public $_metaTags = array();

    /**
     * The rendering engine
     *
     * @var  object
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public $_engine = null;

    /**
     * The document type
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public $_type = null;

    /**
     * Array of buffered output
     *
     * @var  mixed (depends on the renderer)
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public static $_buffer = null;

    /**
     * Class constructor.
     *
     * @param   array  $options  Associative array of options
     * @return  void
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    public function __construct($options = array())
    {
        parent::__construct();

        if (array_key_exists('lineend', $options)) {
            $this->setLineEnd($options['lineend']);
        }

        if (array_key_exists('charset', $options)) {
            $this->setCharset($options['charset']);
        }

        if (array_key_exists('language', $options)) {
            $this->setLanguage($options['language']);
        }

        if (array_key_exists('direction', $options)) {
            $this->setDirection($options['direction']);
        }

        if (array_key_exists('tab', $options)) {
            $this->setTab($options['tab']);
        }

        if (array_key_exists('link', $options)) {
            $this->setLink($options['link']);
        }

        if (array_key_exists('base', $options)) {
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
        if ($name == 'generator') {
            $result = $this->getGenerator();
        } elseif ($name == 'description') {
            $result = $this->getDescription();
        } else {
            if ($httpEquiv == true) {
                if (isset($this->_metaTags['http-equiv'][$name])) {
                    $result = $this->_metaTags['http-equiv'][$name];
                }
            } else {
                if (isset($this->_metaTags['standard'][$name])) {
                    $result = $this->_metaTags['standard'][$name];
                }
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

        if ($name == 'generator') {
            $this->setGenerator($content);
        } elseif ($name == 'description') {
            $this->setDescription($content);
        } else {
            if ($http_equiv == true) {
                $this->_metaTags['http-equiv'][$name] = $content;

                // Syncing with HTTP-header
                if ($sync && strtolower($name) == 'content-type') {
                    $this->setMimeEncoding($content, false);
                }
            } else {
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
        if (!isset($this->_script[strtolower($type)])) {
            $this->_script[strtolower($type)] = array($content);
        } else {
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
        if (!isset($this->_style[strtolower($type)])) {
            $this->_style[strtolower($type)] = $content;
        } else {
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
     * The path of a file inside the active template, or its parent
     *
     * The filesystem companion of asset(): the same child-then-parent search,
     * answering where a file is rather than how to link to it.
     *
     * Templates reach for __DIR__ to find their own files, which is the same
     * assumption asset() used to make about names - and it breaks in the same
     * place. A child template's shells are rendered from its parent's
     * directory, so __DIR__ there is the parent's, and a file the child
     * shipped is invisible to it.
     *
     * @param   string  $file  Path within the template, eg home.php
     * @return  string  The absolute path, or an empty string where there is none
     */
    public function templateFile($file)
    {
        $file = ltrim((string) $file, '/');

        if ($file === '') {
            return '';
        }

        foreach (array($this->templatePath, $this->templateParentPath) as $dir) {
            if (!$dir) {
                continue;
            }

            $path = $dir . DS . str_replace('/', DS, $file);

            if (is_file($path)) {
                return $path;
            }
        }

        return '';
    }

    /**
     * The address of a file inside the active template
     *
     * Templates have built these by hand - the base url, the word templates,
     * the template's own name, the path - and then called filemtime() on a
     * literal path beside it to bust the cache. That asks every template to
     * know where it lives, repeats the same four-part concatenation in every
     * file, and throws when the file it names is not there, because this
     * platform promotes that warning to an exception.
     *
     * Asking here instead puts the question in one place - which is also the
     * place a child template's parent is searched. The active template is
     * asked first and the parent second, so a child carries the files it
     * wants to change and inherits the rest at their own addresses.
     *
     * A file neither has still gets an address, unversioned, rather than
     * stopping the page: a stylesheet that 404s is a worse page, not a broken
     * one.
     *
     * It lives here rather than on the html document because error.php is
     * rendered by a different document type, and an error page that cannot
     * link its own stylesheet is the page you least want to break.
     *
     * @param   string   $file     Path within the template, eg js/core.js
     * @param   boolean  $version  Append the file's modification time
     * @return  string
     */
    public function asset($file, $version = true)
    {
        $file = ltrim((string) $file, '/');
        $url  = $this->baseurl . '/templates/' . $this->template . '/' . $file;

        if ($file === '') {
            return $url;
        }

        $roots = array(
            array($this->templatePath, $this->baseurl, $this->template),
            array($this->templateParentPath, $this->templateParentBase, $this->templateParent),
        );

        foreach ($roots as $root) {
            list($dir, $base, $name) = $root;

            if (!$dir || !$name) {
                continue;
            }

            $path = $dir . DS . str_replace('/', DS, $file);

            if (!is_file($path)) {
                continue;
            }

            $found = $base . '/templates/' . $name . '/' . $file;

            return $version ? $found . '?v=' . filemtime($path) : $found;
        }

        return $url;
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
        if ($sync) {
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
        switch ($style) {
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
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
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
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
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

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(Lang::txt('Unable to load renderer class'), 500);
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
     * @return  void
     */
    public function render($cache = false, $params = array())
    {
        if ($mdate = $this->getModifiedDate()) {
            App::get('response')->headers->set('Last-Modified', $mdate /* gmdate('D, d M Y H:i:s', time() + 900) .
                ' GMT' */);
        }

        App::get('response')->headers->set('Content-Type', $this->_mime .
            ($this->_charset ? '; charset=' .
            $this->_charset : ''));
    }
}
