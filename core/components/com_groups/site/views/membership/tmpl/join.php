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
	<h2><?php echo $this->escape($this->title); ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="group btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')); ?>">
					<?php echo Lang::txt('COM_GROUPS_ACTION_BACK_TO_GROUP'); ?>
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
		<form action="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&task=join'); ?>" method="post">
			<p><?php echo Lang::txt('COM_GROUPS_JOIN_CONFIRM', $this->escape($this->group->get('description'))); ?></p>
			<?php echo Html::input('token'); ?>
			<p class="submit">
				<button class="btn btn-success" type="submit"><?php echo Lang::txt('COM_GROUPS_TOOLBAR_JOIN'); ?></button>
			</p>
		</form>
	</div>
</section><!-- / .main section -->
