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
	Lang::txt('COM_WIKI_SPECIAL_NEW_PAGES'),
	$this->page->link()
);

$sort = strtolower(Request::getVar('sort', 'created'));
if (!in_array($sort, array('created', 'title', 'summary', 'created_by')))
{
	$sort = 'created';
}
$dir = strtoupper(Request::getVar('dir', 'DESC'));
if (!in_array($dir, array('ASC', 'DESC')))
{
	$dir = 'DESC';
}

$limit = Request::getInt('limit', Config::get('list_limit'));
$start = Request::getInt('limitstart', 0);

$filters = array('state' => array(0, 1));

if ($space = Request::getVar('namespace', ''))
{
	$filters['namespace'] = urldecode($space);
}

$rows = $this->book->pages($filters)
	->including([
		'versions',
		function ($version)
		{
			$version
				->select('id')
				->select('page_id')
				->select('version')
				->select('created_by')
				->select('summary');
		}
	])
	->order('created', $dir)
	->paginated()
	->rows();

$altdir = ($dir == 'ASC') ? 'DESC' : 'ASC';
?>
<form method="get" action="<?php echo Route::url($this->page->link()); ?>">
	<p>
		<?php echo Lang::txt('COM_WIKI_SPECIAL_NEW_PAGES_ABOUT'); ?>
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
						<a<?php if ($sort == 'title') { echo ' class="active"'; } ?> href="<?php echo Route::url($this->page->link() . '&sort=title&dir=' . $altdir); ?>">
							<?php if ($sort == 'title') { echo ($dir == 'ASC') ? '&uarr;' : '&darr;'; } ?> <?php echo Lang::txt('COM_WIKI_COL_TITLE'); ?>
						</a>
					</th>
					<th scope="col">
						<a<?php if ($sort == 'created_by') { echo ' class="active"'; } ?> href="<?php echo Route::url($this->page->link() . '&sort=created_by&dir=' . $altdir); ?>">
							<?php if ($sort == 'created_by') { echo ($dir == 'ASC') ? '&uarr;' : '&darr;'; } ?> <?php echo Lang::txt('COM_WIKI_COL_CREATOR'); ?>
						</a>
					</th>
					<th scope="col">
						<a<?php if ($sort == 'summary') { echo ' class="active"'; } ?> href="<?php echo Route::url($this->page->link() . '&sort=summary&dir=' . $altdir); ?>">
							<?php if ($sort == 'summary') { echo ($dir == 'ASC') ? '&uarr;' : '&darr;'; } ?> <?php echo Lang::txt('COM_WIKI_COL_EDIT_SUMMARY'); ?>
						</a>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if ($rows)
			{
				foreach ($rows as $row)
				{
					$name = $this->escape(stripslashes($row->creator->get('name', Lang::txt('COM_WIKI_UNKNOWN'))));
					if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels()))
					{
						$name = '<a href="' . Route::url($row->creator->link()) . '">' . $name . '</a>';
					}
					?>
					<tr>
						<td>
							<time datetime="<?php echo $row->get('created'); ?>"><?php echo $row->get('created'); ?></time>
						</td>
						<td>
							<a href="<?php echo Route::url($row->link()); ?>">
								<?php echo $this->escape(stripslashes($row->title)); ?>
							</a>
						</td>
						<td>
							<?php echo $name; ?>
						</td>
						<td>
							<span><?php echo $this->escape(stripslashes($row->version->get('summary'))); ?></span>
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
		<?php
		$pageNav = $rows->pagination;
		$pageNav->setAdditionalUrlParam('scope', $this->page->get('scope'));
		$pageNav->setAdditionalUrlParam('pagename', $this->page->get('pagename'));

		echo $pageNav;
		?>
		<div class="clearfix"></div>
	</div>
</form>