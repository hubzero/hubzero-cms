<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (!$this->no_html) {
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-prev btn" href="<?php echo $this->course->link(); ?>"><?php echo Lang::txt('COM_COURSES_BACK'); ?></a>
		</p>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
<?php } ?>

	<?php
		foreach ($this->notifications as $notification) {
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>

	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
		<?php if (!$this->no_html) { ?>
			<div class="explaination">
				<p><?php echo Lang::txt('COM_COURSES_NEW_OFFERING_EXPLANATION'); ?></p>
			</div>
		<?php } ?>
		<fieldset>
			<legend><?php echo Lang::txt('COM_COURSES_FIELDSET_NEW_OFFERING'); ?></legend>

			<label for="field-alias">
				<?php echo Lang::txt('COM_COURSES_FIELD_OFFERING_ALIAS'); ?>
				<input name="offering[alias]" id="field-alias" type="text" size="35" value="<?php echo $this->escape($this->offering->get('alias')); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_OFFERING_ALIAS_HINT'); ?></span>
			</label>

			<label for="field-title">
				<?php echo Lang::txt('COM_COURSES_FIELD_TITLE'); ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
				<input type="text" name="offering[title]" id="field-title" size="35" value="<?php echo $this->escape(stripslashes($this->offering->get('title'))); ?>" />
			</label>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="offering[state]" value="<?php echo $this->offering->get('state'); ?>" />
		<input type="hidden" name="offering[course_id]" value="<?php echo $this->course->get('id'); ?>" />
		<input type="hidden" name="offering[id]" value="<?php echo $this->offering->get('id'); ?>" />
		<input type="hidden" name="offering[state]" value="<?php echo $this->offering->get('state', 1); ?>" />
		<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="saveoffering" />

		<?php echo Html::input('token'); ?>

		<p class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('COM_COURSES_SAVE'); ?>" />
		</p>
	</form>

<?php if (!$this->no_html) { ?>
</section><!-- / .section -->
<?php } 