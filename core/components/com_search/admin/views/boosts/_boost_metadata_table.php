<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$boost = $this->boost;
$created = $boost->getCreated();
$createdLocal = Date::of($created)->toLocal();
$author = $boost->getAuthor();
?>

<table class="meta">
	<tbody>
		<tr>
			<th><?php echo Lang::txt('COM_SEARCH_COL_ID'); ?>:</th>
			<td>
				<?php echo $boost->getId(); ?>
			</td>
		</tr>

		<tr>
			<th><?php echo Lang::txt('COM_SEARCH_LABEL_CREATED_BY'); ?>:</th>
			<td>
				<a href="<?php echo $author->link(); ?>"
					class="meta-link">
					<?php echo $author->get('name'); ?>
				</a>
			</td>
		</tr>

		<tr>
			<th><?php echo Lang::txt('COM_SEARCH_LABEL_CREATED'); ?>:</th>
			<td>
				<?php echo $createdLocal; ?>
			</td>
		</tr>
	</tbody>
</table>
