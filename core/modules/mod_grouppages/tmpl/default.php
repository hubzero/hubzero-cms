<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css();

$rows = array();
foreach ($this->unapprovedPages as $unapprovedPage)
{
	$gidNumber = $unapprovedPage->get('gidNumber');
	if (!isset($rows[$gidNumber]['pages']))
	{
		$rows[$gidNumber]['pages'] = 1;
		continue;
	}
	$rows[$gidNumber]['pages']++;
}
foreach ($this->unapprovedModules as $unapprovedModule)
{
	$gidNumber = $unapprovedModule->get('gidNumber');
	if (!isset($rows[$gidNumber]['modules']))
	{
		$rows[$gidNumber]['modules'] = 1;
		continue;
	}
	$rows[$gidNumber]['modules']++;
}
?>
<div class="<?php echo $this->module->module; ?>">
	<table class="adminlist grouppages-list">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('MOD_GROUPPAGES_COL_GROUP'); ?></th>
				<th scope="col"><?php echo Lang::txt('MOD_GROUPPAGES_COL_PAGES'); ?></th>
				<th scope="col"><?php echo Lang::txt('MOD_GROUPPAGES_COL_MODULES'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($rows) > 0) : ?>
				<?php foreach ($rows as $gidNumber => $row) : ?>
					<tr>
						<td>
							<?php
								$group = \Hubzero\User\Group::getInstance($gidNumber);
								echo $group->get('description');
							?>
						</td>
						<td>
							<a class="page" href="<?php echo Route::url('index.php?option=com_groups&gid=' . $group->get('cn') . '&controller=pages'); ?>">
								<?php echo (isset($row['pages'])) ? $row['pages'] : 0; ?>
							</a>
						</td>
						<td>
							<a class="module" href="<?php echo Route::url('index.php?option=com_groups&gid=' . $group->get('cn') . '&controller=modules'); ?>">
								<?php echo (isset($row['modules'])) ? $row['modules'] : 0; ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="3">
						<em><?php echo Lang::txt('MOD_GROUPPAGES_NO_RESULTS'); ?></em>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>