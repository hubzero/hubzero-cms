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
 * @var \Hubzero\Component\View&object{group: mixed, modules: mixed, needsAttention: mixed} $this
 */

// define base link
$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->group->cn;

// create toolbar title
Toolbar::title($this->group->get('description') . ': ' . Lang::txt('COM_GROUPS_PAGES_MODULES'), 'groups.png');

//add buttons to toolbar
$canDo = \Components\Groups\Helpers\Permissions::getActions('group');
if ($canDo->get('core.create')) {
    Toolbar::addNew();
}
if ($canDo->get('core.edit')) {
    Toolbar::editList();
}
if ($canDo->get('core.delete')) {
    Toolbar::deleteList('COM_GROUPS_MODULES_CONFIRM_DELETE', 'delete');
}
Toolbar::spacer();
Toolbar::custom('manage', 'config', 'config', 'COM_GROUPS_MANAGE', false);

$this->css();

// include modal for raw version links
Html::behavior('modal', 'a.version, a.preview', ['handler' => 'iframe', 'fullScreen' => true]);
?>

<?php require_once dirname(dirname(__DIR__)) . DS . 'pages' . DS . 'tmpl' . DS . 'menu.php'; ?>

<?php if ($this->needsAttention->count() > 0) : ?>
    <table class="adminlist attention">
        <thead>
            <tr>
                <th scope="col">
                    (<?= $this->needsAttention->count() ?>) <?= Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION') ?>
                </th>
                <th scope="col"><?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_VIEW'); ?></th>
                <th scope="col"><?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_CHECKS'); ?></th>
                <th scope="col"><?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_APPROVE'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->needsAttention as $needsAttention) : ?>
                <tr>
                    <td>
                        <?php echo $this->escape($needsAttention->get('title')); ?>
                    </td>
                    <td>
                        <ol class="attention-view">
                            <li class="raw">
                                <a class="version"
                                    href="<?= Route::url($base . '&task=raw&moduleid=' . $needsAttention->get('id')) ?>"
                                    class="btn">
                                    <?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_VIEW_RAW'); ?>
                                </a>
                            </li>
                            <?php if ($needsAttention->get('checked_errors') && $needsAttention->get('scanned')) : ?>
                                <li class="preview">
                                    <?php
                                    $previewUrl = Route::url(
                                        $base . '&task=preview&moduleid=' . $needsAttention->get('id'),
                                    );
                                    ?>
                                    <a class="preview" href="<?= $previewUrl ?>" class="btn">
                                        <?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_RENDER_PREVIEW'); ?>
                                    </a>
                                </li>
                            <?php else : ?>
                                <li class="preview">
                                    <?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_RENDER_PREVIEW_HINT'); ?>
                                </li>
                            <?php endif; ?>
                            <li class="edit">
                                <a href="<?= Route::url($base . '&task=edit&id[]=' . $needsAttention->get('id')) ?>"
                                    class="btn">
                                    <?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_EDIT'); ?>
                                </a>
                            </li>
                        </ol>
                    </td>
                    <td>
                        <ol class="attention-actions">
                            <li class="<?php echo $needsAttention->get('checked_errors') ? 'completed' : ''; ?>">
                                <a href="<?= Route::url($base . '&task=errors&id=' . $needsAttention->get('id')) ?>"
                                    class="btn">
                                    <?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_CHECK_FOR_ERRORS'); ?>
                                </a>
                            </li>
                            <li class="<?php echo $needsAttention->get('scanned') ? 'completed' : ''; ?>">
                                <a href="<?= Route::url($base . '&task=scan&id=' . $needsAttention->get('id')) ?>"
                                    class="btn">
                                    <?php echo Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_SCAN_CONTENT'); ?>
                                </a>
                            </li>

                        </ol>
                    </td>
                    <td width="20%">
                        <ol class="attention-actions">
                            <?php if ($needsAttention->get('checked_errors') && $needsAttention->get('scanned')) : ?>
                                <li class="approve">
                                    <?php
                                    $approveUrl = Route::url(
                                        $base . '&task=approve&id=' . $needsAttention->get('id') . '&'
                                            . Session::getFormToken() . '=1',
                                    );
                                    ?>
                                    <a href="<?= $approveUrl ?>" class="btn">
                                        <?php
                                        $strongText = Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_APPROVE');
                                        ?>
                                        <strong><?= $strongText ?></strong>
                                    </a>
                                </li>
                            <?php else : ?>
                                <?php
                                $emphasisText = Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_APPROVE_HINT');
                                ?>
                                <span><em><?= $emphasisText ?></em></span>
                            <?php endif; ?>
                        </ol>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br />
<?php endif; ?>

<?php
$actionUrl = Route::url(
    'index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->group->cn,
);
?>
<form action="<?= $actionUrl ?>" name="adminForm" id="adminForm" method="post">
    <table class="adminlist">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" name="checkall-toggle" id="checkall-toggle" value=""
                        class="checkbox-toggle toggle-all" />
                    <label for="checkall-toggle"
                        class="sr-only visually-hidden"><?= Lang::txt('JGLOBAL_CHECK_ALL') ?></label>
                </th>
                <th scope="col"><?php echo Lang::txt('COM_GROUPS_MODULES_TITLE'); ?></th>
                <th scope="col"><?php echo Lang::txt('COM_GROUPS_MODULES_STATUS'); ?></th>
                <th scope="col"><?php echo Lang::txt('COM_GROUPS_MODULES_POSITION'); ?></th>
            </tr>
        </thead>
        <tbody>
<?php if ($this->modules->count() > 0) : ?>
    <?php foreach ($this->modules as $k => $module) : ?>
            <tr>
                <td>
                    <input type="checkbox" name="id[]" id="cb<?= $k ?>" value="<?= $module->get('id') ?>"
                        class="checkbox-toggle" />
                    <label for="cb<?= $k ?>" class="sr-only visually-hidden"><?= $module->get('id') ?></label>
                </td>
                <td>
                    <?php
                    $editUrl = Route::url(
                        'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&gid='
                            . $this->group->cn . '&id=' . $module->get('id'),
                    );
                    ?>
                    <a href="<?= $editUrl ?>">
                        <?php echo $this->escape($module->get('title')); ?>
                    </a><br />
                    <span class="hint">
                        <?php
                            $pages = [];
                            $menus = $module->menu('list');
                        foreach ($menus as $menu) {
                            $pages[] = $menu->getPageTitle();
                        }
                            echo Lang::txt('COM_GROUPS_MODULES_INCLUDED_ON', implode(', ', $pages));
                        ?>
                    </span>
                </td>
                <td>
                    <?php
                    switch ($module->get('state')) {
                        case 0:
                            echo Lang::txt('COM_GROUPS_MODULES_STATUS_UNPUBLISHED');
                            break;
                        case 1:
                            echo Lang::txt('COM_GROUPS_MODULES_STATUS_PUBLISHED');
                            break;
                        case 2:
                            echo Lang::txt('COM_GROUPS_MODULES_STATUS_DELETED');
                            break;
                    }
                    ?>
                </td>
                <td><?php echo $this->escape($module->get('position')); ?></td>
            </tr>
    <?php endforeach; ?>
<?php else : ?>
            <tr>
                <td colspan="4"><?php echo Lang::txt('COM_GROUPS_MODULES_NO_MODULES'); ?></td>
            </tr>
<?php endif; ?>
        </tbody>
    </table>

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
    <input type="hidden" name="task" value="" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo Html::input('token'); ?>
</form>