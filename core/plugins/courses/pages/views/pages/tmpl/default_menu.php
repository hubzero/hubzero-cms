<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_JEXEC') or die('Restricted access');

$base = $this->offering->link() . '&active=pages';
?>
<div class="pages-menu">
	<ul>
	<?php if (count($this->pages) > 0) { ?>
		<?php
		foreach ($this->pages as $page)
		{
			?>
		<li>
			<a class="<?php echo $page->get('section_id') ? 'page-section' : ($page->get('offering_id') ? 'page-offering' : 'page-courses'); ?> page<?php if ($page->get('url') == $this->page->get('url')) { echo ' active'; } ?>" href="<?php echo Route::url($base . '&unit=' . $page->get('url')); ?>"><?php echo $this->escape(stripslashes($page->get('title'))); ?></a>
		</li>
			<?php
		}
		?>
	<?php } else { ?>
		<li>
			<a class="active page" href="<?php echo $base; ?>"><?php echo Lang::txt('PLG_COURSES_PAGES_NONE_FOUND'); ?></a>
		</li>
	<?php } ?>
	</ul>
<?php if ($this->offering->access('manage', 'section')) { ?>
	<p>
		<a class="icon-add add btn" href="<?php echo Route::url($base . '&unit=add'); ?>">
			<?php echo Lang::txt('PLG_COURSES_PAGES_ADD_PAGE'); ?>
		</a>
	</p>
<?php } ?>
</div>