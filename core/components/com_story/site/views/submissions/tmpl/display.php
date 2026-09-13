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
	<h2><?php echo Lang::txt('COM_STORY_MY_SUBMISSIONS'); ?></h2>
</header>

<section class="main section">

	<p class="story-add">
		<a class="btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&view=submissions&task=new'); ?>"><?php
			echo Lang::txt('COM_STORY_SUBMIT');
		?></a>
		<a href="<?php echo Route::url('index.php?option=' . $this->option . '&view=queue'); ?>"><?php
			echo Lang::txt('COM_STORY_QUEUE');
		?></a>
	</p>

<?php if (!count($this->rows)) { ?>
	<p class="info"><?php echo Lang::txt('COM_STORY_NO_SUBMISSIONS'); ?></p>
<?php } else { ?>
	<table class="story-submissions">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_STORY_FIELD_SUBJECT'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORY_SENT'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORY_COL_STATE'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php foreach ($this->rows as $row) { ?>
			<tr>
				<td><?php
					echo $row->get('story_id')
						? '<a href="' . Route::url('index.php?option=' . $this->option . '&id=' . (int) $row->get('story_id')) . '">' . $this->escape($row->get('subject')) . '</a>'
						: $this->escape($row->get('subject'));
				?></td>
				<td><time datetime="<?php echo $this->escape($row->get('created')); ?>"><?php
					echo Date::of($row->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
				?></time></td>
				<td class="state-<?php echo $this->escape($row->get('state')); ?>"><?php
					echo Lang::txt('COM_STORY_STATE_' . strtoupper($row->get('state')));
				?></td>
			</tr>
<?php } ?>
		</tbody>
	</table>

	<?php echo $this->rows->pagination; ?>
<?php } ?>

</section>
