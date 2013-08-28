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

// Build url
$route = $this->project->provisioned 
	? 'index.php?option=com_publications' . a . 'task=submit'
	: 'index.php?option=com_projects' . a . 'alias=' . $this->project->alias;
	
$attached = isset($this->attachments) && count($this->attachments) > 0 ? $this->attachments[0]->path : '';

?>
<div id="import-link">
	<input type="text" name="url" size="40" id="parse-url" placeholder="http://" value="<?php echo $attached ?>" />
	<div id="link-loading"></div>
	<div id="link-preview" class="hidden"></div>
	<div id="link-submit" class="hidden">
		<p class="submitarea">
			<a class="btn" id="l-submit"><?php echo JText::_('Attach this link'); ?></a>
		</p>
		<span class="block hint mini"><?php echo JText::_('*The new link you attach will replace any current link attachment'); ?></span>
	</div>
</div>