<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Pathway::append(
	Lang::txt('COM_WIKI_SEARCH'),
	$this->page->link('base') . '&pagename=Special:Search'
);

$database = App::get('db');

$limit = Request::getInt('limit', Config::get('list_limit'));
$start = Request::getInt('limitstart', 0);
$term  = Request::getString('q', '');

$filters = array('state' => array(0, 1));

if ($space = Request::getString('namespace', ''))
{
	$filters['namespace'] = urldecode($space);
}

$pages    = \Components\Wiki\Models\Page::blank()->getTableName();
$versions = \Components\Wiki\Models\Version::blank()->getTableName();

$weight = '(match(' . $pages . '.title) against (' . $database->Quote($term) . ') + match(' . $versions . '.pagetext) against (' . $database->Quote($term) . '))';

$rows = $this->book->pages($filters)
	->select($pages . '.*')
	->select($versions . '.created_by')
	->select($versions . '.summary')
	->select($weight, 'weight')
	->join($versions, $versions . '.id', $pages . '.version_id')
	->whereRaw($weight . ' > 0')
	->order('weight', 'desc')
	->paginated()
	->rows();
?>
<form action="<?php echo Route::url($this->page->link('base') . '&pagename=Special:Search'); ?>" method="get">
	<div class="container data-entry">
		<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_WIKI_SEARCH'); ?>" />
		<fieldset class="entry-search">
			<legend><?php echo Lang::txt('COM_WIKI_SEARCH_LEGEND'); ?></legend>
			<label for="entry-search-field"><?php echo Lang::txt('COM_WIKI_SEARCH_PLACEHOLDER'); ?></label>
			<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($term); ?>" placeholder="<?php echo Lang::txt('COM_WIKI_SEARCH_PLACEHOLDER'); ?>" />
		</fieldset>
	</div><!-- / .container -->

	<div class="container">
		<table class="file entries">
			<thead>
				<tr>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_TITLE'); ?>
					</th>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_PATH'); ?>
					</th>
					<th scope="col">
						<?php echo Lang::txt('COM_WIKI_COL_MODIFIED'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ($rows->count())
				{
					foreach ($rows as $row)
					{
						?>
						<tr>
							<td>
								<a href="<?php echo Route::url($row->link()); ?>">
									<?php echo $this->escape(stripslashes($row->title)); ?>
								</a>
							</td>
							<td>
								<?php echo Route::url($row->link()); ?>
							</td>
							<td>
								<time datetime="<?php echo $row->get('modified'); ?>"><?php echo $row->get('modified'); ?></time>
							</td>
						</tr>
						<?php
					}
				}
				else
				{
					?>
					<tr>
						<td colspan="3">
							<?php echo Lang::txt('COM_WIKI_NONE'); ?>
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<?php
		$pageNav = $rows->pagination;
		$pageNav->setAdditionalUrlParam('scope', $this->page->get('scope'));
		$pageNav->setAdditionalUrlParam('pagename', $this->page->get('pagename'));

		echo $pageNav;
		?>
		<div class="clear"></div>
	</div>
</form>