<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Import\Model;

use Hubzero\Base\Obj;
use Exception;
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
     * @var  object
     */
    public $raw;

    /**
     * Record data
     *
     * @var  object
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
     * @var  array
     */
    protected $options;

    /**
     * Database
     *
     * @var  object
     */
    protected $database;

    /**
     * User
     *
     * @var  object
     */
    protected $user;

    /**
     *  Constructor
     *
     * @param   mixes   $raw      Raw data
     * @param   array   $options  Import options
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
     * @return  object  Current object
     */
    public function bind()
    {
        return $this;
    }

    /**
     * Check Data integrity
     *
     * @return  object  Current object
     */
    public function check()
    {
        return $this;
    }

    /**
     * Store Data
     *
     * @param   integer  $dryRun  Dry Run mode
     * @return  object   Current object
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
        return json_encode($this);
    }
}
