<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

/**
 * Courses model class for badges
 */
class MemberBadge extends Base
{
    /**
     * Table class name
     *
     * @var string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_tbl_name = '\\Components\\Courses\\Tables\\MemberBadge';

    /**
     * Object scope
     *
     * @var string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_scope = 'memberbadge';

    /**
     * Constructor
     *
     * @param   integer $oid Record ID
     * @return  void
     */
    public function __construct($oid = null)
    {
        $this->_db = \Hubzero\Facades\App::get('db');

        $this->_tbl = new $this->_tbl_name($this->_db);

        if (is_numeric($oid)) {
            $this->_tbl->load($oid);
        }
    }

    /**
     * Load by member id
     *
     * Member id is unique to a course and section, and badges are unique to members.
     * Therefore, member id also serves as a primary key of this table.
     *
     * @param   integer $id Member ID
     * @return  mixed   Object on success, False on error
     */
    public static function loadByMemberId($id)
    {
        if (is_numeric($id)) {
            $obj = new MemberBadge();
            $obj->_tbl->loadByMemberId($id);
            return $obj;
        } else {
            return false;
        }
    }

    /**
     * Load by validation token
     *
     * Validation token is a unique hash that allows us to identify a users badge
     * evidence without exposing their user id
     *
     * @param    string $token Badge assertion token
     * @return   mixed  Object on success, False on error
     */
    public static function loadByToken($token)
    {
        $obj = new MemberBadge();
        $obj->_tbl->load(array('validation_token' => $token));

        if (isset($obj->_tbl->id)) {
            return $obj;
        } else {
            return false;
        }
    }

    /**
     * Store member badge
     *
     * @param   boolean $check Perform data validation?
     * @return  boolean
     */
    public function store($check = true)
    {
        if (!$this->get('validation_token')) {
            // Generate validation token
            $randomBytes = openssl_random_pseudo_bytes(21);
            $encoded = substr(base64_encode($randomBytes), 0, 20);
            $token = str_replace(array('/', '+'), array('-', '-'), $encoded);
            $this->set('validation_token', $token);
        }

        return parent::store();
    }

    /**
     * Check whether or not a student has earned the badge
     *
     * @return  bool
     */
    public function hasEarned()
    {
        return ($this->get('earned') == 1) ? true : false;
    }
}
