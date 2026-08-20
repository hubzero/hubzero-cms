<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$min = Date::of('now')->modify('+1 day')->format('Y-m-d');
$max = $this->maxDays > 0 ? Date::of('now')->modify('+' . $this->maxDays . ' days')->format('Y-m-d') : '';
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="explaination">
		<p><?php echo Lang::txt('COM_GROUPS_MEMBER_TERM_EXPLANATION'); ?></p>
	</div>

	<?php if ($this->getError()) { ?>
		<?php foreach ($this->getErrors() as $error) { ?>
			<p class="error"><?php echo $this->escape($error); ?></p>
		<?php } ?>
	<?php } ?>

	<fieldset class="adminform">
		<legend><?php echo Lang::txt('COM_GROUPS_MEMBER_TERM'); ?></legend>

		<?php if (!$this->users) { ?>
			<p class="warning"><?php echo Lang::txt('COM_GROUPS_MEMBER_TERM_NO_SELECTION'); ?></p>
		<?php } else { ?>
			<?php
			$names = array();
			foreach ($this->users as $uid)
			{
				$u     = \Hubzero\User\User::oneOrNew($uid);
				$label = $u->get('name') ? $u->get('name') : $uid;

				if (!empty($this->current[$uid]))
				{
					$label .= ' (' . Lang::txt('COM_GROUPS_MEMBER_TERM_ENDS',
						Date::of($this->current[$uid])->toLocal(Lang::txt('DATE_FORMAT_HZ1'))) . ')';
				}

				$names[] = $this->escape($label);
				?>
				<input type="hidden" name="id[]" value="<?php echo (int) $uid; ?>" />
				<?php
			}
			?>

			<div class="input-wrap">
				<label><?php echo Lang::txt('COM_GROUPS_MEMBER_TERM_MEMBERS'); ?></label>
				<strong><?php echo implode(', ', $names); ?></strong>
			</div>

			<div class="input-wrap">
				<label for="expires"><?php echo Lang::txt('COM_GROUPS_MEMBER_TERM_ENDS_ON'); ?></label>
				<input type="date" name="expires" id="expires" min="<?php echo $min; ?>"<?php echo $max ? ' max="' . $max . '"' : ''; ?> />
				<?php if ($this->maxDays > 0) { ?>
					<span class="hint"><?php echo Lang::txt('COM_GROUPS_MEMBERSHIP_TERM_HUB_MAX', $this->maxDays); ?></span>
				<?php } ?>
			</div>

			<div class="input-wrap">
				<label for="mode-clear">
					<input type="checkbox" name="mode" id="mode-clear" value="clear" />
					<?php echo Lang::txt('COM_GROUPS_MEMBER_TERM_CLEAR'); ?>
				</label>
			</div>
		<?php } ?>
	</fieldset>

	<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>" />
	<input type="hidden" name="gid" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
	<input type="hidden" name="task" value="setexpiration" />
	<?php echo Html::input('token'); ?>
</form>
