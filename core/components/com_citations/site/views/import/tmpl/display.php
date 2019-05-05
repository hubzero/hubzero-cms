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
</header>

<section id="import" class="section">
	<div class="section-inner">
		<?php foreach ($this->messages as $message) { ?>
			<p class="<?php echo $message['type']; ?>"><?php echo $message['message']; ?></p>
		<?php } ?>

		<ul id="steps">
			<li><a href="<?php echo Request::base(true); ?>/citations/import" class="active"><?php echo Lang::txt('COM_CITATIONS_IMPORT_STEP1'); ?><span><?php echo Lang::txt('COM_CITATIONS_IMPORT_STEP1_NAME'); ?></span></a></li>
			<li><a><?php echo Lang::txt('COM_CITATIONS_IMPORT_STEP2'); ?><span><?php echo Lang::txt('COM_CITATIONS_IMPORT_STEP2_NAME'); ?></span></a></li>
			<li><a><?php echo Lang::txt('COM_CITATIONS_IMPORT_STEP3'); ?><span><?php echo Lang::txt('COM_CITATIONS_IMPORT_STEP3_NAME'); ?></span></a></li>
		</ul><!-- / #steps -->

		<form id="hubForm" enctype="multipart/form-data" method="post" action="<?php echo Route::url('index.php?option='. $this->option . '&task=import_upload'); ?>">
			<p class="explaination">
				<strong><u><?php echo Lang::txt('COM_CITATIONS_IMPORT_ACCEPTABLE'); ?></u></strong><br />
				<?php echo implode($this->accepted_files, "<br />"); ?>
			</p>
			<fieldset>
				<legend><?php echo Lang::txt('COM_CITATIONS_IMPORT_UPLOAD'); ?>:</legend>
				<label><?php echo Lang::txt('COM_CITATIONS_IMPORT_UPLOAD_FILE'); ?>: <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
					<input type="file" name="citations_file" />
					<span class="hint"><?php echo Lang::txt('COM_CITATIONS_IMPORT_UPLOAD_MAX'); ?></span>
				</label>
			</fieldset>

			<p class="submit">
				<input type="submit" name="submit" value="<?php echo Lang::txt('COM_CITATIONS_IMPORT_UPLOAD'); ?>" />
			</p>

			<?php echo Html::input('token'); ?>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<?php if (isset($this->gid)): ?>
				<input type="hidden" name="group" value="<?php echo $this->gid; ?>" />
			<?php endif; ?>
			<input type="hidden" name="task" value="import_upload" />
		</form>
	</div>
</section><!-- / .section -->
