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

// No direct access
defined('_HZEXEC_') or die();
?>
<ul class="toolbar toolbar-pages">
	<li class="new">
		<a class="btn icon-add" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=add'); ?>">
			<?php echo Lang::txt('COM_GROUPS_PAGES_NEW_PAGE'); ?>
		</a>
	</li>
	<li class="filter">
		<select name="filer">
			<option value=""><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_FILTER'); ?></option>
			<?php foreach ($this->categories as $category) : ?>
				<option data-color="#<?php echo $category->get('color'); ?>" value="<?php echo $category->get('id'); ?>"><?php echo $category->get('title'); ?></option>
			<?php endforeach; ?>
		</select>
	</li>
	<li class="filter-search-divider"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_OR'); ?></li>
	<li class="search">
		<input type="text" name="search" placeholder="<?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_SEARCH'); ?>" value="<?php echo $this->escape(isset($this->search) ? $this->search : ''); ?>" />
	</li>
</ul>

<?php
	$this->view('list')
		 ->set('level', 0)
		 ->set('pages', $this->pages)
		 ->set('categories', $this->categories)
		 ->set('group', $this->group)
		 ->set('config', $this->config)
		 ->display();
?>