<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

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