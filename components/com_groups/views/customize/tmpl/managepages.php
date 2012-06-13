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

$base_link = 'index.php?option=com_groups&gid='.$this->group->get('cn').'&task=managepages';
?>

<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a class="group" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn')); ?>"><?php echo JText::_('Back to Group'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

	<?php
		foreach($this->notifications as $notification) {
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>
<div class="main section">
<form name="groupPages" action="index.php" method="POST" id="hubForm">
	<div class="explaination">
		<p>This is where you can manage any of your groups custom content pages. There is no limit to the number of custom pages and pages can contain text, images, links to files. The pages support wiki syntax that is used throughout the hub.</p>
		<p><a class="add" href="<?php echo JRoute::_($base_link.'&sub_task=add_page'); ?>">Add a New Group Page</a></p>
	</div>
	<fieldset>
		<h3>Manage Pages</h3>
		
		<fieldset>
			<legend>Your Active Pages</legend>
			<table>
				<?php if(count($this->active_pages) > 0) { ?>
					<?php $counter = 0; ?>
					<?php foreach($this->active_pages as $page) { ?>
						<?php $counter++; $cls = ($counter % 2 ==0) ? 'odd' : 'even'; ?>
						<tr class="<?php echo $cls; ?>">
							<td class="quick">
								<a title="Preview :: Preview this page" href="#" class="quick-view tooltips" rel="active-page-<?php echo $counter; ?>-preview">Quick View</a>
								<div id="active-page-<?php echo $counter; ?>-preview" class="preview">
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
									<a class="tooltips" title="Move Page Up :: Move '<?php echo $page['title']; ?>' up" href="<?php echo JRoute::_($base_link.'&sub_task=up_page&page='.$page['id']); ?>">Up</a>
								<?php } ?>
							</td>
							<td class="down">
								<?php if($page['order'] < $this->high_order_pages) { ?>
									<a class="tooltips" title="Move Page Down :: Move '<?php echo $page['title']; ?>' down"  href="<?php echo JRoute::_($base_link.'&sub_task=down_page&page='.$page['id']); ?>">Down</a>
								<?php } ?>
							</td>
							<td class="edit"><a class="tooltips" title="Edit :: Edit '<?php echo $page['title']; ?>'" href="<?php echo JRoute::_($base_link.'&sub_task=edit_page&page='.$page['id']); ?>">Edit</a></td>
							<td class="deactivate"><a class="tooltips" title="Deactivate :: Turn off '<?php echo $page['title']; ?>'" href="<?php echo JRoute::_($base_link.'&sub_task=deactivate_page&page='.$page['id']); ?>">Deactivate</a></td>
						</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="7">Currently this group does not have an active pages.</td>
					</tr>
				<?php } ?>
			</table>
		</fieldset>
		
		<fieldset>
			<legend>Your Inactive Pages</legend>
			<table>
				<?php if(count($this->inactive_pages) > 0) { ?>
					<?php $counter = 0; ?>
					<?php foreach($this->inactive_pages as $page) { ?>
						<?php $counter++; $cls = ($counter % 2 ==0) ? 'odd' : 'even'; ?>
						<tr class="<?php echo $cls; ?>">
							<td class="quick">
								<a title="Preview :: Preview this page" href="#" class="quick-view tooltips" rel="inactive-page-<?php echo $counter; ?>-preview">Quick View</a>
								<div id="inactive-page-<?php echo $counter; ?>-preview" class="preview">
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
									<a class="tooltips" title="Move Page Up :: Move '<?php echo $page['title']; ?>' up" href="<?php echo JRoute::_($base_link.'&sub_task=up_page&page='.$page['id']); ?>">Up</a>
								<?php } ?>
							</td>
							<td class="down">
								<?php if($page['order'] < $this->high_order_pages) { ?>
									<a class="tooltips" title="Move Page Down :: Move '<?php echo $page['title']; ?>' down"  href="<?php echo JRoute::_($base_link.'&sub_task=down_page&page='.$page['id']); ?>">Down</a>
								<?php } ?>
							</td>
							<td class="edit"><a class="tooltips" title="Edit :: Edit '<?php echo $page['title']; ?>'" href="<?php echo JRoute::_($base_link.'&sub_task=edit_page&page='.$page['id']); ?>">Edit</a></td>
							<td class="activate"><a class="tooltips" title="Activate :: Turn on '<?php echo $page['title']; ?>'" href="<?php echo JRoute::_($base_link.'&sub_task=activate_page&page='.$page['id']); ?>">Activate</a></td>
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
	
	<div class="explaination">
		<p>This is where you can manage any of your groups modules. These are the content blocks that appear on the right of the group overview page as well as any custom group pages which can be managed above. Some modules are pre built modules that do specific things like pull in a Twitter feed. You can also create custom modules which work similar to group pages in that there is no limit to the number of modules and they also support wiki syntax. If you would like to see a new group module type, we would love to hear your idea, <a href="/feedback">so send us feedback</a>.</p>
		<p><a class="add" href="<?php echo JRoute::_($base_link.'&sub_task=add_module'); ?>">Add a New Group Module</a></p>
	</div>
	<fieldset>
		<h3>Manage Modules</h3>
		
		<fieldset>
			<legend>Your Active Modules</legend>
			<table>
				<?php if(count($this->active_modules) > 0) { ?>
					<?php $counter = 0; ?>
					<?php foreach($this->active_modules as $module) { ?>
						<?php $counter++; $cls = ($counter % 2 ==0) ? 'odd' : 'even'; ?>
						<tr class="<?php echo $cls; ?>">
							<?php if($module['type'] == 'custom') { ?>
								<td class="quick">
									<a title="Preview :: Preview this module" href="#" class="quick-view tooltips" rel="active-module-<?php echo $counter; ?>-preview">Quick View</a>
									<div id="active-module-<?php echo $counter; ?>-preview" class="preview">
										<h3 class="header">Module Preview: <?php echo $module['type']; ?></h3>
										<div class="parsed">
											<?php echo $this->parser->parse( "\n".stripslashes($module['content']), $this->wikiconfig ); ?>
										</div>
									</div>
								</td>
							<?php } else { ?>
								<td class="quick"></td>
							<?php } ?>
							<td class="title"><?php echo ucfirst($module['type']) .' Module'; ?></td>
							<td class="order"><input type="text" disabled="disabled" value="<?php echo $module['morder']; ?>" /></td>
							<td class="up">
								<?php if($module['morder'] > 1) {?>
									<a class="tooltips" title="Module Up :: Move '<?php echo $module['type']; ?>' up in order" href="<?php echo JRoute::_($base_link.'&sub_task=up_module&module='.$module['id']); ?>">Up</a>
								<?php } ?>
							</td>
							<td class="down">
								<?php if($module['morder'] < $this->high_order_modules) { ?>
									<a class="tooltips" title="Module Down :: Move '<?php echo $module['type']; ?>' down in order"  href="<?php echo JRoute::_($base_link.'&sub_task=down_module&module='.$module['id']); ?>">Down</a>
								<?php } ?>
							</td>
							<td class="edit"><a class="tooltips" title="Edit :: Edit '<?php echo $module['type']; ?>'" href="<?php echo JRoute::_($base_link.'&sub_task=edit_module&module='.$module['id']); ?>">Edit</a></td>
							<td class="deactivate"><a class="tooltips" title="Deactivate :: Turn off '<?php echo $module['type'] ?>'" href="<?php echo JRoute::_($base_link.'&sub_task=deactivate_module&module='.$module['id']); ?>">Deactivate</a></td>
						</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="6">Currently this group does not have an active modules.</td>
					</tr>
				<?php } ?>
			</table>
		</fieldset>
		
		<fieldset>
			<legend>Your Inactive Modules</legend>
			<table>
				<?php if(count($this->inactive_modules) > 0) { ?>
					<?php $counter = 0; ?>
					<?php foreach($this->inactive_modules as $module) { ?>
						<?php $counter++; $cls = ($counter % 2 ==0) ? 'odd' : 'even'; ?>
						<tr class="<?php echo $cls; ?>">
							<?php if($module['type'] == 'custom') { ?>
								<td class="quick">
									<a title="Preview :: Preview this module" href="#" class="quick-view tooltips" rel="active-module-<?php echo $counter; ?>-preview">Quick View</a>
									<div id="active-module-<?php echo $counter; ?>-preview" class="preview">
										<h3 class="header">Module Preview: <?php echo $module['type']; ?></h3>
										<div class="parsed">
											<?php echo $this->parser->parse( "\n".stripslashes($module['content']), $this->wikiconfig ); ?>
										</div>
									</div>
								</td>
							<?php } else { ?>
								<td class="quick"></td>
							<?php } ?>
							<td class="title"><?php echo $module['type']; ?></td>
							<td class="order"><input type="text" disabled="disabled" value="<?php echo $module['morder']; ?>" /></td>
							<td class="up">
								<?php if($module['morder'] > 1) {?>
									<a class="tooltips" title="Module Up :: Move '<?php echo $module['type']; ?>' up in order" href="<?php echo JRoute::_($base_link.'&sub_task=up_module&module='.$module['id']); ?>">Up</a>
								<?php } ?>
							</td>
							<td class="down">
								<?php if($module['morder'] < $this->high_order_modules) { ?>
									<a class="tooltips" title="Module Down :: Move '<?php echo $module['type']; ?>' down in order"  href="<?php echo JRoute::_($base_link.'&sub_task=down_module&module='.$module['id']); ?>">Down</a>
								<?php } ?>
							</td>
							<td class="edit"><a class="tooltips" title="Edit :: Edit '<?php echo $module['type']; ?>'" href="<?php echo JRoute::_($base_link.'&sub_task=edit_module&module='.$module['id']); ?>">Edit</a></td>
							<td class="activate"><a href="<?php echo JRoute::_($base_link.'&sub_task=activate_module&module='.$module['id']); ?>">Activate</a></td>
						</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="7">Currently this group does not have an inactive modules.</td>
					</tr>
				<?php } ?>
			</table>
		</fieldset>
	</fieldset>
	<div class="clear"></div>
</form>
</div>