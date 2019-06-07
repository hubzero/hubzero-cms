<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('Solr Search: Components'));
Toolbar::addNew();
Toolbar::custom('activateIndex', 'publish', 'activateindex', 'COM_SEARCH_ADD_FACET', false);
Toolbar::custom('deleteIndex', 'unpublish', 'deactivateindex', 'COM_SEARCH_DELETE_FACET', true);
Toolbar::custom('trashIndex', 'trash', 'trashindex', 'COM_SEARCH_DELETE_FACET', true);
Toolbar::spacer();
Toolbar::custom('discover', 'refresh', 'refresh', 'COM_SEARCH_SOLR_DISCOVER', false);
Toolbar::spacer();
Toolbar::preferences($this->option, '550');
$this->css();
$this->js('searchable');
$option = $this->option;

Submenu::addEntry(
	Lang::txt('Overview'),
	'index.php?option='.$option.'&task=configure'
);
Submenu::addEntry(
	Lang::txt('Searchable Components'),
	'index.php?option='.$option.'&task=display&controller=searchable',
	true
);
Submenu::addEntry(
	Lang::txt('Index Blacklist'),
	'index.php?option='.$option.'&task=manageBlacklist'
);

$sort_dir = Request::getString('filter_order_Dir', 'asc');
$sort = Request::getString('filter_order', 'id');
?>

<section id="main" class="com_search">
<!-- Content begins -->

<?php $i = 0; ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'ID', 'id', $sort_dir, $sort); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'Title', 'title', $sort_dir, $sort); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'Active?', 'state', $sort_dir, $sort); ?></th>
				<th scope="col">Records</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->components as $component): ?>
			<tr class="row0">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $component->get('id') ?>" class="checkbox-toggle" />
				</td>
				<td class="priority-5">
					<?php echo $component->id; ?>
				</td>
				<td>
					<?php echo '<a href="' . Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $component->get('id')) . '">' . $component->title . '</a>'; ?>
				</td>
				<td>
					<?php 
						if ($component->get('state') == $component::STATE_INDEXED)
						{
							$alt  = 'Indexed';
							$cls  = 'publish';
							$task = 'deleteIndex';
						}
						else
						{
							$alt = 'Not Indexed';
							$cls = 'unpublished unpublishtask';
							$task = 'activateIndex';
						}
					?>

					<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $component->get('id') . '&' . Session::getFormToken() . '=1'); ?>">
						<span><?php echo $alt; ?></span>
					</a>
				</td>
				<td class="total">
					<?php 
						$componentName = $component->getQueryName();
						$componentQuery = $component->getSearchQuery('hubtype');
						$componentCount = !empty($this->componentCounts[$componentName]) ? $this->componentCounts[$componentName] : 0;
						$componentLink = Route::url('index.php?option=com_search&controller=' . $this->controller . '&task=documentListing&facet=' . $componentQuery);
					?>
					<?php if ($componentCount > 0): ?>
					<a href="<?php echo $componentLink;?>">
						<?php echo $componentCount; ?>
					</a>
					<?php else: ?>
						<?php echo $componentCount; ?>
					<?php endif; ?>
				</td>
				<td class="tasks">
					<?php if ($component->get('state') == $component::STATE_INDEXED): ?>
						<a class="button unpublishtask"  data-link="<?php echo $componentLink;?>" data-linktext="Rebuild Index" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=activateIndex' . '&id=' . $component->get('id') . '&' . Session::getFormToken() . '=1');?>">
							Rebuild Index
						</a>
					<?php endif; ?>
				</td>
			</tr>
			<?php $i++; ?>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="com_search" />
	<input type="hidden" name="controller" value="solr" />
	<input type="hidden" name="task" value="searchIndex" autocomplete="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($sort); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($sort_dir); ?>" />
	<?php echo Html::input('token'); ?>
</form>
</section><!-- / #main -->
