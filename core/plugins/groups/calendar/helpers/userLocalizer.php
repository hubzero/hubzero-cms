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
		$userParams = $this->_getUserParams();

		return Arr::getValue($userParams, 'timezone', $this->systemTimezone);
	}

	protected function _getUserParams()
	{
		$userId = User::get('id');
		$query = "SELECT params from jos_users where id = $userId;";
		$this->db->setQuery($query);
		$result = $this->db->loadResult();

		return json_decode($result, 1);
	}

}
