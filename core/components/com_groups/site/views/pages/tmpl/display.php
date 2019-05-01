<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
