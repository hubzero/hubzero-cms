<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$pathway = JFactory::getApplication()->getPathway();
$pathway->addItem(
	JText::_('COM_WIKI_SPECIAL_NEW_PAGES'),
	$this->page->link()
);

$jconfig = JFactory::getConfig();
$juser = JFactory::getUser();

$sort = strtolower(JRequest::getVar('sort', 'created'));
if (!in_array($sort, array('created', 'title', 'summary', 'created_by')))
{
	$sort = 'created';
}
$dir = strtoupper(JRequest::getVar('dir', 'DESC'));
if (!in_array($dir, array('ASC', 'DESC')))
{
	$dir = 'DESC';
}

$limit = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
$start = JRequest::getInt('limitstart', 0);

$database = JFactory::getDBO();

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

jimport('joomla.html.pagination');
$pageNav = new JPagination(
	$total,
	$start,
	$limit
);

$altdir = ($dir == 'ASC') ? 'DESC' : 'ASC';
?>
<form method="get" action="<?php echo JRoute::_($this->page->link()); ?>">
	<p>
		<?php echo JText::_('COM_WIKI_SPECIAL_NEW_PAGES_ABOUT'); ?>
	</p>
	<div class="container">
		<table class="file entries">
			<thead>
				<tr>
					<th scope="col">
						<a<?php if ($sort == 'created') { echo ' class="active"'; } ?> href="<?php echo JRoute::_($this->page->link() . '&sort=created&dir=' . $altdir); ?>">
							<?php if ($sort == 'created') { echo ($dir == 'ASC') ? '&uarr;' : '&darr;'; } ?> <?php echo JText::_('COM_WIKI_COL_DATE'); ?>
						</a>
					</th>
					<th scope="col">
						<a<?php if ($sort == 'title') { echo ' class="active"'; } ?> href="<?php echo JRoute::_($this->page->link() . '&sort=title&dir=' . $altdir); ?>">
							<?php if ($sort == 'title') { echo ($dir == 'ASC') ? '&uarr;' : '&darr;'; } ?> <?php echo JText::_('COM_WIKI_COL_TITLE'); ?>
						</a>
					</th>
					<th scope="col">
						<a<?php if ($sort == 'created_by') { echo ' class="active"'; } ?> href="<?php echo JRoute::_($this->page->link() . '&sort=created_by&dir=' . $altdir); ?>">
							<?php if ($sort == 'created_by') { echo ($dir == 'ASC') ? '&uarr;' : '&darr;'; } ?> <?php echo JText::_('COM_WIKI_COL_CREATOR'); ?>
						</a>
					</th>
					<th scope="col">
						<a<?php if ($sort == 'summary') { echo ' class="active"'; } ?> href="<?php echo JRoute::_($this->page->link() . '&sort=summary&dir=' . $altdir); ?>">
							<?php if ($sort == 'summary') { echo ($dir == 'ASC') ? '&uarr;' : '&darr;'; } ?> <?php echo JText::_('COM_WIKI_COL_EDIT_SUMMARY'); ?>
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
		$name = JText::_('COM_WIKI_UNKNOWN');
		$xprofile = \Hubzero\User\Profile::getInstance($row->created_by);
		if (is_object($xprofile))
		{
			$name = $this->escape(stripslashes($xprofile->get('name')));
			$name = ($xprofile->get('public') ? '<a href="' . JRoute::_($xprofile->getLink()) . '">' . $name . '</a>' : $name);
		}
?>
				<tr>
					<td>
						<time datetime="<?php echo $row->created; ?>"><?php echo $row->created; ?></time>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&scope=' . $row->scope . '&pagename=' . $row->pagename); ?>">
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
						<?php echo JText::_('COM_WIKI_NONE'); ?>
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

		echo $pageNav->getListFooter();
		?>
		<div class="clearfix"></div>
	</div>
</form>