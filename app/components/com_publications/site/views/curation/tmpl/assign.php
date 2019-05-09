<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (!$this->ajax)
{
	$this->css('curation.css')
		->js('curation.js');
}
?>
<div id="abox-content" class="curation-wrap">
	<h3><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGN_VIEW'); ?></h3>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=curation'); ?>" method="post" id="hubForm-ajax" name="curation-form" class="curation-history">
		<fieldset>
			<input type="hidden" name="id" value="<?php echo $this->pub->id; ?>" />
			<input type="hidden" name="vid" value="<?php echo $this->pub->version->id; ?>" />
			<input type="hidden" name="task" value="assign" />
			<input type="hidden" name="confirm" value="1" />
		</fieldset>

		<p class="info"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGN_INSTRUCT'); ?></p>

		<label for="owner">
			<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGN_CHOOSE'); ?>
			<?php
			$selected = $this->pub->curator() ? $this->pub->curator('name') : '';
			$mc = Event::trigger( 'hubzero.onGetSingleEntryWithSelect', array(array('members', 'owner', 'owner', '', $selected,'','owner')) );
			if (count($mc) > 0) {
				echo $mc[0];
			} else { ?>
				<input type="text" name="owner" id="owner" value="" size="35" maxlength="200" />
			<?php } ?>
			<?php if ($selected) { ?>
				<input type="hidden" name="selected" value="<?php echo $this->pub->curator; ?>" />
			<?php } ?>
		</label>

		<p class="submitarea">
			<input type="submit" class="btn" value="<?php echo Lang::txt('COM_PUBLICATIONS_SAVE'); ?>" />
			<?php if ($this->ajax) { ?>
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('JCANCEL'); ?>" />
			<?php } else { ?>
				<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=curation'); ?>" class="btn btn-cancel"><?php echo Lang::txt('JCANCEL'); ?></a>
			<?php } ?>
		</p>
	</form>
</div>
