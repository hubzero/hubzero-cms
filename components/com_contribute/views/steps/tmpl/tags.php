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
	<form action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" method="post" id="hubForm">
		<div class="explaination">
			<h4><?php echo JText::_('COM_CONTRIBUTE_TAGS_WHAT_ARE_TAGS'); ?></h4>
			<p><?php echo JText::_('COM_CONTRIBUTE_TAGS_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
			<input type="hidden" name="step" value="<?php echo $this->next_step; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->id; ?>" />

			<legend><?php echo JText::_('COM_CONTRIBUTE_TAGS_ADD'); ?></legend>
<?php if (isset($this->fats) && count($this->fats) > 0) { ?>
			<fieldset>
				<legend><?php echo JText::_('COM_CONTRIBUTE_TAGS_SELECT_FOCUS_AREA'); ?>: <span class="required">required</span></legend>
				<?php
				foreach ($this->fats as $key => $value)
				{
					if ($key && $value) {
						echo '<label><input class="option" type="radio" name="tagfa" value="' . $value . '"';
						if ($this->tagfa == $value) {
							echo ' checked="checked "';
						}
						echo ' /> '.$key.'</label>'."\n";
					}
				}
				?>
			</fieldset>
<?php } ?>				
			<label>
				<?php echo JText::_('COM_CONTRIBUTE_TAGS_ASSIGNED'); ?>:
				<?php
				JPluginHelper::importPlugin( 'hubzero' );
				$dispatcher =& JDispatcher::getInstance();

				$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','',$this->tags)) );

				if (count($tf) > 0) {
					echo $tf[0];
				} else {
					echo '<textarea name="tags" id="tags-men" rows="6" cols="35">'. $this->tags .'</textarea>'."\n";
				}
				?>
			</label>
			<p><?php echo JText::_('COM_CONTRIBUTE_TAGS_NEW_EXPLANATION'); ?></p>
		</fieldset><div class="clear"></div>
		
		<p class="submit">
			<input type="submit" value="<?php echo JText::_('COM_CONTRIBUTE_NEXT'); ?>" />
		</p>
	</form>
</div><!-- / .main section -->
