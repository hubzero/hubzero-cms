<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
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

// No direct access
defined('_HZEXEC_') or die();

?>
<div class="sidebox<?php if (count($this->notes) == 0) { echo ' suggestions'; } ?>">
		<h4><a href="<?php echo Route::url($this->model->link('notes')); ?>" class="hlink" title="<?php echo Lang::txt('COM_PROJECTS_VIEW') . ' ' . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) . ' ' . strtolower(Lang::txt('PLG_PROJECTS_NOTES')); ?>"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_NOTES')); ?></a>
<?php if (count($this->notes) > 0) { ?>
	<span><a href="<?php echo Route::url($this->model->link('notes')); ?>"><?php echo ucfirst(Lang::txt('COM_PROJECTS_SEE_ALL')); ?> </a></span>
<?php } ?>
</h4>
<?php if (count($this->notes) == 0) { ?>
		<p class="s-notes"><a href="<?php echo Route::url($this->model->link('notes') . '&action=new'); ?>"><?php echo Lang::txt('PLG_PROJECTS_NOTES_ADD_NOTE'); ?></a></p>
<?php } else { ?>
	<ul>
		<?php foreach ($this->notes as $note) {
		?>
		<li>
			<a href="<?php echo Route::url($this->model->link('notes') . '&scope=' . $note->scope . '&pagename=' . $note->pagename); ?>" class="notes"><?php echo \Hubzero\Utility\String::truncate($note->title, 35); ?></a>
		</li><?php } ?>
	</ul><?php } ?>
</div>