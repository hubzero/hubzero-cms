<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('resources.css', 'com_resources')
     ->js('resources.js', 'com_resources');

$config = Component::params('com_resources');

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
			$l = "\t" . '<li' . $a . '><a href="' . Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=resources&area='. urlencode(stripslashes($blob))) . '&limit=' . $this->limit . '">' . $this->escape(stripslashes($cat['title'])) . ' <span class="item-count">' . $cat['total'] . '</span></a>';

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
						$k[] = "\t\t\t" . '<li' . $a . '><a href="' . Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=resources&area='. urlencode(stripslashes($blob))) . '&limit=' . $this->limit . '">' . $this->escape(stripslashes($subcat['title'])) . ' <span class="item-count">' . $subcat['total'] . '</span></a></li>';
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
			<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=com_resources&task=draft&group=' . $this->group->get('cn')); ?>"><?php echo Lang::txt('PLG_GROUPS_RESOURCES_START_A_CONTRIBUTION'); ?></a>
		</li>
	</ul>
<?php } ?>

<section class="section">
	<form method="get" action="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=resources'); ?>">

		<input type="hidden" name="area" value="<?php echo $this->escape($this->active); ?>" />

		<div class="container">
			<nav class="entries-filters">
				<ul class="entries-menu filter-options">
					<?php if (count($links) > 0) { ?>
						<li class="filter-categories">
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=resources&area=' . urlencode(stripslashes($this->active)) . '&sort=' . $this->sort . '&access=' . $this->active . '&limit=' . $this->limit); ?>"><?php echo Lang::txt('PLG_GROUPS_RESOURCES_CATEGORIES'); ?></a>
							<ul>
								<?php echo implode("\n", $links); ?>
							</ul>
						</li>
					<?php } ?>
					<li>
						<a<?php echo ($this->access == 'all') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=resources&area=' . urlencode(stripslashes($this->active)) . '&sort=' . $this->sort . '&access=all&limit=' . $this->limit); ?>">
							<?php echo Lang::txt('PLG_GROUPS_RESOURCES_ACCESS_ALL'); ?>
						</a>
					</li>
					<li>
						<a<?php echo ($this->access == 'public') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=resources&area=' . urlencode(stripslashes($this->active)) . '&sort=' . $this->sort . '&access=public&limit=' . $this->limit); ?>">
							<?php echo Lang::txt('PLG_GROUPS_RESOURCES_ACCESS_PUBLIC'); ?>
						</a>
					</li>
					<li>
						<a<?php echo ($this->access == 'protected') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=resources&area=' . urlencode(stripslashes($this->active)) . '&sort=' . $this->sort . '&access=protected&limit=' . $this->limit); ?>">
							<?php echo Lang::txt('PLG_GROUPS_RESOURCES_ACCESS_PROTECTED'); ?>
						</a>
					</li>
					<li>
						<a<?php echo ($this->access == 'private') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=resources&area=' . urlencode(stripslashes($this->active)) . '&sort=' . $this->sort . '&access=private&limit=' . $this->limit); ?>">
							<?php echo Lang::txt('PLG_GROUPS_RESOURCES_ACCESS_PRIVATE'); ?>
						</a>
					</li>
				</ul>

				<ul class="entries-menu">
					<li>
						<a class="<?php echo ($this->sort == 'date') ? 'active ' . ($this->sortdir == 'desc' ? 'icon-arrow-up' : 'icon-arrow-down') : 'icon-arrow-down'; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=resources&area=' . urlencode(stripslashes($this->active)) . '&sort=date&sortdir=' . ($this->sort == 'date' ? ($this->sortdir == 'desc' ? 'asc' : 'desc') : 'asc') . '&access=' . $this->access . '&limit=' . $this->limit); ?>" title="Sort by newest to oldest">
							<?php echo Lang::txt('PLG_GROUPS_RESOURCES_SORT_BY_DATE'); ?>
						</a>
					</li>
					<li>
						<a class="<?php echo ($this->sort == 'title') ? 'active ' . ($this->sortdir == 'desc' ? 'icon-arrow-up' : 'icon-arrow-down') : 'icon-arrow-down'; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=resources&area=' . urlencode(stripslashes($this->active)) . '&sort=title&sortdir=' . ($this->sort == 'title' ? ($this->sortdir == 'desc' ? 'asc' : 'desc') : 'asc') . '&access=' . $this->access . '&limit=' . $this->limit); ?>" title="Sort by title">
							<?php echo Lang::txt('PLG_GROUPS_RESOURCES_SORT_BY_TITLE'); ?>
						</a>
					</li>
					<?php if ($config->get('show_ranking')) { ?>
						<li>
							<a class="<?php echo ($this->sort == 'ranking') ? 'active ' . ($this->sortdir == 'desc' ? 'icon-arrow-up' : 'icon-arrow-down') : 'icon-arrow-down'; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=resources&area=' . urlencode(stripslashes($this->active)) . '&sort=ranking&sortdir=' . ($this->sort == 'ranking' ? ($this->sortdir == 'desc' ? 'asc' : 'desc') : 'asc') . '&access=' . $this->access . '&limit=' . $this->limit); ?>" title="Sort by popularity">
								<?php echo Lang::txt('PLG_GROUPS_RESOURCES_SORT_BY_RANKING'); ?>
							</a>
						</li>
					<?php } else { ?>
						<li>
							<a class="<?php echo ($this->sort == 'rating') ? 'active ' . ($this->sortdir == 'desc' ? 'icon-arrow-up' : 'icon-arrow-down') : 'icon-arrow-down'; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=resources&area=' . urlencode(stripslashes($this->active)) . '&sort=rating&sortdir=' . ($this->sort == 'rating' ? ($this->sortdir == 'desc' ? 'asc' : 'desc') : 'asc') . '&access=' . $this->access . '&limit=' . $this->limit); ?>" title="Sort by popularity">
								<?php echo Lang::txt('PLG_GROUPS_RESOURCES_SORT_BY_RATING'); ?>
							</a>
						</li>
					<?php } ?>
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
						$html .= '<ol class="resources results">'."\n";
						foreach ($category as $row)
						{
							$k++;
							$html .= $this->view('_item')
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
					echo '<p class="warning">' . Lang::txt('PLG_GROUPS_RESOURCES_NONE') . '</p>';
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
			$pageNav->setAdditionalUrlParam('active', 'resources');
			$pageNav->setAdditionalUrlParam('area', urlencode(stripslashes($this->active)));
			$pageNav->setAdditionalUrlParam('sort', $this->sort);
			$pageNav->setAdditionalUrlParam('access', $this->access);
			echo $pageNav->render();
			?>
			<div class="clearfix"></div>
		</div><!-- / .container -->
	</form>
</section>