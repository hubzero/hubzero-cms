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

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_HOSTS'), 'tools');
Toolbar::spacer();
Toolbar::addNew();
Toolbar::deleteList();
Toolbar::spacer();
Toolbar::help('hosts');

$this->css();
?>

<?php
$formAction = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller);
$sortName = Html::grid('sort', 'COM_TOOLS_COL_NAME', 'hostname', @$this->filters['sort_Dir'], @$this->filters['sort']);
$sortServiceHost = Html::grid(
    'sort',
    'COM_TOOLS_COL_SERVICE_HOST',
    'service_host',
    @$this->filters['sort_Dir'],
    @$this->filters['sort']
);
$sortProvisions = Html::grid(
    'sort',
    'COM_TOOLS_COL_PROVISIONS',
    'provisions',
    @$this->filters['sort_Dir'],
    @$this->filters['sort']
);
$sortStatus = Html::grid(
    'sort',
    'COM_TOOLS_COL_STATUS',
    'status',
    @$this->filters['sort_Dir'],
    @$this->filters['sort']
);
$sortUses = Html::grid(
    'sort',
    'COM_TOOLS_COL_USES',
    'uses',
    @$this->filters['sort_Dir'],
    @$this->filters['sort']
);
$sortZone = Html::grid(
    'sort',
    'COM_TOOLS_COL_ZONE',
    'zone_id',
    @$this->filters['sort_Dir'],
    @$this->filters['sort']
);
?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="adminForm">
    <table class="adminlist">
        <thead>
            <tr>
                <th scope="col">
                    <input type="checkbox" name="checkall-toggle" id="checkall-toggle" value=""
                        class="checkbox-toggle toggle-all" />
                    <label for="checkall-toggle" class="sr-only visually-hidden">
                        <?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
                </th>
                <th scope="col"><?php echo $sortName; ?></th>
                <th scope="col"><?php echo $sortServiceHost; ?></th>
                <th scope="col"><?php echo $sortProvisions; ?></th>
                <th scope="col" class="priority-2"><?php echo $sortStatus; ?></th>
                <th scope="col" class="priority-3"><?php echo $sortUses; ?></th>
                <th scope="col" class="priority-4"><?php echo $sortZone; ?></th>
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
                        $this->filters['limit']
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
        $list = array();
        for ($k = 0; $k < count($this->hosttypes); $k++) {
            $r = $this->hosttypes[$k];
            $list[$r->name] = (int)$r->value & (int)$row->provisions;
        }
        $editUrl = Route::url(
            'index.php?option=' . $this->option
            . '&controller=' . $this->controller
            . '&task=edit&hostname=' . $row->hostname
        );
        $statusCls = ($row->status == 'up') ? 'publish' : 'unpublish';
        $statusUrl = Route::url(
            'index.php?option=' . $this->option
            . '&controller=' . $this->controller
            . '&task=status&hostname=' . $row->hostname
        );
        ?>
            <tr>
                <td>
                    <input type="checkbox" name="id[]" id="cb<?php echo $i;?>"
                        value="<?php echo $row->hostname; ?>" class="checkbox-toggle" />
                    <label for="cb<?php echo $i; ?>" class="sr-only visually-hidden">
                        <?php echo $row->hostname; ?></label>
                </td>
                <td>
                    <a href="<?php echo $editUrl; ?>">
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
                    $activeCls = ($value != '0') ? 'active' : 'inactive';
                    $toggleUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=' . $this->controller
                        . '&task=toggle&hostname=' . $row->hostname
                        . '&item=' . $key
                    );
                    ?>
                    <a class="<?php echo $activeCls; ?>" href="<?php echo $toggleUrl; ?>">
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
                    <a class="state <?php echo $statusCls; ?>" href="<?php echo $statusUrl; ?>">
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
                            . $db->quote($row->hostname)
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
