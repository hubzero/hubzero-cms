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
defined('_JEXEC') or die( 'Restricted access' );

$base_link = 'index.php?option=com_groups&cn='.$this->group->get('cn').'&task=pages';
?>

<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a class="icon-group group btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn')); ?>"><?php echo JText::_('Back to Group'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	<?php foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $notification['message']; ?></p>
	<?php } ?>
<form name="groupPages" action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
	<div class="explaination">
		<p>This is where you can manage any of your groups custom content pages. There is no limit to the number of custom pages and pages can contain text, images, links to files. The pages support wiki syntax that is used throughout the hub.</p>
		<p><a class="icon-add add btn" href="<?php echo JRoute::_($base_link.'&task=addpage'); ?>">Add a New Group Page</a></p>
	</div>
	<fieldset>
		<legend>Manage Pages</legend>
		
		<fieldset class="group-pages">
			<legend>Your Active Pages</legend>
			<table>
				<?php if(count($this->active_pages) > 0) { ?>
					<?php $counter = 0; ?>
					<?php foreach($this->active_pages as $page) { ?>
						<?php $counter++; $cls = ($counter % 2 ==0) ? 'odd' : 'even'; ?>
						<tr class="<?php echo $cls; ?>">
							<td class="quick">
								<a title="Preview :: Preview this page" href="#active-page-<?php echo $counter; ?>-preview" class="quick-view tooltips" rel="">Quick View</a>
								<div id="active-page-<?php echo $counter; ?>-preview" class="group-page-preview">
									<h3 class="header">Page Preview: <?php echo $page['title']; ?></h3>
									<div class="parsed">
										<?php echo $this->parser->parse( "\n".stripslashes($page['content']), $this->wikiconfig ); ?>
									</div>
								</div>
							</td>
							<td class="title">
								<?php echo $page['title']; ?>
							</td>
							<td class="order"><input type="text" disabled="disabled" value="<?php echo $page['order']; ?>" /></td>
							<td class="up">
								<?php if($page['order'] > 1) {?>
									<a class="tooltips" title="Move Page Up :: Move '<?php echo $page['title']; ?>' up" href="<?php echo JRoute::_($base_link.'&task=uppage&page='.$page['id']); ?>">Up</a>
								<?php } ?>
							</td>
							<td class="down">
								<?php if($page['order'] < $this->high_order_pages) { ?>
									<a class="tooltips" title="Move Page Down :: Move '<?php echo $page['title']; ?>' down"  href="<?php echo JRoute::_($base_link.'&task=downpage&page='.$page['id']); ?>">Down</a>
								<?php } ?>
							</td>
							<td class="editpage"><a class="tooltips" title="Edit :: Edit '<?php echo $page['title']; ?>'" href="<?php echo JRoute::_($base_link.'&task=editpage&page='.$page['id']); ?>">Edit</a></td>
							<td class="deactivate"><a class="tooltips" title="Deactivate :: Turn off '<?php echo $page['title']; ?>'" href="<?php echo JRoute::_($base_link.'&task=deactivatepage&page='.$page['id']); ?>">Deactivate</a></td>
						</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="7">Currently this group does not have an active pages.</td>
					</tr>
				<?php } ?>
			</table>
		</fieldset>
		
		<fieldset class="group-pages">
			<legend>Your Inactive Pages</legend>
			<table>
				<?php if(count($this->inactive_pages) > 0) { ?>
					<?php $counter = 0; ?>
					<?php foreach($this->inactive_pages as $page) { ?>
						<?php $counter++; $cls = ($counter % 2 ==0) ? 'odd' : 'even'; ?>
						<tr class="<?php echo $cls; ?>">
							<td class="quick">
								<a title="Preview :: Preview this page" href="#inactive-page-<?php echo $counter; ?>-preview" class="quick-view tooltips" rel="">Quick View</a>
								<div id="inactive-page-<?php echo $counter; ?>-preview" class="group-page-preview">
									<h3 class="header">Page Preview: <?php echo $page['title']; ?></h3>
									<div class="parsed">
										<?php echo $this->parser->parse( "\n".stripslashes($page['content']), $this->wikiconfig ); ?>
									</div>
								</div>
							</td>
							<td class="title"><?php echo $page['title']; ?></td>
							<td class="order"><input type="text" disabled="disabled" value="<?php echo $page['order']; ?>" /></td>
							<td class="up">
								<?php if($page['order'] > 1) {?>
									<a class="tooltips" title="Move Page Up :: Move '<?php echo $page['title']; ?>' up" href="<?php echo JRoute::_($base_link.'&task=uppage&page='.$page['id']); ?>">Up</a>
								<?php } ?>
							</td>
							<td class="down">
								<?php if($page['order'] < $this->high_order_pages) { ?>
									<a class="tooltips" title="Move Page Down :: Move '<?php echo $page['title']; ?>' down"  href="<?php echo JRoute::_($base_link.'&task=downpage&page='.$page['id']); ?>">Down</a>
								<?php } ?>
							</td>
							<td class="editpage"><a class="tooltips" title="Edit :: Edit '<?php echo $page['title']; ?>'" href="<?php echo JRoute::_($base_link.'&task=editpage&page='.$page['id']); ?>">Edit</a></td>
							<td class="activate"><a class="tooltips" title="Activate :: Turn on '<?php echo $page['title']; ?>'" href="<?php echo JRoute::_($base_link.'&task=activatepage&page='.$page['id']); ?>">Activate</a></td>
						</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="7">Currently this group does not have an inactive pages.</td>
					</tr>
				<?php } ?>
			</table>
		</fieldset>
	</fieldset>
	<br class="clear" />
</form>
</div>