<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Plugin for converting support comments from MarkDown to HTML
 */
namespace Plugins\Support\Markdown;

use Hubzero\Plugin\Plugin;

class Markdown extends Plugin
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     */
// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_autoloadLanguage = true;

    /**
     * Holds the parser for re-use
     *
     * @var  object
     */
    public static $parser = null;

    /**
     * Turns MarkDown to HTML
     *
     * @param   string  $context
     * @param   object  $comment
     * @param   string  $text
     * @return  void
     */
    public function onCommentPrepare($context, &$comment)
    {
        if ($context != 'com_support.comment') {
            return;
        }

        $text = $comment->get('comment');

        if (!$text) {
            return;
        }

        if (!self::$parser) {
            $cls = '\\cebe\\markdown\\' . $this->params->get('type', 'Markdown');

            self::$parser = new $cls();
            self::$parser->html5 = true;
            self::$parser->keepListStartNumber = true;
            //self::$parser->enableNewlines = true;
        }

        $text = preg_replace("/<br\s?\/>/i", '', $text);
        $text = rtrim($text);

        $result = self::$parser->parse($text);

        if ($result) {
            $text = $result;
        }

        $comment->set('comment', $text);

        // We only pass back so that the triggerer knows we did something
        return $text;
    }
}
