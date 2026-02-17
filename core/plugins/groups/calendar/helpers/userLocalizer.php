<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Groups\Calendar\Helpers;

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Utility\Arr;

/**
 * User Localizer helper class
 */
class UserLocalizer
{
    /**
     * Database connection
     *
     * @var object
     */
    public $db;

    /**
     * System timezone
     *
     * @var string
     */
    public $systemTimezone;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->db = \App::get('db');
        $this->systemTimezone = \Config::get('offset');
    }

    /**
     * Get the timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        if (!\User::isGuest()) {
            $timezone = $this->_getUserTimezone();
        } else {
            $timezone = $this->systemTimezone;
        }

        return $timezone;
    }

    /**
     * Get the user's timezone
     *
     * @return string
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getUserTimezone()
    {
        $userParams = json_decode(\User::get('params', '[]'), 1);

        return Arr::getValue($userParams, 'timezone', $this->systemTimezone);
    }
}

class_alias(__NAMESPACE__ . '\UserLocalizer', 'UserLocalizer');
