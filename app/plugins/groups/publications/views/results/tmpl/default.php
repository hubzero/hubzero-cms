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
$this->css()
     ->css('publications.css', 'com_publications')
     ->js('publications.js', 'com_publications');
$config = Component::params('com_publications');
// An array for storing all the links we make
$links = array();
$html = '';
if ($this->cats)
{
	// Loop through each category
	foreach ($this->cats as $cat)
	{
		// Only show categories that have returned search results
		if ($cat['total'] > 0)
		{
			// Is this the active category?
			$a = ($cat['category'] == $this->active) ? ' class="active"' : '';
			// If we have a specific category, prepend it to the search term
			$blob = ($cat['category']) ? $cat['category'] : '';
			// Build the HTML
			$l = "\t" . '<li' . $a . '><a href="' . Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=publications&area='. urlencode(stripslashes($blob))) . '">' . $this->escape(stripslashes($cat['title'])) . ' <span class="item-count">' . $cat['total'] . '</span></a>';
			// Are there sub-categories?
			if (isset($cat['_sub']) && is_array($cat['_sub']))
			{
				// An array for storing the HTML we make
				$k = array();
				// Loop through each sub-category
				foreach ($cat['_sub'] as $subcat)
				{
					// Only show sub-categories that returned search results
					if ($subcat['total'] > 0)
					{
						// Is this the active category?
						$a = ($subcat['category'] == $this->active) ? ' class="active"' : '';
						// If we have a specific category, prepend it to the search term
						$blob = ($subcat['category']) ? $subcat['category'] : '';
						// Build the HTML
						$k[] = "\t\t\t" . '<li' . $a . '><a href="' . Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=publications&area='. urlencode(stripslashes($blob))) . '">' . $this->escape(stripslashes($subcat['title'])) . ' <span class="item-count">' . $subcat['total'] . '</span></a></li>';
					}
				}
				// Do we actually have any links?
				// NOTE: this method prevents returning empty list tags "<ul></ul>"
				if (count($k) > 0)
				{
					$l .= "\t\t" . '<ul>' . "\n";
					$l .= implode("\n", $k);
					$l .= "\t\t" . '</ul>' . "\n";
				}
			}
			$l .= '</li>';
			$links[] = $l;
		}
	}
}
?>

<?php if ($this->group->published == 1) { ?>
	<ul id="page_options">
		<li>
			<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=com_publications&task=draft&group=' . $this->group->get('cn')); ?>"><?php echo Lang::txt('PLG_GROUPS_PUBLICATIONS_START_A_CONTRIBUTION'); ?></a>
		</li>
	</ul>
<?php } ?>

<section class="section">
	<form method="get" action="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=publications'); ?>">

		<input type="hidden" name="area" value="<?php echo $this->escape($this->active); ?>" />

		<div class="container">
			<nav class="entries-filters">
				<ul class="entries-menu filter-options">
					<?php if (count($links) > 0) { ?>
						<li class="filter-categories">
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=publications&area=' . urlencode(stripslashes($this->active)) . '&sort=' . $this->sort . '&access=' . $this->active); ?>"><?php echo Lang::txt('PLG_GROUPS_PUBLICATIONS_CATEGORIES'); ?></a>
							<ul>
								<?php echo implode("\n", $links); ?>
							</ul>
						</li>
					<?php } ?>
					<li>
						<a<?php echo ($this->access == 'all') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=publications&area=' . urlencode(stripslashes($this->active)) . '&sort=' . $this->sort . '&access=all'); ?>">
							<?php echo Lang::txt('PLG_GROUPS_PUBLICATIONS_ACCESS_ALL'); ?>
						</a>
					</li>
					<li>
						<a<?php echo ($this->access == 'public') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=publications&area=' . urlencode(stripslashes($this->active)) . '&sort=' . $this->sort . '&access=public'); ?>">
							<?php echo Lang::txt('PLG_GROUPS_PUBLICATIONS_ACCESS_PUBLIC'); ?>
						</a>
					</li>
					<li>
						<a<?php echo ($this->access == 'protected') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=publications&area=' . urlencode(stripslashes($this->active)) . '&sort=' . $this->sort . '&access=protected'); ?>">
							<?php echo Lang::txt('PLG_GROUPS_PUBLICATIONS_ACCESS_PROTECTED'); ?>
						</a>
					</li>
					<li>
						<a<?php echo ($this->access == 'private') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=publications&area=' . urlencode(stripslashes($this->active)) . '&sort=' . $this->sort . '&access=private'); ?>">
							<?php echo Lang::txt('PLG_GROUPS_PUBLICATIONS_ACCESS_PRIVATE'); ?>
						</a>
					</li>
				</ul>

				<ul class="entries-menu">
					<li><a<?php echo ($this->sort == 'date') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=publications&area=' . urlencode(stripslashes($this->active)) . '&sort=date&access=' . $this->access); ?>" title="Sort by newest to oldest">&darr; <?php echo Lang::txt('PLG_GROUPS_PUBLICATIONS_SORT_BY_DATE'); ?></a></li>
					<li><a<?php echo ($this->sort == 'title') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=publications&area=' . urlencode(stripslashes($this->active)) . '&sort=title&access=' . $this->access); ?>" title="Sort by title">&darr; <?php echo Lang::txt('PLG_GROUPS_PUBLICATIONS_SORT_BY_TITLE'); ?></a></li>
					
				</ul>
			</nav>

			<div class="container-block">
				<?php
				$html = '';
				$k = 0;
				foreach ($this->results as $category)
				{
					$amt = count($category);
					if ($amt > 0)
					{
						$html .= '<ol class="publications results">'."\n";
						foreach ($category as $row)
						{
							$k++;
							$html .= $this->view('_item') //calling _item view here
										->set('row', $row)
										->set('authorized', $this->authorized)
										->loadTemplate();
						}
						$html .= '</ol>'."\n";
					}
				}
				echo $html;
				if (!$k)
				{
					echo '<p class="warning">' . Lang::txt('PLG_GROUPS_PUBLICATIONS_NONE') . '</p>';
				}
				?>
			</div><!-- / .container-block -->
			<?php
			$pageNav = $this->pagination(
				$this->total,
				$this->limitstart,
				$this->limit
			);
			$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
			$pageNav->setAdditionalUrlParam('active', 'publications');
			$pageNav->setAdditionalUrlParam('area', urlencode(stripslashes($this->active)));
			$pageNav->setAdditionalUrlParam('sort', $this->sort);
			$pageNav->setAdditionalUrlParam('access', $this->access);
			echo $pageNav->render();
			?>
			<div class="clearfix"></div>
		</div><!-- / .container -->
	</form>
</section>