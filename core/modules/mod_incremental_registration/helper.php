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
 * @author    Steve Snyder
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\IncrementalRegistration;

use Hubzero\Module\Module;
use Components\Members\Models\Incremental\Options;
use Components\Members\Models\Incremental\Groups;
use Components\Members\Models\Incremental\Awards;
use Component;
use Request;
use User;
use App;

/**
 * Incremental Registration Module controller class
 */
class Helper extends Module
{
	/**
	 * Path to template overrides
	 *
	 * @var array
	 */
	private $tpl_path = '/core/templates/system/html/mod_incremental_registration';

	/**
	 * Path to module directory
	 *
	 * @var array
	 */
	private $mod_path = '/core/modules/mod_incremental_registration/assets';

	/**
	 * Determines if the file ($path) passed to it exists
	 *
	 * @param   string  $path  File path
	 * @return  string
	 */
	public function media($path)
	{
		$this->tpl_path = App::get('template')->path . '/html/mod_incremental_registration';

		$b = rtrim(Request::base(true), '/');
		return file_exists(PATH_APP . $this->tpl_path . $path) ? $b . $this->tpl_path . $path : $b . $this->mod_path . $path;
	}

	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		if (User::isGuest())
		{
			return;
		}

		$dbg = isset($_GET['dbg']);
		$uid = (int) User::get('id');
		$dbh = App::get('db');

		require_once Component::path('com_members') . '/models/incremental/awards.php';
		require_once Component::path('com_members') . '/models/incremental/groups.php';
		require_once Component::path('com_members') . '/models/incremental/options.php';

		$opts = new Options;
		if (!$opts->isEnabled($uid))
		{
			return;
		}

		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['REDIRECT_REQUEST_URI'];
		if (preg_match('%(?:members|invoke|session|privacy)%', $uri))
		{
			return;
		}

		// looks like an error page, don't show
		if (\JDocument::getInstance('error')->getTitle())
		{
			return;
		}

		if (isset($_POST['incremental-registration']) && isset($_POST['submit']) && $_POST['submit'] === 'opt-out')
		{
			$awards = new Awards($uid);
			$awards->optOut();
			return;
		}

		$groups = new Groups;

		$hasCurl = file_exists(__DIR__ . '/assets/img/bigcurl.png');
		if (($row = $groups->getActiveColumns($uid)) || $hasCurl)
		{
			if (!isset($_SESSION['return']) && !preg_match('/[.]/', $uri))
			{
				$_SESSION['return'] = $uri;
			}

			$this->css();
			$this->js();

			if ($row)
			{
				$dbh->setQuery('SELECT popover_text, award_per FROM `#__incremental_registration_options` ORDER BY added DESC LIMIT 1');
				list($introText, $awardPer) = $dbh->loadRow();

				if ($_SERVER['REQUEST_METHOD'] == 'GET')
				{
					require $this->getLayoutPath('popover');
				}
				elseif (isset($_POST['incremental-registration']) && $_POST['incremental-registration'] == 'update')
				{
					$errors       = array();
					$orgtype      = null;
					$organization = null;
					$reason       = null;
					$mailPreferenceOption = -1;

					if (isset($_POST['mailPreferenceOption']))
					{
						$mailPreferenceOption = (int)$_POST['mailPreferenceOption'];
					}
					if (isset($_POST['orgtype']) && trim($_POST['orgtype']))
					{
						$orgtype = trim($_POST['orgtype']);
					}
					if (isset($_POST['org-other']) && trim($_POST['org-other']))
					{
						$organization = trim($_POST['org-other']);
					}
					elseif (isset($_POST['org']) && trim($_POST['org']))
					{
						$organization = trim($_POST['org']);
					}

					if (isset($_POST['reason-other']) && trim($_POST['reason-other']))
					{
						$reason = trim($_POST['reason-other']);
					}
					elseif (isset($_POST['reason']) && trim($_POST['reason']))
					{
						$reason = trim($_POST['reason']);
					}

					if (isset($_POST['name']))
					{
						if (!isset($POST['name']['first']) || !isset($_POST['name']['last']))
						{
							$errors['name'] = true;
						}
						$name = preg_replace('/\s+/', ' ', trim(implode(' ', array($name['first'], $name['middle'], $name['last']))));
					}

					if (isset($row['gender']))
					{
						if (!isset($_POST['gender'])
						 || ($_POST['gender'] != 'male' && $_POST['gender'] != 'female' && $_POST['gender'] != 'refused'))
						{
							$errors['gender'] = true;
						}
						else
						{
							$gender = $_POST['gender'];
						}
					}

					if (isset($_POST['url']))
					{
						if (!trim($_POST['url']))
						{
							$errors['url'] = true;
						}
						$url = trim($_POST['url']);
					}

					if (isset($_POST['phone']))
					{
						if (!trim($_POST['phone']))
						{
							$errors['phone'] = true;
						}
						$phone = trim($_POST['phone']);
					}

					if (isset($row['race']))
					{
						if (empty($_POST['race']) || !is_array($_POST['race']))
						{
							$errors['race'] = true;
						}
						else
						{
							$race = array_map('trim',  $_POST['race']);
						}
					}

					if (isset($row['countryorigin']))
					{
						if (isset($_POST['countryorigin_us']) && $_POST['countryorigin_us'] == 'yes') {
							$countryorigin = 'us';
						}
						elseif (!isset($_POST['countryorigin']) || !preg_match('/[A-Za-z]{2}/', $_POST['countryorigin']))
						{
							$errors['countryorigin'] = true;
						}
						else
						{
							$countryorigin = $_POST['countryorigin'];
						}
						// race does not apply to non-us
						if (isset($countryorigin) && strtolower($countryorigin) != 'us' && isset($errors['race']))
						{
							unset($errors['race']);
						}
					}
					if (isset($row['countryresident']))
					{
						if (isset($_POST['countryresident_us']) && $_POST['countryresident_us'] == 'yes')
						{
							$countryresident = 'us';
						}
						elseif (!isset($_POST['countryresident']) || !preg_match('/[A-Za-z]{2}/', $_POST['countryresident']))
						{
							$errors['countryresident'] = true;
						}
						else
						{
							$countryresident = $_POST['countryresident'];
						}
					}

					if (isset($row['disability']))
					{
						if (!isset($_POST['disability']) || ($_POST['disability'] == 'yes' && ((!isset($_POST['specificDisability']) || !$_POST['specificDisability']) && (!isset($_POST['otherDisability']) || !trim($_POST['otherDisability'])))))
						{
							$errors['disability'] = true;
						}
					}

					if (isset($row['orgtype']) && !$orgtype)
					{
						$errors['orgtype'] = true;
					}
					if (isset($row['organization']) && !$organization)
					{
						$errors['organization'] = true;
					}
					if (isset($row['reason']) && !$reason)
					{
						$errors['reason'] = true;
					}
					if (isset($row['mailPreferenceOption']) && $mailPreferenceOption == -1)
					{
						$errors['mailPreferenceOption'] = true;
					}
					if (isset($row['location']) && !$location)
					{
						if (isset($_POST['location']))
						{
							$location = trim($_POST['location']);
						}
						else
						{
							$errors['location'] = true;
						}
					}

					if ($errors)
					{
						require $this->getLayoutPath('popover');
					}
					else
					{
						$dbh->setQuery('SELECT '.implode(', ', array_keys($row)).' FROM #__profile_completion_awards WHERE user_id = ' . $uid);
						$award = 0;
						$awarded = $dbh->loadAssoc();

						if (!empty($awarded))
						{
							foreach ($awarded as $v)
							{
								if (!$v)
								{
									$award += $awardPer;
								}
							}
						}

						//$dbh->setQuery('SELECT COALESCE((SELECT balance FROM `#__users_transactions` WHERE uid = ' . $uid . ' AND id = (SELECT MAX(id) FROM `#__users_transactions` WHERE uid = ' . $uid . ')), 0)');
						$dbh->setQuery('SELECT balance FROM `#__users_points` WHERE uid = ' . $uid);
						$new_amount = intval($dbh->loadResult()) + $award;

						if ($award)
						{
							$BTL = new \Hubzero\Bank\Teller($uid);
							$BTL->deposit($award, Lang::txt('MOD_INCREMENTAL_REGISTRATION_PROFILE_COMPLETION_AWARD'), 'registration', 0);
						}

						$xp_update = 'UPDATE `#__xprofiles` SET ';
						$aw_update = 'UPDATE `#__profile_completion_awards` SET edited_profile = 1, ';
						$first = true;
						foreach (array_keys($row) as $k)
						{
							if ($k == 'race')
							{
								if (isset($race))
								{
									$dbh->setQuery('DELETE FROM `#__xprofiles_race` WHERE uidNumber = '.$uid);
									$dbh->execute();
									foreach ($race as $r)
									{
										$dbh->setQuery('INSERT INTO `#__xprofiles_race` (uidNumber, race) VALUES ('.$uid.', '.$dbh->quote($r).')');
										$dbh->execute();
									}
									if (isset($_POST['racenativetribe']))
									{
										$dbh->setQuery('UPDATE `#__xprofiles` SET nativeTribe = '.$dbh->quote($_POST['racenativetribe']).' WHERE uidNumber = '.$uid);
										$dbh->execute();
									}
								}
								continue;
							}
							if ($k == 'disability')
							{
								$disabilities = array();
								switch ($_POST['disability'])
								{
									case 'yes':
										$disabilities = isset($_POST['specificDisability']) && is_array($_POST['specificDisability']) ? $_POST['specificDisability'] : array();
										if (($other = isset($_POST['otherDisability']) ? trim($_POST['otherDisability']) : NULL))
										{
											$disabilities[] = $other;
										}
									break;
									case 'no':
										$disabilities[] = 'none';
									break;
									case 'refused':
										$disabilities[] = 'refused';
									break;
								}
								foreach ($disabilities as $disability)
								{
									$dbh->setQuery('INSERT INTO `#__xprofiles_disability` (uidNumber, disability) VALUES ('.$uid.', '.$dbh->quote($disability).')');
									$dbh->execute();
								}
								continue;
							}
							if ($k == 'location') {
								$dbh->setQuery('INSERT INTO `#__xprofiles_address` (uidNumber, addressPostal) VALUES('.$uid.', '.$dbh->quote($location).')');
								$dbh->execute();
								continue;
							}
							if ($k == 'name')
							{
								$dbh->setQuery('UPDATE `#__xprofiles` SET givenName = '.$dbh->quote($_POST['name']['first']).', middleName = '.$dbh->quote($_POST['name']['middle']).', surname = '.$dbh->quote($_POST['name']['last']).' WHERE uidNumber = '.$uid);
								$dbh->execute();
							}
							if ($k == 'countryorigin' || $k == 'countryresident')
							{
								$$k = strtoupper($$k);
							}
							if (isset($row[$k]))
							{
								$xp_update .= ($first ? '' : ', ') . $k . ' = ' . $dbh->quote($$k);
								$aw_update .= ($first ? '' : ', ') . $k . ' = 1';
								$first = false;
							}
						}
						if (!$first)
						{
							$dbh->setQuery($xp_update . ' WHERE uidNumber = ' . $uid);
							$dbh->execute();
							$dbh->setQuery($aw_update . ' WHERE user_id = ' . $uid);
							$dbh->execute();
						}

						require $this->getLayoutPath('thanks');
						return;
					}
				}
			}
			else if (!preg_match('%^/members/' . $uid . '/profile%', $uri) && $hasCurl)
			{
				require $this->getLayoutPath('curl');
			}
		}
	}
}
