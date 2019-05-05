<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_INSTALLER_TITLE_PACKAGES'));

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_installer');
	Toolbar::divider();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
	Toolbar::divider();
}

Toolbar::addNew();

Html::behavior('tooltip');

$this->css();
$filterstring = "";
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
				<th scope="col">Extension</th>
				<th scope="col priority-3">Installed Version</th>
				<th scope="col priority-4">Description</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
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
			<?php foreach ($this->packages as $i => $package) : ?>
				<?php
				?>
				<tr>
					<td>
						<input type="checkbox" name="packages[]" id="cb<?php echo $i; ?>" value="<?php echo $package->getPrettyName(); ?>" class="checkbox-toggle" />
					</td>
					<td> 
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&packageName=' . $package->getName() . $filterstring); ?>">
							<strong><?php echo $package->getPrettyName() ?></strong>
						</a>
					</td>
					<td> <?php echo $package->getFullPrettyVersion(); ?> </td>
					<td> <?php echo $package->getDescription(); ?> </td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
