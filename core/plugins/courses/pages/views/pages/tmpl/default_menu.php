<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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