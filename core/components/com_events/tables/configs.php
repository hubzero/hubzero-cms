<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Tables;

/**
 * Events class for getting all configurations
 *
 * The settings are the rows of #__events_config, kept by name through
 * __set() and __get(). The one this class reads itself:
 *
 * @property  array<mixed>|string  $fields  The custom fields: one "name=label" line each, split into lists by load()
 */
class Configs
{
    /**
     * Table name
     *
     * @var  string
     */
    private $tbl;

    /**
     * Database connection
     *
     * @var  \Hubzero\Database\Driver
     */
    private $db;

    /**
     * Properties container
     *
     * @var  array<mixed>
     */
    private $data = [];

    /**
     * Constructor
     *
     * @param   \Hubzero\Database\Driver  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        $this->tbl = '#__events_config';
        $this->db  = $db;
    }

    /**
     * Method to set an overloaded variable to the component
     *
     * @param   string  $property  Name of overloaded variable to add
     * @param   mixed   $value     Value of the overloaded variable
     * @return  void
     */
    public function __set($property, $value)
    {
        $this->data[$property] = $value;
    }

    /**
     * Method to get an overloaded variable of the component
     *
     * @param   string  $property  Name of overloaded variable to retrieve
     * @return  mixed   Value of the overloaded variable
     */
    public function __get($property)
    {
        if (isset($this->data[$property])) {
            return $this->data[$property];
        }

        return null;
    }

    /**
     * Get all configurations and populate $this
     *
     * @return  void
     */
    public function load()
    {
        $this->db->setQuery("SELECT * FROM $this->tbl");
        $configs = $this->db->loadObjectList();

        if (empty($configs)) {
            if ($this->loadDefaults()) {
                $this->db->setQuery("SELECT * FROM $this->tbl");
                $configs = $this->db->loadObjectList();
            }
        }

        if (!empty($configs)) {
            foreach ($configs as $config) {
                $b = $config->param;
                $this->$b = trim($config->value);
            }
        }

        // Until here the fields are the configured "name=label" lines
        $fields = [];
        $lines  = is_string($this->fields) ? trim($this->fields) : '';
        if ($lines != '') {
            $fs = explode("\n", $lines);
            foreach ($fs as $f) {
                $fields[] = explode('=', $f);
            }
        }
        $this->fields = $fields;
    }

    /**
     * Set the default configuration values
     *
     * @return  boolean  True on success, false on errors
     */
    public function loadDefaults()
    {
        $config = [
            'adminmail' => '',
            'starday' => '0',
            'mailview' => 'NO',
            'byview' => 'NO',
            'hitsview' => 'NO',
            'repeatview' => 'NO',
            'dateformat' => '0',
            'calUseStdTime' => 'NO',
            'navbarcolor' => '',
            'startview' => 'month',
            'calEventListRowsPpg' => '30',
            'calSimpleEventForm' => 'NO',
            'defColor' => '',
            'calForceCatColorEventForm' => 'NO',
            'fields' => '',
        ];
        foreach ($config as $p => $v) {
            $this->db->setQuery(
                "INSERT INTO $this->tbl (param, value) VALUES ("
                . $this->db->quote($p) . ", " . $this->db->quote($v) . ")",
            );
            // A failed insert throws
            $this->db->query();
        }
        return true;
    }

    /**
     * Get a configuration value
     *
     * @param   string  $f        Property name
     * @param   mixed   $default  What to give when it has no value
     * @return  mixed
     */
    public function getCfg($f = '', $default = null)
    {
        if ($f) {
            return $this->$f ?? $default;
        }

        return $default;
    }
}
