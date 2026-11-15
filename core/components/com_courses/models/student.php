<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Components\Courses\Models\Member;
use Hubzero\Facades\App;

/**
 * Courses model class for a student
 */
class Student extends Member
{
    /**
     * Object scope
     *
     * @var string
     */
    protected $scope = 'student';

    /**
     * Constructor
     *
     * @param   string|mixed $uid User ID
     * @param   string|null $cid Course ID
     * @param   string|null $oid Offering ID
     * @param   string|null $sid Section ID
     * @return  void
     */
    public function __construct($uid, $cid = null, $oid = null, $sid = null)
    {
        $this->db = App::get('db');

        if ($this->tableClass) {
            $cls = $this->tableClass;
            $this->table = new $cls($this->db);

            if (is_numeric($uid) || is_string($uid)) {
                if ($sid !== null) {
                    $this->table->loadBySection($uid, $sid);
                } elseif ($cid !== null) {
                    $this->table->load($uid, $cid, ($oid ?: null), null, 1);
                } else {
                    $this->table->load($uid);
                }
            } elseif (is_object($uid) || is_array($uid)) {
                $this->bind($uid);
            }
        }
    }

    /**
     * Returns a reference to a student object
     *
     * @param   int|string|null $uid User ID
     * @param   int|string|null $cid Course ID
     * @param   int|string|null $oid Offering ID
     * @param   int|string|null $sid Section ID
     * @return  object \Components\Courses\Models\Member
     */
    public static function &getInstance($uid = null, $cid = 0, $oid = 0, $sid = 0)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = [];
        }

        if (!isset($instances[$oid . '_' . $uid])) {
            $instances[$oid . '_' . $uid] = new self($uid, $cid, $oid, $sid);
        }

        return $instances[$oid . '_' . $uid];
    }

    /**
     * Get the gradebook
     *
     * @return  boolean True on success, false on error
     */
    public function gradebook()
    {
        return true;
    }
}
