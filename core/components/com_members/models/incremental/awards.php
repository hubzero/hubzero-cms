<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models\Incremental;

use Hubzero\Bank\Teller;
use App;

/**
 * Class for incremental registration awards
 */
class Awards
{
    /**
     * Database connection
     *
     * @var  object
     */
    private static $dbh;

    /**
     * Awards
     *
     * @var  array
     */
    private $awards;

    /**
     * User ID
     *
     * @var  integer
     */
    private $uid;


    public $profile;

    /**
     * Get the database connection
     *
     * @return  object
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
     * @param   object  $profile
     * @return  void
     */
    public function __construct($profile)
    {
        $this->profile = $profile;
        $this->uid = is_integer($profile) ? $profile : (int)$this->profile->get('uidNumber');
        self::getDbh();
        do {
            $columns = array(
                'opted_out', 'name', 'orgtype', 'organization', 'countryresident',
                'countryorigin', 'gender', 'url', 'reason', 'race', 'phone', 'picture', 'disability'
            );

            foreach ($columns as $key => $val) {
                // Make sure the column exists
                // This seems to have varied from hub to hub
                if (!self::$dbh->tableHasField('#__profile_completion_awards', $val)) {
                    unset($columns[$key]);
                }
            }

            if (!empty($columns)) {
                $sql = 'SELECT ' . implode(', ', $columns)
                    . ' FROM `#__profile_completion_awards` WHERE user_id = ' . $this->uid;
                self::$dbh->setQuery($sql);
                if (!($this->awards = self::$dbh->loadAssoc())) {
                    $sql = 'INSERT INTO `#__profile_completion_awards` (user_id) VALUES ('
                        . $this->uid . ')';
                    self::$dbh->setQuery($sql);
                    self::$dbh->execute();
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
        self::$dbh->setQuery($sql);
        self::$dbh->execute();
    }

    /**
     * Mark an entry as opted out
     *
     * @return  mixed
     */
    public function award()
    {
        if (!$this->uid) {
            return null;
        }
        $opts = new Options();
        $awardPer = $opts->getAwardPerField();

        $fieldMap = array(
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
            'disability'      => 'Disability'
        );
        $alreadyComplete = 0;
        $eligible = array();
        $newAmount = 0;
        $completeSql = 'UPDATE `#__profile_completion_awards` SET edited_profile = 1';
        $optedOut = null;

        foreach ($this->awards as $k => $complete) {
            if ($k === 'opted_out') {
                $optedOut = $complete;
                continue;
            }
            if ($complete) {
                continue;
            }
            if ($k === 'picture') {
                self::$dbh->setQuery('SELECT picture FROM `#__xprofiles` WHERE uidNumber = ' . $this->uid);
                if (self::$dbh->loadResult()) {
                    $completeSql .= ', ' . $k . ' = 1';
                    $alreadyComplete += $awardPer;
                } else {
                    $eligible['picture'] = 1;
                }
                continue;
            }
            $regField = $fieldMap[$k];
            if ((bool)$this->profile->get($k)) {
                $completeSql .= ', ' . $k . ' = 1';
                $alreadyComplete += $awardPer;
            } else {
                $eligible[$k == 'url' ? 'web' : $k] = 1;
            }
        }

        $sql = 'SELECT SUM(amount) AS amount FROM `#__users_transactions` '
            . "WHERE type = 'deposit' AND category = 'registration' AND uid = " . $this->uid;
        self::$dbh->setQuery($sql);
        $prior = self::$dbh->loadResult();
        self::$dbh->setQuery($completeSql . ' WHERE user_id = ' . $this->uid);
        self::$dbh->execute();

        if ($alreadyComplete) {
            $sql = 'SELECT COALESCE('
                . '(SELECT balance FROM `#__users_transactions` WHERE uid = ' . $this->uid
                . ' AND id = (SELECT MAX(id) FROM `#__users_transactions` WHERE uid = '
                . $this->uid . ')), 0)';
            self::$dbh->setQuery($sql);
            $newAmount = self::$dbh->loadResult() + $alreadyComplete;

            $BTL = new Teller($this->uid);
            $BTL->deposit($alreadyComplete, 'Profile completion award', 'registration', 0);
        }

        return array(
            'prior'     => $prior,
            'new'       => $alreadyComplete,
            'eligible'  => $eligible,
            'opted_out' => $optedOut
        );
    }
}
