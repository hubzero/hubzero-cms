<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Pathway::append(
	Lang::txt('COM_WIKI_SPECIAL_FIX_LENGTH'),
	$this->page->link()
);

$query = Components\Wiki\Models\Version::all();

$v = $query->getTableName();
$p = Components\Wiki\Models\Page::blank()->getTableName();

$rows = $query
	->join($p, $p . '.id', $v . '.page_id', 'inner')
	->whereEquals($v . '.length', 0)
	->whereEquals($p . '.scope', $this->page->get('scope'))
	->whereEquals($p . '.scope_id', $this->page->get('scope_id'))
	->rows();
?>
<form method="get" action="<?php echo Route::url($this->page->link()); ?>">
	<p>
		<?php echo Lang::txt('COM_WIKI_SPECIAL_FIX_LENGTH_ABOUT'); ?>
	</p>
	<div class="container">
		<table class="entries">
			<thead>
				<tr>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_REVISION_ID'); ?>
					</th>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_PAGE_ID'); ?>
					</th>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_LENGTH'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if ($rows->count())
			{
				foreach ($rows as $row)
				{
					$row->set('length', strlen($row->get('pagetext')));
					$row->save();
					?>
					<tr>
						<td>
							<?php echo $row->get('id'); ?>
						</td>
						<td>
							<?php echo $row->get('page_id'); ?>
						</td>
						<td>
							<?php echo Lang::txt('COM_WIKI_HISTORY_BYTES', $row->get('length')); ?>
						</td>
					</tr>
					<?php
				}
			}
			else
			{
				?>
				<tr>
					<td colspan="3">
						<?php echo Lang::txt('COM_WIKI_NONE'); ?>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>

		<div class="clearfix"></div>
	</div>
</form>