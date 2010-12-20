<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$accesses = array('Public','Registered','Special','Protected','Private');
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
<?php
	$view = new JView( array('name'=>'steps','layout'=>'steps') );
	$view->option = $this->option;
	$view->step = $this->step;
	$view->steps = $this->steps;
	$view->id = $this->id;
	$view->progress = $this->progress;
	$view->display();
?>
<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form action="index.php" method="post" id="hubForm">
		<div class="explaination">
			<h4><?php echo JText::_('COM_CONTRIBUTE_GROUPS_HEADER'); ?></h4>
			<p><?php echo JText::_('COM_CONTRIBUTE_GROUPS_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<h3><?php echo JText::_('COM_CONTRIBUTE_GROUPS_OWNERSHIP'); ?></h3>
			<div class="group">
			<label>
				<?php echo JText::_('COM_CONTRIBUTE_GROUPS_GROUP'); ?>: <span class="optional"><?php echo JText::_('COM_CONTRIBUTE_OPTIONAL'); ?></span>
				<select name="group_owner">
					<option value=""><?php echo JText::_('COM_CONTRIBUTE_SELECT_GROUP'); ?></option>
<?php
				if ($this->groups && count($this->groups) > 0) {
					foreach ($this->groups as $group)
					{
?>
					<option value="<?php echo $group->cn; ?>"<?php if ($this->row->group_owner == $group->cn) { echo ' selected="selected"'; } ?>><?php echo $group->description; ?></option>
<?php
					}
				}
?>
				</select>
			</label>
			<label>
				<?php echo JText::_('COM_CONTRIBUTE_GROUPS_ACCESS_LEVEL'); ?>: <span class="optional"><?php echo JText::_('COM_CONTRIBUTE_OPTIONAL'); ?></span>
				<select name="access">';
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
		</fieldset><div class="clear"></div>
		
		<div class="explaination">
			<h4><?php echo JText::_('COM_CONTRIBUTE_AUTHORS_NO_LOGIN'); ?></h4>
			<p><?php echo JText::_('COM_CONTRIBUTE_AUTHORS_NO_LOGIN_EXPLANATION'); ?></p>
		
			<h4><?php echo JText::_('COM_CONTRIBUTE_AUTHORS_NOT_AUTHOR'); ?></h4>
			<p><?php echo JText::_('COM_CONTRIBUTE_AUTHORS_NOT_AUTHOR_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<h3><?php echo JText::_('COM_CONTRIBUTE_AUTHORS_AUTHORS'); ?></h3>
			<iframe width="100%" height="400" frameborder="0" name="authors" id="authors" src="index.php?option=<?php echo $this->option; ?>&amp;task=authors&amp;id=<?php echo $this->id; ?>&amp;no_html=1"></iframe>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
			<input type="hidden" name="step" value="<?php echo $this->next_step; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		</fieldset><div class="clear"></div>
		
		<p id="nextsubmit">
			<input type="submit" value="<?php echo JText::_('COM_CONTRIBUTE_NEXT'); ?>" id="nextbutton"/>
		</p>
	</form>
</div><!-- / .main section -->