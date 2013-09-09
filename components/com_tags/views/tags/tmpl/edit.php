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
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last">
			<a class="icon-tag tag btn" href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">
				<?php echo JText::_('COM_TAGS_MORE_TAGS'); ?>
			</a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->
<div class="clear"></div>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode("\n", $this->getErrors()); ?></p>
<?php } ?>

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p><?php echo JText::_('COM_TAGS_NORMALIZED_TAG_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_TAGS_DETAILS'); ?></legend>

			<label for="field-raw_tag">
				<?php echo JText::_('COM_TAGS_TAG'); ?>
				<input type="text" name="fields[raw_tag]" id="field-raw_tag" value="<?php echo $this->escape(stripslashes($this->tag->get('raw_tag'))); ?>" size="38" />
			</label>

			<label for="field-admin">
				<input class="option" type="checkbox" name="fields[admin]" id="field-admin" value="1" /> 
				<strong><?php echo JText::_('COM_TAGS_ADMINISTRATION'); ?></strong>
				<span class="hint">(<?php echo JText::_('COM_TAGS_ADMINISTRATION_EXPLANATION'); ?>)</span>
			</label>

			<label for="field-description">
				<?php echo JText::_('COM_TAGS_DESCRIPTION'); ?>
				<textarea name="fields[description]" id="field-description" rows="7" cols="35"><?php echo $this->escape(stripslashes($this->tag->get('description'))); ?></textarea>
			</label>

			<label for="field-substitutions">
				<?php echo JText::_('COM_TAGS_COL_ALIAS'); ?>
				<textarea name="fields[substitutions]" id="field-substitutions" rows="5" cols="35"><?php echo $this->escape(stripslashes($this->tag->substitutes('string'))); ?></textarea>
				<span class="hint"><?php echo JText::_('Enter a comma-separated list of tags you wish this tag to be substituted for. For example: If you enter "h20, aqua" for the tag "water", any time someone enters "h20" or "aqua" it will result in a tag of "water".'); ?></span>
			</label>

			<input type="hidden" name="fields[tag]" value="<?php echo $this->tag->get('tag'); ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->tag->get('id'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />
			
			<input type="hidden" name="limit" value="<?php echo $this->escape($this->filters['limit']); ?>" />
			<input type="hidden" name="limitstart" value="<?php echo $this->escape($this->filters['start']); ?>" />
			<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
			<input type="hidden" name="search" value="<?php echo $this->escape($this->filters['search']); ?>" />
		</fieldset>
		<p class="submit"><input type="submit" value="<?php echo JText::_('COM_TAGS_SUBMIT'); ?>" /></p>
	</form>
</div><!-- / .main section -->
