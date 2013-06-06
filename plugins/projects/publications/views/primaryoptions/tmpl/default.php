<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

$masterscope = 'projects' . DS . $this->project->alias . DS . 'notes';

if ($this->duplicateV)
{ ?>
	<p class="notice"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PRIMARY_CONTENT_DUPLICATE_VERSION').' ('.$this->duplicateV.')'; ?></p>
<?php }
elseif ($this->used) 
{ ?>
	<p class="notice">
		<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PRIMARY_CONTENT_USED'); ?>
		<?php 
		$other = '';
		foreach($this->used as $used) {
			$other .= ' <a href="' . JRoute::_('index.php?option=' . $this->option 
				   . a . 'active=publications' . a . 'alias=' . $this->project->alias 
				   . a . 'pid=' . $used->id) . '/?section=content">'
				   . stripslashes($used->title) . ' (' . $used->id . ')' . '</a>,'; 
		} 
		$other = substr($other,0,strlen($other) - 1);
		echo $other;
		?>
	</p>
<?php } ?>
<?php if ($this->selections) { ?>

	<?php if ($this->base == 'notes')
	/*
	{ ?>
		<p class="notice"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_NOTICE_CHILD_NOTES'); ?></p>
	<?php } */

	?>
<div class="serveas_<?php echo $this->serveas; ?>">
	<p>
	<?php 
		switch ($this->serveas) 
		{
			case 'download':       
				echo JText::_('PLG_PROJECTS_PUBLICATIONS_SERVEAS_NOTE_DOWNLOAD');      
				break;
			case 'tardownload':       
				echo JText::_('PLG_PROJECTS_PUBLICATIONS_SERVEAS_NOTE_TARDOWNLOAD');      
				break;
			case 'inlineview':       
				echo JText::_('PLG_PROJECTS_PUBLICATIONS_SERVEAS_NOTE_INLINEVIEW');      
				break;
			case 'external':       
				echo JText::_('PLG_PROJECTS_PUBLICATIONS_SERVEAS_NOTE_EXTERNAL');      
				break;
		}
	?>
	</p>
	<?php if(count($this->choices) > 1) { ?>
	<h5><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_SERVEAS_ALL_OPTIONS'); ?></h5>
		<?php foreach($this->choices as $choice) { ?>
		<label><input name="serveas" type="radio" value="<?php echo $choice; ?>" class="serve_option" <?php if($this->serveas == $choice)  { echo 'checked="checked"'; } ?> /> <?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_SERVEAS_'.strtoupper($choice)); ?></label>
		<?php } ?>
	<?php } else { ?>
	<input type="hidden" name="serveas" value=<?php echo $this->serveas; ?> />
	<?php } ?>
	<div class="po-ima"></div>
</div>
<?php } else { ?>
<span></span>	
<?php } ?>
