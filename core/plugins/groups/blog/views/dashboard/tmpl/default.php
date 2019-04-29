<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<table class="activity">
	<tbody>
<?php
if ($this->entries) {
	foreach ($this->entries as $entry)
	{
?>
		<tr>
			<th scope="row"><?php echo $area; ?></th>
			<td class="author"><a href="<?php echo Route::url('index.php?option=com_members&id='.$entry->created_by); ?>"><?php echo stripslashes($name); ?></a></td>
			<td class="action"><?php echo stripslashes($entry->title); ?></td>
			<td class="date"><?php echo Date::of($entry->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1') . ' @' . Lang::txt('TIME_FORMAT_HZ1')); ?></td>
		</tr>
<?php
	}
} else {
	// Do nothing if there are no events to display
?>
		<tr>
			<td><?php echo Lang::txt('PLG_GROUPS_BLOG_NO_ENTRIES_FOUND'); ?></td>
		</tr>
<?php
}
?>
	</tbody>
</table>
