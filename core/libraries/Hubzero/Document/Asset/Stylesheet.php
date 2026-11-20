<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Asset;

/**
 * Asset class for Stylesheets
 */
class Stylesheet extends File
{
    /**
     * Asset type
     *
     * @var  string
     */
    protected $type = 'css';

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

        // Try to detect if the asset is a declaration
        if (!$this->extension || strstr((string) $name, '{') || strstr((string) $name, '@')) {
            $this->declaration = true;

            // Reset the name in case any parsing/modification
            // happened in the parent constructor.
            $this->name = (string) $name;
        }
    }
}
