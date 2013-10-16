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

//add styles and scripts
ximport('Hubzero_Document');
Hubzero_Document::addPluginStylesheet('groups', $this->name);
Hubzero_Document::addPluginScript('groups', $this->name);
Hubzero_Document::addSystemScript('jquery.timepicker');
Hubzero_Document::addSystemStylesheet('jquery.datepicker.css');
Hubzero_Document::addSystemStylesheet('jquery.timepicker.css');
?>

<ul id="page_options">
	<li>
		<a class="btn add back" title="<?php echo JText::_('Back to Announcements'); ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=announcements'); ?>">
			<?php echo JText::_('Back to Announcements'); ?>
		</a>
	</li>
</ul>

<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=announcements'); ?>" method="post" id="hubForm">
		<div class="explaination">
			<table class="wiki-reference" summary="<?php echo JText::_('Wiki Syntax Reference'); ?>">
				<caption><?php echo JText::_('Wiki Syntax Reference'); ?></caption>
				<tbody>
					<tr>
						<td>'''bold'''</td>
						<td><b>bold</b></td>
					</tr>
					<tr>
						<td>''italic''</td>
						<td><i>italic</i></td>
					</tr>
					<tr>
						<td>__underline__</td>
						<td><span style="text-decoration:underline;">underline</span></td>
					</tr>
					<tr>
						<td>{{{monospace}}}</td>
						<td><code>monospace</code></td>
					</tr>
					<tr>
						<td>~~strike-through~~</td>
						<td><del>strike-through</del></td>
					</tr>
					<tr>
						<td>^superscript^</td>
						<td><sup>superscript</sup></td>
					</tr>
					<tr>
						<td>,,subscript,,</td>
						<td><sub>subscript</sub></td>
					</tr>
				</tbody>
			</table>
		</div><!-- /.aside -->
		
		<fieldset>
			<legend>
				<?php if ($this->announcement->get('id')) : ?>
						<?php echo JText::_('PLG_COURSES_ANNOUNCEMENTS_EDIT'); ?>
				<?php else : ?>
						<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_NEW'); ?>
				<?php endif; ?>
			</legend>

			<label for="field_content">
				<?php echo JText::_('Announcement'); ?> <span class="required">Required</span>
				<?php
					ximport('Hubzero_Wiki_Editor');
					$editor =& Hubzero_Wiki_Editor::getInstance();
					echo $editor->display('fields[content]', 'field_content', $this->escape(stripslashes($this->announcement->get('content'))), 'minimal no-footer', '35', '5');
				?>
			</label>

			<fieldset>
				<legend><?php echo JText::_('Publish window'); ?> <span class="optional"><?php echo JText::_('optional'); ?></span></legend>
				<div class="two columns first">
					<label for="field-publish_up" id="priority-publish_up">
						<?php echo JText::_('Start'); ?>
						<?php 
							$publish_up = $this->announcement->get('publish_up');
							if ($publish_up != '' && $publish_up != '0000-00-00 00:00:00')
							{
								$publish_up = date("m/d/Y @ g:i a", strtotime($publish_up));
							}
						?>
						<input class="datepicker" type="text" name="fields[publish_up]" id="field-publish_up" value="<?php echo $this->escape($publish_up); ?>" /> 
					</label>
				</div>
				<div class="two columns second">
					<label for="field-publish_down" id="priority-publish_down">
						<?php echo JText::_('End'); ?>
						<?php 
							$publish_down = $this->announcement->get('publish_down');
							if ($publish_down != '' && $publish_down != '0000-00-00 00:00:00')
							{
								$publish_down = date("m/d/Y @ g:i a", strtotime($publish_down));
							}
						?>
						<input class="datepicker" type="text" name="fields[publish_down]" id="field-publish_down" value="<?php echo $this->escape($publish_down); ?>" /> 
					</label>
				</div>
				<div class="clear"></div>
			</fieldset>
			
			<label for="field-email" id="email-label">
				<input class="option" type="checkbox" name="fields[email]" id="field-email" value="1" checked="checked" /> 
				<?php if ($this->announcement->sent == 1) : ?>
					<span class="important"><?php echo JText::_('Announcement already emailed, send again?'); ?></span>
				<?php else : ?>
					<?php echo JText::_('Email announcement to group members?'); ?>
				<?php endif; ?>
			</label>

			<label for="field-priority" id="priority-label">
				<input class="option" type="checkbox" name="fields[priority]" id="field-priority" value="1"<?php if ($this->announcement->get('priority')) { echo ' checked="checked"'; } ?> /> 
				<?php echo JText::_('Mark as high priority'); ?> <a href="javascript:;" class="tooltips" title="High Priority :: Marking an announcement as high priority will display the announcement in red.">?</a>
			</label>
			<label for="field-sticky" id="sticky-label">
				<input class="option" type="checkbox" name="fields[sticky]" id="field-sticky" value="1"<?php if ($this->announcement->get('sticky')) { echo ' checked="checked"'; } ?> /> 
				<?php echo JText::_('Sticky Announcement'); ?> <a href="javascript:;" class="tooltips" title="Sticky :: Marking an announcement sticky will make it show on every group page while active.">?</a>
			</label>

			<p class="submit">
				<input type="submit" value="<?php echo JText::_('Save'); ?>" />
			</p>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="fields[id]" value="<?php echo $this->announcement->get('id'); ?>" />
		<input type="hidden" name="fields[state]" value="1" />
		<input type="hidden" name="fields[scope]" value="<?php echo $this->announcement->get('scope'); ?>" />
		<input type="hidden" name="fields[scope_id]" value="<?php echo $this->announcement->get('scope_id'); ?>" />

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
		<input type="hidden" name="active" value="announcements" />
		<input type="hidden" name="action" value="save" />
		
	</form>
</div>