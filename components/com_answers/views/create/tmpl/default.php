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
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div id="content-header-extra">
	<ul id="useroptions">
		<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=myquestions'); ?>" class="myquestions"><span><?php echo JText::_('COM_ANSWERS_MY_QUESTIONS'); ?></span></a></li>
		<li class="last"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=search'); ?>"><span><?php echo JText::_('COM_ANSWERS_ALL_QUESTIONS'); ?></span></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=saveq'); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p><?php echo JText::_('COM_ANSWERS_BE_POLITE'); ?></p>
			<p><?php echo JText::_('COM_ANSWERS_NO_HTML'); ?></p>
<?php if ($this->banking) { ?>
			<p class="help">
				<strong><?php echo JText::_('COM_ANSWERS_WHAT_IS_REWARD'); ?></strong><br />
				<?php echo JText::_('COM_ANSWERS_EXPLAINED_MARKET_VALUE'); ?> <a href="<?php echo $this->infolink; ?>"><?php echo JText::_('COM_ANSWERS_LEARN_MORE'); ?></a> <?php echo JText::_('COM_ANSWERS_ABOUT_POINTS'); ?>
			</p>
<?php } ?>
		</div><!-- / .explaination -->
		<fieldset>
			<h3><?php echo JText::_('COM_ANSWERS_YOUR_QUESTION'); ?></h3>
			
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="saveq" />
			<input type="hidden" name="funds" value="<?php echo $this->funds; ?>" />
			
			<label>
				<input class="option" type="checkbox" name="anonymous" value="1" /> 
				<?php echo JText::_('COM_ANSWERS_POST_QUESTION_ANON'); ?>
			</label>
			<label>
				<?php echo JText::_('COM_ANSWERS_TAGS'); ?>: <span class="required"><?php echo JText::_('COM_ANSWERS_REQUIRED'); ?></span><br />
<?php
JPluginHelper::importPlugin( 'tageditor' );
$dispatcher =& JDispatcher::getInstance();	
$tf = $dispatcher->trigger( 'onTagsEdit', array(array('tags','actags','',$this->tag,'')) );

if (count($tf) > 0) {
				echo $tf[0];
} else { ?>
				<textarea name="tags" id="tags-men" rows="6" cols="35"><?php echo $this->tag; ?></textarea>
<?php } ?>
			</label>
			<?php if (($resid = JRequest::getVar( 'resid',"" )) != "") {?>
			<input type="hidden" name="resid" value="<?php echo $resid;?>" />
			<?php }?>
			<label>
				<?php echo JText::_('COM_ANSWERS_ASK_ONE_LINER'); ?>: <span class="required"><?php echo JText::_('COM_ANSWERS_REQUIRED'); ?></span><br />
				<input type="text" name="subject" value="" />
			</label>
			<label>
				<?php echo JText::_('COM_ANSWERS_ASK_DETAILS'); ?>:<br />
				<textarea name="question" rows="10" cols="50"></textarea>
			</label>
<?php if ($this->banking) { ?>
			<label>
				<?php echo JText::_('COM_ANSWERS_ASSIGN_REWARD'); ?>:<br />
				<input type="text" name="reward" value="" size="5" <?php if ($this->funds <= 0) { echo 'disabled="disabled" '; } ?>/> 
				<?php echo JText::_('COM_ANSWERS_YOU_HAVE'); ?> <strong><?php echo $this->funds; ?></strong> <?php echo JText::_('COM_ANSWERS_POINTS_TO_SPEND'); ?>
			</label>
<?php } else { ?>
			<input type="hidden" name="reward" value="0" />
<?php } ?>
			<input class="option" type="hidden" name="email" value="1" checked="checked" />
		</fieldset>
		<p class="submit"><input type="submit" value="<?php echo JText::_('COM_ANSWERS_SUBMIT'); ?>" /></p>
	</form>
</div><!-- / .main section -->