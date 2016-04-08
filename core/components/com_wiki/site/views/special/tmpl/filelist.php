<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Pathway::append(
	Lang::txt('COM_WIKI_SPECIAL_FILE_LIST'),
	$this->page->link()
);

$database = App::get('db');

$sort = strtolower(Request::getVar('sort', 'created'));
if (!in_array($sort, array('created', 'filename', 'description', 'created_by')))
{
	$sort = 'created';
}
$dir = strtoupper(Request::getVar('dir', 'DESC'));
if (!in_array($dir, array('ASC', 'DESC')))
{
	$dir = 'DESC';
}

$pages  = \Components\Wiki\Models\Page::blank()->getTableName();
$attach = \Components\Wiki\Models\Attachment::blank()->getTableName();

$rows = \Components\Wiki\Models\Attachment::all()
	->select($attach . '.*')
	->select($pages . '.pagename')
	->select($pages . '.path')
	->select($pages . '.scope')
	->select($pages . '.scope_id')
	->join($pages, $pages . '.id', $attach . '.page_id')
	->whereEquals($pages . '.scope', $this->book->get('scope'))
	->whereEquals($pages . '.scope_id', $this->book->get('scope_id'))
	->paginated()
	->rows();

$altdir = ($dir == 'ASC') ? 'DESC' : 'ASC';
?>
<form method="get" action="<?php echo Route::url($this->page->link()); ?>">
	<p>
		<?php echo Lang::txt('COM_WIKI_SPECIAL_FILE_LIST_ABOUT'); ?>
	</p>
	<div class="container">
		<table class="file entries">
			<thead>
				<tr>
					<th scope="col">
						<a<?php if ($sort == 'created') { echo ' class="active"'; } ?> href="<?php echo Route::url($this->page->link() . '&sort=created&dir=' . $altdir); ?>">
							<?php if ($sort == 'created') { echo ($dir == 'ASC') ? '&uarr;' : '&darr;'; } ?> <?php echo Lang::txt('COM_WIKI_COL_DATE'); ?>
						</a>
					</th>
					<th scope="col">
						<a<?php if ($sort == 'filename') { echo ' class="active"'; } ?> href="<?php echo Route::url($this->page->link() . '&sort=filename&dir=' . $altdir); ?>">
							<?php if ($sort == 'filename') { echo ($dir == 'ASC') ? '&uarr;' : '&darr;'; } ?> <?php echo Lang::txt('COM_WIKI_COL_NAME'); ?>
						</a>
					</th>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_PREVIEW'); ?>
					</th>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_SIZE'); ?>
					</th>
					<th scope="col">
						<a<?php if ($sort == 'created_by') { echo ' class="active"'; } ?> href="<?php echo Route::url($this->page->link() . '&sort=created_by&dir=' . $altdir); ?>">
							<?php if ($sort == 'created_by') { echo ($dir == 'ASC') ? '&uarr;' : '&darr;'; } ?> <?php echo Lang::txt('COM_WIKI_COL_UPLOADER'); ?>
						</a>
					</th>
					<th scope="col">
						<a<?php if ($sort == 'description') { echo ' class="active"'; } ?> href="<?php echo Route::url($this->page->link() . '&sort=description&dir=' . $altdir); ?>">
							<?php if ($sort == 'description') { echo ($dir == 'ASC') ? '&uarr;' : '&darr;'; } ?> <?php echo Lang::txt('COM_WIKI_COL_DESCRIPTION'); ?>
						</a>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if ($rows->count())
			{
				foreach ($rows as $row)
				{
					$fsize = Lang::txt('COM_WIKI_UNKNOWN');
					if (is_file($row->filespace() . DS . $row->get('page_id') . DS . $row->get('filename')))
					{
						$fsize = \Hubzero\Utility\Number::formatBytes(filesize($row->filespace() . DS . $row->get('page_id') . DS . $row->get('filename')));
					}

					$name = Lang::txt('COM_WIKI_UNKNOWN');

					$xprofile = \Hubzero\User\Profile::getInstance($row->get('created_by'));
					if (is_object($xprofile))
					{
						$name = $this->escape(stripslashes($xprofile->get('name')));
						$name = ($xprofile->get('public') ? '<a href="' . Route::url($xprofile->getLink()) . '">' . $name . '</a>' : $name);
					}
					?>
					<tr>
						<td>
							<time datetime="<?php echo $row->get('created'); ?>"><?php echo $row->get('created'); ?></time>
						</td>
						<td>
							<a href="<?php echo Route::url($this->page->link('base') . '&pagename=' . ($row->get('path') ? $row->get('path') . '/' : '') . $row->get('pagename') . '/File:' . $row->get('filename')); ?>">
								<?php echo $this->escape(stripslashes($row->get('filename'))); ?>
							</a>
						</td>
						<td>
							<?php if ($row->isImage()) { ?>
								<a rel="lightbox" href="<?php echo Route::url($this->page->link('base') . '&pagename=' . ($row->get('path') ? $row->get('path') . '/' : '') . $row->get('pagename') . '/File:' . $row->get('filename')); ?>">
									<img src="<?php echo Route::url($this->page->link('base') . '&pagename=' . ($row->get('path') ? $row->get('path') . '/' : '') . $row->get('pagename') . '/File:' . $row->get('filename')); ?>" width="50" alt="<?php echo $this->escape(stripslashes($row->get('filename'))); ?>" />
								</a>
							<?php } ?>
						</td>
						<td>
							<span><?php echo $fsize; ?></span>
						</td>
						<td>
							<?php echo $name; ?>
						</td>
						<td>
							<span><?php echo $this->escape(stripslashes($row->get('description'))); ?></span>
						</td>
					</tr>
					<?php
				}
			}
			else
			{
				?>
				<tr>
					<td colspan="6">
						<?php echo Lang::txt('COM_WIKI_NONE'); ?>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
		<?php
		$pageNav = $rows->pagination;
		$pageNav->setAdditionalUrlParam('scope', $this->page->get('path'));
		$pageNav->setAdditionalUrlParam('pagename', $this->page->get('pagename'));
		$pageNav->setAdditionalUrlParam('sort', $sort);
		$pageNav->setAdditionalUrlParam('dir', $dir);

		echo $pageNav;
		?>
		<div class="clearfix"></div>
	</div>
</form>