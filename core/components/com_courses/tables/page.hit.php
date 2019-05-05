<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;
use Request;
use User;
use Date;
use Lang;

/**
 * Table class for course page
 */
class PageHit extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_page_hits', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->offering_id = intval($this->offering_id);
		if (!$this->offering_id)
		{
			$this->setError(Lang::txt('COM_COURSES_LOGS_MUST_HAVE_OFFERING_ID'));
			return false;
		}

		$this->page_id = intval($this->page_id);
		if (!$this->page_id)
		{
			$this->setError(Lang::txt('COM_COURSES_LOGS_MUST_HAVE_PAGE_ID'));
			return false;
		}

		$this->ip = trim($this->ip);

		if (!$this->id)
		{
			$this->timestamp = Date::toSql();
			if (!$this->ip)
			{
				$this->ip = Request::ip();
			}
			if (!$this->user_id)
			{
				$this->user_id = User::get('id');
			}
		}

		$this->user_id = intval($this->user_id);
		if (!$this->user_id)
		{
			$this->setError(Lang::txt('COM_COURSES_LOGS_MUST_HAVE_USER_ID'));
			return false;
		}

		return true;
	}

	/**
	 * Record a page hit
	 *
	 * @param   integer  $offering_id
	 * @param   integer  $page_id
	 * @param   integer  $user_id
	 * @return  boolean
	 */
	public function hit($offering_id, $page_id, $user_id=0)
	{
		if (!$user_id)
		{
			$user_id = User::get('id');
		}
		$this->offering_id = $offering_id;
		$this->page_id     = $page_id;
		$this->user_id     = $user_id;

		if (!$this->check())
		{
			return false;
		}
		if (!$this->store())
		{
			return false;
		}
		return true;
	}
}
