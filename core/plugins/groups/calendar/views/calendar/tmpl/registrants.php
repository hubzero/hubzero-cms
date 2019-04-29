<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$year  = date("Y", strtotime($this->event->publish_up));
$month = date("m", strtotime($this->event->publish_up));
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<ul id="page_options">
	<li>
		<a class="icon-date btn date" title="" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&year='.$year.'&month='.$month); ?>">
			<?php echo Lang::txt('Back to Calendar'); ?>
		</a>
	</li>
</ul>

<div class="event-title-bar">
	<span class="event-title">
		<?php echo $this->event->title; ?>
	</span>
	<?php if ($this->user->get('id') == $this->event->created_by || $this->authorized == 'manager') : ?>
		<a class="delete" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=delete&event_id='.$this->event->id); ?>">
			Delete
		</a>
		<a class="edit" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=edit&event_id='.$this->event->id); ?>">
			Edit
		</a>
	<?php endif; ?>
</div>

<div class="event-sub-menu">
	<ul>
		<li>
			<a href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=details&event_id='.$this->event->id); ?>">
				<span><?php echo Lang::txt('Details'); ?></span>
			</a>
		</li>
		<?php if (isset($this->event->registerby) && $this->event->registerby && $this->event->registerby != '0000-00-00 00:00:00') : ?>
			<li>
				<a href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=register&event_id='.$this->event->id); ?>">
					<span><?php echo Lang::txt('Register'); ?></span>
				</a>
			</li>
		<?php endif; ?>

		<?php if ($this->user->get('id') == $this->event->created_by || $this->authorized == 'manager') : ?>
			<li class="active">
				<a href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=registrants&event_id='.$this->event->id); ?>">
					<span><?php echo Lang::txt('Registrants ('.count($this->registrants).')'); ?></span>
				</a>
			</li>
		<?php endif; ?>
	</ul>
	<div class="clear"></div>
</div>

<table class="group-registrants">
	<thead>
		<tr>
			<th colspan="3">
				<a href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=download&event_id='.$this->event->id); ?>">Download Registrants (.csv)</a>
			</th>
		</tr>
		<tr>
			<th><?php echo Lang::txt('Name'); ?></th>
			<th><?php echo Lang::txt('Email'); ?></th>
			<th><?php echo Lang::txt('Register Date'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (count($this->registrants) > 0) : ?>
			<?php foreach ($this->registrants as $registrant) : ?>
				<tr>
					<td><?php echo $registrant->last_name . ', ' . $registrant->first_name; ?></td>
					<td><?php echo $registrant->email; ?></td>
					<td><?php echo Date::of($registrant->registered)->toLocal('l, F d, Y @ g:i a'); ?></td>
				</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr>
				<td colspan="3">Currently there are no event registrants.</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>