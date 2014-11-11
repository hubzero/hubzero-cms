<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_ADMINISTRATOR . '/tables/incremental/awards.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/tables/incremental/groups.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/tables/incremental/options.php';

/**
 * Controller class for incremental registration
 */
class MembersControllerIncremental extends \Hubzero\Component\AdminController
{
	/**
	 * Display settings
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->view->display();
	}

	/**
	 * Save settings
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		$this->database->setQuery('DELETE FROM `#__incremental_registration_groups`');
		$this->database->execute();

		$this->database->setQuery('DELETE FROM `#__incremental_registration_group_label_rel`');
		$this->database->execute();

		for ($idx = 0; isset($_POST['group-hours-' . $idx]); ++$idx)
		{
			if (!($hours = (int)$_POST['group-hours-' . $idx]))
			{
				continue;
			}

			if ($_POST['group-time-unit-' . $idx] == 'week')
			{
				$hours *= 24 * 7;
			}
			elseif ($_POST['group-time-unit-' . $idx] == 'day')
			{
				$hours *= 24;
			}

			$this->database->setQuery('INSERT INTO `#__incremental_registration_groups` (hours) VALUES (' . $hours . ')');
			$this->database->execute('INSERT INTO `#__incremental_registration_groups` (hours) VALUES (' . $hours . ')');
			$gid = $this->database->insertid();

			foreach ($_POST['group-cols-' . $idx] as $colKey)
			{
				if ($colKey = trim($colKey))
				{
					$this->database->setQuery('INSERT INTO `#__incremental_registration_group_label_rel` (group_id, label_id) VALUES (' . $gid . ', (SELECT id FROM `#__incremental_registration_labels` WHERE field = ' . $this->database->quote($colKey) . '))');
					$this->database->execute();
				}
			}
		}

		if (isset($_POST['popover']))
		{
			$popoverText = stripslashes($_POST['popover']);
			$awardPer    = (int)$_POST['award-per'];
			$testGroup   = (int)$_POST['test-group'];

			$this->database->setQuery('SELECT popover_text, award_per, test_group FROM `#__incremental_registration_options` ORDER BY added DESC LIMIT 1');
			list($exPopover, $exAward, $exGroup) = $row = $this->database->loadRow();

			if ($popoverText != $exPopover || $awardPer != $exAward || $testGroup != $exGroup)
			{
				$this->database->setQuery('INSERT INTO `#__incremental_registration_options` (popover_text, award_per, test_group) VALUES (' . $this->database->quote($popoverText) . ', ' . $awardPer . ', ' . $testGroup . ')');
				$this->database->execute();
			}
		}
		$this->database->setQuery('DELETE FROM `#__incremental_registration_popover_recurrence`');
		$this->database->execute();

		for ($idx = 0; isset($_POST['recur-' . $idx]); ++$idx)
		{
			$hours = (int)$_POST['recur-' . $idx];
			if ($_POST['recur-type-' . $idx] == 'week')
			{
				$hours *= 24 * 7;
			}
			elseif ($_POST['recur-type-' . $idx] == 'day')
			{
				$hours *= 24;
			}

			if ($hours)
			{
				$this->database->setQuery('INSERT INTO `#__incremental_registration_popover_recurrence` (idx, hours) VALUES (' . $idx . ', ' . $hours . ')');
				$this->database->execute();
			}
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Saved')
		);
	}
}
