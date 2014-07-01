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

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="icon-tag tag btn" href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">
					<?php echo JText::_('COM_TAGS_MORE_TAGS'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode("\n", $this->getErrors()); ?></p>
<?php } ?>

<section class="main section">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p><?php echo JText::_('COM_TAGS_NORMALIZED_TAG_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_TAGS_DETAILS'); ?></legend>

			<label for="field-raw_tag">
				<?php echo JText::_('COM_TAGS_FIELD_TAG'); ?>
				<input type="text" name="fields[raw_tag]" id="field-raw_tag" data-error="<?php echo JText::_('COM_TAGS_FIELD_TAG_BLANK'); ?>" value="<?php echo $this->escape(stripslashes($this->tag->get('raw_tag'))); ?>" size="38" />
			</label>

			<label for="field-admin">
				<input class="option" type="checkbox" name="fields[admin]" id="field-admin" value="1" />
				<strong><?php echo JText::_('COM_TAGS_FIELD_ADMINISTRATION'); ?></strong>
				<span class="hint">(<?php echo JText::_('COM_TAGS_FIELD_ADMINISTRATION_EXPLANATION'); ?>)</span>
			</label>

			<label for="field-description">
				<?php echo JText::_('COM_TAGS_FIELD_DESCRIPTION'); ?>
				<textarea name="fields[description]" id="field-description" rows="7" cols="35"><?php echo $this->escape(stripslashes($this->tag->get('description'))); ?></textarea>
			</label>

			<label for="field-substitutions">
				<?php echo JText::_('COM_TAGS_FIELD_ALIAS'); ?>
				<textarea name="fields[substitutions]" id="field-substitutions" rows="5" cols="35"><?php echo $this->escape(stripslashes($this->tag->substitutes('string'))); ?></textarea>
				<span class="hint"><?php echo JText::_('COM_TAGS_FIELD_ALIAS_HINT'); ?></span>
			</label>

			<input type="hidden" name="fields[tag]" value="<?php echo $this->tag->get('tag'); ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->tag->get('id'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />

			<?php echo JHTML::_('form.token'); ?>

			<input type="hidden" name="limit" value="<?php echo $this->escape($this->filters['limit']); ?>" />
			<input type="hidden" name="limitstart" value="<?php echo $this->escape($this->filters['start']); ?>" />
			<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
			<input type="hidden" name="search" value="<?php echo $this->escape($this->filters['search']); ?>" />
		</fieldset>
		<p class="submit">
			<input type="submit" class="btn btn-success" value="<?php echo JText::_('COM_TAGS_SUBMIT'); ?>" />
			<a class="btn btn-secondary" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>">
				<?php echo JText::_('COM_TAGS_CANCEL'); ?>
			</a>
		</p>
	</form>
</section><!-- / .main section -->
