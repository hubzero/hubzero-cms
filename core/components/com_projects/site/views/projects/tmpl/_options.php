<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$role  = Lang::txt('COM_PROJECTS_PROJECT') . ' <span>';
if ($this->model->access('manager')) {
    $role .= Lang::txt('COM_PROJECTS_LABEL_OWNER');
} elseif (!$this->model->access('content')) {
    $role .= Lang::txt('COM_PROJECTS_LABEL_REVIEWER');
} else {
    $role .= Lang::txt('COM_PROJECTS_LABEL_COLLABORATOR');
}
$role .= '</span>';

$counts = $this->model->get('counts');

$member = $this->model->member();
?>
<ul id="member_options">
    <li><?php echo ucfirst($role); ?>
        <div id="options-dock">
            <div><p><?php echo Lang::txt('COM_PROJECTS_JOINED') . ' ' . $this->model->created('date'); ?></p>
                <ul>
        <?php if ($this->model->access('manager')) { ?>
                    <?php
                    $routeUrl = Route::url(
                        'index
    . php?option='
                        . $this->option
                        . '&alias='
                        . $this->model->get('alias')
                        . '&task=edit'
                    );
                    $langTxt2 = Lang::txt('COM_PROJECTS_EDIT_PROJECT');
                    ?>
                    <li><a href="<?php echo $routeUrl; ?>"><?php echo $langTxt2; ?></a></li>
                    <?php
                    $routeUrl3 = Route::url(
                        'index
    . php?option='
                        . $this->option
                        . '&alias='
                        . $this->model->get('alias')
                        . '&task=edit&active=team'
                    );
                    $langTxt4 = Lang::txt('COM_PROJECTS_INVITE_PEOPLE');
                    ?>
                    <li><a href="<?php echo $routeUrl3; ?>"><?php echo $langTxt4; ?></a></li>
        <?php } ?>
        <?php if ($this->model->isPublic()) { ?>
                    <?php
                    $routeUrl5 = Route::url(
                        'index
    . php?option='
                        . $this->option
                        . '&alias='
                        . $this->model->get('alias')
                        . '&preview=1'
                    );
                    $langTxt6 = Lang::txt('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE');
                    ?>
                    <li><a href="<?php echo $routeUrl5; ?>"><?php echo $langTxt6; ?></a></li>
        <?php } ?>
        <?php if (isset($counts['team']) && $counts['team'] > 1 && $member && $member->get('status') == 1) { ?>
                    <?php
                    $routeUrl7 = Route::url(
                        'index
    . php?option='
                        . $this->option
                        . '&alias='
                        . $this->model->get('alias')
                        . '&active=team&action=quit'
                    );
                    $langTxt8 = Lang::txt('COM_PROJECTS_LEAVE_PROJECT');
                    ?>
                    <li><a href="<?php echo $routeUrl7; ?>"><?php echo $langTxt8; ?></a></li>
        <?php } ?>
                </ul>
            </div>
        </div>
    </li>
</ul>
