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
			<h4><?php echo JText::_('COM_CONTRIBUTE_TAGS_WHAT_ARE_TAGS'); ?></h4>
			<p><?php echo JText::_('COM_CONTRIBUTE_TAGS_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
			<input type="hidden" name="step" value="<?php echo $this->next_step; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->id; ?>" />

			<h3><?php echo JText::_('COM_CONTRIBUTE_TAGS_ADD'); ?></h3>
<?php if (count($this->fats) > 0) { ?>
			<fieldset>
				<legend><?php echo JText::_('COM_CONTRIBUTE_TAGS_SELECT_FOCUS_AREA'); ?>:</legend>
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
				JPluginHelper::importPlugin( 'tageditor' );
				$dispatcher =& JDispatcher::getInstance();
				
				$tf = $dispatcher->trigger( 'onTagsEdit', array(array('tags','actags','',$this->tags,'')) );
				
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