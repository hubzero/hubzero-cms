<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	<p><?php echo JText::_('MOD_MYGROUPS_NO_GROUPS'); ?></p>
<?php } ?>

	<ul class="module-nav">
		<li><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=groups'); ?>"><?php echo JText::_('MOD_MYGROUPS_ALL_MY_GROUPS'); ?></a></li>
		<li><a href="<?php echo JRoute::_('index.php?option=com_groups'); ?>"><?php echo JText::_('MOD_MYGROUPS_ALL_GROUPS'); ?></a></li>
		<li><a href="<?php echo JRoute::_('index.php?option=com_groups&task=new'); ?>"><?php echo JText::_('MOD_MYGROUPS_NEW_GROUP'); ?></a></li>
	</ul>
</div>

