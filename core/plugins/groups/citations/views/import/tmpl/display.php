<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('import.css')
     ->js('import.js');

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=citations';
?>
<section id="import" class="section">
	<div class="section-inner">
		<?php if ($this->messages): ?>
		<?php //foreach ($this->messages as $message) { ?>
			<p class="<?php //echo $message['type']; ?>"><?php echo $message['message']; ?></p>
		<?php //} ?>
		<?php endif; ?>
		<ul id="steps">
			<li><a href="<?php echo Route::url($base . '&action=import'); ?>" class="active"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP1'); ?><span><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP1_NAME'); ?></span></a></li>
			<li><a><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP2'); ?><span><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP2_NAME'); ?></span></a></li>
			<li><a><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP3'); ?><span><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP3_NAME'); ?></span></a></li>
		</ul><!-- / #steps -->

		<form id="hubForm" class="full" enctype="multipart/form-data" method="post" action="<?php echo Route::url($base . '&action=upload'); ?>">
			<fieldset>
				<legend><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_UPLOAD'); ?>:</legend>

				<div class="grid">
					<div class="col span6">
						<label for="citations_file">
							<?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_UPLOAD_FILE'); ?>: <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
							<input type="file" name="citations_file" id="citations_file" />
							<span class="hint"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_UPLOAD_MAX'); ?></span>
						</label>
					</div>
					<div class="col span6 omega">
						<p>
							<strong><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_ACCEPTABLE'); ?></strong><br />
							<?php echo implode($this->accepted_files, '<br />'); ?>
						</p>
					</div>
				</div>
			</fieldset>

			<p class="submit">
				<input type="submit" class="btn btn-success" name="submit" value="<?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_UPLOAD'); ?>" />

				<a class="btn btn-secondary" href="<?php echo Route::url($base); ?>">
					<?php echo Lang::txt('JCANCEL'); ?>
				</a>
			</p>

			<?php echo Html::input('token'); ?>
			<input type="hidden" name="option" value="com_groups" />
			<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="active" value="citations" />
			<input type="hidden" name="action" value="upload" />
		</form>
	</div>
</section><!-- / .section -->
