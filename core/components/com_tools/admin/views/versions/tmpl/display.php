<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_TOOLS'), 'tools');
Toolbar::preferences('com_tools', '550');
Toolbar::spacer();
Toolbar::help('versions');

$this->css('tools.css');
?>

<?php
$formAction = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller);
$pipelineUrl = Route::url('index.php?option=' . $this->option . '&controller=pipeline');
$sortId = Html::grid(
    'sort',
    'COM_TOOLS_COL_ID',
    'id',
    @$this->filters['sort_Dir'],
    @$this->filters['sort']
);
$sortInstance = Html::grid(
    'sort',
    'COM_TOOLS_COL_INSTANCE',
    'toolname',
    @$this->filters['sort_Dir'],
    @$this->filters['sort']
);
$sortVersion = Html::grid(
    'sort',
    'COM_TOOLS_COL_VERSION',
    'version',
    @$this->filters['sort_Dir'],
    @$this->filters['sort']
);
$sortRevision = Html::grid(
    'sort',
    'COM_TOOLS_COL_REVISION',
    'revision',
    @$this->filters['sort_Dir'],
    @$this->filters['sort']
);
$sortState = Html::grid(
    'sort',
    'COM_TOOLS_COL_STATE',
    'state',
    @$this->filters['sort_Dir'],
    @$this->filters['sort']
);
?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="adminForm">
    <table class="adminlist">
        <caption>
            <a href="<?php echo $pipelineUrl; ?>"><?php echo Lang::txt('COM_TOOLS_PIPELINE'); ?></a> >
            <?php echo $this->escape(stripslashes($this->tool->title)); ?>
            (<?php echo $this->escape($this->tool->toolname); ?>)
        </caption>
        <thead>
            <tr>
                <th scope="col"></th>
                <th scope="col" class="priority-5"><?php echo $sortId; ?></th>
                <th scope="col"><?php echo $sortInstance; ?></th>
                <th scope="col" class="priority-4"><?php echo $sortVersion; ?></th>
                <th scope="col" class="priority-3"><?php echo $sortRevision; ?></th>
                <th scope="col"><?php echo $sortState; ?></th>
                <?php if ($this->config->get('new_doi')) : ?>
                    <th scope="col"><?php echo Lang::txt('COM_TOOLS_COL_DOI'); ?></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="<?php echo ($this->config->get('new_doi')) ? '7' : '6'; ?>">
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
        $k = 0;
        for ($i = 0, $n = count($this->rows); $i < $n; $i++) {
            $row = &$this->rows[$i];

            switch ($row['state']) {
                case 0:
                    $state = 'unpublished';
                    break;
                case 1:
                    $state = 'registered';
                    break;
                case 2:
                    $state = 'created';
                    break;
                case 3:
                    $state = 'uploaded';
                    break;
                case 4:
                    $state = 'installed';
                    break;
                case 5:
                    $state = 'updated';
                    break;
                case 6:
                    $state = 'approved';
                    break;
                case 7:
                    $state = 'published';
                    break;
                case 8:
                    $state = 'retired';
                    break;
                case 9:
                    $state = 'abandoned';
                    break;
                default:
                    $state = 'unknown';
                    break;
            }
            $editUrl = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&task=edit&version=' . $row['id']
                . '&id=' . $this->tool->id
            );
            $stateLabel = $this->escape(
                Lang::txt(strtoupper($this->option) . '_' . strtoupper($state))
            );
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <input type="radio" name="id" id="cb<?php echo $i; ?>"
                        value="<?php echo $row['id']; ?>" class="checkbox-toggle" />
                </td>
                <td class="priority-5">
                    <?php echo $this->escape($row['id']); ?>
                </td>
                <td>
                    <a href="<?php echo $editUrl; ?>">
                        <?php echo $this->escape(stripslashes($row['instance'])); ?>
                    </a>
                </td>
                <td class="priority-4">
                    <?php echo $this->escape($row['version']); ?>
                </td>
                <td class="priority-3">
                    <?php echo $this->escape($row['revision']); ?>
                </td>
                <td>
                    <span class="state <?php echo $state; ?>" title="<?php echo $stateLabel; ?>">
                        <span><?php echo $stateLabel; ?></span>
                    </span>
                </td>
                <?php if ($this->config->get('new_doi')) : ?>
                    <td><?php echo $row['doi']; ?></td>
                <?php endif; ?>
            </tr>
            <?php
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
