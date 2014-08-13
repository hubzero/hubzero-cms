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
	JText::_('All Pages'),
	$this->page->link()
);

$jconfig = JFactory::getConfig();
$juser = JFactory::getUser();

$dir = strtoupper(JRequest::getVar('dir', 'ASC'));
if (!in_array($dir, array('ASC', 'DESC')))
{
	$dir = 'ASC';
}

$database = JFactory::getDBO();

$where = '';
$namespace = urldecode(JRequest::getVar('namespace', ''));
if ($namespace)
{
	$where .= "AND LOWER(wp.pagename) LIKE " . $database->quote(strtolower($namespace) . '%');
}

if ($scp = $this->page->get('scope'))
{
	$scope = "AND (wp.scope LIKE " . $database->quote($scp . '%');
	if ($this->page->get('group_cn') && (!$namespace || $namespace == 'Help:'))
	{
		$scope .= " OR LOWER(wp.pagename) LIKE " . $database->quote('Help:%');
	}
	$scope .= ") ";
}
else
{
	$scope = "AND (wp.scope='' OR wp.scope IS NULL) ";
}

$query = "SELECT COUNT(*)
			FROM `#__wiki_version` AS wv
			INNER JOIN `#__wiki_page` AS wp
				ON wp.id = wv.pageid
			WHERE wv.approved = 1
				$scope
				AND wp.state < 2
				$where
				AND wv.id = (SELECT MAX(wv2.id) FROM `#__wiki_version` AS wv2 WHERE wv2.pageid = wv.pageid)";

$database->setQuery($query);
$total = $database->loadResult();

$query = "SELECT wv.pageid, (CASE WHEN (wp.`title` IS NOT NULL AND wp.`title` !='') THEN wp.`title` ELSE wp.`pagename` END) AS `title`, wp.pagename, wp.scope, wp.group_cn, wp.access, wv.version, wv.created_by, wv.created
			FROM `#__wiki_version` AS wv
			INNER JOIN `#__wiki_page` AS wp
				ON wp.id = wv.pageid
			WHERE wv.approved = 1
				$scope
				AND wp.state < 2
				$where
				AND wv.id = (SELECT MAX(wv2.id) FROM `#__wiki_version` AS wv2 WHERE wv2.pageid = wv.pageid)
			ORDER BY title $dir";

$database->setQuery($query);
$rows = $database->loadObjectList();
?>
	<form method="get" action="<?php echo JRoute::_($this->page->link()); ?>">
		<fieldset>
			<legend><?php echo JText::_('COM_WIKI_FILTER_LIST'); ?></legend>

			<label for="field-namespace">
				<?php echo JText::_('COM_WIKI_FIELD_NAMESPACE'); ?>
				<select name="namespace" id="field-namespace">
					<option value=""<?php if ($namespace == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_WIKI_ALL'); ?></option>
					<option value="Help:"<?php if ($namespace == 'Help:') { echo ' selected="selected"'; } ?>>Help:</option>
					<option value="Template:"<?php if ($namespace == 'Template:') { echo ' selected="selected"'; } ?>>Template:</option>
				</select>
			</label>

			<input type="submit" value="<?php echo JText::_('COM_WIKI_GO'); ?>" />
		</fieldset>

		<div class="grid">
<?php
if ($rows)
{
	$columns = array_chunk($rows, ceil(count($rows) / 3 ), true /* preserve keys */ );

	$index = '';
	$i = 0;
	foreach ($columns as $column)
	{
		switch ($i)
		{
			case 0: $cls = ''; break;
			case 1: $cls = ''; break;
			case 2: $cls = 'omega'; break;
		}
?>
			<div class="col span4 <?php echo $cls; ?>">
				<?php
			if (count($column) > 0)
			{
				$k = 0;
				foreach ($column as $row)
				{
					if ($this->page->get('group_cn') && !$row->scope)
					{
						$row->scope = $this->page->get('scope');
					}

					if (strtoupper(substr($row->title, 0, 1)) != $index)
					{
						$index = strtoupper(substr($row->title, 0, 1));
						if ($k != 0) {
						?>
						</ul>
						<?php } ?>
						<h3><?php echo $index; ?></h3>
						<ul>
						<?php
					}
					else if ($k == 0)
					{
						?>
						<h3><?php echo JText::sprintf('COM_WIKI_INDEX_CONTINUED', $index); ?></h3>
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
		</div>

		<hr />

		<h3><?php echo JText::_('COM_WIKI_SPECIAL_PAGES'); ?></h3>
		<ul>
		<?php
		foreach ($this->book->special() as $key => $page)
		{
			if ($page == strtolower($this->page->denamespaced()))
			{
				continue;
			}
			?>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&pagename=Special:' . ucfirst($page) . '&scope=' . $this->page->get('scope')); ?>">
						<?php echo 'Special:' . ucfirst($this->escape(stripslashes($page))); ?>
					</a>
				</li>
			<?php
		}
		?>
		</ul>
	</form>