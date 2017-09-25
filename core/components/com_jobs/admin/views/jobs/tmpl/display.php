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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Jobs\Helpers\Permissions::getActions('job');

Toolbar::title(Lang::txt('COM_JOBS'), 'job');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_jobs', '550');
	Toolbar::spacer();
}
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
}
Toolbar::spacer();
Toolbar::help('jobs');

$this->css();

Html::behavior('tooltip');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_JOBS_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo Lang::txt('COM_JOBS_GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_JOBS_COL_CODE'); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_JOBS_COL_TITLE', 'title', @$this->filters['sortdir'], @$this->filters['sortby']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_JOBS_COL_COMPANY', 'location', @$this->filters['sortdir'], @$this->filters['sortby']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_JOBS_COL_STATUS', 'status', @$this->filters['sortdir'], @$this->filters['sortby']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_JOBS_COL_OWNER', 'adminposting', @$this->filters['sortdir'], @$this->filters['sortby']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_JOBS_COL_ADDED', 'added', @$this->filters['sortdir'], @$this->filters['sortby']); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_JOBS_EXPIRATION'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_JOBS_COL_APPLICATIONS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php
				// Initiate paging
				echo $this->pagination(
					$this->total,
					$this->filters['start'],
					$this->filters['limit']
				);
				?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;

		$now = Date::toSql();

		$database = App::get('db');

		$jt = new \Components\Jobs\Tables\JobType($database);
		$jc = new \Components\Jobs\Tables\JobCategory($database);

		for ($i=0, $n=count($this->rows); $i < $n; $i++)
		{
			$row =& $this->rows[$i];

			$admin = $row->employerid == 1 ? 1 : 0;
			$adminclass = $admin ? 'class="adminpost"' : '';

			$curtype = $row->type > 0 ? $jt->getType($row->type) : '';
			$curcat  = $row->cid > 0  ? $jc->getCat($row->cid)   : '';

			// Build some publishing info
			$info  = Lang::txt('COM_JOBS_FIELD_CREATED') . ': ' . Date::of($row->added)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . '<br />';
			$info .= Lang::txt('COM_JOBS_FIELD_CREATOR') . ': ' . $row->addedBy;
			$info .= $admin ? ' ' . Lang::txt('COM_JOBS_ADMIN') : '';
			$info .= '<br />';
			$info .= Lang::txt('COM_JOBS_FIELD_CATEGORY') . ': ' . $curcat . '<br />';
			$info .= Lang::txt('COM_JOBS_FIELD_TYPE') . ': ' . $curtype . '<br />';

			// Get the published status
			switch ($row->status)
			{
				case 0:
					$alt   = Lang::txt('COM_JOBS_STATUS_PENDING');
					$class = 'post_pending';
				break;
				case 1:
					$alt =  $row->inactive && $row->inactive < $now
						 ? Lang::txt('COM_JOBS_STATUS_EXPIRED')
						 : Lang::txt('COM_JOBS_STATUS_ACTIVE');
					$class = $row->inactive && $row->inactive < $now
						   ? 'post_invalidsub'
						   : 'post_active';
				break;
				case 2:
					$alt   = Lang::txt('COM_JOBS_STATUS_DELETED');
					$class = 'post_deleted';
				break;
				case 3:
					$alt   = Lang::txt('COM_JOBS_STATUS_INACTIVE');
					$class = 'post_inactive';
				break;
				case 4:
					$alt   = Lang::txt('COM_JOBS_STATUS_DRAFT');
					$class = 'post_draft';
				break;
				default:
					$alt   = '-';
					$class = '';
				break;
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo Html::grid('id', $i, $row->id, false, 'id'); ?>
				</td>
				<td class="priority-4">
					<?php echo $this->escape($row->code); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a class="editlinktip hasTip" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>" title="<?php echo Lang::txt('COM_JOBS_PUBLISH_INFO'); ?>::<?php echo $info; ?>">
							<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
						</a>
					<?php } else { ?>
						<span class="editlinktip hasTip" title="<?php echo Lang::txt('COM_JOBS_PUBLISH_INFO'); ?>::<?php echo $info; ?>">
							<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-3">
					<span class="glyph company"><?php echo $this->escape($row->companyName); ?></span>, <br />
					<span class="glyph location"><?php echo $this->escape($row->companyLocation); ?></span>
				</td>
				<td>
					<span class="<?php echo $class; ?>">
						<span><?php echo $alt; ?></span>
					</span>
				</td>
				<td class="priority-3">
					<span <?php echo $adminclass; ?>>
						<span><?php echo ($admin ? Lang::txt('COM_JOBS_ADMIN') : ''); ?></span>
					</span>
				</td>
				<td class="priority-4">
					<time datetime="<?php echo $row->added; ?>"><?php echo Date::of($row->added)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
				</td>
				<td class="priority-4">
					<?php if ($row->expiredate != "0000-00-00 00:00:00"): ?>
						<time datetime="<?php echo $row->expiredate; ?>"><?php echo Date::of($row->expiredate)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
					<?php else: ?>
						<span><?php echo Lang::txt('COM_JOBS_NEVER_EXPIRES'); ?></span> 
					<?php endif; ?>
				</td>
				<td class="priority-2">
					<?php echo $row->applications; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sortby']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sortdir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>
