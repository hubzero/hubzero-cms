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

$types = array(
	'content'=>JText::_('COM_FEATURES_CONTENT'),
	'tools'=>JText::_('COM_FEATURES_TOOLS'),
	'resources'=>JText::_('COM_FEATURES_RESOURCES'),
	'answers'=>JText::_('COM_FEATURES_ANSWERS'),
	'profiles'=>JText::_('COM_FEATURES_PROFILES'),
);
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=save'); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p><span class="required">*</span> = <?php echo JText::_('COM_FEATURES_REQUIRED_FIELD'); ?></p>
			<p><a href="<?php echo JRoute::_('index.php?option='.$this->option); ?>"><?php echo JText::_('Back to Features History'); ?></a></p>
		</div><!-- / .aside -->
		<fieldset>
			<h3><?php echo JText::_('COM_FEATURES_FEATURED_ITEM'); ?></h3>
			
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="note" value="<?php echo $this->row->note; ?>" />
			
			<label>
				<?php echo JText::_('COM_FEATURES_OBJECT_ID'); ?>: <span class="required">*</span>
				<input type="text" name="objectid" value="<?php echo $this->row->objectid; ?>" />
			</label>
			<label>
				<?php echo JText::_('COM_FEATURES_OBJECT_TYPE'); ?>: <span class="required">*</span>
				<select name="tbl" id="tbl">
<?php 
				foreach ($types as $avalue => $alabel) 
				{
?>
					<option value="<?php echo $avalue; ?>"<?php echo ($avalue == $this->row->tbl || $alabel == $this->row->tbl) ? ' selected="selected"' : ''; ?>><?php echo $alabel; ?></option>
<?php
				}
?>
				</select>
			</label>
			<label>
				<?php echo JText::_('COM_FEATURES_FEATURED_DATE'); ?>: YYYY-MM-DD <span class="required">*</span>
				<input type="text" name="featured" value="<?php echo $this->row->featured; ?>" />
			</label>
		</fieldset>
		<p class="submit"><input type="submit" value="<?php echo JText::_('COM_FEATURES_SUBMIT'); ?>" /></p>
	</form>
</div><!-- / .main section -->
