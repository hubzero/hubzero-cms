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
	JText::_('Fix Version'),
	$this->page->link()
);

$jconfig = JFactory::getConfig();
$juser = JFactory::getUser();

$database = JFactory::getDBO();

if (JRequest::getInt('force_fix', 0))
{
	$cn = JRequest::getVar('cn', '');
	$query = "UPDATE `#__wiki_page` SET version_id=0 WHERE " . ($cn ? "group_cn=" . $database->quote($cn) : "(group_cn='' OR group_cn IS NULL)");
	$database->setQuery($query);
	$database->query();
}

$query = "SELECT wv.pageid, wv.id AS versionid, wp.title, wp.pagename, wp.scope, wp.group_cn, wp.version_id, wv.version, wv.created_by, wv.created  
			FROM #__wiki_version AS wv 
			INNER JOIN #__wiki_page AS wp 
				ON wp.id = wv.pageid 
			WHERE wp.version_id = '0'
				AND wv.id = (SELECT MAX(wv2.id) FROM #__wiki_version AS wv2 WHERE wv2.pageid = wv.pageid)
			ORDER BY created DESC";

//$query = "SELECT wv.id, wv.pageid, wv.pagetext FROM #__wiki_page AS wv WHERE wv.length = '0'";

$database->setQuery($query);
$rows = $database->loadObjectList();

?>
<form method="get" action="<?php echo JRoute::_($this->page->link()); ?>">
	<p>
		This special page updates the version ID of a page to the latest approved version for every page.
	</p>
	<div class="container">
		<table class="entries">
			<thead>
				<tr>
					<th scope="col">
						<?php echo JText::_('Revision ID'); ?>
					</th>
					<th scope="col">
						<?php echo JText::_('Revision timestamp'); ?>
					</th>
					<th scope="col">
						<?php echo JText::_('Page ID'); ?>
					</th>
					<th scope="col">
						<?php echo JText::_('Page'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
<?php
if ($rows) 
{
	foreach ($rows as $row)
	{
		$database->setQuery("UPDATE #__wiki_page SET `version_id` = '" . $row->versionid . "', `modified`='" . $row->created . "' WHERE `id`='" . $row->pageid . "'");
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
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&scope=' . $row->scope . '&pagename=' . $row->pagename); ?>">
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
						<?php echo JText::_('No pages needed updating.'); ?>
					</td>
				</tr>
<?php
}
?>
			</tbody>
		</table>

		<div class="clearfix"></div>
	</div>
</form>