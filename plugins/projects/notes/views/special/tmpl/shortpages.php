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
	JText::_('Short Pages'),
	'index.php?option=' . $this->option . '&scope=' . $this->page->scope . '&pagename=Special:ShortPages'
);

$jconfig = JFactory::getConfig();
$juser = JFactory::getUser();

$database = JFactory::getDBO();

$limit = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
$start = JRequest::getInt('limitstart', 0);

$query = "SELECT COUNT(*)
			FROM #__wiki_version AS wv
			INNER JOIN #__wiki_page AS wp
				ON wp.id = wv.pageid
			WHERE wv.approved = 1
				AND wp.scope = '{$this->page->scope}'
				AND wp.access != 1
				AND wv.id = (SELECT MIN(wv2.id) FROM #__wiki_version AS wv2 WHERE wv2.pageid = wv.pageid)";

$database->setQuery($query);
$total = $database->loadResult();

$query = "SELECT wv.pageid, wp.title, wv.length, wp.pagename, wp.scope, wp.group_cn, wp.access, wv.version, wv.created_by, wv.created
			FROM #__wiki_version AS wv
			INNER JOIN #__wiki_page AS wp
				ON wp.id = wv.pageid
			WHERE wv.approved = 1
				AND wp.scope = '{$this->page->scope}'
				AND wp.access != 1
				AND wv.id = (SELECT MIN(wv2.id) FROM #__wiki_version AS wv2 WHERE wv2.pageid = wv.pageid)
			ORDER BY length ASC";
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
?>
<form method="get" action="<?php echo JRoute::_('index.php?option=' . $this->option . '&scope=' . $this->page->scope . '&pagename=Special:ShortPages'); ?>">
	<p>
		This special page lists all pages in order of decreasing size. Related: <a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&scope=' . $this->page->scope . '&pagename=Special:LongPages'); ?>">Special: Long Pages</a>
	</p>
	<div class="container">
		<table class="file entries">
			<thead>
				<tr>
					<th scope="col">
						<?php echo JText::_('Date'); ?>
					</th>
					<th scope="col">
						<?php echo JText::_('Title'); ?>
					</th>
					<th scope="col">
						<?php echo JText::_('Created by'); ?>
					</th>
					<th scope="col">
						<?php echo JText::_('Length'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
<?php
if ($rows)
{
	$dateFormat = '%d %b %Y';
	$tz = 0;
	if (version_compare(JVERSION, '1.6', 'ge'))
	{
		$dateFormat = 'd M Y';
		$tz = true;
	}

	foreach ($rows as $row)
	{
		$name = JText::_('(unknown)');
		$xprofile = \Hubzero\User\Profile::getInstance($row->created_by);
		if (is_object($xprofile))
		{
			$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $row->created_by) . '">' . $this->escape(stripslashes($xprofile->get('name'))) . '</a>';
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
						<?php echo JText::sprintf('%s bytes', number_format($row->length)); ?>
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
						<?php echo JText::_('No pages found.'); ?>
					</td>
				</tr>
<?php
}
?>
			</tbody>
		</table>
<?php
$pageNav->setAdditionalUrlParam('scope', $this->page->scope);
$pageNav->setAdditionalUrlParam('pagename', $this->page->pagename);

echo $pageNav->getListFooter();
?>
		<div class="clearfix"></div>
	</div>
</form>