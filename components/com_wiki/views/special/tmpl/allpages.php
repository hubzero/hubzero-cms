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
	JText::_('All Pages'),
	'index.php?option=' . $this->option . '&scope=' . $this->page->scope . '&pagename=Special:NewPages'
);

$jconfig =& JFactory::getConfig();
$juser =& JFactory::getUser();

$dir = strtoupper(JRequest::getVar('dir', 'ASC'));
if (!in_array($dir, array('ASC', 'DESC')))
{
	$dir = 'ASC';
}

$database =& JFactory::getDBO();

$where = '';
$namespace = urldecode(JRequest::getVar('namespace', ''));
if ($namespace)
{
	$where .= "AND LOWER(wp.pagename) LIKE '" . $database->getEscaped(strtolower($namespace)) . "%'";
}

$query = "SELECT COUNT(*) 
			FROM #__wiki_version AS wv 
			INNER JOIN #__wiki_page AS wp 
				ON wp.id = wv.pageid 
			WHERE wv.approved = 1 
				AND wp.scope = " . $database->Quote($this->page->scope) . " 
				AND wp.state < 2
				$where
				AND wv.id = (SELECT MAX(wv2.id) FROM #__wiki_version AS wv2 WHERE wv2.pageid = wv.pageid)";

$database->setQuery($query);
$total = $database->loadResult();

$query = "SELECT wv.pageid, (CASE WHEN (wp.`title` IS NOT NULL AND wp.`title` !='') THEN wp.`title` ELSE wp.`pagename` END) AS `title`, wp.pagename, wp.scope, wp.group_cn, wp.access, wv.version, wv.created_by, wv.created
			FROM #__wiki_version AS wv 
			INNER JOIN #__wiki_page AS wp 
				ON wp.id = wv.pageid 
			WHERE wv.approved = 1 
				AND wp.scope = " . $database->Quote($this->page->scope) . " 
				AND wp.state < 2
				$where
				AND wv.id = (SELECT MAX(wv2.id) FROM #__wiki_version AS wv2 WHERE wv2.pageid = wv.pageid)
			ORDER BY title $dir";

$database->setQuery($query);
$rows = $database->loadObjectList();
?>
<form method="get" action="<?php echo JRoute::_('index.php?option=' . $this->option . '&scope=' . $this->page->scope . '&pagename=Special:AllPages'); ?>">
	<div class="wikipage">
	<fieldset>
		<legend><?php echo JText::_('Filter list'); ?></legend>
		
		<label for="field-namespace">
			<?php echo JText::_('Namespace'); ?>
			<select name="namespace" id="field-namespace">
				<option value=""<?php if ($namespace == '') { echo ' selected="selected"'; } ?>>all</option>
				<option value="Help:"<?php if ($namespace == 'Help:') { echo ' selected="selected"'; } ?>>Help:</option>
				<option value="Template:"<?php if ($namespace == 'Template:') { echo ' selected="selected"'; } ?>>Template:</option>
			</select>
		</label>
		
		<input type="submit" value="<?php echo JText::_('Go'); ?>" />
	</fieldset>
	
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

	$columns = array_chunk($rows, ceil(count($rows) / 3 ), true /* preserve keys */ );

	$index = '';
	$i = 0;
	foreach ($columns as $column)
	{
		switch ($i)
		{
			case 0: $cls = 'first'; break;
			case 1: $cls = 'second'; break;
			case 2: $cls = 'third'; break;
		}
?>
		<div class="three columns <?php echo $cls; ?>">
			<?php
		if (count($column) > 0)
		{
			$k = 0;
			foreach ($column as $row)
			{
				if (strtoupper(substr($row->title, 0, 1)) != $index)
				{
					$index = strtoupper(substr($row->title, 0, 1));
					?>
					</ul>
					<h3><?php echo $index; ?></h3>
					<ul>
					<?php
				} 
				else if ($k == 0)
				{
					?>
					<h3><?php echo $index; ?> contd.</h3>
					<ul>
					<?php
				}
			?>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&pagename=' . $row->pagename . '&scope=' . $row->scope); ?>">
						<?php echo $this->escape(stripslashes($row->title)); ?>
					</a>
				</li>
			<?php
				$k++;
			}
			?>
			</ul>
		<?php 
		}
		?>
		</div>
<?php
		$i++;
	}
}
?>
		<div class="clear"></div>
	</div>
</form>
