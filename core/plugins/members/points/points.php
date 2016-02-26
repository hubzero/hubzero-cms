<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Members Plugin class for points
 */
class plgMembersPoints extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param      object  $user   User
	 * @param      object  $member MembersProfile
	 * @return     array   Plugin name
	 */
	public function &onMembersAreas($user, $member)
	{
		//default areas returned to nothing
		$areas = array();

		//if this is the logged in user show them
		if ($user->get('id') == $member->get('uidNumber'))
		{
			$areas['points'] = Lang::txt('PLG_MEMBERS_POINTS');
			$areas['icon'] = 'f006';
		}

		return $areas;
	}

	/**
	 * Event call to return data for a specific member
	 *
	 * @param      object  $user   User
	 * @param      object  $member MembersProfile
	 * @param      string  $option Component name
	 * @param      string  $areas  Plugins to return data
	 * @return     array   Return array of html
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;
		$returnmeta = true;

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member))))
			{
				$returnhtml = false;
			}
		}

		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		$database = App::get('db');
		$tables = $database->getTableList();
		$table  = $database->getPrefix() . 'users_points';

		if (!in_array($table, $tables))
		{
			$arr['html'] = '<p class="error">' . Lang::txt('PLG_MEMBERS_POINTS_ERROR_MISSING_TABLE') . '</p>';
			return $arr;
		}

		$BTL = new \Hubzero\Bank\Teller($member->get('uidNumber'));

		// Build the final HTML
		if ($returnhtml)
		{
			$view = $this->view('default', 'history');

			$view->sum = $BTL->summary();

			$view->credit = $BTL->credit_summary();
			$funds = $view->sum - $view->credit;

			$view->funds = ($funds > 0) ? $funds : 0;
			$view->hist  = $BTL->history(0);
			if ($this->getError())
			{
				$view->setError($this->getError());
			}

			$arr['html'] = $view->loadTemplate();
		}

		// Build the HTML meant for the "about" tab's metadata overview
		if ($returnmeta)
		{
			$arr['metadata'] = array();

			$points = $BTL->summary();

			$prefix = ($user->get('id') == $member->get('uidNumber')) ? 'I have' : $member->get('name') . ' has';
			$title = $prefix . ' ' . $points . ' points.';

			$arr['metadata']['count'] = $points;
		}

		return $arr;
	}

	/**
	 * Remove all user blog entries for the given user ID
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param   array    $user     Holds the user data
	 * @param   boolean  $success  True if user was succesfully stored in the database
	 * @param   string   $msg      Message
	 * @return  boolean
	 */
	public function onMemberAfterDelete($user, $success, $msg)
	{
		if (!$success)
		{
			return false;
		}

		$userId = \Hubzero\Utility\Arr::getValue($user, 'id', 0, 'int');

		if ($userId)
		{
			try
			{
				$entry = \Hubzero\Bank\Account::oneByUserId($user['id']);

				if (!$entry->destroy())
				{
					throw new Exception($entry->getError());
				}

				$transactions = \Hubzero\Bank\Transaction::all()->whereEquals('uid', $user['id']);

				foreach ($transactions->rows() as $row)
				{
					if (!$row->destroy())
					{
						throw new Exception($row->getError());
					}
				}
			}
			catch (Exception $e)
			{
				return false;
			}
		}

		return true;
	}
}
