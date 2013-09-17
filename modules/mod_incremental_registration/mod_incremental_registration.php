<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Steve Snyder
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if (!class_exists('ModIncrementalRegistrationMediaPath')): 
/**
 * Incremental Registration Module class for media paths
 */
class ModIncrementalRegistrationMediaPath
{
	/**
	 * Path to template overrides
	 * 
	 * @var array
	 */
	private $tpl_path = '/templates/system/html/mod_incremental_registration';

	/**
	 * Path to module directory
	 * 
	 * @var array
	 */
	private $mod_path = '/modules/mod_incremental_registration';

	/**
	 * Constructor
	 * 
	 * @return     void
	 */
	public function __construct() 
	{
		$this->tpl_path = '/templates/' . JFactory::getApplication()->getTemplate() . '/html/mod_incremental_registration';
	}

	/**
	 * Determines if the file ($path) passed to it exists
	 * 
	 * @param      string $path File path
	 * @return     mixed
	 */
	public function get($path) 
	{
		return file_exists($this->tpl_path . $path) ? $this->tpl_path . $path : $this->mod_path . $path;
	}
}

/**
 * Incremental Registration Module controller class
 */
class ModIncrementalRegistrationController
{
	/**
	 * Constructor
	 * 
	 * @return     unknown
	 */
	public function __construct() 
	{
		$user = JFactory::getUser();
		$dbg = isset($_GET['dbg']);
		if ($user->get('guest')) 
		{
			return;
		}
		$uid = (int) $user->get('id');

		$dbh = JFactory::getDBO();

		require_once JPATH_BASE . '/administrator/components/com_register/tables/incremental.php';
			
		$opts = new ModIncrementalRegistrationOptions;
		if (!$opts->isEnabled($uid)) 
		{
			return;
		}

		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['REDIRECT_REQUEST_URI'];
		if (preg_match('%^/(?:session|legal/privacy)%', $uri)) 
		{
			return;
		}

		if (isset($_POST['incremental-registration']) && isset($_POST['submit']) && $_POST['submit'] === 'opt-out') 
		{
			$awards = new ModIncrementalRegistrationAwards($uid);
			$awards->optOut();
			return;
		}

		$media = new ModIncrementalRegistrationMediaPath;
		$groups = new ModIncrementalRegistrationGroups;
		if (($row = $groups->getActiveColumns($uid)) || $opts->isCurlEnabled()) 
		{
			if (!isset($_SESSION['return']) && !preg_match('/[.]/', $uri)) 
			{
				$_SESSION['return'] = $uri;
			}
			$doc = JFactory::getDocument();
			$doc->addStylesheet($media->get('/mod_incremental_registration.css'));
			$jquery = '';
			if (JPluginHelper::isEnabled('system', 'jquery'))
			{
				$jquery = '.jquery';
			}
			$doc->addScript($media->get('/mod_incremental_registration' . $jquery . '.js'));

			if ($row) 
			{
				if ($_SERVER['REQUEST_METHOD'] == 'GET') 
				{
					require JPATH_BASE . $media->get('/views/popover.php');
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
					if (isset($row['mailPreferenceOption']) && $mailPreferenceOption == -1) {
						$errors['mailPreferenceOption'] = true;
					}

					if ($errors) 
					{
						require JPATH_BASE . $media->get('/views/popover.php');
					}
					else 
					{
						$dbh->setQuery('SELECT '.implode(', ', array_keys($row)).' FROM #__profile_completion_awards WHERE user_id = ' . $uid);
						$award = 0;
						$awarded = $dbh->loadAssoc();
						foreach ($awarded as $v) 
						{
							if (!$v) 
							{
								$award += 15;
							}
						}

						$dbh->setQuery('SELECT COALESCE((SELECT balance FROM #__users_transactions WHERE uid = ' . $uid . ' AND id = (SELECT MAX(id) FROM #__users_transactions WHERE uid = ' . $uid . ')), 0)');
						$new_amount = $dbh->loadResult() + $award;

						if ($award) 
						{
							ximport('Hubzero_Bank');
							$BTL = new Hubzero_Bank_Teller($dbh, $uid);
							$BTL->deposit($award, 'Profile completion award', 'registration', 0);
						}

						$xp_update = 'UPDATE #__xprofiles SET ';
						$aw_update = 'UPDATE #__profile_completion_awards SET edited_profile = 1, ';
						$first = true;
						foreach (array_keys($row) as $k) 
						{
							if ($k == 'race') 
							{
								if (isset($race)) 
								{
									$dbh->execute('DELETE FROM #__xprofiles_race WHERE uidNumber = '.$uid);
									foreach ($race as $r) 
									{
										$dbh->execute('INSERT INTO #__xprofiles_race(uidNumber, race) VALUES ('.$uid.', '.$dbh->quote($r).')');
									}
									if (isset($_POST['racenativetribe'])) 
									{
										$dbh->execute('UPDATE #__xprofiles SET nativeTribe = '.$dbh->quote($_POST['racenativetribe']).' WHERE uidNumber = '.$uid);
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
									$dbh->execute('INSERT INTO #__xprofiles_disability(uidNumber, disability) VALUES ('.$uid.', '.$dbh->quote($disability).')');
								}
								continue;
							}
							if ($k == 'name') 
							{
								$dbh->execute('UPDATE #__xprofiles SET givenName = '.$dbh->quote($_POST['name']['first']).', middleName = '.$dbh->quote($_POST['name']['middle']).', surname = '.$dbh->quote($_POST['name']['last']).' WHERE uidNumber = '.$uid);
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
							$dbh->execute($xp_update . ' WHERE uidNumber = ' . $uid);
							$dbh->execute($aw_update . ' WHERE user_id = ' . $uid);
						}

						require JPATH_BASE . $media->get('/views/thanks.php');
						return;
					}
				}
			}
			else if (!preg_match('%^/members/' . $uid . '/profile%', $uri)) 
			{
				require JPATH_BASE . $media->get('/views/curl.php');
			}
		}
	}
}

new ModIncrementalRegistrationController;

endif;
