<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (!$this->model->access('content'))
{
	return;
}

$max_s = 3;
$actual_s = count($this->suggestions) >= $max_s ? $max_s : count($this->suggestions);

if ($actual_s <= 1)
{
	return;
}
$i = 0;
?>
<?php if ($actual_s > 1) { ?>
	<div class="welcome">
		<p class="closethis"><a href="<?php echo Route::url('index.php?option=' . $this->option
		. '&alias=' . $this->model->get('alias') . '&active=feed') . '?c=1'; ?>"><?php echo Lang::txt('COM_PROJECTS_PROJECT_CLOSE_THIS'); ?></a></p>

		<h3><?php echo $this->model->access('owner') ? Lang::txt('COM_PROJECTS_WELCOME_TO_PROJECT_CREATOR') : Lang::txt('COM_PROJECTS_WELCOME_TO').' '.stripslashes($this->model->get('title')).' '.Lang::txt('COM_PROJECTS_PROJECT').'!'; ?> </h3>
		<p><?php echo $this->model->access('owner') ? Lang::txt('COM_PROJECTS_WELCOME_SUGGESTIONS_CREATOR') : Lang::txt('COM_PROJECTS_WELCOME_SUGGESTIONS'); ?></p>
		<div id="suggestions" class="suggestions">
			<?php foreach ($this->suggestions as $suggestion)
				{ $i++;
				  if ($i <= $max_s)
					{ ?>
				<div class="<?php echo $suggestion['class']; ?>">
					<p><a href="<?php echo $suggestion['url']; ?>"><?php echo $suggestion['text']; ?></a></p>
				</div>
			<?php }
			} ?>
			<div class="clear"></div>
		</div>
	</div>
<?php }
