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

$accesses = array('Public','Registered','Special','Protected','Private');
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<p>
		<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=draft'); ?>">
			<?php echo JText::_('COM_CONTRIBUTE_NEW_SUBMISSION'); ?>
		</a>
	</p>
</div><!-- / #content-header -->

<div class="main section">
<?php
	$view = new JView(array(
		'name'   => 'steps',
		'layout' => 'steps'
	));
	$view->option = $this->option;
	$view->step = $this->step;
	$view->steps = $this->steps;
	$view->id = $this->id;
	$view->resource = $this->row;
	$view->progress = $this->progress;
	$view->display();
?>
<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=draft&step=' . $this->next_step . '&id=' . $this->id); ?>" method="post" id="hubForm">
		<div class="explaination">
			<h4><?php echo JText::_('COM_CONTRIBUTE_GROUPS_HEADER'); ?></h4>
			<p><?php echo JText::_('COM_CONTRIBUTE_GROUPS_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_CONTRIBUTE_GROUPS_OWNERSHIP'); ?></legend>
<?php if ($this->groups && count($this->groups) > 0) { ?>
			<div class="group">
			<label for="group_owner">
				<?php echo JText::_('COM_CONTRIBUTE_GROUPS_GROUP'); ?>: <span class="optional"><?php echo JText::_('COM_CONTRIBUTE_OPTIONAL'); ?></span>
				<select name="group_owner" id="group_owner">
					<option value=""><?php echo JText::_('COM_CONTRIBUTE_SELECT_GROUP'); ?></option>
<?php
				if ($this->groups && count($this->groups) > 0) {
					foreach ($this->groups as $group)
					{
?>
					<option value="<?php echo $group->cn; ?>"<?php if ($this->row->group_owner == $group->cn) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($group->description)); ?></option>
<?php
					}
				}
?>
				</select>
			</label>
			<label for="access">
				<?php echo JText::_('COM_CONTRIBUTE_GROUPS_ACCESS_LEVEL'); ?>: <span class="optional"><?php echo JText::_('COM_CONTRIBUTE_OPTIONAL'); ?></span>
				<select name="access" id="access">
<?php
				for ($i=0, $n=count( $accesses ); $i < $n; $i++)
				{
					if ($accesses[$i] != 'Registered' && $accesses[$i] != 'Special') {
?>
					<option value="<?php echo $i; ?>"<?php if ($this->row->access == $i) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CONTRIBUTE_ACCESS_'.strtoupper($accesses[$i])); ?></option>
<?php
					}
				}
?>
				</select>
			</label>
			</div>
			<p>
				<strong><?php echo JText::_('COM_CONTRIBUTE_ACCESS_PUBLIC'); ?></strong> = <?php echo JText::_('COM_CONTRIBUTE_ACCESS_PUBLIC_EXPLANATION'); ?><br />
				<strong><?php echo JText::_('COM_CONTRIBUTE_ACCESS_PROTECTED'); ?></strong> = <?php echo JText::_('COM_CONTRIBUTE_ACCESS_PROTECTED_EXPLANATION'); ?><br />
				<strong><?php echo JText::_('COM_CONTRIBUTE_ACCESS_PRIVATE'); ?></strong> = <?php echo JText::_('COM_CONTRIBUTE_ACCESS_PRIVATE_EXPLANATION'); ?>
			</p>
<?php } else { ?>
			<p class="information">
				<?php echo JText::_('Once you have joined one or more groups you may restrict access to this contribution to one of your groups.'); ?>
			</p>
<?php } ?>
		</fieldset><div class="clear"></div>
		
		<div class="explaination">
			<h4><?php echo JText::_('COM_CONTRIBUTE_AUTHORS_NO_LOGIN'); ?></h4>
			<p><?php echo JText::_('COM_CONTRIBUTE_AUTHORS_NO_LOGIN_EXPLANATION'); ?></p>
		
			<h4><?php echo JText::_('COM_CONTRIBUTE_AUTHORS_NOT_AUTHOR'); ?></h4>
			<p><?php echo JText::_('COM_CONTRIBUTE_AUTHORS_NOT_AUTHOR_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_CONTRIBUTE_AUTHORS_AUTHORS'); ?></legend>
			<div class="field-wrap">
				<iframe width="100%" height="400" frameborder="0" name="authors" id="authors" scrolling="auto" src="/index.php?option=<?php echo $this->option; ?>&amp;controller=authors&amp;id=<?php echo $this->id; ?>&amp;tmpl=component"></iframe>
			</div>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
			<input type="hidden" name="step" value="<?php echo $this->next_step; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		</fieldset><div class="clear"></div>
		
		<div class="submit">
			<input type="submit" value="<?php echo JText::_('COM_CONTRIBUTE_NEXT'); ?>" />
		</div>
	</form>
</div><!-- / .main section -->
