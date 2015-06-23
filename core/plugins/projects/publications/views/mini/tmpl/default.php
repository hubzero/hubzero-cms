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

// No direct access
defined('_HZEXEC_') or die();

$this->css();

?>
<div class="sidebox<?php if (count($this->items) == 0) { echo ' suggestions'; } ?>">
		<h4><a href="<?php echo Route::url($this->model->link('publications')); ?>" class="hlink" title="<?php echo Lang::txt('COM_PROJECTS_VIEW') . ' ' . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) . ' ' . strtolower(Lang::txt('COM_PROJECTS_TAB_PUBLICATIONS')); ?>"><?php echo ucfirst(Lang::txt('COM_PROJECTS_TAB_PUBLICATIONS')); ?></a>
<?php if (count($this->items) > 0) { ?>
	<span><a href="<?php echo Route::url($this->model->link('publications')); ?>"><?php echo ucfirst(Lang::txt('COM_PROJECTS_SEE_ALL')); ?> </a></span>
<?php } ?>
</h4>
<?php if (count($this->items) == 0) { ?>
		<p class="s-publications"><a href="<?php echo Route::url($this->model->link('publications') . '&action=start'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION'); ?></a></p>
<?php } else { ?>
	<ul>
		<?php foreach ($this->items as $pub) {
			$status = $pub->getStatusName();
		?>
		<li>
			<span class="pub-thumb"><img src="<?php echo Route::url($pub->link('thumb')); ?>" alt=""/></span>
			<span class="pub-details">
				<a href="<?php echo Route::url($pub->link('editversion')); ?>" title="<?php echo $this->escape($pub->get('title')); ?>"><?php echo \Hubzero\Utility\String::truncate(stripslashes($pub->get('title')), 100); ?></a>
				 <span class="block faded mini">
					<span>v. <?php echo $pub->get('version_label'); ?> (<?php echo $status; ?>)</span>
				</span>
			</span>
		</li><?php } ?>
	</ul><?php } ?>
</div>