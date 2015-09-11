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
				$version  = $page->versions()->first();

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