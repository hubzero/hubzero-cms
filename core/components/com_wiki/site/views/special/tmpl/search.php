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
	Lang::txt('COM_WIKI_SEARCH'),
	'index.php?option=' . $this->option . '&scope=' . $this->page->get('scope') . '&pagename=Special:Search'
);

$database = App::get('db');

$limit = Request::getInt('limit', Config::get('list_limit'));
$start = Request::getInt('limitstart', 0);
$term  = Request::getVar('q', '');

$weight = '(match(wp.title) against (' . $database->Quote($term) . ') + match(wv.pagetext) against (' . $database->Quote($term) . '))';

$query = "SELECT COUNT(*)
			FROM `#__wiki_version` AS wv
			INNER JOIN `#__wiki_page` AS wp
				ON wp.id = wv.pageid
			WHERE wv.approved = 1
				AND wp.group_cn = " . $database->Quote($this->page->get('group_cn')) . "
				AND $weight > 0
				AND wp.state < 2
				AND wv.id = wp.version_id
			ORDER BY $weight DESC";

$database->setQuery($query);
$total = $database->loadResult();

$query = "SELECT wv.pageid, wp.title, wp.pagename, wp.scope, wp.group_cn, wp.access, wv.version, wv.created_by, wv.created AS modified, wv.summary
			FROM `#__wiki_version` AS wv
			INNER JOIN `#__wiki_page` AS wp
				ON wp.id = wv.pageid
			WHERE wv.approved = 1
				AND wp.group_cn = " . $database->Quote($this->page->get('group_cn')) . "
				AND $weight > 0
				AND wp.state < 2
				AND wv.id = wp.version_id
			ORDER BY $weight DESC";
if ($limit && $limit != 'all')
{
	$query .= " LIMIT $start, $limit";
}

$database->setQuery($query);
$rows = $database->loadObjectList();
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&scope=' . $this->page->get('scope') . '&pagename=Special:Search'); ?>" method="get">
	<div class="container data-entry">
		<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_WIKI_SEARCH'); ?>" />
		<fieldset class="entry-search">
			<legend><?php echo Lang::txt('COM_WIKI_SEARCH_LEGEND'); ?></legend>
			<label for="entry-search-field"><?php echo Lang::txt('COM_WIKI_SEARCH_PLACEHOLDER'); ?></label>
			<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($term); ?>" placeholder="<?php echo Lang::txt('COM_WIKI_SEARCH_PLACEHOLDER'); ?>" />
			<!--
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="page" />
			<input type="hidden" name="pagename" value="Special:Search" />
			<input type="hidden" name="scope" value="<?php echo $this->escape($this->page->get('scope')); ?>" />
			-->
		</fieldset>
	</div><!-- / .container -->

	<div class="container">
		<table class="file entries">
			<thead>
				<tr>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_TITLE'); ?>
					</th>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_PATH'); ?>
					</th>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_MODIFIED'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if ($rows)
			{
				foreach ($rows as $row)
				{
			?>
				<tr>
					<td>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&scope=' . $row->scope . '&pagename=' . $row->pagename); ?>">
							<?php echo $this->escape(stripslashes($row->title)); ?>
						</a>
					</td>
					<td>
						<?php echo Route::url('index.php?option=' . $this->option . '&scope=' . $row->scope . '&pagename=' . $row->pagename); ?>
					</td>
					<td>
						<time datetime="<?php echo $row->modified; ?>"><?php echo $row->modified; ?></time>
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
		<?php
		$pageNav = $this->pagination(
			$total,
			$start,
			$limit
		);
		$pageNav->setAdditionalUrlParam('scope', $this->page->get('scope'));
		$pageNav->setAdditionalUrlParam('pagename', $this->page->get('pagename'));

		echo $pageNav->render();
		?>
		<div class="clear"></div>
	</div>
</form>