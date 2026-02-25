<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Basic;

use Hubzero\Base\Obj;

/**
 * Search terms
 */
class Terms extends Obj
{
    /**
     * Raw search term
     *
     * @var  string
     */
    private $raw;

    /**
     * Positive chunks
     *
     * @var  string
     */
    private $positive_chunks;

    /**
     * Optional chunks
     *
     * @var  array
     */
    private $optional_chunks = array();

    /**
     * Forbidden chunks
     *
     * @var  array
     */
    private $forbidden_chunks = array();

    /**
     * Mandatory chunks
     *
     * @var  array
     */
    private $mandatory_chunks = array();

    /**
     * Search section
     *
     * @var  string
     */
    private $section = null;

    /**
     * Quoted terms
     *
     * @var  array
     */
    private $quoted = array();

    /**
     * Constructor
     *
     * @param   string  $raw
     * @return  void
     */
    public function __construct($raw)
    {
        $this->raw = preg_replace('/^\s+|\s+$/', '', preg_replace('/\s+/', ' ', $raw));
        if ($this->is_set()) {
            $this->parseSearchableChunks();
        }
    }

    /**
     * Is the term quoted?
     *
     * @param   integer  $idx
     * @return  mixed
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function is_quoted($idx)
    {
        return isset($this->quoted[$idx]) ? $this->quoted[$idx] : false;
    }

    /**
     * Get the raw search term
     *
     * @return  string
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function get_raw()
    {
        return $this->raw;
    }

    /**
     * Get the raw search term without a section.
     *
     * @return  string
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function get_raw_without_section()
    {
        if (!$this->section) {
            return $this->raw;
        }

        return preg_replace('/^' . implode(':', $this->section) . ':/', '', $this->raw);
    }

    /**
     * Get a section
     *
     * @return  string
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function get_section()
    {
        return $this->section;
    }

    /**
     * Get optional chunks
     *
     * @return  array
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function get_optional_chunks()
    {
        return $this->optional_chunks;
    }

    /**
     * Get forbidden chunks
     *
     * @return  array
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function get_forbidden_chunks()
    {
        return $this->forbidden_chunks;
    }

    /**
     * Get mandatory chunks
     *
     * @return  array
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function get_mandatory_chunks()
    {
        return $this->mandatory_chunks;
    }

    /**
     * Get positive chunks
     *
     * @return  array
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function get_positive_chunks()
    {
        if (!$this->positive_chunks) {
            $this->positive_chunks = array_unique(array_merge($this->mandatory_chunks, $this->optional_chunks));
        }
        return $this->positive_chunks;
    }

    /**
     * Get stemmed chunks
     *
     * @return  array
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function get_stemmed_chunks()
    {
        $chunks = $this->get_positive_chunks();
        foreach ($chunks as $term) {
            while (($stemmed = stem($term)) != $term) {
                $chunks[] = $stemmed;
                $term = $stemmed;
            }
        }
        $chunks = array_unique(array_merge(array_map('stem', $chunks), $chunks));
        \Hubzero\Facades\Event::trigger('onSearchExpandTerms', array(&$chunks));

        return array_unique($chunks);
    }

    /**
     * Is the raw search term set?
     *
     * @return  boolean
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function is_set()
    {
        return !!$this->raw;
    }

    /**
     * Any chunks?
     *
     * @return  boolean
     */
    public function any()
    {
        return !!($this->optional_chunks || $this->mandatory_chunks);
    }

    /**
     * Parse searchable chunks
     *
     * @return  void
     */
    private function parseSearchableChunks()
    {
        $accumulating_phrase = false;
        $partial = '';
        $sign = '';
        $raw = trim(strtolower($this->raw));
        if (preg_match('/^([_.\-,a-z:]+):/', $raw, $match)) {
            $this->section = explode(':', $match[1]);
            $raw = preg_replace('/^' . preg_quote($match[1]) . ':/', '', $raw);
        } elseif (array_key_exists('section', $_GET)) {
            $this->section = array($_GET['section']);
        }

        $raw = preg_replace('#[^-:/\\+"[:alnum:] ]#', '', preg_replace('/\s+/', ' ', trim($raw)));
        for ($idx = 0, $len = strlen($raw); $idx < $len; ++$idx) {
            $cur = $raw[$idx];
            if ($accumulating_phrase) {
                if ($cur == '"') {
                    $accumulating_phrase = false;
                    $this->addChunk($partial, $sign, true);
                } else {
                    $partial .= $cur;
                }
            } elseif ($cur == '"') {
                if ($partial) {
                    $this->addChunk($partial, $sign);
                }
                $accumulating_phrase = true;
            } elseif ($cur == ' ') {
                $this->addChunk($partial, $sign);
            } elseif ($cur == '+' && $partial == '') {
                $sign = '+';
            } elseif ($cur == '-' && $partial == '') {
                $sign = '-';
            } elseif ($cur != ' ') {
                $partial .= $cur;
            }
        }
        $this->addChunk($partial, $sign);
    }

    /**
     * Add chunk
     *
     * @param   string   &$partial
     * @param   string   &$sign
     * @param   boolean  $quoted
     * @return  void
     */
    private function addChunk(&$partial, &$sign = '', $quoted = false)
    {
        if (!$partial) {
            return;
        }

        if (DocumentMetadata::is_stop_word($partial)) {
            $partial = '';
            $sign = '';
            return;
        }

        if ($sign == '-') {
            $this->forbidden_chunks[] = $partial;
        } elseif ($sign == '+') {
            $this->mandatory_chunks[] = $partial;
        } else {
            $this->optional_chunks[] = $partial;
            $this->quoted[count($this->optional_chunks) - 1] = $quoted;
        }

        $partial = '';
        $sign = '';
    }

    /**
     * Get word regex
     *
     * @return  string
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function get_word_regex()
    {
        $chunks = $this->get_stemmed_chunks();
        usort($chunks, function ($a, $b) {
            $al = strlen($a);
            $bl = strlen($b);
            if ($al == $bl) {
                return 0;
            }
            return $al > $bl ? -1 : 1;
        });
        return '(' . join('|', array_map('preg_quote', $chunks)) . '[[:alpha:]]*)';
    }

    /**
     * To string
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->raw;
    }
}
