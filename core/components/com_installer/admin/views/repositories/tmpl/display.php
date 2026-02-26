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

// No direct access.
defined('_HZEXEC_') or die();

use Hubzero\Utility\Arr;

Toolbar::title(Lang::txt('COM_INSTALLER_TITLE_REPOSITORIES'));

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();
if ($canDo->get('core.admin')) {
    Toolbar::preferences('com_installer');
    Toolbar::divider();
}
if ($canDo->get('core.create')) {
    Toolbar::addNew();
}
if ($canDo->get('core.delete')) {
    Toolbar::deleteList();
    Toolbar::spacer();
}

Toolbar::help('repositories');

Html::behavior('tooltip');

$this->css();
?>

<?php
$pkgCls = ($this->controller == 'packages') ? ' class="active"' : '';
$pkgUrl = Route::url('index.php?option=' . $this->option . '&controller=packages');
$pkgTxt = Lang::txt('COM_INSTALLER_PACKAGES_PACKAGES');
$repoCls = ($this->controller == 'repositories') ? ' class="active"' : '';
$repoUrl = Route::url('index.php?option=' . $this->option . '&controller=repositories');
$repoTxt = Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORIES');
?>
<nav role="navigation" class="sub sub-navigation">
    <ul>
        <li>
            <a<?php echo $pkgCls; ?> href="<?php echo $pkgUrl; ?>">
                <?php echo $pkgTxt; ?>
            </a>
        </li>
        <li>
            <a<?php echo $repoCls; ?> href="<?php echo $repoUrl; ?>">
                <?php echo $repoTxt; ?>
            </a>
        </li>
    </ul>
</nav>

<?php
$formAction = Route::url(
    'index.php?option=' . $this->option . '&controller=' . $this->controller
);
?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="updateRepositoryForm">
    <table id="tktlist" class="adminlist">
        <thead>
            <tr>
                <th scope="col">
                    <input type="checkbox" name="toggle" id="checkall-toggle"
                        value="" class="checkbox-toggle toggle-all" />
                    <?php $checkAllTxt = Lang::txt('JGLOBAL_CHECK_ALL'); ?>
                    <label for="checkall-toggle" class="sr-only visually-hidden"><?php echo $checkAllTxt; ?></label>
                </th>
                <?php $sortDir = @$this->filters['sort_Dir']; ?>
                <?php $sort = @$this->filters['sort']; ?>
                <th scope="col">
                    <?php echo Html::grid('sort', 'COM_INSTALLER_COL_REPO', 'repo', $sortDir, $sort); ?>
                </th>
                <th scope="col">
                    <?php echo Html::grid('sort', 'COM_INSTALLER_COL_TYPE', 'type', $sortDir, $sort); ?>
                </th>
                <th scope="col">
                    <?php echo Html::grid('sort', 'COM_INSTALLER_COL_DESCRIPTION', 'description', $sortDir, $sort); ?>
                </th>
                <th scope="col">
                    <?php echo Html::grid('sort', 'COM_INSTALLER_COL_URL', 'url', $sortDir, $sort); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="4">
                    <?php
                    echo $this->pagination($this->total, $this->filters['start'], $this->filters['limit']);
                    ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
            <?php $id = 1; ?>
            <?php foreach ($this->repositories as $alias => $config) : ?>
                <?php
                if ($alias == 'hz-installer' || $alias == 'packagist.org') :
                    continue;
                endif;
                ?>
                <tr>
                    <td>
                        <input type="checkbox" name="repositories[]"
                            id="cb<?php echo $id; ?>"
                            value="<?php echo $alias; ?>"
                            class="checkbox-toggle" />
                    </td>
                    <td>
                        <?php
                        $editUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=' . $this->controller
                            . '&task=edit&alias=' . $alias
                        );
                        ?>
                        <a href="<?php echo $editUrl; ?>">
                            <?php echo Arr::getValue($config, 'name', ''); ?>
                        </a>
                        <br />
                        <strong><?php echo $alias; ?></strong>
                    </td>
                    <td>
                        <?php echo Arr::getValue($config, 'type', ''); ?>
                    </td>
                    <td>
                        <?php echo Arr::getValue($config, 'description', ''); ?>
                    </td>
                    <td>
                        <?php echo Arr::getValue($config, 'url', ''); ?>
                    </td>
                </tr>
                <?php $id++; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <input type="hidden" name="option" value="<?php echo $this->option ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
    <input type="hidden" name="task" value="" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

    <?php echo Html::input('token'); ?>
</form>
