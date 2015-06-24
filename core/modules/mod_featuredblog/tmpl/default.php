<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_HZEXEC_') or die();

if ($this->getError())
{
	?>
	<p class="error"><?php echo Lang::txt('MOD_FEATUREDBLOG_MISSING_CLASS'); ?></p>
	<?php
}
else if ($this->row)
{
	$row = new \Components\Blog\Models\Entry($this->row);
	$base = rtrim(Request::base(true), '/');
	?>
	<div class="<?php echo $this->cls; ?>">
		<p class="featured-img">
			<a href="<?php echo Route::url($row->link()); ?>">
				<img width="50" height="50" src="<?php echo $base; ?>/core/modules/mod_featuredblog/assets/img/blog_thumb.gif" alt="<?php echo $this->escape(stripslashes($row->get('title'))); ?>" />
			</a>
		</p>
		<p>
			<a href="<?php echo Route::url($row->link()); ?>">
				<?php echo $this->escape(stripslashes($row->get('title'))); ?>
			</a>:
			<?php if ($row->get('content')) { ?>
				<?php echo $row->content('clean', $this->txt_length); ?>
			<?php } ?>
		</p>
	</div>
	<?php
}
