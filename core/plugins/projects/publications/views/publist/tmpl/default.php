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

// No direct access
defined('_HZEXEC_') or die();

if (count($this->items) > 0) {
?>
<div class="public-list-header">
	<h3><?php echo Lang::txt('COM_PROJECTS_PUBLICATIONS'); ?></h3>
</div>
<div class="public-list-wrap">
	<ul class="public-list">
		<?php foreach ($this->items as $pub) {
		?>
		<li>
			<span class="pub-thumb"><img src="<?php echo Route::url($pub->link('thumb')); ?>" alt=""/></span>
			<span class="pub-details">
				<a href="<?php echo Route::url($pub->link('version')); ?>" title="<?php echo $this->escape($pub->get('title')); ?>"><?php echo stripslashes($pub->get('title')) . ' v.' . $pub->get('version_label'); ?></a>
				 <span class="public-list-info">
					- <?php echo Lang::txt('COM_PROJECTS_PUBLISHED') . ' ' . $pub->published('date') . ' ' . Lang::txt('COM_PROJECTS_IN') . ' <a href="' . Route::url($pub->link('category')) . '">' . $pub->category()->name . '</a>'; ?>
				</span>
			</span>
		</li><?php } ?>
	</ul>
</div>
<?php } ?>
