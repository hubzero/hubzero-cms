<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();

Pathway::append(
	Lang::txt('COM_WIKI_SPECIAL_SHORT_PAGES'),
	$this->page->link()
);

$database = App::get('db');

$limit = Request::getInt('limit', Config::get('list_limit'));
$start = Request::getInt('limitstart', 0);

$query = "SELECT COUNT(*)
			FROM `#__wiki_version` AS wv
			INNER JOIN `#__wiki_page` AS wp
				ON wp.id = wv.pageid
			WHERE wv.approved = 1
				" . ($this->page->get('scope') ? "AND wp.scope LIKE " . $database->quote($this->page->get('scope') . '%') . " " : "AND (wp.scope='' OR wp.scope IS NULL) ") . "
				AND wp.state < 2
				AND wp.access != 2
				AND wv.id = (SELECT MIN(wv2.id) FROM #__wiki_version AS wv2 WHERE wv2.pageid = wv.pageid)";

$database->setQuery($query);
$total = $database->loadResult();

$query = "SELECT wv.pageid, wp.title, wv.length, wp.pagename, wp.scope, wp.group_cn, wp.access, wv.version, wv.created_by, wv.created
			FROM `#__wiki_version` AS wv
			INNER JOIN `#__wiki_page` AS wp
				ON wp.id = wv.pageid
			WHERE wv.approved = 1
				" . ($this->page->get('scope') ? "AND wp.scope LIKE " . $database->quote($this->page->get('scope') . '%') . " " : "AND (wp.scope='' OR wp.scope IS NULL) ") . "
				AND wp.state < 2
				AND wp.access != 2
				AND wv.id = (SELECT MIN(wv2.id) FROM #__wiki_version AS wv2 WHERE wv2.pageid = wv.pageid)
			ORDER BY length ASC";
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
		<?php echo Lang::txt('COM_WIKI_SPECIAL_SHORT_PAGES_ABOUT', Route::url($this->page->link('base'))); ?>
	</p>
	<div class="container">
		<table class="file entries">
			<thead>
				<tr>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_DATE'); ?>
					</th>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_TITLE'); ?>
					</th>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_CREATOR'); ?>
					</th>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_LENGTH'); ?>
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
						<?php echo Lang::txt('COM_WIKI_HISTORY_BYTES', number_format($row->length)); ?>
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