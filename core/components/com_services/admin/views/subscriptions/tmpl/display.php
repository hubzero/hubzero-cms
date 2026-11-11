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
use Hubzero\Facades\User;

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Services\Helpers\Permissions::getActions('service');

Toolbar::title(Lang::txt('COM_SERVICES') . ': ' . Lang::txt('COM_SERVICES_SUBSCRIPTIONS'), 'services');
if ($canDo->get('core.admin')) {
    Toolbar::preferences('com_services', '550');
}

$now = Date::toSql();

// Push some styles to the template
$this->css();
$this->css('admin.subscriptions.css');
?>

<form action="<?= Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller) ?>" method="post"
    name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <label for="filter-status"><?php echo Lang::txt('COM_SERVICES_FILTER_BY'); ?>:</label>
        <select name="filter_status" id="filter-status" class="filter filter-submit">
            <option value="pending"<?= $this->filters['status'] == 'pending' ? ' selected="selected"' : '' ?>><?= Lang::txt('COM_SERVICES_FILTER_BY_PENDING') ?></option>
            <option value="active"<?= $this->filters['status'] == 'active' ? ' selected="selected"' : '' ?>><?= Lang::txt('COM_SERVICES_FILTER_BY_ACTIVE') ?></option>
            <option value="cancelled"<?= $this->filters['status'] == 'cancelled' ? ' selected="selected"' : '' ?>><?= Lang::txt('COM_SERVICES_FILTER_BY_CANCELLED') ?></option>
            <option value="all"<?= $this->filters['status'] == 'all' ? ' selected="selected"' : '' ?>><?= Lang::txt('COM_SERVICES_FILTER_BY_ALL') ?></option>
        </select>
    </fieldset>

    <table class="adminlist">
        <thead>
            <tr>
                <th scope="col"><?= Html::grid('sort', 'COM_SERVICES_COL_ID_CODE', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']) ?></th>
                <th scope="col"><?= Html::grid('sort', 'COM_SERVICES_COL_STATUS', 'status', @$this->filters['sort_Dir'], @$this->filters['sort']) ?></th>
                <th scope="col"><?php echo Lang::txt('COM_SERVICES_COL_SERVICE'); ?></th>
                <th scope="col"><?php echo Lang::txt('COM_SERVICES_COL_PENDING'); ?></th>
                <th scope="col"><?= Html::grid('sort', 'COM_SERVICES_COL_USER', 'uid', @$this->filters['sort_Dir'], @$this->filters['sort']) ?></th>
                <th scope="col"><?= Html::grid('sort', 'COM_SERVICES_COL_ADDED', 'added', @$this->filters['sort_Dir'], @$this->filters['sort']) ?></th>
                <th scope="col"><?= Html::grid('sort', 'COM_SERVICES_COL_LAST_UPDATED', 'updated', @$this->filters['sort_Dir'], @$this->filters['sort']) ?></th>
                <th scope="col"><?= Html::grid('sort', 'COM_SERVICES_COL_EXPIRES', 'expires', @$this->filters['sort_Dir'], @$this->filters['sort']) ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="8">
                    <?php
                    // Initiate paging
                    echo $this->rows->pagination;
                    ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
        <?php
        $i = 0;
        $k = 0;
        foreach ($this->rows as $row) {
            $service = $row->service;

            $name  = Lang::txt('COM_SERVICES_UNKNOWN');
            $login = Lang::txt('COM_SERVICES_UNKNOWN');
            $ruser = User::getInstance($row->uid);
            if ($ruser->get('id')) {
                $name  = $ruser->get('name');
                $login = $ruser->get('username');
            }

            $status = '';
            $pending = Lang::txt(
                'COM_SERVICES_FOR_UNITS',
                $service->get('currency') . ' ' . $row->pendingpayment,
                $row->pendingunits,
            );

            $expires = (intval($row->expires) <> 0)
                ? Date::of($row->expires)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
                : Lang::txt('COM_SERVICES_NOT_APPLICABLE');

            switch ($row->status) {
                case '1':
                    $status = ($row->expires > $now)
                        ? '<span class="service-active">' . strtolower(Lang::txt('COM_SERVICES_STATE_ACTIVE'))
                            . '</span>'
                        : '<span  class="service-expired">' . strtolower(Lang::txt('COM_SERVICES_EXPIRED')) . '</span>';
                    break;
                case '0':
                    $status = '<span class="service-pending">' . strtolower(Lang::txt('COM_SERVICES_STATE_PENDING'))
                        . '</span>';
                    break;
                case '2':
                    $status = '<span class="service-cancelled">' . strtolower(Lang::txt('COM_SERVICES_STATE_CANCELED'))
                        . '</span>';
                    $pending .= $row->pendingpayment ? ' (' . Lang::txt('COM_SERVICES_REFUND') . ')' : '';
                    break;
            }
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <a href="<?= Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id) ?>" title="<?= Lang::txt('COM_SERVICES_VIEW_SUBSCRIPTION_DETAILS') ?>">
                        <?php echo $row->id . ' -- ' . $row->code; ?>
                    </a>
                </td>
                <td>
                    <?php echo $status; ?>
                </td>
                <td>
                    <a href="<?= Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id) ?>" title="<?= Lang::txt('COM_SERVICES_VIEW_SUBSCRIPTION_DETAILS') ?>">
                        <span><?= $this->escape($service->get('category')) . ' -- ' . $this->escape($service->get('title')) ?></span>
                    </a>
                </td>
                <td>
                    <?= $row->pendingpayment && ($row->pendingpayment > 0 or $row->pendingunits > 0)
                        ? '<span class="service-pending">' . $pending . '</span>'
                        : $pending ?>
                </td>
                <td>
                    <?php echo $this->escape($name) . ' (' . $this->escape($login) . ')'; ?>
                </td>
                <td>
                    <?php echo Date::of($row->added)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?>
                </td>
                <td>
                    <?= $row->updated
                        ? Date::of($row->updated)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
                        : Lang::txt('COM_SERVICES_NEVER') ?>
                </td>
                <td>
                    <?php echo $expires; ?>
                </td>
            </tr>
            <?php
            $i++;
            $k = 1 - $k;
        }
        ?>
        </tbody>
    </table>

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />

    <?php echo Html::input('token'); ?>
</form>
