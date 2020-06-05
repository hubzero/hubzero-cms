<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css();

// get whos online summary
$siteUserCount  = 0;
$adminUserCount = 0;
$found = array();
foreach ($this->rows as $i => $row):
	if ($row->userid && in_array($row->client_id . '.' . $row->userid, $found)):
		unset($this->rows[$i]);
		continue;
	endif;

	$found[] = $row->client_id . '.' . $row->userid;

	if ($row->client_id == 0):
		$siteUserCount++;
	else:
		$adminUserCount++;
	endif;
endforeach;

$editAuthorized = User::authorise('core.manage', 'com_members');
?>

<div class="<?php echo $this->module->module; ?>" id="<?php echo $this->module->module . $this->module->id; ?>">
	<table class="adminlist whosonline-summary">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('MOD_WHOSONLINE_COL_SITE'); ?></th>
				<th scope="col"><?php echo Lang::txt('MOD_WHOSONLINE_COL_ADMIN'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="front-end"><?php echo $siteUserCount; ?></td>
				<td class="back-end"><?php echo $adminUserCount; ?></td>
			</tr>
		</tbody>
	</table>

	<table class="adminlist whosonline-list">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('MOD_WHOSONLINE_COL_USER'); ?></td>
				<th scope="col"><?php echo Lang::txt('MOD_WHOSONLINE_COL_LOCATION'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('MOD_WHOSONLINE_COL_ACTIVITY'); ?></th>
				<?php if ($editAuthorized): ?>
					<th scope="col"><?php echo Lang::txt('MOD_WHOSONLINE_COL_LOGOUT'); ?></th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->rows) > 0) : ?>
				<?php foreach ($this->rows as $k => $row) : ?>
					<?php if (($k+1) <= $this->params->get('display_limit', 25)) : ?>
						<tr>
							<td>
								<?php
									// Get user object
									$user = User::getInstance($row->username);

									// Display link if we are authorized
									if ($editAuthorized):
										echo '<a href="' . Route::url('index.php?option=com_members&task=edit&id='. $row->userid) . '" title="' . Lang::txt('MOD_WHOSONLINE_EDIT_USER') . '">' . $this->escape($user->get('name')) . ' [' . $this->escape($user->get('username')) . ']' . '</a>';
									else:
										echo $this->escape($user->get('name')) . ' [' . $this->escape($user->get('username')) . ']';
									endif;
								?>
							</td>
							<td>
								<?php
									$clientInfo = \Hubzero\Base\ClientManager::client($row->client_id);
									echo '<span class="client client-' . $clientInfo->name . '" data-client="' . substr($clientInfo->name, 0, 1) . '">' . ucfirst($clientInfo->name) . '</span>';
								?>
							</td>
							<td class="priority-3">
								<?php echo Lang::txt('MOD_WHOSONLINE_HOURS_AGO', (time() - $row->time)/3600.0); ?>
							</td>
							<?php if ($editAuthorized): ?>
								<td>
									<a class="force-logout" href="<?php echo Route::url('index.php?option=com_login&task=logout&uid=' . $row->userid .'&'. Session::getFormToken() .'=1'); ?>">
										<span><?php echo Lang::txt('JLOGOUT'); ?></span>
									</a>
								</td>
							<?php endif; ?>
						</tr>
					<?php endif; ?>
				<?php endforeach; ?>
				<tr>
					<td colspan="<?php echo ($editAuthorized) ? 4 : 3; ?>" class="view-all">
						<a href="<?php echo Route::url('index.php?option=com_members&controller=whosonline'); ?>"><?php echo Lang::txt('MOD_WHOSONLINE_VIEW_ALL'); ?></a>
					</td>
				</tr>
			<?php else : ?>
				<tr>
					<td colspan="<?php echo ($editAuthorized) ? 4 : 3; ?>">
						<?php echo Lang::txt('MOD_WHOSONLINE_NO_RESULTS'); ?>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>
