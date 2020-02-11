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
		$userId = User::get('id');
		$query = "SELECT params from jos_users where id = $userId;";
		$this->db->setQuery($query);
		$result = $this->db->loadColumn();
		$params = json_decode(Arr::getValue($result, 0, '{}'), 1);

		return Arr::getValue($params, 'timezone', $this->systemTimezone);
	}

}
