<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if ($this->row instanceof \Components\Collections\Models\Collection)
{
	$collection = $this->row;
}
else
{
	$collection = \Components\Collections\Models\Collection::getInstance($this->row->item()->get('object_id'));
	if ($this->row->get('description'))
	{
		$collection->set('description', $this->row->get('description'));
	}
}
?>
		<h4<?php if ($collection->get('access', 0) == 4) { echo ' class="private"'; } ?>>
			<a href="<?php echo Route::url($collection->link()); ?>">
				<?php echo $this->escape(stripslashes($collection->get('title'))); ?>
			</a>
		</h4>
		<div class="description">
			<?php echo $collection->description('parsed'); ?>
		</div>
		<?php /* <table>
			<tbody>
				<tr>
					<td>
						<strong><?php echo $collection->count('file'); ?></strong> <span class="post-type file"><?php echo Lang::txt('files'); ?></span>
					</td>
					<td>
						<strong><?php echo $collection->count('collection'); ?></strong> <span class="post-type collection"><?php echo Lang::txt('collections'); ?></span>
					</td>
					<td>
						<strong><?php echo $collection->count('link'); ?></strong> <span class="post-type link"><?php echo Lang::txt('links'); ?></span>
					</td>
				</tr>
			</tbody>
		</table> */
