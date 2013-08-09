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

defined('_JEXEC') or die('Restricted access');

//$pages = $this->offering->pages();

$base = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . ($this->offering->section()->get('alias') != '__default' ? ':' . $this->offering->section()->get('alias') : '') . '&active=pages';
?>
<div class="pages-menu">
	<ul>
<?php if (count($this->pages) > 0) { ?>
		<?php
		foreach ($this->pages as $page)
		{
			?>
		<li>
			<a class="<?php echo $page->get('section_id') ? 'page-section' : ($page->get('offering_id') ? 'page-offering' : 'page-courses'); ?> page<?php if ($page->get('url') == $this->page->get('url')) { echo ' active'; } ?>" href="<?php echo JRoute::_($base . '&unit=' . $page->get('url')); ?>"><?php echo $this->escape(stripslashes($page->get('title'))); ?></a>
		</li>
			<?php
		}
		?>
<?php } else { ?>
		<li>
			<a class="active page" href="<?php echo $base; ?>"><?php echo JText::_('Notice!'); ?></a>
		</li>
<?php } ?>
	</ul>
<?php if ($this->offering->access('manage', 'section')) { ?>
	<p>
		<a class="icon-add add btn" href="<?php echo JRoute::_($base . '&unit=add'); ?>">
			<?php echo JText::_('Add page'); ?>
		</a>
	</p>
<?php } ?>
</div>