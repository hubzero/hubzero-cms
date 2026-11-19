<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models\Incremental;

use Hubzero\Bank\Teller;
use Hubzero\Facades\App;

/**
 * Class for incremental registration awards
 */
class Awards
{
    /**
     * Database connection, once looked up
     *
     * @var  \Hubzero\Database\Driver|null
     */
    private static $dbh = null;

    /**
     * Awards
     *
     * @var  array<mixed>|int|null
     */
    private $awards;

    /**
     * User ID
     *
     * @var  integer
     */
    private $uid;


    /**
     * @var  mixed
     */
    public $profile;

    /**
     * Get the database connection
     *
     * @return  \Hubzero\Database\Driver
     */
    private static function getDbh()
    {
        if (!self::$dbh) {
            self::$dbh = App::get('db');
        }
        return self::$dbh;
    }

    /**
     * Constructor
     *
     * @param   object|mixed  $profile
     * @return  void
     */
    public function __construct($profile)
    {
        $this->profile = $profile;
        $this->uid = is_integer($profile) ? $profile : (int) $this->profile->get('uidNumber');
        self::getDbh();
        do {
            $columns = [
                'opted_out',
                'name',
                'orgtype',
                'organization',
                'countryresident',
                'countryorigin',
                'gender',
                'url',
                'reason',
                'race',
                'phone',
                'picture',
                'disability',
            ];

            foreach ($columns as $key => $val) {
                // Make sure the column exists
                // This seems to have varied from hub to hub
                if (!self::getDbh()->tableHasField('#__profile_completion_awards', $val)) {
                    unset($columns[$key]);
                }
            }

            if (!empty($columns)) {
                $sql = 'SELECT ' . implode(', ', $columns)
                    . ' FROM `#__profile_completion_awards` WHERE user_id = ' . $this->uid;
                self::getDbh()->setQuery($sql);
                if (!($this->awards = self::getDbh()->loadAssoc())) {
                    $sql = 'INSERT INTO `#__profile_completion_awards` (user_id) VALUES ('
                        . $this->uid . ')';
                    self::getDbh()->setQuery($sql);
                    self::getDbh()->execute();
                }
            } else {
                $this->awards = 1;
            }
        } while (!$this->awards);
    }

    /**
     * Mark an entry as opted out
     *
     * @return  void
     */
    public function optOut()
    {
        $sql = 'UPDATE `#__profile_completion_awards` '
            . 'SET opted_out = opted_out + 1, last_bothered = CURRENT_TIMESTAMP '
            . 'WHERE user_id = ' . $this->uid;
        self::getDbh()->setQuery($sql);
        self::getDbh()->execute();
    }

    /**
     * Mark an entry as opted out
     *
     * @return  mixed
     */
    public function award()
    {
        $uid = $this->uid;
        if (!$uid) {
            return null;
        }
        $opts = new Options();
        $awardPer = $opts->getAwardPerField();

        $fieldMap = [
            'name'            => 'Fullname',
            'orgtype'         => 'Employment',
            'organization'    => 'Organization',
            'countryorigin'   => 'Citizenship',
            'countryresident' => 'Residency',
            'gender'          => 'Sex',
            'url'             => 'URL',
            'reason'          => 'Reason',
            'race'            => 'Race',
            'phone'           => 'Phone',
            'disability'      => 'Disability',
        ];
        $alreadyComplete = 0;
        $eligible = [];
        $newAmount = 0;
        $completeSql = 'UPDATE `#__profile_completion_awards` SET edited_profile = 1';
        $optedOut = null;

        // 1 stands for "no award columns": nothing to go through
        $awards = is_array($this->awards) ? $this->awards : [];
        foreach ($awards as $k => $complete) {
            if ($k === 'opted_out') {
                $optedOut = $complete;
                continue;
            }
            if ($complete) {
                continue;
            }
            if ($k === 'picture') {
                self::getDbh()->setQuery('SELECT picture FROM `#__xprofiles` WHERE uidNumber = ' . $uid);
                if (self::getDbh()->loadResult()) {
                    $completeSql .= ', ' . $k . ' = 1';
                    $alreadyComplete += $awardPer;
                } else {
                    $eligible['picture'] = 1;
                }
                continue;
            }
            $regField = $fieldMap[$k];
            if ((bool) $this->profile->get($k)) {
                $completeSql .= ', ' . $k . ' = 1';
                $alreadyComplete += $awardPer;
            } else {
                $eligible[$k == 'url' ? 'web' : $k] = 1;
            }
        }

        $sql = 'SELECT SUM(amount) AS amount FROM `#__users_transactions` '
            . "WHERE type = 'deposit' AND category = 'registration' AND uid = " . $uid;
        self::getDbh()->setQuery($sql);
        $prior = self::getDbh()->loadResult();
        self::getDbh()->setQuery($completeSql . ' WHERE user_id = ' . $uid);
        self::getDbh()->execute();

        if ($alreadyComplete) {
            $sql = 'SELECT COALESCE('
                . '(SELECT balance FROM `#__users_transactions` WHERE uid = ' . $uid
                . ' AND id = (SELECT MAX(id) FROM `#__users_transactions` WHERE uid = '
                . $uid . ')), 0)';
            self::getDbh()->setQuery($sql);
            $newAmount = (int) self::getDbh()->loadResult() + $alreadyComplete;

            $BTL = new Teller($uid);
            $BTL->deposit($alreadyComplete, 'Profile completion award', 'registration', 0);
        }

        return [
            'prior'     => $prior,
            'new'       => $alreadyComplete,
            'eligible'  => $eligible,
            'opted_out' => $optedOut,
        ];
    }
}
