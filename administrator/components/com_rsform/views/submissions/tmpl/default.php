<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.calendar');
?>
<script type="text/javascript">
function submitbutton(task)
{
	if (task == 'submissions.resend')
	{
		if (document.adminForm.boxchecked.value == 0)
			alert('<?php echo JText::sprintf('PLEASE MAKE A SELECTION FROM THE LIST TO', JText::_('resend')); ?>');
		else
			submitform(task);
	}
	else
		submitform(task);
}

<?php if (RSFormProHelper::isJ16()) { ?>
	Joomla.submitbutton = submitbutton;
<?php } ?>

function toggleCheckColumns()
{
	var tocheck = document.getElementById('checkColumns').checked;
	var staticcolumns = document.getElementsByName('staticcolumns[]');
	for (i=0; i<staticcolumns.length; i++)
		staticcolumns[i].checked = tocheck;
		
	var columns = document.getElementsByName('columns[]');
	for (i=0; i<columns.length; i++)
		columns[i].checked = tocheck;
}
</script>

<form action="index.php?option=com_rsform&amp;task=submissions.manage" method="post" name="adminForm" id="adminForm">
	<table class="adminform">
		<tr>
			<td width="100%">
				<?php echo JText::_('RSFP_VIEW_SUBMISSIONS_FOR'); ?> <?php echo $this->lists['forms']; ?>
				<?php echo JText::_( 'SEARCH' ); ?>
				<input type="text" name="search" id="search" value="<?php echo $this->escape($this->filter); ?>" class="text_area" onChange="document.adminForm.submit();" />
				<?php echo JText::_( 'DATE' ); ?> <?php echo $this->calendars['from']; ?> <?php echo JText::_('TO'); ?> <?php echo $this->calendars['to']; ?>
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="this.form.getElementById('search').value='';this.form.getElementById('dateFrom').value='';this.form.getElementById('dateTo').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<button type="button" onclick="toggleCustomizeColumns();"><?php echo JText::_('RSFP_CUSTOMIZE_COLUMNS'); ?></button>
				<div id="columnsContainer">
				<div id="columnsDiv">
					<input type="checkbox" onclick="toggleCheckColumns();" id="checkColumns" /> <label for="checkColumns"><strong><?php echo JText::_('RSFP_CHECK_ALL'); ?></strong></label><br />
				<?php $i = 0; ?>
				<?php foreach ($this->staticHeaders as $header) { ?>
					<input type="checkbox" <?php echo $this->isHeaderEnabled($header, 1) ? 'checked="checked"' : ''; ?> name="staticcolumns[]" value="<?php echo $this->escape($header); ?>" id="column<?php echo $i; ?>" /> <label for="column<?php echo $i; ?>"><?php echo JText::_('RSFP_'.$header); ?></label><br />
					<?php $i++; ?>
				<?php } ?>
				<?php foreach ($this->headers as $header) { ?>
					<input type="checkbox" <?php echo $this->isHeaderEnabled($header, 0) ? 'checked="checked"' : ''; ?> name="columns[]" value="<?php echo $this->escape($header); ?>" id="column<?php echo $i; ?>" /> <label for="column<?php echo $i; ?>"><?php echo $header != '_STATUS' ? $header : JText::_('RSFP_PAYPAL_STATUS'); ?></label><br />
					<?php $i++; ?>
				<?php } ?>
				<button type="button" onclick="submitbutton('submissions.columns')"><?php echo JText::_('Submit'); ?></button>
				</div>
				</div>
			</td>
		</tr>
	</table>
	
	<table class="adminlist">
		<thead>
		<tr>
			<th width="5"><?php echo JText::_('#'); ?></th>
			<th width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->submissions); ?>);" /></th>
			<?php foreach ($this->staticHeaders as $header) { ?>
			<th <?php echo !$this->isHeaderEnabled($header, 1) ? 'style="display: none"' : ''; ?> class="title" nowrap="nowrap" width="5"><?php echo JHTML::_('grid.sort', JText::_('RSFP_'.$header), $header, $this->sortOrder, $this->sortColumn); ?></th>
			<?php } ?>
			<?php foreach ($this->headers as $header) { ?>
			<th <?php echo !$this->isHeaderEnabled($header, 0) ? 'style="display: none"' : ''; ?> class="title"><?php echo JHTML::_('grid.sort', $header != '_STATUS' ? $header : JText::_('RSFP_PAYPAL_STATUS'), $header, $this->sortOrder, $this->sortColumn); ?></th>
			<?php } ?>
		</tr>
		</thead>
		<?php
		$i = 0;
		$k = 0;
		foreach ($this->submissions as $submissionId => $submission) { ?>
			<tr class="row<?php echo $k; ?>">
				<td align="center" width="30"><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td><?php echo JHTML::_('grid.id', $i, $submissionId); ?></td>
				<?php foreach ($this->staticHeaders as $header) { ?>
				<td <?php echo !$this->isHeaderEnabled($header, 1) ? 'style="display: none"' : ''; ?> nowrap="nowrap"><?php echo $submission[$header]; ?></td>
				<?php } ?>
				<?php foreach ($this->headers as $header) { ?>
				<td <?php echo !$this->isHeaderEnabled($header, 0) ? 'style="display: none"' : ''; ?>><?php echo isset($submission['SubmissionValues'][$header]['Value']) ? $submission['SubmissionValues'][$header]['Value'] : ''; ?></td>
				<?php } ?>
			</tr>
		<?php
			$i++;
			$k=1-$k;
		}
		?>
	</table>
	
	<table class="adminlist">
	<tfoot>
		<tr>
			<td>
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	</table>

	<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortOrder; ?>"/>
	<input type="hidden" name="formId" value="<?php echo $this->formId; ?>"/>
	<input type="hidden" name="task" value="submissions.manage"/>
	<input type="hidden" name="option" value="com_rsform"/>
	<input type="hidden" name="boxchecked" value="0" />
</form>

<?php JHTML::_('behavior.keepalive'); ?>