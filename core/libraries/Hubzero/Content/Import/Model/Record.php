<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Import\Model;

use Hubzero\Base\Obj;
use stdClass;
use Hubzero\Facades\App;
use Hubzero\Facades\User;

/**
 * Import Record Model
 */
class Record extends Obj
{
    /**
     * Title match
     *
     * @var  integer
     */
    public const TITLE_MATCH = 10;

    /**
     * Raw record data
     *
     * @var  \stdClass
     */
    public $raw;

    /**
     * Record data: the entry, its errors and notices, and what the
     * component adds
     *
     * @var  \stdClass
     */
    public $record;

    /**
     * Operation mode (update|patch)
     *
     * @var  string
     */
    protected $mode;

    /**
     * List of options to be applied to record
     *
     * @var  array<mixed>
     */
    protected $options;

    /**
     * Database
     *
     * @var  \Hubzero\Database\Driver
     */
    protected $database;

    /**
     * User
     *
     * @var  \Hubzero\User\User
     */
    protected $user;

    /**
     *  Constructor
     *
     * @param   \stdClass   $raw      Raw data
     * @param   array<mixed>   $options  Import options
     * @param   string  $mode     Operation mode (update|patch)
     * @return  void
     */
    public function __construct($raw, $options = [], $mode = 'UPDATE')
    {
        // Store our incoming data
        $this->raw      = $raw;
        $this->options = $options;
        $this->mode    = strtoupper($mode);

        // Create core objects
        $this->database = App::get('db');
        $this->user     = User::getInstance();

        // Create objects
        $this->record = new stdClass();

        // Message bags for user
        $this->record->errors  = [];
        $this->record->notices = [];

        // Bind data
        $this->bind();
    }

    /**
     * Bind all raw data
     *
     * @return  $this  Current object
     */
    public function bind()
    {
        return $this;
    }

    /**
     * Check Data integrity
     *
     * @return  $this  Current object
     */
    public function check()
    {
        return $this;
    }

    /**
     * Store Data
     *
     * @param   bool|int  $dryRun  Dry Run mode
     * @return  $this   Current object
     */
    public function store($dryRun = 1)
    {
        // Are we running in dry run mode?
        if ($dryRun || count($this->record->errors) > 0) {
            return $this;
        }

        return $this;
    }

    /**
     * Output object of string
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * To String object
     *
     * Removes private properties before returning
     *
     * @return  string
     */
    public function toString()
    {
        // Reflect on class to get private or protected props
        $privateProperties = with(new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PROTECTED);

        // Remove each private or protected prop
        foreach ($privateProperties as $prop) {
            $name = (string) $prop->name;
            unset($this->$name);
        }

        // Output as json
        return (string) json_encode($this);
    }
}
