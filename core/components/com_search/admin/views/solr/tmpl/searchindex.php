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

if (isset($this->parent))
{
	Toolbar::title(Lang::txt('Solr Search Facets : ') . $this->parent->name);
	Toolbar::back();
}
else
{
	Toolbar::title(Lang::txt('Solr Search Facets'));
}
Toolbar::custom('addfacet', 'new', 'add', 'COM_SEARCH_ADD_FACET', false);
Toolbar::custom('deletefacet', 'delete', 'delete', 'COM_SEARCH_DELETE_FACET', true);
Toolbar::spacer();
Toolbar::preferences($this->option, '550');
$this->css('solr');
$option = $this->option;

\Submenu::addEntry(
	Lang::txt('Overview'),
	'index.php?option='.$option.'&task=configure'
);
\Submenu::addEntry(
	Lang::txt('Search Index'),
	'index.php?option='.$option.'&task=searchindex'
);
\Submenu::addEntry(
	Lang::txt('Index Blacklist'),
	'index.php?option='.$option.'&task=manageBlacklist'
);
?>

<?php if (isset($this->processing)) { ?>
<div class="info">
	<span><?php echo Lang::txt('COM_SEARCH_BUILDING_INDEX'); ?></span>
</div>
<?php } ?>

<?php if (isset($this->stalled)) { ?>
<div class="warning">
	<span><?php echo Lang::txt('COM_SEARCH_INDEX_STALLED'); ?></span>
</div>
<?php } ?>

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

<form action="/administrator/index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(1);" /></th>
				<th scope="col" class="priority-5"><a href="#" onclick="Joomla.tableOrdering('id','asc','');return false;" title="Click to sort by this column" class="active desc sort">ID</a></th>
				<th scope="col"><a href="#" onclick="Joomla.tableOrdering('title','asc','');return false;" title="Click to sort by this column" class="sort">Title</a></th>
				<th scope="col" class="priority-2"><a href="#" onclick="Joomla.tableOrdering('state','asc','');return false;" title="Click to sort by this column" class="sort">State</a></th>
				<th scope="col" class="priority-4"><a href="#" onclick="Joomla.tableOrdering('access','asc','');return false;" title="Click to sort by this column" class="sort"><?php echo Lang::txt('COM_SEARCH_COUNT'); ?></a></th>

				<?php if (!isset($this->parent)): ?>
				<th scope="col"><?php echo Lang::txt('COM_SEARCH_FACETS'); ?></th>
				<?php endif; ?>

			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->facets as $facet): ?>
			<tr class="row0">
				<td>
					<input type="checkbox" name="id[]" id="cb0" value="<?php echo $facet->id; ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td class="priority-5">
					<?php echo $facet->id; ?>
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=com_search&controller=solr&task=editFacet&id=' . $facet->id); ?>">
						<?php echo $facet->name; ?>
						<?php $protected = $facet->get('protected'); ?>
						<?php if ($protected == 1)
						{
							dlog($facet);
							echo '(' . Lang::txt('COM_SEARCH_PROTECTED') . ')';
						}
						?>
					</a>
				</td>
				<td class="priority-2">
					<?php
						if ($facet->state == 0)
						{
							$class = 'unpublish';
						}
						else
						{
							$class = 'publish';
						}
					?>

					<a class="state <?php echo $class; ?>" href="<?php echo Route::url('index.php?option=com_search&task=saveFacet&action=togglestate&id=' 
						. $facet->id . '&' . Session::getFormToken() . '=1'
						. '&return' . Request::current()
						) ?>" title="Set this to unpublish">
						<span>Published</span>
					</a>
				</td>
				<td class="priority-4">
					<span>
					<a href="<?php echo Route::url('index.php?option=com_search&task=documentListing&facet=' . $facet->facet); ?>">
						<?php echo $facet->count; ?>
					</a>
					</span>
				</td>
				<?php if (!isset($this->parent)): ?>
				<td>
					<a class="glyph category" href="<?php echo Route::url('index.php?option=com_search&task=searchIndex&parent_id=' . $facet->id .  '&' . Session::getFormToken() . '=1');?>">
						<?php echo $facet->children()->count(); ?></span>
					</a>
				</td>
				<?php endif; ?>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="com_search" />
	<input type="hidden" name="controller" value="solr" />
	<input type="hidden" name="task" value="searchIndex" autocomplete="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="id" />
	<input type="hidden" name="filter_order_Dir" value="DESC" />
	<?php //echo Html::input('token'); ?>
</form>
</section><!-- / #main -->
</section><!-- / #component-content -->
