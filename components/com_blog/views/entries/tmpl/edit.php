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

//$juser =& JFactory::getUser();
/*if ($this->entry->id) {
	$lid = $this->entry->id;
} else {
	$lid = time().rand(0,10000);
}*/

JPluginHelper::importPlugin( 'hubzero' );
$dispatcher =& JDispatcher::getInstance();
$tf = $dispatcher->trigger(
	'onGetMultiEntry', 
	array(
		array('tags', 'tags', 'actags','', $this->entry->tags('string'))
	)
);

if ($this->entry->get('publish_down') && $this->entry->get('publish_down') == '0000-00-00 00:00:00')
{
	$this->entry->set('publish_down', '');
}
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>
<div id="content-header-extra">
	<p><a class="icon-archive archive btn" href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>"><?php echo JText::_('COM_BLOG_ARCHIVE'); ?></a></p>
</div>

<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=save'); ?>" method="post" id="hubForm">
		<div class="explaination">
			<table class="wiki-reference" summary="Wiki Syntax Reference">
				<caption>Wiki Syntax Reference</caption>
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
					<tr>
						<td colspan="2"><a class="wiki-macros image-macro" href="<?php echo JRoute::_('index.php?option=com_wiki&scope=&pagename=Help:WikiMacros#image'); ?>">[[Image(filename.jpg)]]</a> includes an image</td>
					</tr>
					<tr>
						<td colspan="2"><a class="wiki-macros file-macro" href="<?php echo JRoute::_('index.php?option=com_wiki&scope=&pagename=Help:WikiMacros#file'); ?>">[[File(filename.pdf)]]</a> includes a file</td>
					</tr>
				</tbody>
			</table>
			<h4 id="files-header"><?php echo JText::_('COM_BLOG_FIELD_FILES'); ?></h4>
			<iframe width="100%" height="370" name="filer" id="filer" src="index.php?option=<?php echo $this->option; ?>&amp;tmpl=component&amp;controller=media"></iframe>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_BLOG_EDIT_DETAILS'); ?></legend>

			<label for="field-title"<?php if ($this->task == 'save' && !$this->entry->get('title')) { echo ' class="fieldWithErrors"'; } ?>>
				<?php echo JText::_('COM_BLOG_FIELD_TITLE'); ?>
				<input type="text" name="entry[title]" id="field-title" size="35" value="<?php echo $this->escape(stripslashes($this->entry->get('title'))); ?>" />
			</label>

		<?php if ($this->task == 'save' && !$this->entry->get('title')) { ?>
			<p class="error"><?php echo JText::_('PLG_MEMBERS_BLOG_ERROR_PROVIDE_TITLE'); ?></p>
		<?php } ?>

			<label for="entrycontent">
				<?php echo JText::_('COM_BLOG_FIELD_CONTENT'); ?>
				<?php
				ximport('Hubzero_Wiki_Editor');
				$editor =& Hubzero_Wiki_Editor::getInstance();
				echo $editor->display('entry[content]', 'entrycontent', $this->escape(stripslashes($this->entry->get('content'))), '', '50', '40');
				?>
				<span class="hint"><a class="popup" href="<?php echo JRoute::_('index.php?option=com_wiki&scope=&pagename=Help:WikiFormatting'); ?>">Wiki formatting</a> is allowed.</span>
			</label>
		<?php if ($this->task == 'save' && !$this->entry->get('content')) { ?>
			<p class="error"><?php echo JText::_('PLG_MEMBERS_BLOG_ERROR_PROVIDE_CONTENT'); ?></p>
		<?php } ?>

			<label>
				<?php echo JText::_('COM_BLOG_FIELD_TAGS'); ?>
			<?php if (count($tf) > 0) {
				echo $tf[0];
			} else { ?>
				<input type="text" name="tags" value="<?php echo $this->entry->tags('string'); ?>" />
			<?php } ?>
				<span class="hint"><?php echo JText::_('COM_BLOG_FIELD_TAGS_HINT'); ?></span>
			</label>

			<div class="grid">
				<div class="col span-half">
					<label for="field-allow_comments">
						<input type="checkbox" class="option" name="entry[allow_comments]" id="field-allow_comments" value="1"<?php if ($this->entry->get('allow_comments') == 1) { echo ' checked="checked"'; } ?> /> 
						<?php echo JText::_('COM_BLOG_FIELD_ALLOW_COMMENTS'); ?>
					</label>
				</div>

				<div class="col span-half omega">
					<label for="field-state">
						<?php echo JText::_('COM_BLOG_FIELD_PRIVACY'); ?>
						<select name="entry[state]" id="field-state">
							<option value="1"<?php if ($this->entry->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_BLOG_FIELD_PRIVACY_PUBLIC'); ?></option>
							<option value="2"<?php if ($this->entry->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_BLOG_FIELD_PRIVACY_REGISTERED'); ?></option>
							<option value="0"<?php if ($this->entry->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_BLOG_FIELD_PRIVACY_PRIVATE'); ?></option>
						</select>
					</label>
				</div>
			</div>

			<div class="grid">
				<div class="col span-half">
					<label for="field-publish_up">
						<?php echo JText::_('COM_BLOG_FIELD_PUBLISH_UP'); ?>
						<input type="text" name="entry[publish_up]" class="datetime-field" id="field-publish_up" size="35" value="<?php echo $this->escape(stripslashes($this->entry->get('publish_up'))); ?>" />
					</label>
				</div>

				<div class="col span-half omega">
					<label for="field-publish_down">
						<?php echo JText::_('COM_BLOG_FIELD_PUBLISH_DOWN'); ?>
						<input type="text" name="entry[publish_down]" class="datetime-field" id="field-publish_down" size="35" value="<?php echo $this->escape(stripslashes($this->entry->get('publish_down'))); ?>" />
					</label>
				</div>
			</div>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="lid" value="<?php //echo $lid; ?>" />
		<input type="hidden" name="id" value="<?php echo $this->entry->get('created_by'); ?>" />
		<input type="hidden" name="entry[id]" value="<?php echo $this->entry->get('id'); ?>" />
		<input type="hidden" name="entry[alias]" value="<?php echo $this->entry->get('alias'); ?>" />
		<input type="hidden" name="entry[created]" value="<?php echo $this->entry->get('created'); ?>" />
		<input type="hidden" name="entry[created_by]" value="<?php echo $this->entry->get('created_by'); ?>" />
		<input type="hidden" name="entry[scope]" value="site" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="save" />

		<?php echo JHTML::_('form.token'); ?>
		
		<p class="submit">
			<input type="submit" value="<?php echo JText::_('COM_BLOG_SAVE'); ?>" />
<?php if ($this->entry->get('id')) { ?>
			<a href="<?php echo JRoute::_($this->entry->link()); ?>"><?php echo JText::_('Cancel'); ?></a>
<?php } ?>
		</p>
	</form>
</div><!-- / .section -->
