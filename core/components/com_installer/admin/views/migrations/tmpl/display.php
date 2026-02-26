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
use Hubzero\Facades\Session;
use Hubzero\Facades\Toolbar;

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();

Toolbar::title(Lang::txt('COM_INSTALLER_TITLE_MIGRATIONS'));

if ($canDo->get('core.edit.state')) {
    Toolbar::custom('runup', 'up', '', 'COM_INSTALLER_TOOLBAR_MIGRATE_UP');
    Toolbar::custom('rundown', 'down', '', 'COM_INSTALLER_TOOLBAR_MIGRATE_DOWN');
    Toolbar::spacer();
    Toolbar::custom('migrate', 'purge', '', 'COM_INSTALLER_TOOLBAR_MIGRATE_PENDING', false);
}
Html::behavior('tooltip');

$this->css();

?>
<?php
$manageCls = ($this->controller == 'manage') ? ' class="active"' : '';
$manageUrl = Route::url('index.php?option=' . $this->option . '&controller=manage');
$coreTxt = Lang::txt('COM_INSTALLER_SUBMENU_CORE');
$migCls = ($this->controller == 'migrations') ? ' class="active"' : '';
$migUrl = Route::url('index.php?option=' . $this->option . '&controller=migrations');
$migTxt = Lang::txt('COM_INSTALLER_SUBMENU_MIGRATIONS');
?>
<nav role="navigation" class="sub sub-navigation">
    <ul>
        <li>
            <a<?php echo $manageCls; ?> href="<?php echo $manageUrl; ?>">
                <?php echo $coreTxt; ?>
            </a>
        </li>
        <li>
            <a<?php echo $migCls; ?> href="<?php echo $migUrl; ?>">
                <?php echo $migTxt; ?>
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
    <?php if (!empty($this->breadcrumb)) : ?>
        <fieldset id="filter-bar">
            <?php
            $breadcrumbUrl = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller . '&folder='
            );
            $filterLabel = Lang::txt('JGLOBAL_FILTER_TYPE_LABEL');
            ?>
            <a href="<?php echo $breadcrumbUrl; ?>" class="breadcrumb">
                <?php echo $filterLabel; ?>: <?php echo $this->breadcrumb; ?>
            </a>
        </fieldset>
    <?php endif; ?>
    <table id="tktlist" class="adminlist">
        <thead>
            <tr>
                <th scope="col">
                    <input type="checkbox" name="toggle" id="checkall-toggle"
                        value="" class="checkbox-toggle toggle-all" />
                    <?php $checkAllTxt = Lang::txt('JGLOBAL_CHECK_ALL'); ?>
                    <label for="checkall-toggle" class="sr-only visually-hidden"><?php echo $checkAllTxt; ?></label>
                </th>
                <th scope="col"><?php echo Lang::txt('COM_INSTALLER_HEADING_EXTENSION'); ?></th>
                <th scope="col" class="priority-3"><?php echo Lang::txt('JDATE'); ?></th>
                <th scope="col"><?php echo Lang::txt('COM_INSTALLER_HEADING_FILENAME'); ?></th>
                <th scope="col"><?php echo Lang::txt('COM_INSTALLER_HEADING_STATUS'); ?></th>
                <th scope="col" class="priority-4"><?php echo Lang::txt('COM_INSTALLER_HEADING_DESCRIPTION'); ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="6">
                    <?php
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
            <?php foreach ($this->rows as $i => $row) : ?>
                <?php
                $parts = explode('/', $row['entry']);

                $row['file']  = array_pop($parts);
                $row['scope'] = implode('/', $parts);
                $row['core']  = ($parts[0] == 'core');

                $item      = ltrim($row['file'], 'Migration');
                $date      = Date::of(strtotime(substr($item, 0, 14) . 'UTC'))->format('Y-m-d g:i:sa');
                $component = substr($item, 14, -4);

                if (is_file(PATH_ROOT . DS . $row['entry'])) {
                    if (!class_exists(substr($row['file'], 0, -4))) {
                        require_once PATH_ROOT . DS . $row['entry'];
                    }
                    $class = new ReflectionClass(substr($row['file'], 0, -4));
                    $desc  = trim(rtrim(ltrim($class->getDocComment(), "/**\n *"), '**/'));
                } else {
                    $notFoundTxt = Lang::txt('COM_INSTALLER_MSG_MIGRATIONS_FILE_NOT_FOUND');
                    $desc = '<span class="warning">' . $notFoundTxt . '</span>';
                }

                $cls = ($row['core'] ? 'dir-core' : 'dir-app');
                ?>
                <tr>
                    <td>
                        <input type="checkbox" name="migration[]"
                            id="cb<?php echo $i; ?>"
                            value="<?php echo $this->escape($row['file']); ?>"
                            class="checkbox-toggle" />
                    </td>
                    <td>
                        <?php echo $component; ?><br />
                        <?php
                        $scopePath = str_replace('/migrations', '', $row['scope']);
                        $scopeUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=' . $this->controller
                            . '&folder=' . urlencode($scopePath)
                        );
                        ?>
                        <a href="<?php echo $scopeUrl; ?>"
                            class="dir-locale <?php echo $cls; ?>"><?php echo $scopePath; ?></a>
                    </td>
                    <td class="priority-3"><?php echo $date; ?></td>
                    <td>
                        <?php echo basename($row['entry']); ?>
                    </td>
                    <td class="status">
                        <?php if ($row['status'] == 'pending') : ?>
                            <?php
                            $migrateUrl = Route::url(
                                'index.php?option=' . $this->option
                                . '&controller=' . $this->controller
                                . '&task=migrate&file=' . $row['file']
                            ) . '&' . Session::getFormToken() . '=1';
                            ?>
                            <a href="<?php echo $migrateUrl; ?>">
                        <?php endif; ?>
                            <?php $stateClass = ($row['status'] == 'complete') ? 'published' : $row['status']; ?>
                            <span class="state <?php echo $stateClass; ?>">
                                <span class="text"><?php echo $row['status']; ?></span>
                            </span>
                        <?php if ($row['status'] == 'pending') : ?>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td class="priority-4"><?php echo $desc; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <input type="hidden" name="option" value="<?php echo $this->option ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
    <input type="hidden" name="task" value="" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="folder" value="<?php echo urlencode($this->filters['folder']); ?>" />

    <?php echo Html::input('token'); ?>
</form>
