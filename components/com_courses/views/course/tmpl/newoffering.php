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

if (!$this->no_html) {
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-prev btn" href="<?php echo $this->course->link(); ?>"><?php echo JText::_('COM_COURSES_BACK'); ?></a>
		</p>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
<?php } ?>

	<?php
		foreach ($this->notifications as $notification) {
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>

	<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
		<?php if (!$this->no_html) { ?>
			<div class="explaination">
				<p><?php echo JText::_('COM_COURSES_NEW_OFFERING_EXPLANATION'); ?></p>
			</div>
		<?php } ?>
		<fieldset>
			<legend><?php echo JText::_('COM_COURSES_FIELDSET_NEW_OFFERING'); ?></legend>

			<label for="field-alias">
				<?php echo JText::_('COM_COURSES_FIELD_OFFERING_ALIAS'); ?>
				<input name="offering[alias]" id="field-alias" type="text" size="35" value="<?php echo $this->escape($this->offering->get('alias')); ?>" />
				<span class="hint"><?php echo JText::_('COM_COURSES_FIELD_OFFERING_ALIAS_HINT'); ?></span>
			</label>

			<label for="field-title">
				<?php echo JText::_('COM_COURSES_FIELD_TITLE'); ?> <span class="required"><?php echo JText::_('JREQUIRED'); ?></span>
				<input type="text" name="offering[title]" id="field-title" size="35" value="<?php echo $this->escape(stripslashes($this->offering->get('title'))); ?>" />
			</label>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="offering[state]" value="<?php echo $this->offering->get('state'); ?>" />
		<input type="hidden" name="offering[course_id]" value="<?php echo $this->course->get('id'); ?>" />
		<input type="hidden" name="offering[id]" value="<?php echo $this->offering->get('id'); ?>" />
		<input type="hidden" name="offering[state]" value="<?php echo $this->offering->get('state', 1); ?>" />
		<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="saveoffering" />

		<?php echo JHTML::_('form.token'); ?>

		<p class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo JText::_('COM_COURSES_SAVE'); ?>" />
		</p>
	</form>

<?php if (!$this->no_html) { ?>
</section><!-- / .section -->
<?php } ?>
