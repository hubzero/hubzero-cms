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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// define base links
$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->group->cn;

// define title
$text = ($this->task == 'edit' ? JText::_('Edit Page') : JText::_('New Page'));

// create toolbar
$canDo = GroupsHelper::getActions('group');
JToolBarHelper::title(JText::_('COM_GROUPS').': <small><small>[ ' . $text . ' ]</small></small>', 'groups.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

// include modal for raw version links
JHtml::_('behavior.modal', 'a.version', array('handler' => 'iframe', 'fullScreen'=>true));
?>

<form action="<?php echo $base; ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Page Details'); ?></span></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="field-title"><?php echo JText::_('Title'); ?>:</label></td>
						<td>
							<input type="text" name="page[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->page->get('title'))); ?>" />
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-alias"><?php echo JText::_('Alias'); ?>:</label></td>
						<td>
							<input type="text" name="page[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->page->get('alias'))); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Page Settings'); ?></span></legend>
			
			<table class="admintable">
				<tbody>
					<?php if ($this->page->get('id')) : ?>
						<tr>
							<td class="key"><label for="field-order"><?php echo JText::_('Order'); ?>:</label></td>
							<td>
								<select name="page[ordering]">
									<?php foreach($this->order as $k => $order) : ?>
										<?php $sel = ($order->get('title') == $this->page->get('title')) ? 'selected="selected"' : ''; ?>
										<option <?php echo $sel ;?> value="<?php echo $order->get('ordering'); ?>"><?php echo ($order->get('ordering') + 0) . '. '; ?><?php echo $order->get('title'); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
					<?php endif; ?>
					<tr>
						<td class="key"><label for="field-order"><?php echo JText::_('Category'); ?>:</label></td>
						<td>
							<select name="page[category]">
								<option value="">- Select Page Category &mdash;</option>
								<?php foreach ($this->categories as $pageCategory) : ?>
									<?php $sel = ($this->page->get('category') == $pageCategory->get('id')) ? 'selected="selected"' : ''; ?>
									<option <?php echo $sel; ?> value="<?php echo $pageCategory->get('id'); ?>"><?php echo $pageCategory->get('title'); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<?php if ($this->group->isSuperGroup()) : ?>
						<tr>
							<td class="key"><label for="field-order"><?php echo JText::_('Template'); ?>:</label></td>
							<td>
								<select name="page[template]">
									<option value="">- Default</option>
									<?php foreach ($this->pageTemplates as $name => $file) : ?>
										<?php
											$tmpl = str_replace('.php', '', $file);
											$sel  = ($this->page->get('template') == $tmpl) ? 'selected="selected"' : ''; ?>
										<option <?php echo $sel; ?> value="<?php echo $tmpl; ?>"><?php echo $name; ?></option>
									<?php endforeach;?>
								</select>
							</td>
						</tr>
					<?php endif; ?>
					<tr>
						<td class="key"><label for="field-order"><?php echo JText::_('Home'); ?>:</label></td>
						<td>
							<select name="page[home]">
								<option value="0" <?php if($this->page->get('home') == 0) { echo "selected"; } ?>>Use current home page</option>
								<option value="1" <?php if($this->page->get('home') == 1) { echo "selected"; } ?>>Set as home page</option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Page Access'); ?></span></legend>
			
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="field-state"><?php echo JText::_('Status'); ?>:</label></td>
						<td>
							<select name="page[state]">
								<?php
								$states = array(
									1 => JText::_('Published'),
									0 => JText::_('Unpublished'),
									2 => JText::_('Trashed')
								);
								
								foreach ($states as $k => $v)
								{
									$sel = ($this->page->get('state') == $k) ? 'selected="selected"' : '';
									echo '<option '.$sel.' value="'.$k.'">'.$v.'</option>';
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-privacy"><?php echo JText::_('Privacy'); ?>:</label></td>
						<td>
							<?php
								$access = \Hubzero\User\Group\Helper::getPluginAccess($this->group, 'overview');
								switch($access)
								{
									case 'anyone':		$name = "Any HUB Visitor";		break;
									case 'registered':	$name = "Registered HUB Users";	break;
									case 'members':		$name = "Group Members Only";	break;
								}
							?>
							<select name="page[privacy]">
								<option value="default" <?php if($this->page->get('privacy') == "default") { echo 'selected="selected"'; } ?>>Inherits overview tab's privacy setting (Currently set to: <?php echo $name; ?>)</option>
								<option value="members" <?php if($this->page->get('privacy') == "members") { echo 'selected="selected"'; } ?>>Private Page (Accessible to members only)</option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Page Content'); ?></span></legend>
			
			<table class="admintable">
				<tbody>
					<tr>
						<td>
							<label for="field-content"><?php echo JText::_('Content'); ?>:</label>
							<textarea name="pageversion[content]" rows="30"><?php echo $this->escape(stripslashes($this->version->get('content'))); ?></textarea>
							<input type="hidden" name="pageversion[version]" value="<?php echo $this->version->get('version'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	
	<div class="col width-50 fltrt">
		<?php if ($this->page->get('id')) : ?>
			<table class="meta" summary="Metadata">
				<tbody>
					<tr>
						<th><?php echo JText::_('Owner'); ?></th>
						<td><?php echo $this->group->get('description'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('ID'); ?></th>
						<td><?php echo $this->page->get('id'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('Current Version'); ?></th>
						<td><?php echo $this->version->get('version'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('Created'); ?></th>
						<td><?php echo JHTML::_('date', $this->firstversion->get('created'), 'F j, Y @ g:ia'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('Created By'); ?></th>
						<td>
							<?php
								$profile = \Hubzero\User\Profile::getInstance($this->firstversion->get('created_by'));
								echo (is_object($profile)) ? $profile->get('name') . ' (' . $profile->get('uidNumber') . ')' : JText::_('System');
							?>
						</td>
					</tr>
					<tr>
						<th><?php echo JText::_('Last Modified'); ?></th>
						<td><?php echo JHTML::_('date', $this->version->get('created'), 'F j, Y @ g:ia'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('Last Modified By'); ?></th>
						<td>
							<?php
								$profile = \Hubzero\User\Profile::getInstance($this->version->get('created_by'));
								echo (is_object($profile)) ? $profile->get('name') . ' (' . $profile->get('uidNumber') . ')' : JText::_('System');
							?>
						</td>
					</tr>
				
				</tbody>
			</table>
			
			<fieldset class="adminform">
				<legend><span><?php echo JText::_('Page Versions'); ?></span></legend>
			
				<table class="admintable">
					<thead>
						<tr>
							<th>#</th>
							<th>Created</th>
							<th>Approved</th>
							<th>View</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($this->page->versions() as $version) : ?>
							<tr>
								<td><?php echo $version->get('version'); ?></td>
								<td>
									<?php
										$profile = \Hubzero\User\Profile::getInstance($version->get('created_by'));
										echo 'by: ' . ((is_object($profile)) ? $profile->get('name') : JText::_('System'));
										echo '<br /> on: ' . JHTML::_('date', $version->get('created'));
									?>
								</td>
								<td>
									<?php
										if ($version->get('approved'))
										{
											$profile = \Hubzero\User\Profile::getInstance($version->get('approved_by'));
											echo 'by: ' . ((is_object($profile)) ? $profile->get('name') : JText::_('System'));
											echo '<br /> on: ' . JHTML::_('date', $version->get('approved_on'));
										}
										else
										{
											echo 'Not approved';
										}
									?>
								</td>
								<td>
									<a class="version" href="<?php echo $base; ?>&amp;task=raw&amp;pageid=<?php echo $this->page->get('id'); ?>&amp;version=<?php echo $version->get('version'); ?>">
										<?php echo JText::_('View Raw'); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</fieldset>
		<?php endif; ?>
	</div>
	
	<input type="hidden" name="page[id]" value="<?php echo $this->page->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="task" value="save" />
	<?php echo JHTML::_('form.token'); ?>
</form>