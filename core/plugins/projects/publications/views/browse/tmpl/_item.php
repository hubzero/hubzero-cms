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
<li>
	<span class="mypub-options">
		<a href="<?php echo $this->row->link('version'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_TITLE'); ?>"><?php echo strtolower(Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW')); ?></a> |
		<a href="<?php echo $this->row->link('editversion'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_MANAGE_TITLE'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_MANAGE'); ?></a>
	</span>
	<span class="pub-thumb"><img src="<?php echo Route::url($this->row->link('thumb')); ?>" alt=""/></span>
	<span class="pub-details">
		<?php echo $this->row->get('title'); ?>
		<span class="block faded mini">
			<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION') . ' ' . $this->row->get('version_label'); ?>
			<span class="<?php echo $this->row->getStatusCss(); ?> major_status"><?php echo $this->row->getStatusName(); ?></span>
			<span class="block">
				<?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_CREATED')) . ' ' . $this->row->created('date') . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_BY') . ' ' . $this->row->creator('name') ; ?>
				<?php if (!$this->row->project()->isProvisioned()) {
				echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_IN_PROJECT') . ' <a href="' . $this->row->project()->link() . '">' . \Hubzero\Utility\String::truncate(stripslashes($this->row->project()->get('title')), 80) . '</a>';
			} ?></span>
		</span>
	</span>
</li>