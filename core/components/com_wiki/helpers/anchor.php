<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Helpers;

/**
 * The anchor a wiki heading is linked to
 *
 * A heading's anchor is in every link to its section, on the hub and off it,
 * so it is made the way wiki pages have always made it: spaces become
 * underscores, character references are decoded, and the result is
 * URL-encoded with "." in place of "%" and ":" left as it is. "Café au lait"
 * becomes Caf.C3.A9_au_lait.
 */
class Anchor
{
    /**
     * Other names wiki text has used for an entity, and the entity they mean
     *
     * @var  array
     */
    public const ENTITY_ALIASES = [
        "\u{05E8}\u{05DC}\u{05DE}" => 'rlm',
        "\u{0631}\u{0644}\u{0645}" => 'rlm',
    ];

    /**
     * Character references: a named entity, or a decimal or hexadecimal number
     *
     * @var  string
     */
    public const REFERENCE = '/&([A-Za-z0-9\x80-\xff]+);|&#([0-9]+);|&#[xX]([0-9A-Za-z]+);/';

    /**
     * The anchor for a heading
     *
     * @param   string  $heading  The heading's text, without its markup
     * @return  string
     */
    public static function fromHeading($heading)
    {
        $id = urlencode(self::decodeReferences(strtr($heading, ' ', '_')));

        return str_replace(['%3A', '%'], [':', '.'], $id);
    }

    /**
     * Decode the character references in a piece of text
     *
     * Named entities are HTML 4's; any other name is left as it was written.
     * A number that is not a character a document may hold becomes U+FFFD,
     * the replacement character.
     *
     * @param   string  $text
     * @return  string
     */
    public static function decodeReferences($text)
    {
        return preg_replace_callback(
            self::REFERENCE,
            function ($match) {
                if (isset($match[3]) && $match[3] !== '') {
                    return self::character(hexdec($match[3]));
                }

                if (isset($match[2]) && $match[2] !== '') {
                    return self::character(intval($match[2]));
                }

                $name = isset(self::ENTITY_ALIASES[$match[1]]) ? self::ENTITY_ALIASES[$match[1]] : $match[1];

                return html_entity_decode('&' . $name . ';', ENT_QUOTES | ENT_HTML401, 'UTF-8');
            },
            $text,
        );
    }

    /**
     * The character a numeric reference names
     *
     * @param   integer|float  $codepoint
     * @return  string
     */
    private static function character($codepoint)
    {
        $valid = $codepoint == 0x09
            || $codepoint == 0x0a
            || $codepoint == 0x0d
            || ($codepoint >= 0x20 && $codepoint <= 0xd7ff)
            || ($codepoint >= 0xe000 && $codepoint <= 0xfffd)
            || ($codepoint >= 0x10000 && $codepoint <= 0x10ffff);

        return $valid ? mb_chr((int) $codepoint, 'UTF-8') : "\u{FFFD}";
    }
}
