<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<div id="project-wrap">
	<section class="main section">
		<?php
		$this->view('_header', 'projects')
		     ->set('model', $this->model)
		     ->set('showPic', 0)
		     ->set('showPrivacy', 0)
		     ->set('goBack', 0)
		     ->set('showUnderline', 1)
		     ->set('option', $this->option)
		     ->display();
		?>

		<p class="warning"><?php echo Lang::txt('COM_PROJECTS_INFO_OWNER_DELETED'); ?></p>

		<form method="post" action="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias')); ?>" id="hubForm">
			<fieldset>
				<legend><?php echo Lang::txt('COM_PROJECTS_OWNER_DELETED_OPTIONS'); ?></legend>

				<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
				<input type="hidden" name="task" value="fixownership" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />

				<div class="form-group form-check">
					<label for="keep1" class="form-check-label">
						<input class="option form-check-input" name="keep" type="radio" id="keep1" value="1" checked="checked" />
						<?php echo Lang::txt('COM_PROJECTS_OWNER_KEEP_PROJECT'); ?>
					</label>
				</div>

				<div class="form-group form-check">
					<label for="keep0" class="form-check-label">
						<input class="option form-check-input" name="keep" type="radio" id="keep0" value="0" />
						<?php echo Lang::txt('COM_PROJECTS_OWNER_DELETE_PROJECT'); ?>
					</label>
				</div>

				<p class="submitarea">
					<input type="submit" class="btn" value="<?php echo Lang::txt('COM_PROJECTS_SAVE_MY_CHOICE'); ?>" />
				</p>
			</fieldset>
		</form>
	</section><!-- / .main section -->
</div>