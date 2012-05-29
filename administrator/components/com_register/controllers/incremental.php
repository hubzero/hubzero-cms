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

ximport('Hubzero_Controller');

/**
 * 
 */
class RegisterControllerIncremental extends Hubzero_Controller
{
	public function displayTask() {
		$this->view->display();
	}

	public function saveTask() {
		$dbh =& JFactory::getDBO();
		if (isset($_POST['popover'])) {
			$popoverText = stripslashes($_POST['popover']);
			$awardPer = (int)$_POST['award-per'];
			$testGroup = (int)$_POST['test-group'];
			$dbh->setQuery('SELECT popover_text, award_per, test_group FROM #__incremental_registration_options ORDER BY added DESC LIMIT 1');
			list($exPopover, $exAward, $exGroup) = $dbh->loadRow();

			if ($popoverText != $exPopover || $awardPer != $exAward || $testGroup != $exGroup) {
				$dbh->execute('INSERT INTO #__incremental_registration_options(popover_text, award_per, test_group) VALUES ('.$dbh->quote($popoverText).', '.$awardPer.', '.$testGroup.')');
			}
		}
		$dbh->execute('DELETE FROM #__incremental_registration_popover_recurrence');
		for ($idx = 0; isset($_POST['recur-'.$idx]); ++$idx) {
			$hours = (int)$_POST['recur-'.$idx];
			if ($_POST['recur-type-'.$idx] == 'week') {
				$hours *= 24 * 7;
			}
			elseif ($_POST['recur-type-'.$idx] == 'day') {
				$hours *= 24;
			}
			if ($hours) {
				$dbh->execute('INSERT INTO #__incremental_registration_popover_recurrence(idx, hours) VALUES ('.$idx.', '.$hours.')');
			}
		}
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Saved')
		);
	}
}
