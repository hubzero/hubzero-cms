<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin\Controllers;

use Hubzero\Component\AdminController;
use Route;
use Lang;
use App;

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

        for ($idx = 0; isset($_POST['group-hours-' . $idx]); ++$idx) {
            if (!($hours = (int)$_POST['group-hours-' . $idx])) {
                continue;
            }

            if ($_POST['group-time-unit-' . $idx] == 'week') {
                $hours *= 24 * 7;
            } elseif ($_POST['group-time-unit-' . $idx] == 'day') {
                $hours *= 24;
            }

            $sql = 'INSERT INTO `#__incremental_registration_groups` (hours) VALUES (' . $hours . ')';
            $this->database->setQuery($sql);
            $this->database->execute($sql);
            $gid = $this->database->insertid();

            foreach ($_POST['group-cols-' . $idx] as $colKey) {
                if ($colKey = trim($colKey)) {
                    $sql = 'INSERT INTO `#__incremental_registration_group_label_rel` '
                        . '(group_id, label_id) VALUES (' . $gid . ', '
                        . '(SELECT id FROM `#__incremental_registration_labels` WHERE field = '
                        . $this->database->quote($colKey) . '))';
                    $this->database->setQuery($sql);
                    $this->database->execute();
                }
            }
        }

        if (isset($_POST['popover'])) {
            $popoverText = stripslashes($_POST['popover']);
            $awardPer    = (int)$_POST['award-per'];
            $testGroup   = (int)$_POST['test-group'];

            $sql = 'SELECT popover_text, award_per, test_group '
                . 'FROM `#__incremental_registration_options` ORDER BY added DESC LIMIT 1';
            $this->database->setQuery($sql);
            list($exPopover, $exAward, $exGroup) = $row = $this->database->loadRow();

            if ($popoverText != $exPopover || $awardPer != $exAward || $testGroup != $exGroup) {
                $sql = 'INSERT INTO `#__incremental_registration_options` '
                    . '(popover_text, award_per, test_group) VALUES ('
                    . $this->database->quote($popoverText) . ', ' . $awardPer . ', ' . $testGroup . ')';
                $this->database->setQuery($sql);
                $this->database->execute();
            }
        }
        $this->database->setQuery('DELETE FROM `#__incremental_registration_popover_recurrence`');
        $this->database->execute();

        for ($idx = 0; isset($_POST['recur-' . $idx]); ++$idx) {
            $hours = (int)$_POST['recur-' . $idx];
            if ($_POST['recur-type-' . $idx] == 'week') {
                $hours *= 24 * 7;
            } elseif ($_POST['recur-type-' . $idx] == 'day') {
                $hours *= 24;
            }

            if ($hours) {
                $sql = 'INSERT INTO `#__incremental_registration_popover_recurrence` '
                    . '(idx, hours) VALUES (' . $idx . ', ' . $hours . ')';
                $this->database->setQuery($sql);
                $this->database->execute();
            }
        }

        App::redirect(
            Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
            Lang::txt('Saved')
        );
    }
}
