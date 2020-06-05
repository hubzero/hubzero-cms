<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if ($this->isUser) : ?>
	<div class="section-edit-container">
		<?php if ($this->registration == Components\Members\Models\Profile\Field::STATE_READONLY) : ?>
			<p class="notice warning"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_READONLY', $this->title); ?></p>
		<?php else : ?>
			<div class="section-edit-content">
				<form action="<?php echo Route::url('index.php?option=com_members'); ?>" method="post" data-section-registation="<?php echo $this->registration_field; ?>" data-section-profile="<?php echo $this->profile_field; ?>">
					<span class="section-edit-errors"></span>

					<div class="input-wrap">
						<?php echo $this->inputs; ?>
					</div>
					<div class="input-wrap">
						<?php echo $this->access; ?>
					</div>

					<input type="submit" class="section-edit-submit btn" value="<?php echo Lang::txt('PLG_MEMBERS_PROFILE_SAVE'); ?>" />
					<input type="reset" class="section-edit-cancel btn" value="<?php echo Lang::txt('JCANCEL'); ?>" />
					<input type="hidden" name="field_to_check[]" value="<?php echo $this->registration_field; ?>" />
					<input type="hidden" name="option" value="com_members" />
					<input type="hidden" name="controller" value="profiles" />
					<input type="hidden" name="id" value="<?php echo $this->profile->get('id'); ?>" />
					<input type="hidden" name="task" value="save" />
					<input type="hidden" name="no_html" value="1" />
					<?php echo Html::input('token'); ?>
				</form>
			</div>
		<?php endif; ?>
	</div>
<?php endif;
