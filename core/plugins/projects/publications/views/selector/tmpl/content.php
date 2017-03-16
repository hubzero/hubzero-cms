<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$pubParams = $this->publication->params;


$filters = array(
	'category'    => Request::getVar('category', ''),
	'sortby'      => Request::getCmd('sortby', 'date'),
	'limit'       => Request::getInt('limit', 25), //Config::get('list_limit')),
	'start'       => Request::getInt('limitstart', 0),
	'search'      => Request::getVar('search', ''),
	'tag'         => trim(Request::getVar('tag', '', 'request', 'none', 2)),
	'tag_ignored' => array()
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
<input id="pub-search" name="search" placeholder="Start typing here" type="text" data-list=".pub-selector" autocomplete="off" />
<ul class="pub-selector" id="pub-selector">
	<?php
	foreach ($results as $item)
	{
		$selected = false;

		$liId = 'choice-' . $item->get('id');

		/*$info = $item->info;
		if ($item->url)
		{
			$info .= ' <a href="' . $item->url . '" target="_blank">' . Lang::txt('Read license terms &rsaquo;') . '</a>';
		}

		$icon = $item->icon;
		$icon = str_replace('/components/com_publications/assets/img/', '/core/components/com_publications/site/assets/img/', $icon);
		*/
		$authors = $pa->getAuthors($item->get('version_id'));

		$description = '';
		if ($item->get('abstract'))
		{
			$description = \Hubzero\Utility\String::truncate(stripslashes($item->get('abstract')), 300) . "\n";
		}
		else if ($item->get('description'))
		{
			$description = \Hubzero\Utility\String::truncate(stripslashes($item->get('description')), 300) . "\n";
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
				<?php echo $item->get('title'); ?><br />
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
	<?php } ?>
</ul>
<?php echo $pageNav->render(); ?>