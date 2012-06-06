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
</div>
<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last">
			<a class="tag" href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">
				<?php echo JText::_('COM_TAGS_MORE_TAGS'); ?>
			</a>
		</li>
	</ul>
</div>
<div class="clear"></div>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode("\n", $this->getErrors()); ?></p>
<?php } ?>

<div class="main section">
	<form action="index.php" method="post" id="hubForm">
		<div class="explaination">
			<p><?php echo JText::_('COM_TAGS_NORMALIZED_TAG_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_TAGS_DETAILS'); ?></legend>

			<label>
				<?php echo JText::_('COM_TAGS_TAG'); ?>
				<input type="text" name="fields[raw_tag]" value="<?php echo $this->escape(stripslashes($this->tag->raw_tag)); ?>" size="38" />
			</label>

			<label>
				<?php echo JText::_('COM_TAGS_COL_ALIAS'); ?>
				<input type="text" name="fields[alias]" value="<?php echo $this->escape(stripslashes($this->tag->alias)); ?>" size="38" />
			</label>

			<label>
				<input class="option" type="checkbox" name="fields[admin]" value="1" /> 
				<strong><?php echo JText::_('COM_TAGS_ADMINISTRATION'); ?></strong>
			</label>
			<p class="hint"><?php echo JText::_('COM_TAGS_ADMINISTRATION_EXPLANATION'); ?></p>
	
			<label>
				<?php echo JText::_('COM_TAGS_DESCRIPTION'); ?>
				<textarea name="fields[description]" rows="10" cols="35"><?php echo $this->escape(stripslashes($this->tag->description)); ?></textarea>
			</label>

			<input type="hidden" name="fields[tag]" value="<?php echo $this->tag->tag; ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->tag->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />
		</fieldset>
		<p class="submit"><input type="submit" value="<?php echo JText::_('COM_TAGS_SUBMIT'); ?>" /></p>
	</form>
</div><!-- / .main section -->
