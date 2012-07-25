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
		if ($user->guest) 
		{
			return;
		}
		$uid = (int)$user->get('id');

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
			$doc->addStylesheet($media->get('/incremental_registration.css'));
			$doc->addScript($media->get('/incremental_registration.js'));

			if ($row) 
			{
				if ($_SERVER['REQUEST_METHOD'] == 'GET') 
				{
					require JPATH_BASE . $media->get('/views/popover.php');
				}
				elseif (isset($_POST['incremental-registration']) && $_POST['incremental-registration'] == 'update') 
				{
					$errors = array();
					$orgtype = null;
					$organization = null;
					$reason = null;
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

					if ($errors) 
					{
						require JPATH_BASE . $media->get('/views/popover.php');
					}
					else 
					{
						$base_award = (isset($row['orgtype']) ? 15 : 0) + (isset($row['organization']) ? 15 : 0) + (isset($row['reason']) ? 15 : 0);
						$dbh->setQuery('SELECT organization, orgtype, reason FROM #__profile_completion_awards WHERE user_id = ' . $uid);
						$awarded = $dbh->loadAssoc();
						$award = $base_award + (!isset($row['orgtype']) && !$awarded['orgtype'] ? 15 : 0) + (!isset($row['organization']) && !$awarded['organization'] ? 15 : 0) + (!isset($row['reason']) && !$awarded['reason'] ? 15 : 0);

						$dbh->setQuery('SELECT COALESCE((SELECT balance FROM #__users_transactions WHERE uid = ' . $uid . ' AND id = (SELECT MAX(id) FROM #__users_transactions WHERE uid = ' . $uid . ')), 0)');
						$new_amount = $dbh->loadResult() + $award;

						$dbh->execute('INSERT INTO #__users_transactions(uid, type, description, category, amount, balance) VALUES 
							(' . $uid . ', \'deposit\', \'Profile completion award\', \'registration\', ' . $award . ', ' . $new_amount . ')');

						$xp_update = 'UPDATE #__xprofiles SET ';
						$aw_update = 'UPDATE #__profile_completion_awards SET edited_profile = 1, ';
						$first = true;
						foreach (array('orgtype', 'organization', 'reason') as $k)
						{
							if (isset($row[$k]))
							{
								$xp_update .= ($first ? '' : ', ') . $k . ' = ' . $dbh->quote($$k);
								$aw_update .= ($first ? '' : ', ') . $k . ' = 1';
								$first = false;
							}
						}
						$dbh->execute($xp_update . ' WHERE uidNumber = ' . $uid);
						$dbh->execute($aw_update . ' WHERE user_id = ' . $uid);

						require JPATH_BASE.$media->get('/views/thanks.php');
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
