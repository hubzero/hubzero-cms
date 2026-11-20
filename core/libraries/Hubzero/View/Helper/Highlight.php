<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Helper;

use Hubzero\Utility\Str;

/**
 * Helper for highlighting a phrase or word in a body of text
 */
class Highlight extends AbstractHelper
{
    /**
     * Highlight some text
     *
     * @param   string|null   $text     Text to find phrases in
     * @param   string|array<mixed>|null  $phrase   Phrase to highlight
     * @param   array<mixed>    $options  Options for highlighting
     * @return  string
     * @throws  \InvalidArgumentException If no text was passed
     */
    public function __invoke($text = null, $phrase = null, $options = [])
    {
        if (null === $text) {
            throw new \InvalidArgumentException(__METHOD__ . '(); No text passed.');
        }

        return Str::highlight($text, $phrase, $options);
    }
}
