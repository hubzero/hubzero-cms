<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Helpers;

/**
 * XHTML sanitizer, based on MediaWiki's Sanitizer.
 *
 * Originally Copyright (C) 2002-2005 Brion Vibber <brion@pobox.com> et al
 * http://www.mediawiki.org/
 * Licensed under GPL v2+.
 *
 * Refactored to a self-contained class with only the functionality
 * needed by HubZero (escapeId for headline anchors).
 */
class Sanitizer
{
    public const NONE = 0;
    public const INITIAL_NONLETTER = 1;

    /**
     * Regex to match character references: named, decimal, hex, or bare &.
     */
    private const CHAR_REFS_REGEX =
        '/&([A-Za-z0-9\x80-\xff]+);'
        . '|&\#([0-9]+);'
        . '|&\#x([0-9A-Za-z]+);'
        . '|&\#X([0-9A-Za-z]+);'
        . '|(&)/x';

    /**
     * UTF-8 encoding of U+FFFD REPLACEMENT CHARACTER.
     */
    private const UTF8_REPLACEMENT = "\xEF\xBF\xBD";

    /**
     * HTML 4.01 named character entities.
     *
     * @see http://www.w3.org/TR/html4/sgml/entities.html
     */
    private static $htmlEntities = array(
        'Aacute'   => 193,  'aacute'   => 225,  'Acirc'    => 194,
        'acirc'    => 226,  'acute'    => 180,  'AElig'    => 198,
        'aelig'    => 230,  'Agrave'   => 192,  'agrave'   => 224,
        'alefsym'  => 8501, 'Alpha'    => 913,  'alpha'    => 945,
        'amp'      => 38,   'and'      => 8743, 'ang'      => 8736,
        'Aring'    => 197,  'aring'    => 229,  'asymp'    => 8776,
        'Atilde'   => 195,  'atilde'   => 227,  'Auml'     => 196,
        'auml'     => 228,  'bdquo'    => 8222, 'Beta'     => 914,
        'beta'     => 946,  'brvbar'   => 166,  'bull'     => 8226,
        'cap'      => 8745, 'Ccedil'   => 199,  'ccedil'   => 231,
        'cedil'    => 184,  'cent'     => 162,  'Chi'      => 935,
        'chi'      => 967,  'circ'     => 710,  'clubs'    => 9827,
        'cong'     => 8773, 'copy'     => 169,  'crarr'    => 8629,
        'cup'      => 8746, 'curren'   => 164,  'dagger'   => 8224,
        'Dagger'   => 8225, 'darr'     => 8595, 'dArr'     => 8659,
        'deg'      => 176,  'Delta'    => 916,  'delta'    => 948,
        'diams'    => 9830, 'divide'   => 247,  'Eacute'   => 201,
        'eacute'   => 233,  'Ecirc'    => 202,  'ecirc'    => 234,
        'Egrave'   => 200,  'egrave'   => 232,  'empty'    => 8709,
        'emsp'     => 8195, 'ensp'     => 8194, 'Epsilon'  => 917,
        'epsilon'  => 949,  'equiv'    => 8801, 'Eta'      => 919,
        'eta'      => 951,  'ETH'      => 208,  'eth'      => 240,
        'Euml'     => 203,  'euml'     => 235,  'euro'     => 8364,
        'exist'    => 8707, 'fnof'     => 402,  'forall'   => 8704,
        'frac12'   => 189,  'frac14'   => 188,  'frac34'   => 190,
        'frasl'    => 8260, 'Gamma'    => 915,  'gamma'    => 947,
        'ge'       => 8805, 'gt'       => 62,   'harr'     => 8596,
        'hArr'     => 8660, 'hearts'   => 9829, 'hellip'   => 8230,
        'Iacute'   => 205,  'iacute'   => 237,  'Icirc'    => 206,
        'icirc'    => 238,  'iexcl'    => 161,  'Igrave'   => 204,
        'igrave'   => 236,  'image'    => 8465, 'infin'    => 8734,
        'int'      => 8747, 'Iota'     => 921,  'iota'     => 953,
        'iquest'   => 191,  'isin'     => 8712, 'Iuml'     => 207,
        'iuml'     => 239,  'Kappa'    => 922,  'kappa'    => 954,
        'Lambda'   => 923,  'lambda'   => 955,  'lang'     => 9001,
        'laquo'    => 171,  'larr'     => 8592, 'lArr'     => 8656,
        'lceil'    => 8968, 'ldquo'    => 8220, 'le'       => 8804,
        'lfloor'   => 8970, 'lowast'   => 8727, 'loz'      => 9674,
        'lrm'      => 8206, 'lsaquo'   => 8249, 'lsquo'    => 8216,
        'lt'       => 60,   'macr'     => 175,  'mdash'    => 8212,
        'micro'    => 181,  'middot'   => 183,  'minus'    => 8722,
        'Mu'       => 924,  'mu'       => 956,  'nabla'    => 8711,
        'nbsp'     => 160,  'ndash'    => 8211, 'ne'       => 8800,
        'ni'       => 8715, 'not'      => 172,  'notin'    => 8713,
        'nsub'     => 8836, 'Ntilde'   => 209,  'ntilde'   => 241,
        'Nu'       => 925,  'nu'       => 957,  'Oacute'   => 211,
        'oacute'   => 243,  'Ocirc'    => 212,  'ocirc'    => 244,
        'OElig'    => 338,  'oelig'    => 339,  'Ograve'   => 210,
        'ograve'   => 242,  'oline'    => 8254, 'Omega'    => 937,
        'omega'    => 969,  'Omicron'  => 927,  'omicron'  => 959,
        'oplus'    => 8853, 'or'       => 8744, 'ordf'     => 170,
        'ordm'     => 186,  'Oslash'   => 216,  'oslash'   => 248,
        'Otilde'   => 213,  'otilde'   => 245,  'otimes'   => 8855,
        'Ouml'     => 214,  'ouml'     => 246,  'para'     => 182,
        'part'     => 8706, 'permil'   => 8240, 'perp'     => 8869,
        'Phi'      => 934,  'phi'      => 966,  'Pi'       => 928,
        'pi'       => 960,  'piv'      => 982,  'plusmn'   => 177,
        'pound'    => 163,  'prime'    => 8242, 'Prime'    => 8243,
        'prod'     => 8719, 'prop'     => 8733, 'Psi'      => 936,
        'psi'      => 968,  'quot'     => 34,   'radic'    => 8730,
        'rang'     => 9002, 'raquo'    => 187,  'rarr'     => 8594,
        'rArr'     => 8658, 'rceil'    => 8969, 'rdquo'    => 8221,
        'real'     => 8476, 'reg'      => 174,  'rfloor'   => 8971,
        'Rho'      => 929,  'rho'      => 961,  'rlm'      => 8207,
        'rsaquo'   => 8250, 'rsquo'    => 8217, 'sbquo'    => 8218,
        'Scaron'   => 352,  'scaron'   => 353,  'sdot'     => 8901,
        'sect'     => 167,  'shy'      => 173,  'Sigma'    => 931,
        'sigma'    => 963,  'sigmaf'   => 962,  'sim'      => 8764,
        'spades'   => 9824, 'sub'      => 8834, 'sube'     => 8838,
        'sum'      => 8721, 'sup'      => 8835, 'sup1'     => 185,
        'sup2'     => 178,  'sup3'     => 179,  'supe'     => 8839,
        'szlig'    => 223,  'Tau'      => 932,  'tau'      => 964,
        'there4'   => 8756, 'Theta'    => 920,  'theta'    => 952,
        'thetasym' => 977,  'thinsp'   => 8201, 'THORN'    => 222,
        'thorn'    => 254,  'tilde'    => 732,  'times'    => 215,
        'trade'    => 8482, 'Uacute'   => 218,  'uacute'   => 250,
        'uarr'     => 8593, 'uArr'     => 8657, 'Ucirc'    => 219,
        'ucirc'    => 251,  'Ugrave'   => 217,  'ugrave'   => 249,
        'uml'      => 168,  'upsih'    => 978,  'Upsilon'  => 933,
        'upsilon'  => 965,  'Uuml'     => 220,  'uuml'     => 252,
        'weierp'   => 8472, 'Xi'       => 926,  'xi'       => 958,
        'Yacute'   => 221,  'yacute'   => 253,  'yen'      => 165,
        'Yuml'     => 376,  'yuml'     => 255,  'Zeta'     => 918,
        'zeta'     => 950,  'zwj'      => 8205, 'zwnj'     => 8204,
    );

    /**
     * MediaWiki-specific entity aliases.
     */
    private static $htmlEntityAliases = array(
        "\xD7\xA8\xD7\x9C\xD7\x9E" => 'rlm',
        "\xD8\xB1\xD9\x84\xD9\x85" => 'rlm',
    );

    /**
     * Escape a value for use in an HTML id attribute.
     *
     * @param  string $id    The value to escape
     * @param  int    $flags INITIAL_NONLETTER (default) permits initial
     *                       non-letter characters. NONE will prepend 'x'
     *                       if the id starts with a non-letter.
     * @return string
     */
    public static function escapeId($id, $flags = self::INITIAL_NONLETTER)
    {
        static $replace = array(
            '%3A' => ':',
            '%' => '.',
        );

        $id = urlencode(
            self::decodeCharReferences(strtr($id, ' ', '_'))
        );
        $id = str_replace(
            array_keys($replace),
            array_values($replace),
            $id
        );

        if (
            ~$flags & self::INITIAL_NONLETTER
            && !preg_match('/[a-zA-Z]/', $id[0])
        ) {
            $id = "x$id";
        }

        return $id;
    }

    /**
     * Decode any character references (named, numeric, hex) in the text
     * and return a UTF-8 string.
     *
     * @param  string $text
     * @return string
     */
    private static function decodeCharReferences($text)
    {
        return preg_replace_callback(
            self::CHAR_REFS_REGEX,
            array(self::class, 'decodeCharReferencesCallback'),
            $text
        );
    }

    /**
     * @param  array $matches
     * @return string
     */
    private static function decodeCharReferencesCallback($matches)
    {
        if ($matches[1] != '') {
            return self::decodeEntity($matches[1]);
        } elseif ($matches[2] != '') {
            return self::decodeChar(intval($matches[2]));
        } elseif ($matches[3] != '') {
            return self::decodeChar(hexdec($matches[3]));
        } elseif ($matches[4] != '') {
            return self::decodeChar(hexdec($matches[4]));
        }

        return $matches[0];
    }

    /**
     * Decode a named HTML entity to its UTF-8 character.
     *
     * @param  string $name
     * @return string
     */
    private static function decodeEntity($name)
    {
        if (isset(self::$htmlEntityAliases[$name])) {
            $name = self::$htmlEntityAliases[$name];
        }

        if (isset(self::$htmlEntities[$name])) {
            return self::codepointToUtf8(self::$htmlEntities[$name]);
        }

        return "&$name;";
    }

    /**
     * Return UTF-8 string for a codepoint if valid, otherwise
     * the Unicode replacement character.
     *
     * @param  int $codepoint
     * @return string
     */
    private static function decodeChar($codepoint)
    {
        if (self::validateCodepoint($codepoint)) {
            return self::codepointToUtf8($codepoint);
        }

        return self::UTF8_REPLACEMENT;
    }

    /**
     * Check whether a Unicode codepoint is a valid XML character.
     *
     * @param  int $codepoint
     * @return bool
     */
    private static function validateCodepoint($codepoint)
    {
        return ($codepoint ==    0x09)
            || ($codepoint ==    0x0a)
            || ($codepoint ==    0x0d)
            || ($codepoint >=    0x20 && $codepoint <=   0xd7ff)
            || ($codepoint >=  0xe000 && $codepoint <=   0xfffd)
            || ($codepoint >= 0x10000 && $codepoint <= 0x10ffff);
    }

    /**
     * Return the UTF-8 sequence for a Unicode code point.
     *
     * Based on code by Brion Vibber from MediaWiki.
     *
     * @param  int $codepoint
     * @return string
     */
    private static function codepointToUtf8($codepoint)
    {
        if ($codepoint < 0x80) {
            return chr($codepoint);
        }
        if ($codepoint < 0x800) {
            return chr($codepoint >> 6 & 0x3f | 0xc0)
                . chr($codepoint & 0x3f | 0x80);
        }
        if ($codepoint < 0x10000) {
            return chr($codepoint >> 12 & 0x0f | 0xe0)
                . chr($codepoint >> 6 & 0x3f | 0x80)
                . chr($codepoint & 0x3f | 0x80);
        }
        if ($codepoint < 0x110000) {
            return chr($codepoint >> 18 & 0x07 | 0xf0)
                . chr($codepoint >> 12 & 0x3f | 0x80)
                . chr($codepoint >> 6 & 0x3f | 0x80)
                . chr($codepoint & 0x3f | 0x80);
        }

        return self::UTF8_REPLACEMENT;
    }
}
