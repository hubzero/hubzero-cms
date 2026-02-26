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

<?php $formAction = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <label for="filter-status"><?php echo Lang::txt('COM_SERVICES_FILTER_BY'); ?>:</label>
        <select name="filter_status" id="filter-status" class="filter filter-submit">
            <option value="pending"<?php if ($this->filters['status'] == 'pending') {
                echo ' selected="selected"';
                                   } ?>><?php echo Lang::txt('COM_SERVICES_FILTER_BY_PENDING'); ?></option>
            <option value="active"<?php if ($this->filters['status'] == 'processed') {
                echo ' selected="selected"';
                                  } ?>><?php echo Lang::txt('COM_SERVICES_FILTER_BY_ACTIVE'); ?></option>
            <option value="cancelled"<?php if ($this->filters['status'] == 'cancelled') {
                echo ' selected="selected"';
                                     } ?>><?php echo Lang::txt('COM_SERVICES_FILTER_BY_CANCELLED'); ?></option>
            <option value="all"<?php if ($this->filters['status'] == 'all') {
                echo ' selected="selected"';
                               } ?>><?php echo Lang::txt('COM_SERVICES_FILTER_BY_ALL'); ?></option>
        </select>
    </fieldset>

    <table class="adminlist">
        <thead>
            <tr>
                <?php $sortDir = @$this->filters['sort_Dir']; ?>
                <?php $sort = @$this->filters['sort']; ?>
                <th scope="col">
                    <?php echo Html::grid('sort', 'COM_SERVICES_COL_ID_CODE', 'id', $sortDir, $sort); ?>
                </th>
                <th scope="col">
                    <?php echo Html::grid('sort', 'COM_SERVICES_COL_STATUS', 'status', $sortDir, $sort); ?>
                </th>
                <th scope="col"><?php echo Lang::txt('COM_SERVICES_COL_SERVICE'); ?></th>
                <th scope="col"><?php echo Lang::txt('COM_SERVICES_COL_PENDING'); ?></th>
                <th scope="col">
                    <?php echo Html::grid('sort', 'COM_SERVICES_COL_USER', 'uid', $sortDir, $sort); ?>
                </th>
                <th scope="col">
                    <?php echo Html::grid('sort', 'COM_SERVICES_COL_ADDED', 'added', $sortDir, $sort); ?>
                </th>
                <th scope="col">
                    <?php echo Html::grid('sort', 'COM_SERVICES_COL_LAST_UPDATED', 'updated', $sortDir, $sort); ?>
                </th>
                <th scope="col">
                    <?php echo Html::grid('sort', 'COM_SERVICES_COL_EXPIRES', 'expires', $sortDir, $sort); ?>
                </th>
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
            $name  = Lang::txt('COM_SERVICES_UNKNOWN');
            $login = Lang::txt('COM_SERVICES_UNKNOWN');
            $ruser = User::getInstance($row->uid);
            if ($ruser->get('id')) {
                $name  = $ruser->get('name');
                $login = $ruser->get('username');
            }

            $status = '';
            $pendingAmount = $row->currency . ' ' . $row->pendingpayment;
            $pending = Lang::txt('COM_SERVICES_FOR_UNITS', $pendingAmount, $row->pendingunits);

            $dateFmt = Lang::txt('DATE_FORMAT_HZ1');
            $na = Lang::txt('COM_SERVICES_NOT_APPLICABLE');
            $expires = (intval($row->expires) <> 0)
                ? Date::of($row->expires)->toLocal($dateFmt) : $na;

            switch ($row->status) {
                case '1':
                    $activeLabel = strtolower(Lang::txt('COM_SERVICES_STATE_ACTIVE'));
                    $expiredLabel = strtolower(Lang::txt('COM_SERVICES_EXPIRED'));
                    $status = ($row->expires > $now)
                        ? '<span class="service-active">' . $activeLabel . '</span>'
                        : '<span  class="service-expired">' . $expiredLabel . '</span>';
                    break;
                case '0':
                    $pendingLabel = strtolower(Lang::txt('COM_SERVICES_STATE_PENDING'));
                    $status = '<span class="service-pending">' . $pendingLabel . '</span>';
                    break;
                case '2':
                    $cancelLabel = strtolower(Lang::txt('COM_SERVICES_STATE_CANCELED'));
                    $status = '<span class="service-cancelled">' . $cancelLabel . '</span>';
                    $pending .= $row->pendingpayment ? ' (' . Lang::txt('COM_SERVICES_REFUND') . ')' : '';
                    break;
            }
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <?php
                    $editUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=' . $this->controller
                        . '&task=edit&id=' . $row->id
                    );
                    ?>
                    <?php $detailsTitle = Lang::txt('COM_SERVICES_VIEW_SUBSCRIPTION_DETAILS'); ?>
                    <a href="<?php echo $editUrl; ?>" title="<?php echo $detailsTitle; ?>">
                        <?php echo $row->id . ' -- ' . $row->code; ?>
                    </a>
                </td>
                <td>
                    <?php echo $status; ?>
                </td>
                <td>
                    <a href="<?php echo $editUrl; ?>" title="<?php echo $detailsTitle; ?>">
                        <span><?php echo $this->escape($row->category) . ' -- ' . $this->escape($row->title); ?></span>
                    </a>
                </td>
                <td>
                    <?php
                    $hasPending = $row->pendingpayment && ($row->pendingpayment > 0 or $row->pendingunits > 0);
                    echo $hasPending ? '<span class="service-pending">' . $pending . '</span>' : $pending;
                    ?>
                </td>
                <td>
                    <?php echo $name . ' (' . $login . ')'; ?>
                </td>
                <td>
                    <?php echo Date::of($row->added)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?>
                </td>
                <td>
                    <?php
                    echo $row->updated
                        ? Date::of($row->updated)->toLocal($dateFmt)
                        : Lang::txt('COM_SERVICES_NEVER');
                    ?>
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
