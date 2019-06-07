<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Pathway::append(
	Lang::txt('COM_WIKI_SPECIAL_FIX_VERSION'),
	$this->page->link()
);

$filters = array('state' => array(0, 1));

$pages = $this->book->pages($filters);

if (!Request::getInt('force_fix', 0))
{
	$pages->whereEquals('version_id', 0);
}

$rows = $pages->rows();
?>
<form method="get" action="<?php echo Route::url($this->page->link()); ?>">
	<p>
		<?php echo Lang::txt('COM_WIKI_SPECIAL_FIX_VERSION_ABOUT'); ?>
	</p>
	<div class="container">
		<table class="entries">
			<thead>
				<tr>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_REVISION_ID'); ?>
					</th>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_REVISION_TIME'); ?>
					</th>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_PAGE_ID'); ?>
					</th>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_PAGE'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if ($rows->count())
			{
				foreach ($rows as $row)
				{
					$version = $row->versions()
						->whereEquals('approved', 1)
						->order('version', 'desc')
						->row();

					if (!$version->get('id'))
					{
						$version->set('page_id', $row->get('id'));
						$version->set('pagetext', 'New page.');
						$version->set('approved', 1);
						$version->set('summary', 'Auto-created version.');
						$version->set('length', strlen($this->get('pagetext')));
						$version->save();
					}

					if ($version->get('id'))
					{
						$row->set('version_id', (int) $version->get('id'));
						$row->set('modified', $version->get('created'));
						$row->save();
					}
					?>
					<tr>
						<td>
							<?php echo $row->get('version_id'); ?>
						</td>
						<td>
							<time datetime="<?php echo $version->get('created'); ?>"><?php echo $version->get('created'); ?></time>
						</td>
						<td>
							<?php echo $row->get('id'); ?>
						</td>
						<td>
							<a href="<?php echo Route::url($row->link()); ?>">
								<?php echo $this->escape(stripslashes($row->title)); ?>
							</a>
						</td>
					</tr>
					<?php
				}
			}
			else
			{
				?>
				<tr>
					<td colspan="4">
						<?php echo Lang::txt('COM_WIKI_NONE'); ?>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
	</div>
</form>