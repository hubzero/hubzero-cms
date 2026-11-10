<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Tables;

/**
 * Events class for getting all configurations
 */
class Configs
{
    /**
     * Table name
     *
     * @var  string
     */
    private $tbl = null;

    /**
     * Database connection
     *
     * @var  object
     */
    private $db = null;

    /**
     * Properties container
     *
     * @var  array
     */
    private $data = [];

    /**
     * Constructor
     *
     * @param   object  &$db  Database
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

        if (empty($configs) || count($configs) <= 0) {
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

        $fields = [];
        if (trim($this->fields) != '') {
            $fs = explode("\n", trim($this->fields));
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
            if (!$this->db->query()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get a configuration value
     *
     * @param   string  $f  Property name
     * @return  string
     */
    public function getCfg($f = '')
    {
        if ($f) {
            return $this->$f;
        } else {
            return null;
        }
    }
}
