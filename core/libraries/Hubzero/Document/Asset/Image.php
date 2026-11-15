<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Asset;

/**
 * Image asset class
 */
class Image extends File
{
    /**
     * Asset type
     *
     * @var  string
     */
    protected $type = 'img';

    /**
     * Constructor
     *
     * @param   string  $extension  CMS Extension to load asset from
     * @param   string|null  $name       Asset name (optional)
     * @return  void
     */
    public function __construct($extension, $name = null)
    {
        parent::__construct($extension, $name);
    }

    /**
     * Get the file name
     *
     * @return  string
     */
    public function file()
    {
        return $this->name;
    }
}
