<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
    * o------------------------------------------------------------------------------o
    * | This package is dual licensed as GPL and a commercial license.               |
    * | If you use the code commercially (or if you don't want to be restricted by   |
    * | the GPL license), you will need the commercial license. It's only £49 (GBP - |
    * | roughly $98 depending on the exchange rate) and helps me out a lot. Thanks.  |
    * o------------------------------------------------------------------------------o
    *
    * © Copyright Richard Heyes
    */

    
    /**
    * PHP5 Implementation of the Porter Stemmer algorithm. Certain elements
    * were borrowed from the (broken) implementation by Jon Abernathy.
    * 
    * Usage:
    * 
    *  $stem = PorterStemmer::Stem($word);
    * 
    * How easy is that?
    */
    
    class PorterStemmer
    {
        /**
        * Regex for matching a consonant
        * @var string
        */
        private static $regex_consonant = '(?:[bcdfghjklmnpqrstvwxz]|(?<=[aeiou])y|^y)';
    
    
        /**
        * Regex for matching a vowel
        * @var string
        */
        private static $regex_vowel = '(?:[aeiou]|(?<![aeiou])y)';


        /**
        * Cache for mass stemmings. Do not use for mass stemmings of *different* words. Use for
        * stemming words from documents for example, where the same word may crop up multiple times.
        * @var array
        */
        private static $cache = array();


        /**
        * Stems a word. Simple huh?
        * 
        * @param  string $word  Word to stem
        * @param  bool   $cache Whether to use caching
        * @return string        Stemmed word
        */
        public static function Stem($word, $cache = false)
        {
            if (strlen($word) <= 2) {
                return $word;
            }

            // Check cache
            if ($cache AND !empty(self::$cache[$word])) {
                return self::$cache[$word];
            }
            
            /**
            * Remove: 've, n't, 'd
            */
            $word = preg_replace("/('ve|n't|'d)$/", '', $word);

            $stem = self::step1ab($word);
            $stem = self::step1c($stem);
            $stem = self::step2($stem);
            $stem = self::step3($stem);
            $stem = self::step4($stem);
            $stem = self::step5($stem);

            // Store in cache
            if ($cache) {
                self::$cache[$word] = $stem;
            }

            return $stem;
        }
    
    
        /**
        * Step 1
        */
        private static function step1ab($word)
        {
            // Part a
            if (substr($word, -1) == 's') {

                   self::replace($word, 'sses', 'ss')
                OR self::replace($word, 'ies', 'i')
                OR self::replace($word, 'ss', 'ss')
                OR self::replace($word, 's', '');
            }

            // Part b
            if (substr($word, -2, 1) != 'e' OR !self::replace($word, 'eed', 'ee', 0)) { // First rule
                $v = self::$regex_vowel;

                // ing and ed
                if (   preg_match("#$v+#", substr($word, 0, -3)) && self::replace($word, 'ing', '')
                    OR preg_match("#$v+#", substr($word, 0, -2)) && self::replace($word, 'ed', '')) { // Note use of && and OR, for precedence reasons

                    // If one of above two test successful
                    if (    !self::replace($word, 'at', 'ate')
                        AND !self::replace($word, 'bl', 'ble')
                        AND !self::replace($word, 'iz', 'ize')) {

                        // Double consonant ending
                        if (    self::doubleConsonant($word)
                            AND substr($word, -2) != 'll'
                            AND substr($word, -2) != 'ss'
                            AND substr($word, -2) != 'zz') {
                            
                            $word = substr($word, 0, -1);
                        
                        } else if (self::m($word) == 1 AND self::cvc($word)) {
                            $word .= 'e';
                        }
                    }
                }
            }

            return $word;
        }
    
    
        /**
        * Step 1c
        * 
        * @param string $word Word to stem
        */
        private static function step1c($word)
        {
            $v = self::$regex_vowel;

            if (substr($word, -1) == 'y' && preg_match("#$v+#", substr($word, 0, -1))) {
                self::replace($word, 'y', 'i');
            }

            return $word;
        }


        /**
        * Step 2
        * 
        * @param string $word Word to stem
        */
        private static function step2($word)
        {
            switch (substr($word, -2, 1)) {
                case 'a':
                       self::replace($word, 'ational', 'ate', 0)
                    OR self::replace($word, 'tional', 'tion', 0);
                    break;

                case 'c':
                       self::replace($word, 'enci', 'ence', 0)
                    OR self::replace($word, 'anci', 'ance', 0);
                    break;

                case 'e':
                    self::replace($word, 'izer', 'ize', 0);
                    break;

                case 'g':
                    self::replace($word, 'logi', 'log', 0);
                    break;

                case 'l':
                       self::replace($word, 'entli', 'ent', 0)
                    OR self::replace($word, 'ousli', 'ous', 0)
                    OR self::replace($word, 'alli', 'al', 0)
                    OR self::replace($word, 'bli', 'ble', 0)
                    OR self::replace($word, 'eli', 'e', 0);
                    break;

                case 'o':
                       self::replace($word, 'ization', 'ize', 0)
                    OR self::replace($word, 'ation', 'ate', 0)
                    OR self::replace($word, 'ator', 'ate', 0);
                    break;

                case 's':
                       self::replace($word, 'iveness', 'ive', 0)
                    OR self::replace($word, 'fulness', 'ful', 0)
                    OR self::replace($word, 'ousness', 'ous', 0)
                    OR self::replace($word, 'alism', 'al', 0);
                    break;

                case 't':
                       self::replace($word, 'biliti', 'ble', 0)
                    OR self::replace($word, 'aliti', 'al', 0)
                    OR self::replace($word, 'iviti', 'ive', 0);
                    break;
            }

            return $word;
        } 

    
        /**
        * Step 3
        * 
        * @param string $word String to stem
        */
        private static function step3($word)
        {
            switch (substr($word, -2, 1)) {
                case 'a':
                    self::replace($word, 'ical', 'ic', 0);
                    break;
                    
                case 's':
                       self::replace($word, 'alise', 'al', 0)
                    OR self::replace($word, 'ness', '', 0);
                    break;
                    
                case 't':
                       self::replace($word, 'icate', 'ic', 0)
                    OR self::replace($word, 'iciti', 'ic', 0);
                    break;
                    
                case 'u':
                    self::replace($word, 'ful', '', 0);
                    break;
                    
                case 'v':
                    self::replace($word, 'ative', '', 0);
                    break;
                    
                case 'z':
                    self::replace($word, 'alize', 'al', 0);
                    break;
            }
            
            return $word;
        }
    
    
        /**
        * Step 4
        * 
        * @param string $word Word to stem
        */
        private static function step4($word)
        {
            switch (substr($word, -2, 1)) {
                case 'a':
                    self::replace($word, 'al', '', 1);
                    break;

                case 'c':
                       self::replace($word, 'ance', '', 1)
                    OR self::replace($word, 'ence', '', 1);
                    break;

                case 'e':
                    self::replace($word, 'er', '', 1);
                    break;

                case 'i':
                    self::replace($word, 'ic', '', 1);
                    break;

                case 'l':
                       self::replace($word, 'able', '', 1)
                    OR self::replace($word, 'ible', '', 1);
                    break;

                case 'n':
                       self::replace($word, 'ant', '', 1)
                    OR self::replace($word, 'ement', '', 1)
                    OR self::replace($word, 'ment', '', 1)
                    OR self::replace($word, 'ent', '', 1);
                    break;

                case 'o':
                    if (substr($word, -4) == 'tion' OR substr($word, -4) == 'sion') {
                       self::replace($word, 'ion', '', 1);
                    } else {
                        self::replace($word, 'ou', '', 1);
                    }
                    break;

                case 's':
                    self::replace($word, 'ism', '', 1);
                    break;

                case 't':
                       self::replace($word, 'ate', '', 1)
                    OR self::replace($word, 'iti', '', 1);
                    break;

                case 'u':
                    self::replace($word, 'ous', '', 1);
                    break;

                case 'v':
                    self::replace($word, 'ive', '', 1);
                    break;

                case 'z':
                    self::replace($word, 'ize', '', 1);
                    break;
            }
            
            return $word;
        }

    
        /**
        * Step 5
        * 
        * @param string $word Word to stem
        */
        private static function step5($word)
        {
            // Part a
            if (substr($word, -1) == 'e') {
                if (self::m(substr($word, 0, -1)) > 1) {
                    self::replace($word, 'e', '');
    
                } else if (self::m(substr($word, 0, -1)) == 1) {

                    if (!self::cvc(substr($word, 0, -1))) {
                        self::replace($word, 'e', '');
                    }
                }
            }

            // Part b
            if (self::m($word) > 1 AND self::doubleConsonant($word) AND substr($word, -1) == 'l') {
                $word = substr($word, 0, -1);
            }

            return $word;
        }
    
    
        /**
        * Replaces the first string with the second, at the end of the string. If third
        * arg is given, then the preceding string must match that m count at least.
        * 
        * @param  string $str   String to check
        * @param  string $check Ending to check for
        * @param  string $repl  Replacement string
        * @param  int    $m     Optional minimum number of m() to meet
        * @return bool          Whether the $check string was at the end
        *                       of the $str string. True does not necessarily mean
        *                       that it was replaced.
        */
        private static function replace(&$str, $check, $repl, $m = null)
        {
            $len = 0 - strlen($check);
    
            if (substr($str, $len) == $check) {
                $substr = substr($str, 0, $len);
                if (is_null($m) OR self::m($substr) > $m) {
                    $str = $substr . $repl;
                }

                return true;
            }
    
            return false;
        }
    
    
        /**
        * What, you mean it's not obvious from the name?
        * 
        * m() measures the number of consonant sequences in $str. if c is 
        * a consonant sequence and v a vowel sequence, and <..> indicates arbitrary 
        * presence, 
        * 
        * <c><v>       gives 0 
        * <c>vc<v>     gives 1 
        * <c>vcvc<v>   gives 2 
        * <c>vcvcvc<v> gives 3 
        * 
        * @param  string $str The string to return the m count for
        * @return int         The m count
        */
        private static function m($str)
        {
            $c = self::$regex_consonant;
            $v = self::$regex_vowel;

            $str = preg_replace("#^$c+#", '', $str);
            $str = preg_replace("#$v+$#", '', $str);
    
            preg_match_all("#($v+$c+)#", $str, $matches);

            return count($matches[1]);
        }

    
        /**
        * Returns true/false as to whether the given string contains two
        * of the same consonant next to each other at the end of the string.
        * 
        * @param  string $str String to check
        * @return bool        Result
        */
        private static function doubleConsonant($str)
        {
            $c = self::$regex_consonant;
            
            return preg_match("#$c{2}$#", $str, $matches) AND $matches[0]{0} == $matches[0]{1};
        }


        /**
        * Checks for ending CVC sequence where second C is not W, X or Y
        * 
        * @param  string $str String to check
        * @return bool        Result
        */
        private static function cvc($str)
        {
            $c = self::$regex_consonant;
            $v = self::$regex_vowel;

            return     preg_match("#($c$v$c)$#", $str, $matches)
                   AND strlen($matches[1]) == 3
                   AND $matches[1]{2} != 'w'
                   AND $matches[1]{2} != 'x'
                   AND $matches[1]{2} != 'y';
        }
    }
