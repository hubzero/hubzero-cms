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

$pathway =& JFactory::getApplication()->getPathway();
$pathway->addItem(
	JText::_('Search'),
	'index.php?option=' . $this->option . '&scope=' . $this->page->scope . '&pagename=Special:Search'
);

$jconfig =& JFactory::getConfig();
$juser =& JFactory::getUser();

/*
$sort = strtolower(JRequest::getVar('sort', 'created'));
if (!in_array($sort, array('created', 'title', 'summary', 'created_by')))
{
	$sort = 'created';
}
$dir = strtoupper(JRequest::getVar('dir', 'DESC'));
if (!in_array($dir, array('ASC', 'DESC')))
{
	$dir = 'DESC';
}*/

$limit = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
$start = JRequest::getInt('limitstart', 0);
$term  = JRequest::getVar('q', '');

$database =& JFactory::getDBO();

$weight = '(match(wp.title) against (' . $database->Quote($term) . ') + match(wv.pagetext) against (' . $database->Quote($term) . '))';

$query = "SELECT COUNT(*) 
			FROM #__wiki_version AS wv 
			INNER JOIN #__wiki_page AS wp 
				ON wp.id = wv.pageid 
			WHERE wv.approved = 1 
				AND wp.group_cn = " . $database->Quote($this->page->group_cn) . " 
				AND $weight > 0
				AND wp.state < 2
				AND wv.id = wp.version_id 
			ORDER BY $weight DESC";

$database->setQuery($query);
$total = $database->loadResult();

//(SELECT MIN(wv2.id) FROM #__wiki_version AS wv2 WHERE wv2.pageid = wv.pageid)
$query = "SELECT wv.pageid, wp.title, wp.pagename, wp.scope, wp.group_cn, wp.access, wv.version, wv.created_by, wv.created AS modified, wv.summary 
			FROM #__wiki_version AS wv 
			INNER JOIN #__wiki_page AS wp 
				ON wp.id = wv.pageid 
			WHERE wv.approved = 1 
				AND wp.group_cn = " . $database->Quote($this->page->group_cn) . " 
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

jimport('joomla.html.pagination');
$pageNav = new JPagination(
	$total, 
	$start, 
	$limit
);

//$altdir = ($dir == 'ASC') ? 'DESC' : 'ASC';
?>
<div class="wikipage-search" style="margin-top: 2em">

	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&scope=' . $this->page->scope . '&pagename=Special:Search'); ?>" method="post">
		<div class="container data-entry">
			<input class="entry-search-submit" type="submit" value="<?php echo JText::_('Search'); ?>" />
			<fieldset class="entry-search">
				<legend><?php echo JText::_('Search pages'); ?></legend>
				<label for="entry-search-field"><?php echo JText::_('Enter keyword or phrase'); ?></label>
				<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($term); ?>" placeholder="<?php echo JText::_('Enter keyword or phrase'); ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="page" />
				<input type="hidden" name="pagename" value="Special:Search" />
				<input type="hidden" name="scope" value="<?php echo $this->page->scope; ?>" />
			</fieldset>
		</div><!-- / .container -->
	</form>

	<div class="container">
		<table class="file entries">
			<thead>
				<tr>
					<th scope="col">
						<?php echo JText::_('Title'); ?>
					</th>
					<th scope="col">
						<?php echo JText::_('Path'); ?>
					</th>
					<th scope="col">
						<?php echo JText::_('Last Modified'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
<?php
if ($rows) 
{
	ximport('Hubzero_User_Profile');

	$dateFormat = '%d %b %Y';
	$tz = 0;
	if (version_compare(JVERSION, '1.6', 'ge'))
	{
		$dateFormat = 'd M Y';
		$tz = true;
	}

	foreach ($rows as $row)
	{
		/*$name = JText::_('(unknown)');
		$xprofile = Hubzero_User_Profile::getInstance($row->created_by);
		if (is_object($xprofile))
		{
			$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $row->created_by) . '">' . $this->escape(stripslashes($xprofile->get('name'))) . '</a>';
		}*/
?>
				<tr>
					<td>
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&scope=' . $row->scope . '&pagename=' . $row->pagename); ?>">
							<?php echo $this->escape(stripslashes($row->title)); ?>
						</a>
					</td>
					<td>
						<?php echo JRoute::_('index.php?option=' . $this->option . '&scope=' . $row->scope . '&pagename=' . $row->pagename); ?>
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
		<div class="clear"></div>
	</div>
</div>
