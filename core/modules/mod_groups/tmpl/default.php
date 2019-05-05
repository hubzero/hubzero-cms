<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css();
?>
<div class="<?php echo $this->module->module; ?>">
	<table class="stats-overview">
		<tbody>
			<tr>
				<td class="public">
					<a href="<?php echo Route::url('index.php?option=com_groups&controller=manage&type=' . $this->type . '&discoverability=0&policy='); ?>" title="<?php echo Lang::txt('MOD_GROUPS_VISIBLE_TITLE'); ?>">
						<?php echo $this->escape($this->visible); ?>
						<span><?php echo Lang::txt('MOD_GROUPS_VISIBLE'); ?></span>
					</a>
				</td>
				<td class="protected">
					<a href="<?php echo Route::url('index.php?option=com_groups&controller=manage&type=' . $this->type . '&discoverability=1&policy='); ?>" title="<?php echo Lang::txt('MOD_GROUPS_HIDDEN_TITLE'); ?>">
						<?php echo $this->escape($this->hidden); ?>
						<span><?php echo Lang::txt('MOD_GROUPS_HIDDEN'); ?></span>
					</a>
				</td>
			</tr>
		</tbody>
	</table>

	<table class="stats-overview">
		<tbody>
			<tr>
				<td class="closed">
					<a href="<?php echo Route::url('index.php?option=com_groups&controller=manage&type=' . $this->type . '&discoverability=&policy=closed&approved='); ?>" title="<?php echo Lang::txt('MOD_GROUPS_CLOSED_TITLE'); ?>">
						<?php echo $this->escape($this->closed); ?>
						<span><?php echo Lang::txt('MOD_GROUPS_CLOSED'); ?></span>
					</a>
				</td>
				<td class="invite">
					<a href="<?php echo Route::url('index.php?option=com_groups&controller=manage&type=' . $this->type . '&discoverability=&policy=invite&approved='); ?>" title="<?php echo Lang::txt('MOD_GROUPS_INVITE_TITLE'); ?>">
						<?php echo $this->escape($this->invite); ?>
						<span><?php echo Lang::txt('MOD_GROUPS_INVITE'); ?></span>
					</a>
				</td>
				<td class="restricted">
					<a href="<?php echo Route::url('index.php?option=com_groups&controller=manage&type=' . $this->type . '&discoverability=&policy=restricted&approved='); ?>" title="<?php echo Lang::txt('MOD_GROUPS_RESTRICTED_TITLE'); ?>">
						<?php echo $this->escape($this->restricted); ?>
						<span><?php echo Lang::txt('MOD_GROUPS_RESTRICTED'); ?></span>
					</a>
				</td>
				<td class="open">
					<a href="<?php echo Route::url('index.php?option=com_groups&controller=manage&type=' . $this->type . '&discoverability=&policy=open&approved='); ?>" title="<?php echo Lang::txt('MOD_GROUPS_OPEN_TITLE'); ?>">
						<?php echo $this->escape($this->open); ?>
						<span><?php echo Lang::txt('MOD_GROUPS_OPEN'); ?></span>
					</a>
				</td>
			</tr>
		</tbody>
	</table>

	<table class="stats-overview">
		<tbody>
			<tr>
				<td class="approved">
					<a href="<?php echo Route::url('index.php?option=com_groups&controller=manage&type=' . $this->type . '&approved=1&discoverability=&policy='); ?>" title="<?php echo Lang::txt('MOD_GROUPS_PUBLISHED_TITLE'); ?>">
						<?php echo $this->escape($this->approved); ?>
						<span><?php echo Lang::txt('MOD_GROUPS_PUBLISHED'); ?></span>
					</a>
				</td>
				<td class="pending">
					<a href="<?php echo Route::url('index.php?option=com_groups&controller=manage&type=' . $this->type . '&approved=0&discoverability=&policy='); ?>" title="<?php echo Lang::txt('MOD_GROUPS_PENDING_TITLE'); ?>">
						<?php echo $this->escape($this->pending); ?>
						<span><?php echo Lang::txt('MOD_GROUPS_PENDING'); ?></span>
					</a>
				</td>
				<td class="newest">
					<a href="<?php echo Route::url('index.php?option=com_groups&controller=manage&type=' . $this->type . '&created=pastday&discoverability=&policy=&approved='); ?>" title="<?php echo Lang::txt('MOD_GROUPS_NEW_TITLE'); ?>">
						<?php echo $this->escape($this->pastDay); ?>
						<span><?php echo Lang::txt('MOD_GROUPS_NEW'); ?></span>
					</a>
				</td>
			</tr>
		</tbody>
	</table>
</div>