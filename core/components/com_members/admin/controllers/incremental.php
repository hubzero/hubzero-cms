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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin\Controllers;

use Hubzero\Component\AdminController;
use Route;
use Lang;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'incremental' . DS . 'awards.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'incremental' . DS . 'groups.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'incremental' . DS . 'options.php';

/**
 * Controller class for incremental registration
 */
class Incremental extends AdminController
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

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('Saved')
		);
	}
}
