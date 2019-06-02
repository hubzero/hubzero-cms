<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$tmpl = Request::getString('tmpl', '');
$no_html = Request::getInt('no_html', 0);

if (!$tmpl && !$no_html) {
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_SUPPORT_EDIT_FOLDER'); ?></h2>
</header><!-- / #content-header -->

<section class="main section">
<?php } ?>
	<div class="section-inner">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>
		<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=savefolder'); ?>" method="post" id="hubForm">
			<fieldset>
				<legend><?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE'); ?></legend>

				<label for="field-title"><?php echo Lang::txt('COM_SUPPORT_FIELD_TITLE'); ?></label>
				<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />

				<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->id); ?>" />

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="no_html" value="<?php echo ($tmpl) ? 1 : Request::getInt('no_html', 0); ?>" />
				<input type="hidden" name="tmpl" value="<?php echo $this->escape($tmpl); ?>" />
				<input type="hidden" name="task" value="savefolder" />

				<?php echo Html::input('token'); ?>
			</fieldset>
			<p class="submit">
				<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_SUPPORT_SUBMIT'); ?>" />
			</p>
		</form>
	</div>
<?php if (!$no_html) { ?>
</section><!-- / .main section -->
<?php
}