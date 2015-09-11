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

$database = App::get('db');

$query = "SELECT COUNT(*)
			FROM #__wiki_version AS wv
			INNER JOIN #__wiki_page AS wp
				ON wp.id = wv.pageid
			WHERE wv.approved = 1
				" . ($this->page->get('scope') ? "AND wp.scope LIKE " . $database->quote($this->page->get('scope') . '%') . " " : "AND (wp.scope='' OR wp.scope IS NULL) ") . "
				AND wp.access != 2
				AND wp.state < 2
				AND wv.id = (SELECT MIN(wv2.id) FROM #__wiki_version AS wv2 WHERE wv2.pageid = wv.pageid)";

$database->setQuery($query);
$total = $database->loadResult();

$query = "SELECT wv.pageid, wp.title, wp.pagename, wp.scope, wp.group_cn, wp.access, wv.version, wv.created_by, wv.created, wv.summary
			FROM #__wiki_version AS wv
			INNER JOIN #__wiki_page AS wp
				ON wp.id = wv.pageid
			WHERE wv.approved = 1
				" . ($this->page->get('scope') ? "AND wp.scope LIKE " . $database->quote($this->page->get('scope') . '%') . " " : "AND (wp.scope='' OR wp.scope IS NULL) ") . "
				AND wp.access != 2
				AND wp.state < 2
				AND wv.id = (SELECT MIN(wv2.id) FROM #__wiki_version AS wv2 WHERE wv2.pageid = wv.pageid)
			ORDER BY $sort $dir";
if ($limit && $limit != 'all')
{
	$query .= " LIMIT $start, $limit";
}

$database->setQuery($query);
$rows = $database->loadObjectList();

$pageNav = $this->pagination(
	$total,
	$start,
	$limit
);

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
		$name = Lang::txt('COM_WIKI_UNKNOWN');
		$xprofile = \Hubzero\User\Profile::getInstance($row->created_by);
		if (is_object($xprofile))
		{
			$name = $this->escape(stripslashes($xprofile->get('name')));
			$name = ($xprofile->get('public') ? '<a href="' . Route::url($xprofile->getLink()) . '">' . $name . '</a>' : $name);
		}
?>
				<tr>
					<td>
						<time datetime="<?php echo $row->created; ?>"><?php echo $row->created; ?></time>
					</td>
					<td>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&scope=' . $row->scope . '&pagename=' . $row->pagename); ?>">
							<?php echo $this->escape(stripslashes($row->title)); ?>
						</a>
					</td>
					<td>
						<?php echo $name; ?>
					</td>
					<td>
						<span><?php echo $this->escape(stripslashes($row->summary)); ?></span>
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
		$pageNav->setAdditionalUrlParam('scope', $this->page->get('scope'));
		$pageNav->setAdditionalUrlParam('pagename', $this->page->get('pagename'));

		echo $pageNav->render();
		?>
		<div class="clearfix"></div>
	</div>
</form>