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
$text = ($this->task == 'edit' ? JText::_('COM_GROUPS_PAGES_EDIT_PAGE') : JText::_('COM_GROUPS_PAGES_NEW_PAGE'));

// create toolbar
$canDo = GroupsHelper::getActions('group');
JToolBarHelper::title(JText::_('COM_GROUPS').': ' . $text, 'groups.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('page');

// include modal for raw version links
JHtml::_('behavior.modal', 'a.version', array('handler' => 'iframe', 'fullScreen'=>true));
?>

<form action="<?php echo $base; ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_GROUPS_PAGES_PAGE_DETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_GROUPS_PAGES_TITLE'); ?>:</label><br />
				<input type="text" name="page[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->page->get('title'))); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-alias"><?php echo JText::_('COM_GROUPS_PAGES_ALIAS'); ?>:</label><br />
				<input type="text" name="page[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->page->get('alias'))); ?>" />
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_GROUPS_PAGES_PAGE_SETTINGS'); ?></span></legend>
			<div class="input-wrap">
				<label for="field-category"><?php echo JText::_('COM_GROUPS_PAGES_CATEGORY'); ?>:</label><br />
				<select name="page[category]" id="field-category">
					<option value=""><?php echo JText::_('COM_GROUPS_PAGES_CATEGORY_OPTION_NULL'); ?></option>
					<?php foreach ($this->categories as $pageCategory) : ?>
						<?php $sel = ($this->page->get('category') == $pageCategory->get('id')) ? 'selected="selected"' : ''; ?>
						<option <?php echo $sel; ?> value="<?php echo $pageCategory->get('id'); ?>"><?php echo $pageCategory->get('title'); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<?php if (!$this->page->get('home')) : ?>
				<div class="input-wrap">
					<label for="field-order"><?php echo JText::_('COM_GROUPS_PAGES_PARENT'); ?>:</label><br />
					<select name="page[parent]" id="field-order">
						<?php foreach ($this->pages as $page) : ?>
							<?php if ($page->get('id') == $this->page->get('id')) { continue; } ?>
							<?php $sel = ($this->page->get('parent') == $page->get('id')) ? 'selected="selected"' : ''; ?>
							<option <?php echo $sel; ?> value="<?php echo $page->get('id'); ?>">
								<?php echo $page->heirarchyIndicator(' &ndash; ') . $page->get('title'); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>

			<?php if ($this->group->isSuperGroup()) : ?>
				<div class="input-wrap">
					<label for="field-order"><?php echo JText::_('COM_GROUPS_PAGES_TEMPLATE'); ?>:</label><br />
					<select name="page[template]" id="field-order">
						<option value=""><?php echo JText::_('COM_GROUPS_PAGES_TEMPLATE_OPTION_NULL'); ?></option>
						<?php foreach ($this->pageTemplates as $name => $file) : ?>
							<?php
								$tmpl = str_replace('.php', '', $file);
								$sel  = ($this->page->get('template') == $tmpl) ? 'selected="selected"' : ''; ?>
							<option <?php echo $sel; ?> value="<?php echo $tmpl; ?>"><?php echo $name; ?></option>
						<?php endforeach;?>
					</select>
				</div>
			<?php endif; ?>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_GROUPS_PAGES_PAGE_ACCESS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-state"><?php echo JText::_('COM_GROUPS_PAGES_STATE'); ?>:</label><br />
				<select name="page[state]" id="field-state" <?php if ($this->page->get('home') == 1) { echo 'disabled="disabled"'; } ?>>
					<?php
					$states = array(
						1 => JText::_('COM_GROUPS_PAGES_STATE_PUBLISHED'),
						0 => JText::_('COM_GROUPS_PAGES_STATE_UNPUBLISHED'),
						2 => JText::_('COM_GROUPS_PAGES_STATE_DELETED')
					);

					foreach ($states as $k => $v)
					{
						$sel = ($this->page->get('state') == $k) ? 'selected="selected"' : '';
						echo '<option '.$sel.' value="'.$k.'">'.$v.'</option>';
					}
					?>
				</select>
			</div>
			<div class="input-wrap">
				<label for="field-privacy"><?php echo JText::_('COM_GROUPS_PAGES_PRIVACY'); ?>:</label><br />
				<?php
					$access = \Hubzero\User\Group\Helper::getPluginAccess($this->group, 'overview');
					switch ($access)
					{
						case 'anyone':		$name = "Any HUB Visitor";		break;
						case 'registered':	$name = "Registered HUB Users";	break;
						case 'members':		$name = "Group Members Only";	break;
					}
				?>
				<select name="page[privacy]" id="page[privacy]">
					<option value="default" <?php if ($this->page->get('privacy') == "default") { echo 'selected="selected"'; } ?>>
						<?php echo JText::sprintf('COM_GROUPS_PAGES_PRIVACY_OPTION_INHERIT', $name); ?>
					</option>
					<option value="members" <?php if ($this->page->get('privacy') == "members") { echo 'selected="selected"'; } ?>>
						<?php echo JText::_('COM_GROUPS_PAGES_PRIVACY_OPTION_PRIVATE'); ?>
					</option>
				</select>
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_GROUPS_PAGES_PAGE_CONTENT'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-content"><?php echo JText::_('COM_GROUPS_PAGES_CONTENT'); ?>:</label><br />
				<textarea name="pageversion[content]" id="field-content" rows="30"><?php echo $this->escape(stripslashes($this->version->get('content'))); ?></textarea>
				<input type="hidden" name="pageversion[version]" value="<?php echo $this->version->get('version'); ?>" />
			</div>
		</fieldset>
	</div>

	<div class="col width-50 fltrt">
		<?php if ($this->page->get('id')) : ?>
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo JText::_('COM_GROUPS_PAGES_OWNER'); ?></th>
						<td><?php echo $this->group->get('description'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_GROUPS_PAGES_ID'); ?></th>
						<td><?php echo $this->page->get('id'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_GROUPS_PAGES_CURRENT_VERSION'); ?></th>
						<td><?php echo $this->version->get('version'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_GROUPS_PAGES_CREATED'); ?></th>
						<td><?php echo JHTML::_('date', $this->firstversion->get('created'), 'F j, Y @ g:ia'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_GROUPS_PAGES_CREATED_BY'); ?></th>
						<td>
							<?php
								$profile = \Hubzero\User\Profile::getInstance($this->firstversion->get('created_by'));
								echo (is_object($profile)) ? $profile->get('name') . ' (' . $profile->get('uidNumber') . ')' : JText::_('COM_GROUPS_PAGES_SYSTEM');
							?>
						</td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_GROUPS_PAGES_LAST_MODIFIED'); ?></th>
						<td><?php echo JHTML::_('date', $this->version->get('created'), 'F j, Y @ g:ia'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_GROUPS_PAGES_LAST_MODIFIED_BY'); ?></th>
						<td>
							<?php
								$profile = \Hubzero\User\Profile::getInstance($this->version->get('created_by'));
								echo (is_object($profile)) ? $profile->get('name') . ' (' . $profile->get('uidNumber') . ')' : JText::_('COM_GROUPS_PAGES_SYSTEM');
							?>
						</td>
					</tr>

				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo JText::_('COM_GROUPS_PAGES_PAGE_VERSIONS'); ?></span></legend>

				<table class="admintable">
					<thead>
						<tr>
							<th scope="col"><?php echo JText::_('COM_GROUPS_PAGES_VERSION_NUMBER'); ?></th>
							<th scope="col"><?php echo JText::_('COM_GROUPS_PAGES_VERSION_CREATED'); ?></th>
							<th scope="col"><?php echo JText::_('COM_GROUPS_PAGES_VERSION_APPROVED'); ?></th>
							<th scope="col"><?php echo JText::_('COM_GROUPS_PAGES_VERSION_VIEW'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($this->page->versions() as $version) : ?>
							<tr>
								<td><?php echo $version->get('version'); ?></td>
								<td>
									<?php
										$profile = \Hubzero\User\Profile::getInstance($version->get('created_by'));
										$name = ((is_object($profile)) ? $profile->get('name') : JText::_('COM_GROUPS_PAGES_SYSTEM'));
										echo JText::sprintf('COM_GROUPS_PAGES_VERSION_CREATED_DETAILS', $name, JHTML::_('date', $version->get('created')));
									?>
								</td>
								<td>
									<?php
										if ($version->get('approved'))
										{
											$profile = \Hubzero\User\Profile::getInstance($version->get('approved_by'));
											$name = ((is_object($profile)) ? $profile->get('name') : JText::_('COM_GROUPS_PAGES_SYSTEM'));
											echo JText::sprintf('COM_GROUPS_PAGES_VERSION_APPROVED_DETAILS', $name, JHTML::_('date', $version->get('approved_on')));
										}
										else
										{
											echo JText::_('COM_GROUPS_PAGES_VERSION_NOT_APPROVED');
										}
									?>
								</td>
								<td>
									<a class="version" href="<?php echo $base; ?>&amp;task=raw&amp;pageid=<?php echo $this->page->get('id'); ?>&amp;version=<?php echo $version->get('version'); ?>">
										<?php echo JText::_('COM_GROUPS_PAGES_VERSION_VIEW_RAW'); ?>
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