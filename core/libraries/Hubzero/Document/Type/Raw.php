<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Type;

use Hubzero\Document\Base;

/**
 * Raw document class for outputting raw data
 *
 * Inspired by Joomla's JDocumentRaw class
 */
class Raw extends Base
{
    /**
     * Class constructor
     *
     * @param   array  $options  Associative array of options
     * @return  void
     */
    public function __construct($options = [])
    {
        parent::__construct($options);

        // Set mime type
        $this->mime = 'text/html';

        // Set document type
        $this->type = 'raw';
    }

    /**
     * Render the document.
     *
     * @param   boolean  $cache   If true, cache the output
     * @param   array    $params  Associative array of attributes
     * @return  string   The rendered data
     */
    public function render($cache = false, $params = [])
    {
        parent::render();

        return $this->getBuffer();
    }
}
