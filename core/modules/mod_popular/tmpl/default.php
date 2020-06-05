<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

?>
<table class="adminlist">
	<thead>
		<tr>
			<th scope="col">
				<?php echo Lang::txt('MOD_POPULAR_ITEMS'); ?>
			</th>
			<th scope="col">
				<?php echo Lang::txt('MOD_POPULAR_CREATED'); ?>
			</th>
			<th scope="col">
				<?php echo Lang::txt('JGLOBAL_HITS');?>
			</th>
		</tr>
	</thead>
<?php if (count($list)) : ?>
	<tbody>
	<?php foreach ($list as $i => $item) : ?>
		<tr>
			<th scope="row">
				<?php if ($item->checked_out) : ?>
					<?php echo Html::grid('checkedout', $i, $item->editor->get('name'), $item->checked_out_time); ?>
				<?php endif; ?>

				<?php if ($item->link) :?>
					<a href="<?php echo $item->link; ?>">
						<?php echo $this->escape($item->title); ?>
					</a>
				<?php else :
					echo $this->escape($item->title);
				endif; ?>
			</th>
			<td class="center">
				<time datetime="<?php echo $item->created; ?>"><?php echo Date::of($item->created)->toLocal('Y-m-d H:i:s'); ?></time>
			</td>
			<td class="center">
				<?php echo $item->hits; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
<?php else : ?>
	<tbody>
		<tr>
			<td colspan="3">
				<p class="noresults"><?php echo Lang::txt('MOD_POPULAR_NO_MATCHING_RESULTS'); ?></p>
			</td>
		</tr>
	</tbody>
<?php endif; ?>
</table>
