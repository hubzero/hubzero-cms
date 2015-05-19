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

$this->css()
     ->js();
?>
<div id="plg-header">
	<h3 class="newsupdate"><?php echo $this->title; ?></h3>
</div>

<?php
	// New update form
	$this->view('default', 'addupdate')
	     ->set('option', $this->option)
	     ->set('model', $this->model)
	     ->display();
?>

<div id="latest_activity" class="infofeed">
		<?php
		// Display item list
		$this->view('default', 'activity')
		     ->set('option', $this->option)
		     ->set('model', $this->model)
		     ->set('activities', $this->activities)
		     ->set('limit', $this->limit)
		     ->set('total', $this->total)
		     ->set('filters', $this->filters)
		     ->set('uid', $this->uid)
		     ->set('database', $this->database)
		     ->display();
		?>
	<form id="hubForm" method="post" action="<?php echo Route::url($this->model->link()); ?>">
		<div>
			<input type="hidden" id="pid" name="id" value="<?php echo $this->model->get('id'); ?>" />
			<input type="hidden" name="task" value="view" />
			<input type="hidden" name="action" value="" />
		</div>
	</form>
</div>