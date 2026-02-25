<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parsermarkdown;

use Hubzero\Facades\Html;
use Hubzero\Facades\Route;

/**
 * Markdown parser class
 */
class MarkdownParser
{
    /**
     * Token storage for stripped blocks
     *
     * @var  array
     */
    private $tokens = array();

    /**
     * A unique token
     *
     * @var string
     */
    private $_token = null;

    /**
     * Configuration options
     *
     * @var array
     */
    private $_config = array(
        'option'    => null,
        'scope'     => null,
        'pagename'  => null,
        'pageid'    => null,
        'filepath'  => null,
        'path'      => null,
        'macros'    => true,
        'domain'    => '',
        'domain_id' => 0,
        'url'       => '',

        'fullparse' => true,
        'camelcase' => true,
        'loglinks'  => false,
        'style'     => 'Markdown'
    );

    /**
     * Data store
     *
     * @var array
     */
    private $_data = array(
        'input'  => null,
        'output' => null,
        'links'  => array()
    );

    /**
     * Constructor
     *
     * @param      array $config Configuration options
     * @return     void
     */
    public function __construct($config = array())
    {
        if (is_array($config)) {
            // We need this info for links that may get generated
            foreach ($config as $k => $s) {
                $this->set($k, $s);
            }
        }

        // Set the unique token
        $this->_token = "\x07UNIQ" . $this->_randomString();
    }

    /**
     * Modifies a property of the object, creating it if it does not already exist.
     *
     * @param   string  $property  The name of the property.
     * @param   mixed   $value     The value of the property to set.
     * @return  object  Method chaining
     */
    public function set($property, $value = null)
    {
        $this->_config[$property] = $value;
        return $this;
    }

    /**
     * Returns a property of the object or the default value if the property is not set.
     *
     * @param   string  $property  The name of the property.
     * @param   mixed   $default   The default value.
     * @return  mixed   The value of the property.
     */
    public function get($property, $default = null)
    {
        if (isset($this->_config[$property])) {
            return $this->_config[$property];
        }
        return $default;
    }

    /**
     * Get the unique string
     *
     * @return     string
     */
    public function token()
    {
        return $this->_token;
    }

    /**
     * Get the raw input
     *
     * @return     string
     */
    public function input()
    {
        return $this->_data['input'];
    }

    /**
     * Get the parsed output
     *
     * @return     string
     */
    public function output()
    {
        return $this->_data['output'];
    }

    /**
     * Generate a unique prefix
     *
     * @return     integer
     */
    private function _randomString()
    {
        return bin2hex(random_bytes(8));
    }

    /**
     * Where all the magic takes place
     * Turns raw wiki text to HTML
     *
     * @param      string  $text      Raw wiki markup
     * @param      boolean $fullparse Full or limited parse? Limited does not parse macros
     * @param      integer $linestart Parameter description (if any) ...
     * @param      integer $camelcase Parameter description (if any) ...
     * @return     unknown Return description (if any) ...
     */
    public function parse($text, $fullparse = true, $linestart = 0, $camelcase = 1)
    {
        // Store the raw input
        $this->_data['input'] = $text;

        // Set some configs
        $this->set('fullparse', $fullparse);
        $this->set('camelcase', $camelcase);
        if (!$this->get('fullparse')) {
            $this->set('camelcase', 0);
        }

        // Remove any trailing whitespace
        $text = rtrim($text);

        // Replace $$ with math for parsing
        $pattern = '/\$\$(.+?)\$\$/';
        $replacement = '<math>${1}</math>';
        $text = preg_replace($pattern, $replacement, $text);

        // The Extra and GitHub flavours carry tables, and ours add
        // scope="col" to the header cells; plain Markdown has no tables
        $parsers = array(
            'Markdown'       => \cebe\markdown\Markdown::class,
            'MarkdownExtra'  => Markdown\MarkdownExtra::class,
            'GithubMarkdown' => Markdown\GithubMarkdown::class,
        );
        $style = $this->get('style', 'Markdown');
        $cls   = isset($parsers[$style]) ? $parsers[$style] : $parsers['Markdown'];

        $parser = new $cls();
        $text = $parser->parse($text);

        // parse local images
        $pattern = '@src="Image\(([^"]+)\)"@';

        if ($this->get('pageid')) { // Only valid on actual wiki pages (not to-do comments, etc)
            $page = \Components\Wiki\Models\Page::oneOrFail($this->get('pageid'));
            $link = $page->link();

            $text = preg_replace_callback($pattern, function ($matches) use ($link) {
                return 'src="' . Route::url($link . '/Image:' . $matches[1]) . '"';
            }, $text);
        }

        // If full parse and we have a page ID (i.e., this is a *wiki* page) and link logging is turned on...
        /*if ($this->get('fullparse') && $this->get('pageid') && $this->get('loglinks'))
        {
            $links = \Components\Wiki\Models\Link::blank();
            $links->updateLinks($this->get('pageid'), $this->_data['links']);
        }*/

        // Process LaTeX math forumlas and strip out
        // This will return either simple HTML or an image
        // We'll put this back after other processes
        $text = $this->math($text);

        // Put back removed blocks <pre>, <code>, <a>, <math>
        $text = $this->unstrip($text);

        $this->_data['output'] = $text;

        if (trim($this->_data['input']) && !trim($this->_data['output'])) {
            $this->_data['output']  = '<p class="warning">Parsing error resulted in empty content. Displaying raw markup below.</p>';
            $this->_data['output'] .= '<pre>' . htmlentities($this->_data['input'], ENT_COMPAT, 'UTF-8') . '</pre>';
        }

        return $this->_data['output'];
    }

    /**
     * Convert math forumlas
     *
     * @param   string  $text  Wiki markup
     * @return  string  Parsed wiki content
     */
    private function math($text)
    {
        Html::behavior('math');
        return preg_replace_callback('/<math>(.*?)<\/math>/s', array(&$this, '_stripMath'), $text);
    }

    /**
     * Wrap an older math formula in $$ delimiters for MathJax to render
     *
     * @param   array   $matches    Wiki markup matching a <math>formula</math>
     * @return  string
     */
    private function _stripMath($matches)
    {
        return $this->_dataPush(array(
            $matches[0],
            'math',
            $this->_randomString(),
            '$$' . $matches[1] . '$$'
        ));
    }

    /**
     * Format math output before injecting back into primary text
     *
     * @param   string  $txt  Math output
     * @return  string
     */
    private function _restoreMath($txt)
    {
        return '<span class="asciimath">' . $txt . '</span>';
    }

    /**
     * Put <pre> blocks back into the main content flow
     *
     * @param      string  $text Wiki markup
     * @param      boolean $html Escape HTML?
     * @return     string
     */
    private function unstrip($text, $html = true)
    {
        $this->set('wikitohtml', $html);

        if (!empty($this->_tokens)) {
            foreach ($this->_tokens as $tag => $vals) {
                $text = preg_replace_callback('/<(' . $tag . ') (.+?)>(.*)<\/(\1) \2>/si', array(&$this, '_dataPull'), $text);
                $this->_tokens[$tag] = array();
            }
        }

        return $text;
    }

    /**
     * Store an item in the shelf
     * Returns a unique ID as a placeholder for content retrieval later on
     *
     * @param      array   $matches Content to store
     * @return     integer Unique ID
     */
    private function _dataPush($matches)
    {
        $tag = $matches[1];
        $key = $matches[2];
        $val = $matches[3];

        $this->_tokens[$tag][$key] = $val;
        return '<' . $tag . ' ' . $key . '></' . $tag . ' ' . $key . '>';
    }

    /**
     * Store an item in the shelf
     * Returns a unique ID as a placeholder for content retrieval later on
     *
     * @param      string  $matches Content to store
     * @return     integer Unique ID
     */
    private function _dataPull($matches)
    {
        $tag = $matches[1];
        $key = $matches[2];

        if (isset($this->_tokens[$tag]) && isset($this->_tokens[$tag][$key])) {
            return $this->{'_restore' . ucfirst($tag)}($this->_tokens[$tag][$key]);
        }
        if ($tag == 'macro') {
            return '';
        }
        return $matches[0];
    }
}
