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
	<div id="plg-header">
		<h3 class="newsupdate"><?php echo $this->title; ?></h3>
	</div>

	<?php
		// New update form
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'blog',
				'name'=>'addupdate'
			)
		);
		$view->option = $this->option;
		$view->project = $this->project;
		$view->goto = 'alias=' . $this->project->alias;
		echo $view->loadTemplate();	
	?>
	<h4 class="sumup"><?php echo JText::_('COM_PROJECTS_LATEST_ACTIVITY'); ?></h4>
	<div id="latest_activity" class="infofeed">
	<?php 
		// Display item list
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'blog',
				'name'=>'activity'
			)
		);
		$view->option = $this->option;
		$view->project = $this->project;
		$view->activities = $this->activities;
		$view->goto = 'alias=' . $this->project->alias;
		$view->limit = $this->limit;
		$view->total = $this->total;
		$view->filters = $this->filters;
		$view->uid = $this->uid;
		$view->database = $this->database;
		echo $view->loadTemplate();
		?>
<form id="hubForm" method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'alias=' . $this->project->alias) . '/?active=feed'; ?>">
 <div>
	<input type="hidden" id="pid" name="id" value="<?php echo $this->project->id; ?>" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="action" value="" />
 </div>
</form>
</div>