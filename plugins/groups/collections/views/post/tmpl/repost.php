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

//tag editor
$task = 'post/' . $this->post_id . '/collect';
if ($this->collection_id)
{
	$task = JRequest::getVar('board', 0) . '/collect';
}
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name . '&scope=' . $task); ?>" method="post" id="hubForm" class="full">
	<fieldset>
		<legend><?php echo JText::_('Collect'); ?></legend>

		<div class="grid">
		<div class="col span-half">
		<label for="field-collection_id">
			<?php echo JText::_('Select collection'); ?>
			<select name="collection_id" id="field-collection_id">
				<option value="0"><?php echo JText::_('Select ...'); ?></option>
				<optgroup label="<?php echo JText::_('My collections'); ?>">
<?php
if ($this->myboards)
{
	foreach ($this->myboards as $board)
	{
		if ($board->id == $this->collection_id)
		{
			continue;
		}
?>
					<option value="<?php echo $this->escape($board->id); ?>"><?php echo $this->escape(stripslashes($board->title)); ?></option>
<?php
	}
}
?>
				</optgroup>
<?php
if ($this->groupboards)
{
	foreach ($this->groupboards as $optgroup => $boards)
	{
?>
				<optgroup label="<?php echo $this->escape(stripslashes($optgroup)); ?>">
<?php
		foreach ($boards as $board)
		{
			if ($board->id == $this->collection_id)
			{
				continue;
			}
?>
					<option value="<?php echo $this->escape($board->id); ?>"><?php echo $this->escape(stripslashes($board->title)); ?></option>
<?php
		}
?>
				</optgroup>
<?php
	}
}
?>
			</select>
		</label>
		</div>
		<p class="or">OR</p>
		<div class="col span-half omega">
			<label for="field-collection_title">
				<?php echo JText::_('Create collection'); ?>
				<input type="text" name="collection_title" id="field-collection_title" value="" />
			</label>
		</div>
		</div>

		<label for="field_description">
			<?php echo JText::_('Add a description'); ?>
			<?php echo $this->editor('description', '', 35, 5, 'field_description', array('class' => 'minimal no-footer')); ?>
		</label>
	</fieldset>

	<input type="hidden" name="post_id" value="<?php echo $this->post_id; ?>" />
	<input type="hidden" name="repost" value="1" />

	<input type="hidden" name="item_id" value="<?php echo $this->item_id; ?>" />
	<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="active" value="<?php echo $this->escape($this->name); ?>" />
	<input type="hidden" name="action" value="collect" />

	<?php echo JHTML::_('form.token'); ?>

	<p class="submit">
		<input type="submit" value="<?php echo JText::_('PLG_GROUPS_' . strtoupper($this->name) . '_POST'); ?>" />
	</p>
</form>
