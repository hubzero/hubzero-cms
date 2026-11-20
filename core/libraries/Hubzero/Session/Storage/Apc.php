<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Session\Storage;

use Hubzero\Session\Store;
use Exception;
use Hubzero\Facades\Lang;

/**
 * APC session storage handler, backed by the APCu extension
 *
 * Inspired by Joomla's JSessionStorageApc class. The original APC extension does
 * not exist for PHP 7 and later; APCu is its user cache.
 */
class Apc extends Store
{
    /**
     * Key prefix
     *
     * @var  string
     */
    private $prefix  = 'sess_';

    /**
     * Constructor
     *
     * @param   array<mixed>  $options  Optional parameters
     * @return  void
     */
    public function __construct($options = [])
    {
        if (!self::isAvailable()) {
            throw new Exception(Lang::txt('JLIB_SESSION_APC_EXTENSION_NOT_AVAILABLE'));
        }

        if (isset($options['prefix'])) {
            $this->prefix = $options['prefix'];
        }

        parent::__construct($options);
    }

    /**
     * Read the data for a particular session identifier from the
     * SessionHandler backend.
     *
     * @param   string  $id  The session identifier.
     * @return  string  The session data.
     */
    #[\ReturnTypeWillChange]
    public function read($id)
    {
        return (string) apcu_fetch($this->key($id));
    }

    /**
     * Write session data to the SessionHandler backend.
     *
     * @param   string   $id    The session identifier.
     * @param   string   $data  The session data.
     * @return  boolean  True on success, false otherwise.
     */
    #[\ReturnTypeWillChange]
    public function write($id, $data)
    {
        return apcu_store($this->key($id), $data, (int) ini_get("session.gc_maxlifetime"));
    }

    /**
     * Destroy the data for a particular session identifier in the SessionHandler backend.
     *
     * @param   string   $id  The session identifier.
     * @return  boolean  True on success, false otherwise.
     */
    #[\ReturnTypeWillChange]
    public function destroy($id)
    {
        return apcu_delete($this->key($id));
    }

    /**
     * Build the storage key
     *
     * @param   string  $id  The session identifier.
     * @return  string
     */
    protected function key($id)
    {
        return $this->prefix . $id;
    }

    /**
     * Test to see if the SessionHandler is available.
     *
     * @return  boolean  True on success, false otherwise.
     */
    public static function isAvailable()
    {
        return extension_loaded('apcu');
    }
}
