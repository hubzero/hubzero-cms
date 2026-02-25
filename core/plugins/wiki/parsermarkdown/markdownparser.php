<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parsermarkdown;

use Hubzero\Facades\Route;
use Hubzero\Facades\Html;

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
    private $token = null;

    /**
     * Configuration options
     *
     * @var array
     */
    private $config = array(
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
    private $data = array(
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
        $this->token = "\x07UNIQ" . $this->randomString();
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
        $this->config[$property] = $value;
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
        if (isset($this->config[$property])) {
            return $this->config[$property];
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
        return $this->token;
    }

    /**
     * Get the raw input
     *
     * @return     string
     */
    public function input()
    {
        return $this->data['input'];
    }

    /**
     * Get the parsed output
     *
     * @return     string
     */
    public function output()
    {
        return $this->data['output'];
    }

    /**
     * Generate a unique prefix
     *
     * @return     integer
     */
    private function randomString()
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
        $this->data['input'] = $text;

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

        $cls = '\\cebe\\markdown\\' . $this->get('style', 'Markdown');

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
            $links->updateLinks($this->get('pageid'), $this->data['links']);
        }*/

        // Process LaTeX math forumlas and strip out
        // This will return either simple HTML or an image
        // We'll put this back after other processes
        $text = $this->math($text);

        // Put back removed blocks <pre>, <code>, <a>, <math>
        $text = $this->unstrip($text);

        $this->data['output'] = $text;

        if (trim($this->data['input']) && !trim($this->data['output'])) {
            $this->data['output']  = '<p class="warning">Parsing error resulted in empty content. '
                . 'Displaying raw markup below.</p>';
            $this->data['output'] .= '<pre>' . htmlentities($this->data['input'], ENT_COMPAT, 'UTF-8') . '</pre>';
        }

        return $this->data['output'];
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
        return preg_replace_callback('/<math>(.*?)<\/math>/s', array(&$this, 'stripMath'), $text);
    }

    /**
     * Wrap an older math formula in $$ delimiters for MathJax to render
     *
     * @param    array    $matches    Wiki markup matching a <math>formula</math>
     * @return    string
     */
    private function stripMath($matches)
    {
        return $this->dataPush(array(
            $matches[0],
            'math',
            $this->randomString(),
            '$$' . $matches[1] . '$$'
        ));
    }

    /**
     * Format math output before injecting back into primary text
     *
     * @param   string  $txt  Math output
     * @return  string
     */
    private function restoreMath($txt)
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

        if (!empty($this->tokens)) {
            foreach ($this->tokens as $tag => $vals) {
                $pattern = '/<(' . $tag . ') (.+?)>(.*)<\/(\1) \2>/si';
                $text = preg_replace_callback($pattern, array(&$this, 'dataPull'), $text);
                $this->tokens[$tag] = array();
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
    private function dataPush($matches)
    {
        $tag = $matches[1];
        $key = $matches[2];
        $val = $matches[3];

        $this->tokens[$tag][$key] = $val;
        return '<' . $tag . ' ' . $key . '></' . $tag . ' ' . $key . '>';
    }

    /**
     * Store an item in the shelf
     * Returns a unique ID as a placeholder for content retrieval later on
     *
     * @param      string  $matches Content to store
     * @return     integer Unique ID
     */
    private function dataPull($matches)
    {
        $tag = $matches[1];
        $key = $matches[2];

        if (isset($this->tokens[$tag]) && isset($this->tokens[$tag][$key])) {
            return $this->{'restore' . ucfirst($tag)}($this->tokens[$tag][$key]);
        }
        if ($tag == 'macro') {
            return '';
        }
        return $matches[0];
    }
}
