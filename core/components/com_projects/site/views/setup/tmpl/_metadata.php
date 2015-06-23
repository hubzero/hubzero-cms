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

if (!$this->model->exists())
{
	return;
}
?>
<div class="info_blurb grid">
	<div class="col span1">
		<img src="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=media'); ?>" alt="" />
	</div>
	<div class="col span6">
		<?php echo '<span class="prominent">' . Lang::txt('COM_PROJECTS_PROJECT'). '</span>: ' . $this->escape($this->model->get('title')); ?>
		(<span><?php echo $this->model->get('alias'); ?></span>)
		<span class="block faded"><?php echo Lang::txt('COM_PROJECTS_CREATED') . ' ' . $this->model->created('date'); ?></span>
	</div>
	<div class="col span5 omega">
	</div>
</div>
