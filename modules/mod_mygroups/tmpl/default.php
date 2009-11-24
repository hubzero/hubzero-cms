<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juser =& JFactory::getUser();
$groups = $modmygroups->groups;
?>
<div<?php echo ($modmygroups->moduleclass) ? ' class="'.$modmygroups->moduleclass.'"' : '';?>>
<?php if ($groups && count($groups) > 0) { ?>
	<ul class="compactlist">
<?php
	$i = 0;
	foreach ($groups as $group)
	{
		if ($group->published && $i < $modmygroups->limit) {
			$status = $modmygroups->getStatus( $group );
?>
		<li class="group">
			<a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$group->cn); ?>"><?php echo $group->description; ?></a>
			<span><span class="<?php echo $status; ?> status"><?php echo JText::_('MOD_MYGROUPS_STATUS_'.strtoupper($status)); ?></span></span>
		</li>
<?php
			$i++;
		}
	}
?>
	</ul>
<?php } else { ?>
	<p><?php echo JText::_('NO_GROUPS'); ?></p>
<?php } ?>

	<ul class="module-nav">
		<li><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=groups'); ?>"><?php echo JText::_('MOD_MYGROUPS_ALL_MY_GROUPS'); ?></a></li>
		<li><a href="<?php echo JRoute::_('index.php?option=com_groups'); ?>"><?php echo JText::_('MOD_MYGROUPS_ALL_GROUPS'); ?></a></li>
		<li><a href="<?php echo JRoute::_('index.php?option=com_groups&task=new'); ?>"><?php echo JText::_('MOD_MYGROUPS_NEW_GROUP'); ?></a></li>
	</ul>
</div>