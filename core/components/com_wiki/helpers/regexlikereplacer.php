<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Class to replace regex matches with a string similar to that used in preg_replace()
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class RegexlikeReplacer extends Replacer
{
    /**
     * Description for 'r'
     *
     * @var unknown
     */
    public $r;

    /**
     * Short description for '__construct'
     *
     * Long description (if any) ...
     *
     * @param      unknown $r Parameter description (if any) ...
     * @return     void
     */
    public function __construct($r)
    {
        $this->r = $r;
    }

    /**
     * Short description for 'replace'
     *
     * Long description (if any) ...
     *
     * @param      array $matches Parameter description (if any) ...
     * @return     array Return description (if any) ...
     */
    public function replace($matches)
    {
        $pairs = array();
        foreach ($matches as $i => $match) {
            $pairs["\$$i"] = $match;
        }
        return strtr($this->r, $pairs);
    }
}
