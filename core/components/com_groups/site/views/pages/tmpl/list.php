<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$cls    = '';
$params = '';
if ($this->level == 0)
{
	$cls    = 'item-list pages';
	$params = 'data-url="' . Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=reorder&no_html=1') . '"';
	$params .= ' data-max-depth="' . ($this->config->get('page_depth', 5) + 1) . '"';
}
?>

<ul class="<?php echo $cls; ?>" <?php echo $params; ?>>
	<?php if (count($this->pages) > 0) : ?>
		<?php foreach ($this->pages as $page) : ?>
			<?php
				// get page details
				$category = $this->categories->fetch('id', $page->get('category'));
				$version  = $page->versions(array('limit' => 1))->first();

				// page class
				$cls = '';
				if ($page->get('home') == 1)
				{
					$cls .= ' root';
				}

				//get file check outs
				$checkout = \Components\Groups\Helpers\Pages::getCheckout($page->get('id'));
			?>
			<li id="<?php echo $page->get('id'); ?>" class="<?php echo $cls; ?>">
				<?php
					$this->view('item')
						 ->set('page', $page)
						 ->set('category', $category)
						 ->set('group', $this->group)
						 ->set('version', $version)
						 ->set('checkout', $checkout)
						 ->display();

					// display page children
					if ($children = $page->get('children'))
					{
						$this->view('list')
							 ->set('level', 10)
							 ->set('pages', $children)
							 ->set('categories', $this->categories)
							 ->set('group', $this->group)
							 ->display();
					}
				?>
			</li>
		<?php endforeach; ?>
		<?php if ($this->level == 0) : ?>
			<div class="item-list-loader"></div>
		<?php endif; ?>
	<?php elseif ($this->level == 0) : ?>
		<li class="no-results">
			<p><?php echo Lang::txt('COM_GROUPS_PAGES_NO_PAGES'); ?></p>
		</li>
	<?php endif; ?>
</ul>