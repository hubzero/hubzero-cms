<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = Components\Installer\Admin\Helpers\Installer::getActions();

Toolbar::title(Lang::txt('COM_INSTALLER_TITLE_MIGRATIONS'));

if ($canDo->get('core.edit.state'))
{
	Toolbar::custom('runup', 'up', '', 'COM_INSTALLER_TOOLBAR_MIGRATE_UP');
	Toolbar::custom('rundown', 'down', '', 'COM_INSTALLER_TOOLBAR_MIGRATE_DOWN');
	Toolbar::spacer();
	Toolbar::custom('migrate', 'purge', '', 'COM_INSTALLER_TOOLBAR_MIGRATE_PENDING', false);
}
Html::behavior('tooltip');

$this->css();

?>
<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li>
			<a<?php if ($this->controller == 'manage') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=manage'); ?>"><?php echo Lang::txt('COM_INSTALLER_SUBMENU_CORE'); ?></a>
		</li>
		<li>
			<a<?php if ($this->controller == 'migrations') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=migrations'); ?>"><?php echo Lang::txt('COM_INSTALLER_SUBMENU_MIGRATIONS'); ?></a>
		</li>
	</ul>
</nav>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="updateRepositoryForm">
	<?php if (!empty($this->breadcrumb)): ?>
		<fieldset id="filter-bar">
			<a href="<?php echo Route::url('index.php?option='.$this->option.'&controller='.$this->controller.'&folder='); ?>" class="breadcrumb"><?php echo Lang::txt('JGLOBAL_FILTER_TYPE_LABEL'); ?>: <?php echo $this->breadcrumb; ?></a>
		</fieldset>
	<?php endif; ?>
	<table id="tktlist" class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<input type="checkbox" name="toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
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
				$date      = Date::of(strtotime(substr($item, 0, 14).'UTC'))->format('Y-m-d g:i:sa');
				$component = substr($item, 14, -4);

				if (is_file(PATH_ROOT . DS . $row['entry']))
				{
					if (!class_exists(substr($row['file'], 0, -4)))
					{
						require_once PATH_ROOT . DS . $row['entry'];
					}
					$class = new ReflectionClass(substr($row['file'], 0, -4));
					$desc  = trim(rtrim(ltrim($class->getDocComment(), "/**\n *"), '**/'));
				}
				else
				{
					$desc = '<span class="warning">' . Lang::txt('COM_INSTALLER_MSG_MIGRATIONS_FILE_NOT_FOUND') . '</span>';
				}

				$cls = ($row['core'] ? 'dir-core' : 'dir-app');
				?>
				<tr>
					<td>
						<input type="checkbox" name="migration[]" id="cb<?php echo $i; ?>" value="<?php echo $this->escape($row['file']); ?>" class="checkbox-toggle" />
					</td>
					<td>
						<?php echo $component; ?><br />
						<a href="<?php echo Route::url('index.php?option='.$this->option.'&controller='.$this->controller.'&folder='.urlencode(str_replace('/migrations', '', $row['scope']))); ?>" class="dir-locale <?php echo $cls; ?>"><?php echo str_replace('/migrations', '', $row['scope']); ?></a>
					</td>
					<td class="priority-3"><?php echo $date; ?></td>
					<td>
						<?php echo basename($row['entry']); ?>
					</td>
					<td class="status">
						<?php if ($row['status'] == 'pending') : ?>
							<a href="<?php echo Route::url('index.php?option='.$this->option.'&controller='.$this->controller.'&task=migrate&file='.$row['file']).'&'.Session::getFormToken().'=1'; ?>">
						<?php endif; ?>
							<span class="state <?php echo ($row['status'] == 'complete') ? 'published' : $row['status']; ?>">
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
