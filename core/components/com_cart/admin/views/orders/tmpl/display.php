<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_HZEXEC_') or die();

$canDo = \Components\Cart\Admin\Helpers\Permissions::getActions('orders');

Toolbar::title(Lang::txt('COM_CART') . ': Orders', 'cart.png');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}

Toolbar::custom('download', 'download.png', '', 'Download CSV', false);

Toolbar::spacer();
Toolbar::help('downloads');
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
<script>
	$( function() {
		var dateFormat = "mm/dd/yy",
			from = $( "#filter-report-from" )
				.datepicker({
					defaultDate: "+1w",
					changeMonth: true,
					numberOfMonths: 1
				})
				.on( "change", function() {
					to.datepicker( "option", "minDate", getDate( this ) );
				}),
			to = $( "#filter-report-to" ).datepicker({
					defaultDate: "+1w",
					changeMonth: true,
					numberOfMonths: 1
				})
				.on( "change", function() {
					from.datepicker( "option", "maxDate", getDate( this ) );
				});

		function getDate( element ) {
			var date;
			try {
				date = $.datepicker.parseDate( dateFormat, element.value );
			} catch( error ) {
				date = null;
			}

			return date;
		}
	} );
</script>

<?php
$this->view('_submenu')
	->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span5">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
				<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('JSEARCH_FILTER'); ?>" />
			</div>
			<div class="col span7 align-right">
				<label for="filter-reportnotes"><?php echo Lang::txt('COM_CART_SHOW_NOTES'); ?>:</label>
				<select name="report-notes" id="filter-report-notes" onchange="this.form.submit();">
					<option value="0"<?php if ($this->filters['report-notes'] === 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CART_SHOW_NOTES_ALL'); ?></option>
					<option value="1"<?php if ($this->filters['report-notes'] === 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CART_SHOW_NOTES_ONLY'); ?></option>
				</select>
				&nbsp;&nbsp;
				<label for="filter-report-from">From:</label>
				<input type="text" name="report-from" id="filter-report-from" value="<?php echo $this->escape($this->filters['report-from']); ?>" placeholder="<?php echo Lang::txt('From'); ?>" />
				&mdash;
				<label for="filter-report-to">To:</label>
				<input type="text" name="report-to" id="filter-report-to" value="<?php echo $this->escape($this->filters['report-to']); ?>" placeholder="<?php echo Lang::txt('To'); ?>" />
				<input type="submit" value="<?php echo Lang::txt('Update'); ?>" />
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
		<?php if ($this->filters['uidNumber']) { ?>
			<tr>
				<th colspan="6"><?php echo Lang::txt('COM_CART_ORDERS_FOR'); ?>: <?php
				$user = User::getInstance($this->filters['uidNumber']);

				echo ($user->get('id') ? $user->get('name') . ' (' . $user->get('username') . ')' : Lang::txt('COM_CART_USER_ID') . ': ' . $this->filters['uidNumber']);
				?>
					<button type="button" onclick="$('#filter_uidNumber').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
				</th>
			</tr>
		<?php } ?>
			<tr>
				<th scope="col"><?php echo Html::grid('sort', 'COM_CART_ORDER_ID', 'tId', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo LANG::txt('COM_CART_ORDER_TOTAL'); ?></th>
				<th scope="col"><?php echo LANG::txt('COM_CART_ORDER_NUM_ITEMS'); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_CART_ORDER_PLACED', 'tLastUpdated', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_CART_ORDERED_BY', 'Name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'Payment method', 'tiPayment', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php
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
			$i = 0;
			foreach ($this->rows as $row)
			{
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<span>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=view&id=' . $row->tId); ?>">
								<?php echo $this->escape(stripslashes($row->tId)); ?>
							</a>
						</span>
					</td>
					<td>
						<span><?php echo $this->escape(stripslashes($row->tiTotal)); ?></span>
					</td>
					<td>
						<span>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=items&order=' . $row->tId); ?>">
								<?php echo $this->escape(stripslashes($row->tiItemsQty)); ?>
							</a>
						</span>
					</td>
					<td>
						<span><?php echo $this->escape(stripslashes($row->tLastUpdated)); ?></span>
					</td>
					<td>
						<?php if ($row->uidNumber) { ?>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&uidNumber=' . $row->uidNumber); ?>">
						<?php } ?>
						<span><?php echo ($row->name ? $this->escape(stripslashes($row->name)) : Lang::txt('COM_CART_UNKNOWN')); ?></span>
						<?php if ($row->uidNumber) { ?>
							</a>
						<?php } ?>
					</td>
					<td>
						<span><?php echo $this->escape(stripslashes($row->tiPayment)); ?></span>
					</td>
				</tr>
				<?php
				$i++;
				$k = 1 - $k;
			}
			?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	<input type="hidden" name="uidNumber" id="filter_uidNumber" value="<?php echo $this->filters['uidNumber']; ?>" />

	<?php echo Html::input('token'); ?>
</form>