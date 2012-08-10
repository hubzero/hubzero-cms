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
?>
<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->project->id.'&active=files'); ?>" method="post" enctype="multipart/form-data" id="app-form" class="file-browser" >	
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="id" id="pid" value="<?php echo $this->project->id; ?>" />
			<input type="hidden" name="uid" id="uid" value="<?php echo $this->uid; ?>" />
			<input type="hidden" name="active" value="files" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="content" value="app" />
			<input type="hidden" name="view" value="browser" />	
			<label class="d-lbl"><?php echo JText::_('COM_PROJECTS_APP_NAME_YOUR_APP'); ?>
				<input name="toolname" maxlength="50" size="35" type="text" value="" />
				
			</label>
</form>