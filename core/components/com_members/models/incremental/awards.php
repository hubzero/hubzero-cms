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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

	/**
	 * Get the database connection
	 *
	 * @return  object
	 */
	private static function getDbh()
	{
		if (!self::$dbh)
		{
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
		do
		{
			self::$dbh->setQuery('SELECT opted_out, name, orgtype, organization, countryresident, countryorigin, gender, url, reason, race, phone, picture, disability FROM `#__profile_completion_awards` WHERE user_id = ' . $this->uid);
			if (!($this->awards = self::$dbh->loadAssoc()))
			{
				self::$dbh->setQuery('INSERT INTO `#__profile_completion_awards` (user_id) VALUES (' . $this->uid . ')');
				self::$dbh->execute();
			}
		}
		while (!$this->awards);
	}

	/**
	 * Mark an entry as opted out
	 *
	 * @return  void
	 */
	public function optOut()
	{
		self::$dbh->setQuery('UPDATE `#__profile_completion_awards` SET opted_out = opted_out + 1, last_bothered = CURRENT_TIMESTAMP WHERE user_id = ' . $this->uid);
		self::$dbh->execute();
	}

	/**
	 * Mark an entry as opted out
	 *
	 * @return  mixed
	 */
	public function award()
	{
		if (!$this->uid)
		{
			return NULL;
		}
		$opts = new Options;
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
		$optedOut = NULL;

		foreach ($this->awards as $k => $complete)
		{
			if ($k === 'opted_out')
			{
				$optedOut = $complete;
				continue;
			}
			if ($complete)
			{
				continue;
			}
			if ($k === 'picture')
			{
				self::$dbh->setQuery('SELECT picture FROM `#__xprofiles` WHERE uidNumber = ' . $this->uid);
				if (self::$dbh->loadResult())
				{
					$completeSql .= ', ' . $k . ' = 1';
					$alreadyComplete += $awardPer;
				}
				else
				{
					$eligible['picture'] = 1;
				}
				continue;
			}
			$regField = $fieldMap[$k];
			if ((bool)$this->profile->get($k))
			{
				$completeSql .= ', ' . $k . ' = 1';
				$alreadyComplete += $awardPer;
			}
			else
			{
				$eligible[$k == 'url' ? 'web' : $k] = 1;
			}
		}

		self::$dbh->setQuery('SELECT SUM(amount) AS amount FROM `#__users_transactions` WHERE type = \'deposit\' AND category = \'registration\' AND uid = ' . $this->uid);
		$prior = self::$dbh->loadResult();
		self::$dbh->setQuery($completeSql . ' WHERE user_id = ' . $this->uid);
		self::$dbh->execute();

		if ($alreadyComplete)
		{
			self::$dbh->setQuery('SELECT COALESCE((SELECT balance FROM `#__users_transactions` WHERE uid = ' . $this->uid . ' AND id = (SELECT MAX(id) FROM `#__users_transactions` WHERE uid = ' . $this->uid . ')), 0)');
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
