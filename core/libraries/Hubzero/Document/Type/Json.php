<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Type;

use Hubzero\Document\Base;
use Hubzero\Facades\App;

/**
 * JSON document class for parsing and displaying JSON data
 *
 * Inspired by Joomla's JDocumentJson class
 */
class Json extends Base
{
    /**
     * Document name
     *
     * @var  string
     */
    protected $name = 'hubzero';

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
        $this->mime = 'application/json';

        // Set document type
        $this->type = 'json';
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
        App::get('response')->headers->set('Cache-Control', 'no-cache', false);
        App::get('response')->headers->set('Pragma', 'no-cache');
        App::get('response')->headers->set(
            'Content-disposition',
            'attachment; filename="' . preg_replace('/[^A-Za-z0-9._-]/', '', (string) $this->getName()) . '.json"',
            true,
        );

        parent::render();

        return $this->getBuffer();
    }

    /**
     * Returns the document name
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the document name
     *
     * @param   string  $name  Document name
     * @return  $this  instance of $this to allow chaining
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }
}
