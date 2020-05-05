<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
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
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=curation'); ?>" method="post" id="hubForm-ajax" name="curation-form" class="curation-history">
		<fieldset>
			<legend><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGN_VIEW'); ?></legend>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->pub->id; ?>" />
			<input type="hidden" name="vid" value="<?php echo $this->pub->version->id; ?>" />
			<input type="hidden" name="task" value="assign" />
			<input type="hidden" name="confirm" value="1" />

			<p class="info"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGN_INSTRUCT'); ?></p>

			<div class="form-group">
				<label for="owner">
					<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGN_CHOOSE'); ?>
					<?php
					$selected = $this->pub->curator() ? $this->pub->curator('name') : '';

					$mc = Event::trigger( 'hubzero.onGetSingleEntryWithSelect', array(array('members', 'owner', 'owner', '', $selected, '', 'owner')));
					$mc = implode("\n", $mc);
					$ms = trim($mc);

					if (!$mc):
						$ms = '<input type="text" name="owner" id="owner" value="" class="form-control" size="35" maxlength="200" />';
					endif;

					echo $ms;
					?>

					<?php if ($selected): ?>
						<input type="hidden" name="selected" value="<?php echo $this->pub->curator; ?>" />
					<?php endif; ?>
				</label>
			</div>
		</fieldset>

		<p class="submitarea">
			<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_PUBLICATIONS_SAVE'); ?>" />

			<?php if ($this->ajax): ?>
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('JCANCEL'); ?>" />
			<?php else: ?>
				<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=curation'); ?>" class="btn btn-cancel">
					<?php echo Lang::txt('JCANCEL'); ?>
				</a>
			<?php endif; ?>
		</p>
	</form>
</div>
