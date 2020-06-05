<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$status = '';
if (!$row->wasViewed())
{
	$status = 'new';

	$row->markAsViewed();
}

$name = $this->escape(stripslashes($row->log->creator->get('name')));

?>
<li
	data-time="<?php echo $row->get('created'); ?>"
	data-id="<?php echo $row->get('id'); ?>"
	data-log_id="<?php echo $row->get('log_id'); ?>"
	class="activity <?php echo $this->escape($row->get('scope') . '.' . $row->get('scope_id') . ' ' . $row->log->get('action')) . ' ' . $status; ?>">

	<div class="activity <?php echo $this->escape($row->log->get('component')); ?>">
		<span class="activity-details">
			<span class="activity-actor">
				<?php if (in_array($row->log->creator->get('access'), User::getAuthorisedViewLevels())) { ?>
					<a href="<?php echo Route::url($row->log->creator->link()); ?>">
						<?php echo $name; ?>
					</a>
				<?php } else { ?>
					<?php echo $name; ?>
				<?php } ?>
			</span>
			<span class="activity-time"><time datetime="<?php echo $row->get('created'); ?>"><?php echo Date::of($row->get('created'))->relative(); ?></time></span>
			<!-- <span class="activity-channel"><?php echo $this->escape($row->get('scope') . '.' . $row->get('scope_id')); ?></span> -->
		</span>
		<span class="activity-event">
			<?php echo $row->log->get('description'); ?>
		</span>
	</div>

</li>
