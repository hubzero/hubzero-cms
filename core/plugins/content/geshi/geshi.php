<?php

namespace Plugins\Content\Geshi;

use Hubzero\Plugin\Plugin;
use Highlight\Highlighter;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die;

/**
 * Code syntax highlighting plugin
 *
 * Uses scrivo/highlight.php (a PHP port of highlight.js)
 */
class Geshi extends Plugin
{
    /**
     * GeSHi language names to highlight.php equivalents
     *
     * @var array
     */
    private static $langMap = [
        'js'           => 'javascript',
        'sh'           => 'bash',
        'asp'          => 'vbnet',
        'html4strict'  => 'xml',
        'php-brief'    => 'php',
        'mysql'        => 'sql',
    ];

    /**
     * Prepare the content for display
     *
     * @param   string   $context  The context of the content being passed to the plugin
     * @param   object   $article  The row object
     * @param   object   $params   The article params
     * @param   integer  $page     The 'page' number
     * @return  void
     */
    public function onContentPrepare($context, &$article, &$params, $page = 0)
    {
        if ($context != 'com_content.article') {
            return true;
        }

        // Simple performance check to determine whether bot should process further.
        if (\Hubzero\Utility\Str::contains($article->text, 'pre>') === false) {
            return true;
        }

        // Define the regular expression for the bot.
        $regex = "#<pre xml:\s*(.*?)>(.*?)</pre>#s";

        // Perform the replacement.
        $article->text = preg_replace_callback($regex, array(&$this, '_replace'), $article->text);

        // Load highlight.js CSS theme
        $this->css('highlight.css');

        return true;
    }

    /**
     * Replaces the matched tags.
     *
     * @param   array   $matches  An array of matches (see preg_match_all)
     * @return  string
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _replace(&$matches)
    {
        $args = self::parseAttributes($matches[1]);
        $text = $matches[2];

        $lang  = \Hubzero\Utility\Arr::getValue($args, 'lang', 'php');
        $lines = \Hubzero\Utility\Arr::getValue($args, 'lines', 'false');

        $html_entities_match   = array("|\<br \/\>|", "#<#", "#>#", "|&#39;|", '#&quot;#', '#&nbsp;#');
        $html_entities_replace = array("\n", '&lt;', '&gt;', "'", '"', ' ');

        $text = preg_replace($html_entities_match, $html_entities_replace, $text);
        $text = str_replace('&lt;', '<', $text);
        $text = str_replace('&gt;', '>', $text);
        $text = str_replace("\t", '  ', $text);

        // Map legacy GeSHi language names to highlight.php names
        $hlLang = self::$langMap[$lang] ?? $lang;

        $hl = new Highlighter();
        try {
            $result = $hl->highlight($hlLang, $text);
            $html = $result->value;
        } catch (\DomainException $e) {
            $html = htmlspecialchars($text);
        }

        if ($lines == 'true') {
            $html = self::addLineNumbers($html);
        }

        return '<pre><code class="hljs ' . htmlspecialchars($lang) . '">' . $html . '</code></pre>';
    }

    /**
     * Wrap highlighted code with line numbers
     *
     * @param   string  $html  Highlighted HTML from highlight.php
     * @return  string
     */
    protected static function addLineNumbers($html)
    {
        $lines = explode("\n", $html);
        $numbered = '<ol class="linenums">';
        foreach ($lines as $line) {
            $numbered .= '<li>' . $line . '</li>';
        }
        $numbered .= '</ol>';
        return $numbered;
    }

    /**
     * Method to extract key/value pairs out of a string with XML style attributes
     *
     * @param   string  $string  String containing XML style attributes
     * @return  array   Key/Value pairs for the attributes
     */
    protected static function parseAttributes($string)
    {
        // Initialise variables
        $attr = array();
        $retarray = array();

        // Let's grab all the key/value pairs using a regular expression
        preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $attr);

        if (is_array($attr)) {
            $numPairs = count($attr[1]);
            for ($i = 0; $i < $numPairs; $i++) {
                $retarray[$attr[1][$i]] = $attr[2][$i];
            }
        }

        return $retarray;
    }
}
