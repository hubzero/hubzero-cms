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
	<form action="index.php" method="get" id="hubForm">
		<div class="explaination">
			
			<h4><?php echo JText::_('COM_CONTRIBUTE_ATTACH_WHAT_ARE_ATTACHMENTS'); ?></h4>
			<p><?php echo JText::_('COM_CONTRIBUTE_ATTACH_EXPLANATION'); ?></p>

			<h4><?php echo JText::_('COM_CONTRIBUTE_ATTACH_HOW_TO_ATTACH_BREEZE'); ?></h4>
			<p><?php echo JText::_('COM_CONTRIBUTE_ATTACH_BREEZE_EXPLANATION'); ?></p>
			
			<h4>Attaching External Links</h4>
			<p>You can attach a link to an external website (such as a youtube video) by pasting in the link in the external link box, then clicking 'Add External Link'. Note that the link will be moderated before it is made available to the public.</p>
<?php if($this->row->type == 31) {?>			

			<h4>NEESHub Resource ID?</h4>
			<p>Every resource in the Hub has a unique ID. This is the last part of the URL when you view that resource, for example: "http://nees.org/education/academy-resources/444" indicates resource ID "444". </p>
<?php }?>			
		</div>
		<fieldset>
			<h3><?php echo JText::_('COM_CONTRIBUTE_ATTACH_ATTACHMENTS'); ?></h3>
			<iframe width="100%" height="480" frameborder="0" name="attaches" id="attaches" src="index.php?option=<?php echo $this->option; ?>&amp;task=attach&amp;id=<?php echo $this->id; ?>&amp;no_html=1&amp;type=<?php echo $this->row->type; ?>"></iframe>
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