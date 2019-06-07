<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$pubParams = $this->publication->params;


$filters = array(
	'category'    => Request::getString('category', ''),
	'sortby'      => Request::getCmd('sortby', 'title'),
	'limit'       => Request::getInt('limit', 25), //Config::get('list_limit')),
	'start'       => Request::getInt('limitstart', 0),
	'search'      => Request::getString('search', ''),
	'tag'         => trim(Request::getString('tag', '')),
	'tag_ignored' => array(),
	'all_versions' => true
);
// Instantiate a publication object
$model = new \Components\Publications\Models\Publication();

// Execute count query
$total = $model->entries('count', $filters);

// Run query with limit
$results = $model->entries('list', $filters);

// Initiate paging
$pageNav = new \Hubzero\Pagination\Paginator(
	$total,
	$filters['start'],
	$filters['limit']
);
$pageNav->setAdditionalUrlParam('task', '');
$pageNav->setAdditionalUrlParam('active', 'publications');
$pageNav->setAdditionalUrlParam('action', 'select');
$pageNav->setAdditionalUrlParam('pid', $this->publication->id);
$pageNav->setAdditionalUrlParam('vid', $this->publication->version_id);

$database = \App::get('db');
$pa = new \Components\Publications\Tables\Author($database);
?>
<label for="pub-search"><?php echo Lang::txt('Search'); ?></label>
<input id="pub-search" name="search" placeholder="Start typing here" type="text" data-list=".pub-selector-" autocomplete="off" />
<div id="pub-selector-results">
	<ul class="pub-selector" id="pub-selector">
		<?php
		if (count($results) > 0)
		{
		foreach ($results as $item)
		{
			$selected = false;

			$liId = 'choice-' . $item->get('version_id');

			/*$info = $item->info;
			if ($item->url)
			{
				$info .= ' <a href="' . $item->url . '" rel="nofollow external">' . Lang::txt('Read license terms &rsaquo;') . '</a>';
			}

			$icon = $item->icon;
			$icon = str_replace('/components/com_publications/assets/img/', '/core/components/com_publications/site/assets/img/', $icon);
			*/
			$authors = $pa->getAuthors($item->get('version_id'));

			$description = '';
			if ($item->get('abstract'))
			{
				$description = \Hubzero\Utility\Str::truncate(stripslashes($item->get('abstract')), 300) . "\n";
			}
			else if ($item->get('description'))
			{
				$description = \Hubzero\Utility\Str::truncate(stripslashes($item->get('description')), 300) . "\n";
			}

			$info = array();
			if ($item->get('category'))
			{
				$info[] = $item->get('cat_name');
			}
			if ($item->get('doi'))
			{
				$info[] = 'doi:' . $item->get('doi');
			}
			?>
			<li class="type-publication allowed <?php if ($selected) { echo ' selectedfilter'; } ?>" id="<?php echo $liId; ?>">
				<div class="item-thumb"><img src="<?php echo Route::url($item->link('thumb')); ?>" width="40" height="40" alt=""/></div>
				<!-- <span class="item-info"><?php echo implode(' <span>-</span> ', $info); ?></span> -->
				<span class="item-wrap">
					<?php echo $item->get('title'); ?> <span class="item-version">(<?php echo $item->get('version_label'); ?>)</span><br />
					<span class="item-info"><?php echo implode(' <span>-</span> ', $info); ?></span>
				</span>
				<span class="item-fullinfo">
					<?php echo $description; ?>
					<p class="details">
						<?php
						if ($authors)
						{
							echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_AUTHORS_LIST') . ': ' . \Components\Publications\Helpers\Html::showContributors($authors, false, true);
						}
						?>
					</p>
				</span>
			</li>
		<?php
			}
		}
		else
		{
			?>
			<li><?php echo Lang::txt('No results found.'); ?></li>
			<?php
		}
		?>
	</ul>
	<?php echo $pageNav->render(); ?>
</div>
