<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     * @param   array<mixed>  $options  Associative array of options
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
     * @param   array<mixed>    $params  Associative array of attributes
     * @return  string   The rendered data
     */
    public function render($cache = false, $params = [])
    {
        parent::render();

        return $this->getBuffer();
    }
}
