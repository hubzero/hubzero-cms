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
<div id="blab" class="miniblog">
	<form id="blogForm" method="post" action="<?php echo JRoute::_('index.php?option='.$this->option.a.$this->goto).'/?active=feed'; ?>">
		<fieldset>
			<textarea name="blogentry" cols="5" rows="5" id="blogentry" placeholder="Got an update?"></textarea>
			<p id="blog-submitarea">
			 <span id="counter_number_blog" class="leftfloat mini"></span>
			 <input type="hidden" name="task" value="view" />
			 <input type="hidden" name="active" value="feed" />	
			 <input type="hidden" name="action" value="save" />
			 <input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
			 <input type="hidden" name="managers_only" value="0" />	
			 <input type="submit" value="<?php echo JText::_('COM_PROJECTS_SHARE_WITH_TEAM'); ?>" id="blog-submit" class="blog-submit" />
			</p>
		</fieldset>
	</form>	
</div>