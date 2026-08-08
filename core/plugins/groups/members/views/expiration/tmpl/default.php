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
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=members'); ?>" method="post" id="hubForm">
	<div class="explaination">
		<p class="info"><?php echo Lang::txt('PLG_GROUPS_MEMBERS_EXPIRATION_EXPLANATION'); ?></p>
	</div>

	<?php if ($this->getError()) { ?>
		<?php foreach ($this->getErrors() as $error) { ?>
			<p class="error"><?php echo $this->escape($error); ?></p>
		<?php } ?>
	<?php } ?>

	<fieldset>
		<legend><?php echo Lang::txt('PLG_GROUPS_MEMBERS_EXPIRATION'); ?></legend>

		<?php
		$names = array();
		foreach ($this->users as $user)
		{
			$u = User::getInstance($user);
			$label = is_object($u) && $u->get('name') ? $u->get('name') : $user;

			if (!empty($this->current[$user]))
			{
				$label .= ' (' . Lang::txt(
					'PLG_GROUPS_MEMBERS_EXPIRATION_CURRENTLY',
					Date::of($this->current[$user])->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
				) . ')';
			}

			$names[] = $this->escape($label);
			?>
			<input type="hidden" name="users[]" value="<?php echo $this->escape($user); ?>" />
			<?php
		}
		?>

		<label>
			<?php echo Lang::txt('PLG_GROUPS_MEMBERS_EXPIRATION_USERS'); ?><br />
			<strong><?php echo implode(', ', $names); ?></strong>
		</label>

		<label for="expires">
			<?php echo Lang::txt('PLG_GROUPS_MEMBERS_EXPIRATION_DATE'); ?>
			<input type="date" name="expires" id="expires" min="<?php echo $min; ?>"<?php echo $max ? ' max="' . $max . '"' : ''; ?> />
		</label>
		<?php if ($this->maxDays > 0) { ?>
			<p class="hint"><?php echo Lang::txt('PLG_GROUPS_MEMBERS_EXPIRATION_MAX_HINT', $this->maxDays); ?></p>
		<?php } ?>

		<label for="extend_days">
			<?php echo Lang::txt('PLG_GROUPS_MEMBERS_EXPIRATION_EXTEND'); ?>
			<select name="extend_days" id="extend_days">
				<option value="0"><?php echo Lang::txt('PLG_GROUPS_MEMBERS_EXPIRATION_EXTEND_NONE'); ?></option>
				<?php foreach (array(30, 90, 180, 365) as $days) : ?>
					<?php if ($this->maxDays > 0 && $days > $this->maxDays) { continue; } ?>
					<option value="<?php echo $days; ?>"><?php echo Lang::txt('PLG_GROUPS_MEMBERS_EXPIRATION_EXTEND_DAYS', $days); ?></option>
				<?php endforeach; ?>
			</select>
			<br /><span class="hint"><?php echo Lang::txt('PLG_GROUPS_MEMBERS_EXPIRATION_EXTEND_HINT'); ?></span>
		</label>

		<label for="mode-clear">
			<input type="checkbox" name="mode" id="mode-clear" value="clear" />
			<?php echo Lang::txt('PLG_GROUPS_MEMBERS_EXPIRATION_CLEAR'); ?>
		</label>
	</fieldset><div class="clear"></div>

	<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
	<input type="hidden" name="active" value="members" />
	<input type="hidden" name="filter" value="<?php echo $this->escape($this->filter); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
	<input type="hidden" name="action" value="confirmsetexpiration" />
	<?php echo Html::input('token'); ?>

	<p class="submit">
		<input type="submit" value="<?php echo Lang::txt('PLG_GROUPS_MEMBERS_SUBMIT'); ?>" />
	</p>
</form>
