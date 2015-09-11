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
	Lang::txt('COM_WIKI_SPECIAL_FIX_VERSION'),
	$this->page->link()
);

$database = App::get('db');

if (Request::getInt('force_fix', 0))
{
	$cn = Request::getVar('cn', '');
	$query = "UPDATE `#__wiki_page` SET version_id=0 WHERE " . ($cn ? "group_cn=" . $database->quote($cn) : "(group_cn='' OR group_cn IS NULL)");
	$database->setQuery($query);
	$database->query();
}

$query = "SELECT wv.pageid, wv.id AS versionid, wp.title, wp.pagename, wp.scope, wp.group_cn, wp.version_id, wv.version, wv.created_by, wv.created
			FROM `#__wiki_version` AS wv
			INNER JOIN `#__wiki_page` AS wp
				ON wp.id = wv.pageid
			WHERE wp.version_id = '0'
				AND wv.id = (SELECT MAX(wv2.id) FROM `#__wiki_version` AS wv2 WHERE wv2.pageid = wv.pageid)
			ORDER BY created DESC";

$database->setQuery($query);
$rows = $database->loadObjectList();
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
			if ($rows)
			{
				foreach ($rows as $row)
				{
					$database->setQuery("UPDATE `#__wiki_page` SET `version_id` = " . $database->quote($row->versionid) . ", `modified`=" . $database->quote($row->created) . " WHERE `id`=" . $database->quote($row->pageid));
					if (!$database->query())
					{
						$this->setError($database->getErrorMsg());
					}
			?>
				<tr>
					<td>
						<?php echo $row->versionid; ?>
					</td>
					<td>
						<time datetime="<?php echo $row->created; ?>"><?php echo $row->created; ?></time>
					</td>
					<td>
						<?php echo $row->pageid; ?>
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
	</div>
</form>