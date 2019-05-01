<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-group btn" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn')); ?>">
				<?php echo Lang::txt('COM_GROUPS_ACTION_BACK_TO_GROUP'); ?>
			</a>
		</p>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<?php foreach ($this->notifications as $notification) : ?>
		<p class="<?php echo $notification['type']; ?>">
			<?php echo $notification['message']; ?>
		</p>
	<?php endforeach; ?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=delete'); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p><strong><?php echo Lang::txt('COM_GROUPS_DELETE_ARE_YOU_SURE_TITLE'); ?></strong></p>
			<p><?php echo Lang::txt('COM_GROUPS_DELETE_ARE_YOU_SURE_DESC'); ?></p>

			<p><strong><?php echo Lang::txt('COM_GROUPS_DELETE_ALTERNATIVE_TITLE'); ?></strong></p>
			<p><?php echo Lang::txt('COM_GROUPS_DELETE_ALTERNATIVE_DESC'); ?></p>
			<p>
				<a class="config btn" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&task=edit'); ?>">
					<?php echo Lang::txt('COM_GROUPS_DELETE_ALTERNATIVE_BTN_TEXT'); ?>
				</a>
			</p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_GROUPS_DELETE_CONFIRM_BOX_HEADING'); ?></legend>

	 		<p class="warning"><?php echo Lang::txt('COM_GROUPS_DELETE_CONFIRM_BOX_WARNING', $this->group->get('description')) . '<br /><br />' . $this->log; ?></p>

			<div class="form-group">
				<label for="msg">
					<?php echo Lang::txt('COM_GROUPS_DELETE_CONFIRM_BOX_MESSAGE_LABEL'); ?>
					<textarea class="form-control" name="msg" id="msg" rows="12" cols="50"><?php echo htmlentities($this->msg); ?></textarea>
				</label>
			</div>

			<div class="form-group form-check">
				<label for="confirmdel" class="form-check-label">
					<input type="checkbox" class="option form-check-input" name="confirmdel" id="confirmdel" value="1" />
					<?php echo Lang::txt('COM_GROUPS_DELETE_CONFIRM_CONFIRM'); ?>
				</label>
			</div>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
		<input type="hidden" name="task" value="dodelete" />

		<p class="submit">
			<input class="btn btn-danger" type="submit" value="<?php echo Lang::txt('DELETE'); ?>" />
		</p>
	</form>
</section><!-- / .main section -->
