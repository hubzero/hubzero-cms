<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Breadcrumbs
Pathway::append(
	Lang::txt('COM_WIKI_SPECIAL_LINKS'),
	$this->page->link()
);

// Sorting
$sort = strtolower(Request::getString('sort', 'title'));
if (!in_array($sort, array('timestamp', 'title'))):
	$sort = 'timestamp';
endif;

$dir = strtoupper(Request::getString('dir', 'DESC'));
if (!in_array($dir, array('ASC', 'DESC'))):
	$dir = 'DESC';
endif;

$altdir = ($dir == 'ASC') ? 'DESC' : 'ASC';

// Pagination
$limit = Request::getInt('limit', Config::get('list_limit'));
$start = Request::getInt('limitstart', 0);

// The current page
$page = $this->book->pages()
	->whereEquals('pagename', Request::getString('page', ''))
	->whereEquals('path', Request::getString('scope', ''))
	->row();

if ($v = Request::getInt('version', 0)):
	$revision = $page->versions()
		->whereEquals('id', $v)
		->row();
else:
	$revision = $page->version();
endif;

$permalink = rtrim(Request::base(), '/') . '/' . ltrim(Route::url($page->link() . '&version=' . $revision->get('version')), '/');

// Find what links to the current page
$l = Components\Wiki\Models\Link::blank()->getTableName();
$p = Components\Wiki\Models\Page::blank()->getTableName();

$rows = Components\Wiki\Models\Page::all()
	->select($p . '.*')
	->select($l . '.timestamp')
	->join($l, $l . '.scope_id', $p . '.id', 'inner')
	->whereEquals($p . '.scope', $page->get('scope'))
	->whereEquals($p . '.scope_id', $page->get('scope_id'))
	->whereEquals($p . '.state', Components\Wiki\Models\Page::STATE_PUBLISHED)
	->whereEquals($l . '.scope', 'internal')
	->whereEquals($l . '.scope_id', $page->get('id'))
	->order($sort, $dir)
	->paginated()
	->rows();
?>
<form method="get" action="<?php echo Route::url($this->page->link()); ?>">
	<p><?php echo Lang::txt('The following pages link to %s', '<a href="' . Route::url($page->link()) . '">' . $this->escape($page->get('title')) . '</a>'); ?></p>
	<div class="container">
		<table class="file entries">
			<thead>
				<tr>
					<th scope="col">
						<a<?php if ($sort == 'timestamp') { echo ' class="active"'; } ?> href="<?php echo Route::url($this->page->link() . '&sort=timestamp&dir=' . $altdir); ?>">
							<?php if ($sort == 'timestamp') { echo ($dir == 'ASC') ? '&uarr;' : '&darr;'; } ?> <?php echo Lang::txt('COM_WIKI_COL_DATE'); ?>
						</a>
					</th>
					<th scope="col">
						<a<?php if ($sort == 'title') { echo ' class="active"'; } ?> href="<?php echo Route::url($this->page->link() . '&sort=title&dir=' . $altdir); ?>">
							<?php if ($sort == 'title') { echo ($dir == 'ASC') ? '&uarr;' : '&darr;'; } ?> <?php echo Lang::txt('COM_WIKI_COL_TITLE'); ?>
						</a>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if ($rows->count()):
				foreach ($rows as $row):
					?>
					<tr>
						<td>
							<time datetime="<?php echo $this->escape($row->get('timestamp')); ?>"><?php echo $this->escape(Date::of($row->get('timestamp'))->toLocal()); ?></time>
						</td>
						<td>
							<a href="<?php echo Route::url($row->link()); ?>">
								<?php echo $this->escape(stripslashes($row->get('title',''))); ?>
							</a>
						</td>
					</tr>
					<?php
				endforeach;
			else:
				?>
				<tr>
					<td colspan="4">
						<?php echo Lang::txt('COM_WIKI_NONE'); ?>
					</td>
				</tr>
				<?php
			endif;
			?>
			</tbody>
		</table>

		<?php
		$pageNav = $rows->pagination;
		$pageNav->setAdditionalUrlParam('scope', $this->page->get('scope'));
		$pageNav->setAdditionalUrlParam('pagename', $this->page->get('pagename'));

		echo $pageNav;
		?>
		<div class="clearfix"></div>
	</div>
</form>
