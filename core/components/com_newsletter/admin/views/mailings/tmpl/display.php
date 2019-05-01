<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt( 'COM_NEWSLETTER_NEWSLETTER_MAILINGS' ), 'mailing');
Toolbar::spacer();
Toolbar::custom('tracking', 'stats', '', 'COM_NEWSLETTER_TOOLBAR_STATS');
Toolbar::custom('stop', 'trash', '', 'COM_NEWSLETTER_TOOLBAR_STOP');
Toolbar::spacer();
Toolbar::preferences($this->option, '550');

$this->js();
?>

<?php if ($this->getError()) : ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php endif; ?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="admin-form" data-confirm-stop="<?php echo Lang::txt('COM_NEWSLETTER_MAILING_STOP_CHECK'); ?>">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_NEWSLETTER_FILTER_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" value="<?php echo Lang::txt('COM_NEWSLETTER_GO'); ?>" />
		<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_NEWSLETTER_NEWSLETTER', 'subject', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_NEWSLETTER_MAILING_DATE', 'date', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_NEWSLETTER_MAILING_PERCENT_COMPLETE'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_NEWSLETTER_MAILING_REOCCUR'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5"><?php
				// initiate paging
				echo $this->mailings->pagination;
				$k = 0;

				?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (count($this->mailings) > 0) { ?>
				<?php foreach ($this->mailings as $mailing) { ?>
					<tr>
						<td>
							<input type="checkbox" name="id[]" id="cb<?php echo $k; ?>" value="<?php echo $mailing->id; ?>" class="checkbox-toggle" />
						</td>
						<td>
							<?php echo $mailing->newsletter->get('name', Lang::txt('COM_NEWSLETTER_UNKNOWN')); ?>
						</td>
						<td class="priority-3">
							<?php echo Date::of($mailing->date)->toLocal("F d, Y @ g:ia"); ?>
						</td>
						<td class="priority-2">
							<?php
								if ($mailing->emails_total != 0)
								{
									echo number_format(($mailing->emails_sent/$mailing->emails_total) * 100, 2) . ' %';
								}
								else
								{
									echo '0%';
								}
							 ?>
							(<?php echo Lang::txt('COM_NEWSLETTER_NUM_OF_EMAILS_SENT', number_format($mailing->emails_sent), number_format($mailing->emails_total)); ?>)
						</td>
						<td class="priority-4">
							<?php
								switch ($mailing->newsletter->get('autogen'))
								{
									case 0:
										echo Lang::txt("N/A");
									break;
									case 1:
										echo Lang::txt("Daily");
									break;
									case 2:
										echo Lang::txt("Weekly");
									break;
									case 3:
										echo Lang::txt("Monthy");
									break;
								}
							?>
						</td>
					</tr>
				<?php $k++; } ?>
			<?php } else { ?>
				<tr>
					<td colspan="5">
						<?php echo Lang::txt('COM_NEWSLETTER_NO_MAILINGS'); ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
