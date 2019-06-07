<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Html::behavior('tooltip');
?>
<div class="unotes">
	<h2 class="modal-title"><?php echo Lang::txt('COM_MEMBERS_NOTES_FOR_USER', $this->user->get('name'), $this->user->get('id')); ?></h2>
	<table class="adminlist">
		<tbody>
			<tr>
				<td>
<?php if (!$this->rows->count()) : ?>
	<?php echo Lang::txt('COM_MEMBERS_NO_NOTES'); ?>
<?php else : ?>
	<ol class="alternating">
	<?php foreach ($this->rows as $row) : ?>
		<li>
			<div class=" utitle">
				<?php if ($row->get('subject')) : ?>
					<h4><?php echo Lang::txt('COM_MEMBERS_NOTE_N_SUBJECT', (int) $row->get('id'), $this->escape($row->get('subject'))); ?></h4>
				<?php else : ?>
					<h4><?php echo Lang::txt('COM_MEMBERS_NOTE_N_SUBJECT', (int) $row->get('id'), Lang::txt('COM_MEMBERS_EMPTY_SUBJECT')); ?></h4>
				<?php endif; ?>
			</div>

			<div class="utitle">
				<?php echo Date::of($row->get('created_time'))->toLocal('D d M Y H:i'); ?>,
				<em><?php echo $this->escape($row->category->get('title')); ?></em>
			</div>

			<div class="ubody">
				<?php echo $row->get('body'); ?>
			</div>
		</li>
	<?php endforeach; ?>
	</ol>
<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
