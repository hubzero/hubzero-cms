<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Utility\Arr;
use App;
use Config;
use User;

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
		$this->db = App::get('db');
		$this->systemTimezone = Config::get('offset');
	}

	/**
	 * Get the timezone
	 *
	 * @return string
	 */
	public function getTimezone()
	{
		if (!User::isGuest())
		{
			$timezone = $this->_getUserTimezone();
		}
		else
		{
			$timezone = $this->systemTimezone;
		}

		return $timezone;
	}

	/**
	 * Get the user's timezone
	 *
	 * @return string
	 */
	protected function _getUserTimezone()
	{
		$userParams = json_decode(User::get('params', '[]'), 1);

		return Arr::getValue($userParams, 'timezone', $this->systemTimezone);
	}
}
