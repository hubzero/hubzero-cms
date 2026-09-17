<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Template;

/**
 * The one place that says which positions a template has
 *
 * A template declares its positions in the `<positions>` block of its
 * templateDetails.xml, and those declarations are the whole contract: a
 * module sits in a position, the template's index.php asks for that position
 * by name, and a name the template never asks for renders nowhere.
 *
 * Joomla, which this forked from, treated the declarations as authoritative -
 * a position that left templateDetails.xml left the picker with it. Here the
 * position became a free-text field backed by a picker that also offers every
 * position any module happens to sit in, so a template can stop declaring a
 * position and the modules in it stay put, published, and invisible. Lucent
 * dropping home-1 through home-4 while still shipping front page modules for
 * them is that bug, and it is silent.
 *
 * So the declarations are read in one place, and asking whether a position is
 * one the active template renders is a question with an answer.
 **/
class Positions
{
    /**
     * Declarations already read, keyed by template
     *
     * @var  array
     */
    protected static $cache = array();

    /**
     * The positions a template declares, in the order it declares them
     *
     * @param   string  $template  The template element, e.g. lucent
     * @return  array   Position names; empty when the template declares none
     */
    public static function declared($template)
    {
        return array_keys(self::labelled($template));
    }

    /**
     * The positions a template declares, each with the label it gave
     *
     * A declaration carries its name either as a `value` attribute, in which
     * case the text is a label, or as the text itself, in which case there is
     * no label and the caller decides what to show. The label is left as the
     * template wrote it: turning it into a language key is presentation, and
     * belongs to whoever is doing the presenting.
     *
     * @param   string  $template  The template element, e.g. lucent
     * @return  array   Position name => label, the label being '' when none was given
     */
    public static function labelled($template)
    {
        if (!Style::exists($template)) {
            // Covers the empty name and anything trying to leave the
            // templates directory, both of which have no declarations.
            return array();
        }

        if (isset(self::$cache[$template])) {
            return self::$cache[$template];
        }

        self::$cache[$template] = array();

        if (!($path = self::file($template))) {
            return self::$cache[$template];
        }

        $xml = @simplexml_load_file($path);

        if (!$xml || !isset($xml->positions[0])) {
            // A template with no positions block declares nothing, which is
            // different from a template that is not there: both give an empty
            // list, and neither is an error.
            return self::$cache[$template];
        }

        foreach ($xml->positions[0] as $position) {
            $name  = (string) $position['value'];
            $label = (string) $position;

            if (!$name) {
                $name  = $label;
                $label = '';
            }

            if ($name === '') {
                continue;
            }

            self::$cache[$template][$name] = $label;
        }

        return self::$cache[$template];
    }

    /**
     * Does a template declare this position
     *
     * @param   string   $template  The template element
     * @param   string   $position  The position name
     * @return  boolean
     */
    public static function declares($template, $position)
    {
        return array_key_exists((string) $position, self::labelled($template));
    }

    /**
     * Where a template's templateDetails.xml is, or an empty string
     *
     * A hub's own copy of a template answers before the shipped one, the same
     * order everything else reads a template in.
     *
     * @param   string  $template  The template element
     * @return  string
     */
    public static function file($template)
    {
        if (!Style::exists($template)) {
            return '';
        }

        foreach (array('PATH_APP', 'PATH_CORE') as $constant) {
            if (!defined($constant)) {
                continue;
            }

            $path = constant($constant) . '/templates/' . $template . '/templateDetails.xml';

            if (is_file($path)) {
                return $path;
            }
        }

        return '';
    }

    /**
     * Forget what has been read
     *
     * Only the tests need this: within a request a template's declarations do
     * not change.
     *
     * @return  void
     */
    public static function forget()
    {
        self::$cache = array();
    }
}
