<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Models\Middleware;

use Components\Tools\Helpers\Utils;
use Hubzero\Base\Model;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Component;

/**
 * Abstract model class
 */
class Base extends Model
{
    /**
     * Registry
     *
     * @var  object|null
     */
    private $config = null;

    /**
     * Constructor
     *
     * @param   mixed  $oid  Integer (ID), string (alias), object or array
     * @return  void
     */
    public function __construct($oid = null)
    {
        $this->db = Utils::getMWDBO();

        if ($this->tableClass) {
            $cls = $this->tableClass;
            $this->table = new $cls($this->db);

            if (!($this->table instanceof \Hubzero\Database\Table)) {
                $this->logError(
                    __CLASS__ . '::' . __FUNCTION__ . '(); '
                    . Lang::txt('Table class must be an instance of \\Hubzero\\Database\\Table.'),
                );
                throw new \LogicException(
                    Lang::txt('Table class must be an instance of \\Hubzero\\Database\\Table.'),
                );
            }

            if (is_numeric($oid) || is_string($oid)) {
                // Make sure $oid isn't empty
                // This saves a database call
                if ($oid) {
                    $this->table->load($oid);
                }
            } elseif (is_object($oid) || is_array($oid)) {
                $this->bind($oid);
            }
        }
    }

    /**
     * Get a config value
     *
     * @param   string  $key      Property to return
     * @param   mixed   $default  Default value
     * @return  mixed
     */
    public function config($key = '', $default = null)
    {
        if (!isset($this->config)) {
            $this->config = Component::params('com_tools');
        }

        if ($key) {
            return $this->config->get((string) $key, $default);
        }
        return $this->config;
    }
}
