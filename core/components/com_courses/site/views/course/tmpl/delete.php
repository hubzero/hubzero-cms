<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
		<ul id="useroptions">
			<li class="last"><a class="course" href="<?php echo Route::url('index.php?option='.$this->option.'&gid='.$this->course->get('alias')); ?>"><?php echo Lang::txt('Back to Course'); ?></a></li>
		</ul>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<?php
		foreach ($this->notifications as $notification)
		{
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p><strong>Are you sure you want to delete?</strong></p>
			<p>Deleting a course will permanently remove the course and all data associated with that course.</p>
			<p>&nbsp;</p>

			<p><strong>Alternative to deleting</strong></p>
			<p>You could set the course join policy to closed to restrict further membership activity and set the discoverability to hidden so the course is hidden to the world but still there later if you decide you want to use the course again.</p>
			<p><a href="<?php echo Route::url('index.php?option='.$this->option.'&gid='.$this->course->get('alias').'&task=edit'); ?>">&raquo; Click here to edit course settings</a></p>
		</div>
		<fieldset>
			<h3><?php echo Lang::txt('COURSES_DELETE_HEADER'); ?></h3>

	 		<p class="warning"><?php echo Lang::txt('COURSES_DELETE_WARNING', $this->course->get('description')).'<br /><br />'.$this->log; ?></p>

			<div class="form-group">
				<label for="msg">
					<?php echo Lang::txt('COURSES_DELETE_MESSAGE'); ?>
					<textarea name="msg" id="msg" rows="12" class="form-control" cols="50"><?php echo $this->escape($this->msg); ?></textarea>
				</label>
			</div>

			<div class="form-group form-check">
				<label for="confirmdel" class="form-check-label">
					<input type="checkbox" class="option form-check-input" name="confirmdel" id="confirmdel" value="1" />
					<?php echo Lang::txt('COURSES_DELETE_CONFIRM'); ?>
				</label>
			</div>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="gid" value="<?php echo $this->course->get('cn'); ?>" />
		<input type="hidden" name="task" value="delete" />
		<input type="hidden" name="process" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />

		<p class="submit">
			<input class="btn btn-danger" type="submit" value="<?php echo Lang::txt('DELETE'); ?>" />
		</p>
	</form>
</section><!-- / .main section -->
