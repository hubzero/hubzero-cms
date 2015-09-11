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
	Lang::txt('COM_WIKI_SPECIAL_FIX_LINKS'),
	$this->page->link()
);

$limit = Request::getInt('limit', Config::get('list_limit'));
$start = Request::getInt('limitstart', 0);

$database = App::get('db');

$query = "SELECT COUNT(*)
			FROM #__wiki_page AS wp
			INNER JOIN #__wiki_version AS wv ON wp.version_id = wv.id";

$database->setQuery($query);
$total = $database->loadResult();

$query = "SELECT wp.id, wp.title, wp.pagename, wp.scope, wp.group_cn, wp.version_id, wv.created_by, wv.created, wv.pagetext
			FROM #__wiki_page AS wp
			INNER JOIN #__wiki_version AS wv ON wp.version_id = wv.id
			ORDER BY created DESC";
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
?>
<form method="get" action="<?php echo Route::url($this->page->link()); ?>">
	<p>
		<?php echo Lang::txt('COM_WIKI_SPECIAL_FIX_LINKS_ABOUT'); ?>
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
if ($rows)
{
	$p = \Components\Wiki\Helpers\Parser::getInstance();

	foreach ($rows as $row)
	{
		$wikiconfig = array(
			'option'   => $this->option,
			'scope'    => $row->scope,
			'pagename' => $row->pagename,
			'pageid'   => $row->id,
			'filepath' => '',
			'domain'   => $row->group_cn,
			'loglinks' => true
		);

		$row->pagehtml = $p->parse($row->pagetext, $wikiconfig, true, true);
?>
				<tr>
					<td>
						<?php echo $row->version_id; ?>
					</td>
					<td>
						<time datetime="<?php echo $row->created; ?>"><?php echo $row->created; ?></time>
					</td>
					<td>
						<?php echo $row->id; ?>
					</td>
					<td>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&scope=' . $row->scope . '&pagename=' . $row->pagename); ?>">
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
		<?php
		$pageNav->setAdditionalUrlParam('scope', $this->page->get('scope'));
		$pageNav->setAdditionalUrlParam('pagename', 'Special:' . $this->page->get('pagename'));

		echo $pageNav->render();
		?>
		<div class="clearfix"></div>
	</div>
</form>