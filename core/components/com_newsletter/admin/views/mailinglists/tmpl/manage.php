<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Newsletter\Helpers\Permissions::getActions('mailinglist');

Toolbar::title(Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILINGLISTS') . ': ' . $this->escape($this->list->name), 'list');
if ($canDo->get('core.edit')) {
    Toolbar::addNew('addemail', 'COM_NEWSLETTER_TOOLBAR_ADDEMAILS');
    Toolbar::deleteList(
        'COM_NEWSLETTER_MAILINGLIST_DELETE_EMAILS_CHECK',
        'deleteemail',
        'COM_NEWSLETTER_TOOLBAR_REMOVE',
    );
    Toolbar::spacer();
}
Toolbar::custom('export', 'export', '', 'COM_NEWSLETTER_TOOLBAR_EXPORT', false);
Toolbar::spacer();
Toolbar::cancel();

$this->js();

$view = $this;

// The attribute that marks the current value in the form fields below
$statusSelected = function ($value) use ($view) {
    if ($view->filters['status'] == $value) {
        return 'selected="selected"';
    }

    return '';
};
?>

<form action="<?= Route::url('index.php?option=' . $this->option) ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <label><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS'); ?>:</label>
        <select name="status">
            <option value="all" <?= $statusSelected('all') ?>><?= Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS_ALL') ?></option>
            <option value="active" <?= $statusSelected('active') ?>><?= Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS_ACTIVE') ?></option>
            <option value="removed" <?= $statusSelected('removed') ?>><?= Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS_REMOVED') ?></option>
            <option value="unsubscribed" <?= $statusSelected('unsubscribed') ?>><?= Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS_UNSUBSCRIBED') ?></option>
            <option value="inactive" <?= $statusSelected('inactive') ?>><?= Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS_INACTIVE') ?></option>
        </select>
        <input type="submit" value="<?php echo Lang::txt('Go'); ?>" id="btn-manage" />
    </fieldset>

    <table class="adminlist">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" name="checkall-toggle" id="checkall-toggle" value=""
                        class="checkbox-toggle toggle-all" />
                    <label for="checkall-toggle"
                        class="sr-only visually-hidden"><?= Lang::txt('JGLOBAL_CHECK_ALL') ?></label>
                </th>
                <th scope="col"><?= Html::grid('sort', 'COM_NEWSLETTER_MAILINGLIST_MANAGE_EMAIL', 'email', @$this->filters['sort_Dir'], @$this->filters['sort']) ?></th>
                <th scope="col"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS'); ?></th>
                <th scope="col"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_CONFIRMED'); ?></th>
                <th scope="col"><?= Html::grid('sort', 'COM_NEWSLETTER_MAILINGLIST_MANAGE_DATE_ADDED', 'date_added', @$this->filters['sort_Dir'], @$this->filters['sort']) ?></th>
                <th scope="col">
                    <?= Html::grid(
                        'sort',
                        'COM_NEWSLETTER_MAILINGLIST_MANAGE_DATE_CONFIRMED',
                        'date_confirmed',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="6"><?php
                // initiate paging
                echo $this->list_emails->pagination;
                $k = 0;

                ?></td>
            </tr>
        </tfoot>
        <tbody>
            <?php if (count($this->list_emails) > 0) { ?>
                <?php foreach ($this->list_emails as $le) { ?>
                    <tr>
                        <td width="30">
                            <input type="checkbox" name="email_id[]" id="cb<?= $k ?>" value="<?= $le->id ?>"
                                class="checkbox-toggle" />
                            <label for="cb<?= $k ?>" class="sr-only visually-hidden"><?= $le->id ?></label>
                        </td>
                        <td>
                            <a href="mailto:<?= $this->escape($le->email) ?>"><?= $this->escape($le->email) ?></a>
                            <?php
                            if ($le->unsubscribe->reason) {
                                echo '<p><strong>' . Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_UNSUBSCRIBE_REASON')
                                    . '</strong> ' . $this->escape($le->unsubscribe->reason) . '</p>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo ucfirst($le->status); ?>
                        </td>
                        <td>
                            <?php
                            if ($le->confirmed) {
                                echo Lang::txt('JYES');
                            } else {
                                $resendLink = Route::url(
                                    'index.php?option=' . $this->option . '&controller=' . $this->controller
                                        . '&task=sendconfirmation&id=' . $le->id . '&mid=' . $this->list->id,
                                );
                                echo Lang::txt('JNO') . '(<a href="' . $resendLink . '">'
                                    . Lang::txt('Send Confirmation') . '</a>)';
                            }
                            ?>
                        </td>
                        <td>
                            <time datetime="<?= $le->date_added ?>"><?= Date::of($le->date_added)->format('l, F d, Y @ g:ia') ?></time>
                        </td>
                        <td>
                            <?php
                            if ($le->date_confirmed && $le->date_confirmed != '0000-00-00 00:00:00') {
                                echo '<time datetime="' . $le->date_confirmed . '">'
                                    . Date::of($le->date_confirmed)->format('l, F d, Y @ g:ia') . '</time>';
                            } else {
                                echo Lang::txt('NA');
                            }
                            ?>
                        </td>
                    </tr>
                    <?php $k++;
                } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="6">
                        <?= Lang::txt('COM_NEWSLETTER_MAILINGLIST_NO_EMAILS', "javascript:submitbutton('addemail');") ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="manage" />
    <input type="hidden" name="id[]" value="<?php echo $this->list->id; ?>" />
    <input type="hidden" name="mid" value="<?php echo $this->list->id; ?>" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

    <?php echo Html::input('token'); ?>
</form>
