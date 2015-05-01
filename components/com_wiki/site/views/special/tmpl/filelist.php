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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

Pathway::append(
	Lang::txt('COM_WIKI_SPECIAL_FILE_LIST'),
	$this->page->link()
);

$database = JFactory::getDBO();

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

$limit = Request::getInt('limit', Config::get('list_limit'));
$start = Request::getInt('limitstart', 0);

$where = " AND (wp.group_cn='' OR wp.group_cn IS NULL) ";
if ($this->sub)
{
	$parts = explode('/', $this->page->get('scope'));
	$where = " AND wp.group_cn=" . $database->Quote(trim($parts[0])) . " ";
}

$query = "SELECT COUNT(*)
		FROM #__wiki_attachments AS wa
		INNER JOIN #__wiki_page AS wp
			ON wp.id=wa.pageid
		WHERE wp.scope LIKE " . $database->quote($this->page->get('scope') . '%') . " $where";

$database->setQuery($query);
$total = $database->loadResult();

$query = "SELECT wa.*, wp.scope, wp.pagename
		FROM #__wiki_attachments AS wa
		INNER JOIN #__wiki_page AS wp
			ON wp.id=wa.pageid
		WHERE wp.scope LIKE " . $database->quote($this->page->get('scope') . '%') . "
			$where
		ORDER BY $sort $dir";
if ($limit && $limit != 'all')
{
	$query .= " LIMIT $start, $limit";
}

$database->setQuery($query);
$rows = $database->loadObjectList();

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
			if ($rows)
			{
				jimport('joomla.filesystem.file');

				$database = \JFactory::getDBO();
				$asset = new \Components\Wiki\Tables\Attachment($database);

				foreach ($rows as $row)
				{
					$fsize = Lang::txt('COM_WIKI_UNKNOWN');
					if (is_file($asset->filespace() . DS . $row->pageid . DS . $row->filename))
					{
						$fsize = \Hubzero\Utility\Number::formatBytes(filesize($asset->filespace() . DS . $row->pageid . DS . $row->filename));
					}

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
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&scope=' . $row->scope . '/' . $row->pagename . '/File:' . $row->filename); ?>">
							<?php echo $this->escape(stripslashes($row->filename)); ?>
						</a>
					</td>
					<td>
						<?php
						if (in_array(strtolower(JFile::getExt($row->filename)), array('png', 'gif', 'jpg', 'jpeg', 'jpe'))) {
						?>
						<a rel="lightbox" href="<?php echo Route::url('index.php?option=' . $this->option . '&scope=' . $row->scope . '/' . $row->pagename . '/File:' . $row->filename); ?>">
							<img src="<?php echo Route::url('index.php?option=' . $this->option . '&scope=' . $row->scope . '/' . $row->pagename . '/File:' . $row->filename); ?>" width="50" alt="<?php echo $this->escape(stripslashes($row->filename)); ?>" />
						</a>
						<?php
						}
						?>
					</td>
					<td>
						<span><?php echo $fsize; ?></span>
					</td>
					<td>
						<?php echo $name; ?>
					</td>
					<td>
						<span><?php echo $this->escape(stripslashes($row->description)); ?></span>
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
		$pageNav = $this->pagination(
			$total,
			$start,
			$limit
		);
		$pageNav->setAdditionalUrlParam('scope', $this->page->get('scope'));
		$pageNav->setAdditionalUrlParam('pagename', $this->page->get('pagename'));
		$pageNav->setAdditionalUrlParam('sort', $sort);
		$pageNav->setAdditionalUrlParam('dir', $dir);

		echo $pageNav->render();
		?>
		<div class="clearfix"></div>
	</div>
</form>