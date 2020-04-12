<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Utility\Arr;

class UserLocalizer
{

	public function __construct()
	{
		$this->db = App::get('db');
		$this->systemTimezone = Config::get('offset');
	}

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

	protected function _getUserTimezone()
	{
		$userParams = json_decode(User::get('params'), 1);

		return Arr::getValue($userParams, 'timezone', $this->systemTimezone);
	}

}
