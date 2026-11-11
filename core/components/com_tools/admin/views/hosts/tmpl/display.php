<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{filters: mixed, hosttypes: mixed, rows: mixed, total: mixed} $this
 */

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_HOSTS'), 'tools');
Toolbar::spacer();
Toolbar::addNew();
Toolbar::deleteList();
Toolbar::spacer();
Toolbar::help('hosts');

$this->css();
?>

<form action="<?= Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller) ?>" method="post"
    name="adminForm" id="adminForm">
    <table class="adminlist">
        <thead>
            <tr>
                <th scope="col">
                    <input type="checkbox" name="checkall-toggle" id="checkall-toggle" value=""
                        class="checkbox-toggle toggle-all" />
                    <label for="checkall-toggle"
                        class="sr-only visually-hidden"><?= Lang::txt('JGLOBAL_CHECK_ALL') ?></label>
                </th>
                <th scope="col">
                    <?= Html::grid(
                        'sort',
                        'COM_TOOLS_COL_NAME',
                        'hostname',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col">
                    <?= Html::grid(
                        'sort',
                        'COM_TOOLS_COL_SERVICE_HOST',
                        'service_host',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col">
                    <?= Html::grid(
                        'sort',
                        'COM_TOOLS_COL_PROVISIONS',
                        'provisions',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col" class="priority-2">
                    <?= Html::grid(
                        'sort',
                        'COM_TOOLS_COL_STATUS',
                        'status',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col" class="priority-3">
                    <?= Html::grid(
                        'sort',
                        'COM_TOOLS_COL_USES',
                        'uses',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col" class="priority-4">
                    <?= Html::grid(
                        'sort',
                        'COM_TOOLS_COL_ZONE',
                        'zone_id',
                        @$this->filters['sort_Dir'],
                        @$this->filters['sort'],
                    ) ?>
                </th>
                <th scope="col" class="priority-3"><?php echo Lang::txt('COM_TOOLS_COL_BROKEN_CONTAINERS'); ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="7">
                    <?php
                    // Initiate paging
                    echo $this->pagination(
                        $this->total,
                        $this->filters['start'],
                        $this->filters['limit'],
                    );
                    ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
<?php
if ($this->rows) {
    $db = \Components\Tools\Helpers\Utils::getMWDBO();

    $i = 0;
    foreach ($this->rows as $row) {
        $list = [];
        for ($k = 0; $k < count($this->hosttypes); $k++) {
            $r = $this->hosttypes[$k];
            $list[$r->name] = (int) $r->value & (int) $row->provisions;
        }
        ?>
            <tr>
                <td>
                    <input type="checkbox" name="id[]" id="cb<?= $i ?>" value="<?= $row->hostname ?>"
                        class="checkbox-toggle" />
                    <label for="cb<?= $i ?>" class="sr-only visually-hidden"><?= $row->hostname ?></label>
                </td>
                <td>
                    <?php
                    $editUrl = Route::url(
                        'index.php?option=' . $this->option . '&controller=' . $this->controller
                            . '&task=edit&hostname=' . $row->hostname,
                    );
                    ?>
                    <a href="<?= $editUrl ?>">
                        <span><?php echo $this->escape($row->hostname); ?></span>
                    </a>
                </td>
                <td>
                    <?php echo $row->service_host; ?></label>
                </td>
                <td>
                <?php
                foreach ($list as $key => $value) {
                    if ($value != '0') {
                        echo '<strong>';
                    }
                    ?>
                    <?php
                    $toggleUrl = Route::url(
                        'index.php?option=' . $this->option . '&controller=' . $this->controller
                            . '&task=toggle&hostname=' . $row->hostname . '&item=' . $key . '&'
                            . Session::getFormToken() . '=1',
                    );
                    ?>
                    <a class="<?= ($value != '0') ? 'active' : 'inactive' ?>" href="<?= $toggleUrl ?>">
                        <span><?php echo $this->escape($key); ?></span>
                    </a>
                        <?php
                        if ($value != '0') {
                            echo '</strong>';
                        }
                        echo '<br />';
                }
                ?>
                </td>
                <td class="priority-2">
                    <?php
                    $statusUrl = Route::url(
                        'index.php?option=' . $this->option . '&controller=' . $this->controller
                            . '&task=status&hostname=' . $row->hostname,
                    );
                    ?>
                    <a class="state <?= ($row->status == 'up') ? 'publish' : 'unpublish' ?>" href="<?= $statusUrl ?>">
                        <span><?php echo $this->escape($row->status); ?></span>
                    </a>
                </td>
                <td class="priority-3">
                    <?php echo $this->escape($row->uses); ?>
                </td>
                <td class="priority-4">
                    <?php echo $this->escape(stripslashes($row->zone == null ? '' : $row->zone)); ?>
                </td>
                <td class="priority-3">
                    <?php
                        $db->setQuery(
                            "SELECT count(*) FROM `display` WHERE `status`='broken' AND `hostname`="
                                . $db->quote($row->hostname),
                        );
                        echo $db->loadResult();
                    ?>
                </td>
            </tr>
        <?php
        $i++;
    }
}
?>
        </tbody>
    </table>

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

    <?php echo Html::input('token'); ?>
</form>
