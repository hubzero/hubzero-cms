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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('Solr Search: Components'));
Toolbar::custom('activateIndex', 'publish', 'activateindex', 'COM_SEARCH_ADD_FACET', false);
Toolbar::custom('deleteIndex', 'unpublish', 'deactivateindex', 'COM_SEARCH_DELETE_FACET', true);
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
?>

<section id="main" class="com_search">
<!-- Content begins -->
<script type="text/javascript">
function submitbutton(pressbutton)
{
	console.log(pressbutton);
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>
<?php $i = 0; ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->components); ?>);" /></th>
				<th scope="col" class="priority-5"><a href="#" onclick="Joomla.tableOrdering('id','asc','');return false;" title="Click to sort by this column" class="active desc sort">ID</a></th>
				<th scope="col"><a href="#" onclick="Joomla.tableOrdering('title','asc','');return false;" title="Click to sort by this column" class="sort">Title</a></th>
				<th scope="col"><a href="#" onclick="Joomla.tableOrdering('state','asc','');return false;" title="Click to sort by this column" class="sort">Active?</a></th>
				<th scope="col">Records</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->components as $component): ?>
			<tr class="row0">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $component->get('id') ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td class="priority-5">
					<?php echo $component->id; ?>
				</td>
				<td>
					<?php echo '<a href="' . Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $component->get('id')) . '">' . $component->title . '</a>'; ?>
				</td>
				<td>
					<?php 
						if ($component->get('state') == 1)
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
						$componentCount = !empty($this->componentCounts[$componentName]) ? $this->componentCounts[$componentName] : 0;
						$componentLink = Route::url('index.php?option=com_search&controller=' . $this->controller . '&task=documentListing&facet=hubtype:' . $component->getSearchNamespace());
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
					<?php if ($component->get('state') == 1): ?>
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
	<input type="hidden" name="filter_order" value="id" />
	<input type="hidden" name="filter_order_Dir" value="DESC" />
	<?php echo Html::input('token'); ?>
</form>
</section><!-- / #main -->
