<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="group btn" href="<?php echo Route::url('index.php?option='.$this->option); ?>">
					<?php echo Lang::txt('COM_GROUPS_ACTION_BACK_TO_ALL_GROUPS'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<div class="section-inner">
		<?php
			foreach ($this->notifications as $notification)
			{
				echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
			}
		?>
		<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
			<div class="explaination">
				<p class="info"><?php echo Lang::txt('COM_GROUPS_JOIN_HELP'); ?></p>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_GROUPS_JOIN_SECTION_TITLE'); ?></legend>

				<?php if ($this->group->get('restrict_msg')) { ?>
					<p class="warning"><?php echo Lang::txt('NOTE') . ': ' . $this->escape(stripslashes($this->group->get('restrict_msg'))); ?></p>
				<?php } ?>

				<label for="reason">
					<?php echo Lang::txt('COM_GROUPS_JOIN_REASON'); ?>
					<textarea name="reason" id="reason" rows="10" cols="50"></textarea>
				</label>
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="membership" />
				<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
				<input type="hidden" name="task" value="dorequest" />
				<?php echo Html::input('token'); ?>
			</fieldset>
			<p class="submit">
				<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('COM_GROUPS_JOIN_BTN_TEXT'); ?>" />
			</p>
		</form>
	</div>
</section><!-- / .main section -->
