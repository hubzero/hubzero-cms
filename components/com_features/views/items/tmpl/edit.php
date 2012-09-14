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

$types = array(
	'content'   => JText::_('COM_FEATURES_CONTENT'),
	'tools'     => JText::_('COM_FEATURES_TOOLS'),
	'resources' => JText::_('COM_FEATURES_RESOURCES'),
	'answers'   => JText::_('COM_FEATURES_ANSWERS'),
	'profiles'  => JText::_('COM_FEATURES_PROFILES'),
);
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<p><a class="main-page btn" href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>"><?php echo JText::_('Back to Features History'); ?></a></p>
</div><!-- / #content-header-extra -->

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=save'); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p><span class="required">*</span> = <?php echo JText::_('COM_FEATURES_REQUIRED_FIELD'); ?></p>
		</div><!-- / .aside -->
		<fieldset>
			<legend><?php echo JText::_('COM_FEATURES_FEATURED_ITEM'); ?></legend>
			
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="note" value="<?php echo $this->escape($this->row->note); ?>" />
			
			<label>
				<?php echo JText::_('COM_FEATURES_OBJECT_ID'); ?>: <span class="required">*</span>
				<input type="text" name="objectid" value="<?php echo $this->escape($this->row->objectid); ?>" />
			</label>
			<label>
				<?php echo JText::_('COM_FEATURES_OBJECT_TYPE'); ?>: <span class="required">*</span>
				<select name="tbl" id="tbl">
<?php 
				foreach ($types as $avalue => $alabel)
				{
?>
					<option value="<?php echo $avalue; ?>"<?php echo ($avalue == $this->row->tbl || $alabel == $this->row->tbl) ? ' selected="selected"' : ''; ?>><?php echo $this->escape($alabel); ?></option>
<?php
				}
?>
				</select>
			</label>
			<label>
				<?php echo JText::_('COM_FEATURES_FEATURED_DATE'); ?>: YYYY-MM-DD <span class="required">*</span>
				<input type="text" name="featured" value="<?php echo $this->escape($this->row->featured); ?>" />
			</label>
		</fieldset>
		<p class="submit"><input type="submit" value="<?php echo JText::_('COM_FEATURES_SUBMIT'); ?>" /></p>
	</form>
</div><!-- / .main section -->

