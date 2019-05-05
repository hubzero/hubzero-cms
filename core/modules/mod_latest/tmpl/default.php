<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

?>
<table class="adminlist">
	<thead>
		<tr>
			<th scope="col">
				<?php echo Lang::txt('MOD_LATEST_LATEST_ITEMS'); ?>
			</th>
			<th scope="col">
				<?php echo Lang::txt('JSTATUS'); ?>
			</th>
			<th scope="col">
				<?php echo Lang::txt('MOD_LATEST_CREATED'); ?>
			</th>
			<th scope="col">
				<?php echo Lang::txt('MOD_LATEST_CREATED_BY');?>
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

				<?php if ($item->link) : ?>
					<a href="<?php echo $item->link; ?>">
						<?php echo $this->escape($item->title); ?>
					</a>
				<?php else :
					echo $this->escape($item->title);
				endif; ?>
			</th>
			<td class="center">
				<?php echo Html::grid('published', $item->state, $i, '', false); ?>
			</td>
			<td class="center">
				<time datetime="<?php echo $item->created; ?>"><?php echo Date::of($item->created)->toLocal('Y-m-d H:i:s'); ?></time>
			</td>
			<td class="center">
				<?php echo $item->author_name; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
<?php else : ?>
	<tbody>
		<tr>
			<td colspan="4">
				<p class="noresults"><?php echo Lang::txt('MOD_LATEST_NO_MATCHING_RESULTS'); ?></p>
			</td>
		</tr>
	</tbody>
<?php endif; ?>
</table>
