<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Category navigation. Expects: option, archive, catid (the active top-level
// category id, 0 for all), category (the active category), filters (sort,
// search), which the links carry along so a reader keeps their search while
// narrowing by category.
$filters    = array('state' => 1, 'access' => User::getAuthorisedViewLevels());
$categories = $this->archive->categories($filters);

$carry = '';
if (!empty($this->filters['sort']))
{
	$carry .= '&sort=' . $this->escape($this->filters['sort']);
}
if (!empty($this->filters['search']))
{
	$carry .= '&search=' . urlencode($this->filters['search']);
}
?>
<nav aria-label="<?php echo Lang::txt('COM_KB_CATEGORIES'); ?>">
	<div class="container">
		<h3><?php echo Lang::txt('COM_KB_CATEGORIES'); ?></h3>
		<ul class="categories">
			<li>
				<a<?php if ($this->catid <= 0) { echo ' class="active" aria-current="page"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&section=all' . $carry); ?>">
					<?php echo Lang::txt('COM_KB_ALL_ARTICLES'); ?>
				</a>
			</li>
			<?php foreach ($categories as $row) { ?>
				<?php
				if ($row->get('articles', 0) <= 0)
				{
					continue;
				}
				?>
				<li>
					<a <?php if ($this->catid == $row->get('id')) { echo 'class="active" aria-current="page" '; } ?> href="<?php echo Route::url($row->link() . $carry); ?>">
						<?php echo $this->escape(stripslashes($row->get('title'))); ?> <span class="item-count"><?php echo $row->get('articles', 0); ?></span>
					</a>
					<?php if ($this->catid == $row->get('id') && $row->children($filters)->total() > 0) { ?>
						<ul class="categories">
						<?php foreach ($row->children() as $cat) { ?>
							<li>
								<a <?php if ($this->category->get('id') == $cat->get('id')) { echo 'class="active" aria-current="page" '; } ?> href="<?php echo Route::url($cat->link() . $carry); ?>">
									<?php echo $this->escape(stripslashes($cat->get('title'))); ?> <span class="item-count"><?php echo $cat->get('articles', 0); ?></span>
								</a>
							</li>
						<?php } ?>
						</ul>
					<?php } ?>
				</li>
			<?php } ?>
		</ul>
	</div><!-- / .container -->
</nav>
