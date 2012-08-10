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
<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->project->id.'&active=apps'); ?>" method="post" enctype="multipart/form-data" id="app-register" >	
<h4 class="welcome-h"><?php echo JText::_('COM_PROJECTS_APPS_REGISTER_NEW_APP'); ?></h4>
<div id="new-app">
	<p id="v-toolname"></p>
	<label>
		<?php echo JText::_('COM_PROJECTS_APPS_NAME'); ?>:
		<input name="toolname" id="toolname" maxlength="100" size="35" type="text" value="" class="inline-inp" />
	</label>
	<p class="hint"><?php echo JText::_('COM_PROJECTS_APPS_NAME_HINT'); ?></p>
	<label>
		<?php echo JText::_('COM_PROJECTS_APPS_TITLE'); ?>:
		<input name="title" maxlength="100" size="35" type="text" value="" class="inline-inp" />
	</label>
	<p class="c-submit">
		<input type="submit" value="<?php echo JText::_('COM_PROJECTS_APPS_REGISTER_APP'); ?>"  />
	</p>
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="id" id="pid" value="<?php echo $this->project->id; ?>" />
	<input type="hidden" name="active" value="<?php echo $this->active; ?>" />
	<input type="hidden" name="action" value="register" />
	<input type="hidden" name="verified" value="1" />
</div>
</form>