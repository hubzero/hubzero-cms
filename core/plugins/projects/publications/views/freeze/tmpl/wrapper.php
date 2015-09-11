<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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
<div id="pub-editor" class="pane-desc freeze">
 	<div id="c-pane" class="columns">
		 <div class="c-inner">
			<h4><?php echo $this->manifest->title; ?></h4>
			<div class="block-aside">
				<div class="block-info">
					<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_LOCKED'); ?>
					<?php if ($this->pub->isPublished()) {
						echo ' <a href="' . Route::url($this->pub->link('edit')) . '/?action=newversion">' . ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION')) . '</a>'; } ?>
					</p>
				</div>
			</div>
			<div class="block-subject">
			<?php echo $this->content; ?>
			</div>
		 </div>
	</div>
</div>