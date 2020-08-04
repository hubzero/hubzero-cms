<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

use Hubzero\Utility\Arr;

Toolbar::title(Lang::txt('COM_INSTALLER_TITLE_REPOSITORIES'));

$canDo = Components\Installer\Admin\Helpers\Installer::getActions();
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_installer');
	Toolbar::divider();
}
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
	Toolbar::spacer();
}

Toolbar::help('repositories');

Html::behavior('tooltip');

$this->css();
?>

<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li>
			<a<?php if ($this->controller == 'packages') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=packages'); ?>"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_PACKAGES'); ?></a>
		</li>
		<li>
			<a<?php if ($this->controller == 'repositories') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=repositories'); ?>"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORIES'); ?></a>
		</li>
	</ul>
</nav>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="updateRepositoryForm">
	<table id="tktlist" class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_INSTALLER_COL_REPO', 'repo', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_INSTALLER_COL_TYPE', 'type', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_INSTALLER_COL_DESCRIPTION', 'description', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_INSTALLER_COL_URL', 'url', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
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
				if ($alias == 'hz-installer' || $alias == 'packagist.org'):
					continue;
				endif;
				?>
				<tr>
					<td>
						<input type="checkbox" name="repositories[]" id="cb<?php echo $id; ?>" value="<?php echo $alias; ?>" class="checkbox-toggle" />
					</td>
					<td>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&alias=' . $alias); ?>">
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
