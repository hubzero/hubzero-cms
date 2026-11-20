<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Encryption;

/**
 * Encryption key object
 *
 * Inspired by Joomla's JCryptKey class
 */
class Key
{
    /**
     * The private key.
     *
     * @var  string|null
     */
    public $private = null;

    /**
     * The public key.
     *
     * @var  string|null
     */
    public $public = null;

    /**
     * The key type.
     *
     * @var  string
     */
    public $type;

    /**
     * Constructor.
     *
     * @param   string|int|float  $type     The key type.
     * @param   string|int|float|null  $private  The private key.
     * @param   string|int|float|null  $public   The public key.
     * @return  void
     */
    public function __construct($type, $private = null, $public = null)
    {
        // Set the key type.
        $this->type = (string) $type;

        // Set the optional public/private key strings.
        $this->private = isset($private) ? (string) $private : null;
        $this->public  = isset($public) ? (string) $public : null;
    }
}
