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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>

<div id='content-header' class='full'>
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id='content-header-extra'>
<ul id='useroptions'>
	<li>
	<a class="icon-browse btn" href="<?php echo JRoute::_('index.php?option='. $this->option . '&controller=posts'); ?>"><?php echo JText::_('View Posts'); ?></a>
	</li>
	
	<li class="last">
		<a class="icon-add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=feeds&task=new'); ?>"><?php echo JText::_('Add Feed'); ?></a>
	</li>
</ul>
</div><!-- / #content-header-extra -->
<?php if (count($this->feeds) > 0): ?>
<div class='main section'> 
	<div id='page-main'>
	<form class='contentForm'>
	<table class='entries'>
		<thead class='table-head'>
				<th scope='col'>Name</th>
				<th scope='col'>URL</th>
				<th scope='col'>Actions</th>
		</thead>
		<tbody>	
<?php foreach($this->feeds as $feed): ?>
			<tr class='shade-table'>
			<td><?php echo $feed->name; ?></td>
			<td><a href='<?php echo $feed->url; ?>'><?php echo $feed->url; ?></a></td>
			<td><a class='btn' href='index.php?option=com_feedaggregator&controller=posts&task=PostsById&id=<?php echo $feed->id; ?>'>View Posts</a> 
				<a class='btn' href="index.php?option=com_feedaggregator&controller=feeds&task=edit&id=<?php echo $feed->id;?>">Edit</a>
				<?php if($feed->enabled == '1'):?>
					<a class='btn disableBtn' href='index.php?option=com_feedaggregator&controller=feeds&task=status&action=disable&id=<?php echo $feed->id;?>'>Disable</a>
				<?php elseif($feed->enabled == '0'): ?>
					<b><a class='btn' href='index.php?option=com_feedaggregator&controller=feeds&task=status&action=enable&id=<?php echo $feed->id;?>'>Enable</a></b>
				<?php endif; ?>
			</td>
			</tr>			
<?php endforeach; ?>
	</table>
<?php else: ?>
<p align='center'>There are no feeds here.</br>
<a class='icon-add add btn' href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=feeds&task=new'); ?>"><?php echo JText::_('Add Feed'); ?></a>
</p>
<?php endif; ?>
	</div>
	</form>
</div><!-- /.main section -->

