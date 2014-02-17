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

/**
 * Controller class for incremental registration
 */
class RegisterControllerIncremental extends \Hubzero\Component\AdminController
{
	public function displayTask() {
		$this->view->display();
	}

	public function saveTask() {
		$dbh = JFactory::getDBO();
		$dbh->setQuery('DELETE FROM #__incremental_registration_groups');
		$dbh->execute();
		$dbh->setQuery('DELETE FROM #__incremental_registration_group_label_rel');
		$dbh->execute();

		for ($idx = 0; isset($_POST['group-hours-'.$idx]); ++$idx) {
			if (!($hours = (int)$_POST['group-hours-'.$idx])) {
				continue;
			}
			if ($_POST['group-time-unit-'.$idx] == 'week') {
				$hours *= 24 * 7;
			}
			elseif ($_POST['group-time-unit-'.$idx] == 'day') {
				$hours *= 24;
			}
			$dbh->setQuery('INSERT INTO #__incremental_registration_groups(hours) VALUES ('.$hours.')');
			$dbh->execute('INSERT INTO #__incremental_registration_groups(hours) VALUES ('.$hours.')');
			$gid = $dbh->insertid();
			foreach ($_POST['group-cols-'.$idx] as $colKey) {
				if (($colKey = trim($colKey))) {
					$dbh->setQuery('INSERT INTO #__incremental_registration_group_label_rel(group_id, label_id) VALUES ('.$gid.', (SELECT id FROM #__incremental_registration_labels WHERE field = '.$dbh->quote($colKey).'))');	
					$dbh->execute();
				}
			}
		}
		if (isset($_POST['popover'])) {
			$popoverText = stripslashes($_POST['popover']);
			$awardPer = (int)$_POST['award-per'];
			$testGroup = (int)$_POST['test-group'];
			$dbh->setQuery('SELECT popover_text, award_per, test_group FROM #__incremental_registration_options ORDER BY added DESC LIMIT 1');
			list($exPopover, $exAward, $exGroup) = $row = $dbh->loadRow();

			if ($popoverText != $exPopover || $awardPer != $exAward || $testGroup != $exGroup) {
				$dbh->setQuery('INSERT INTO #__incremental_registration_options(popover_text, award_per, test_group) VALUES ('.$dbh->quote($popoverText).', '.$awardPer.', '.$testGroup.')');
				$dbh->execute();
			}
		}
		$dbh->setQuery('DELETE FROM #__incremental_registration_popover_recurrence');
		$dbh->execute();
		for ($idx = 0; isset($_POST['recur-'.$idx]); ++$idx) {
			$hours = (int)$_POST['recur-'.$idx];
			if ($_POST['recur-type-'.$idx] == 'week') {
				$hours *= 24 * 7;
			}
			elseif ($_POST['recur-type-'.$idx] == 'day') {
				$hours *= 24;
			}
			if ($hours) {
				$dbh->setQuery('INSERT INTO #__incremental_registration_popover_recurrence(idx, hours) VALUES ('.$idx.', '.$hours.')');
				$dbh->execute();
			}
		}
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Saved')
		);
	}
}
