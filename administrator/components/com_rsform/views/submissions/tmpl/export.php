<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
?>
<script type="text/javascript">
function submitbutton(task)
{
	var totalHeaders = <?php echo count($this->previewArray); ?>;
	
	if (task == 'submissions.export.task')
	{
		var isChecked = false;
		for (var i=1; i<=totalHeaders; i++)
			if (document.getElementById('header' + i).checked)
			{
				isChecked = true;
				break;
			}
		
		if (isChecked)
			submitform(task);
		else
			alert('<?php echo JText::_('RSFP_EXPORT_PLEASE_SELECT', true); ?>');
	}
	else
		submitform(task);
}

<?php if (RSFormProHelper::isJ16()) { ?>
	Joomla.submitbutton = submitbutton;
<?php } ?>

function updateCSVPreview()
{
	<?php if ($this->exportType != 'csv') { ?>
	return;
	<?php } ?>
	var form = document.adminForm;
	var headersPre = document.getElementById('headersPre');
	var rowPre = document.getElementById('rowPre');
	var delimiter = form.ExportDelimiter.value;
	var enclosure = form.ExportFieldEnclosure.value;
	var totalHeaders = <?php echo count($this->previewArray); ?>;
	
	var headers = new Array();
	var previewArray = new Array();
	var orderArray = new Array();
	
	for (var i=1; i<=totalHeaders; i++)
		if (document.getElementById('header' + i).checked)
		{
			var header = document.getElementById('header' + i).value;
			
			var order = document.getElementsByName('ExportOrder[' + header + ']')[0].value;
			orderArray.push(order + '_' + header);
		}
	
	orderArray.sort(function (a,b) {
			a = a.split('_');
			a = a[0];
			b = b.split('_');
			b = b[0];
			return a - b;
		});
		
	for (var i=0; i<orderArray.length; i++)
	{
		var header = orderArray[i].split('_');
		var header = enclosure + header[1] + enclosure;
		
		headers.push(header);
	}
	
	headersPre.innerHTML = headers.join(delimiter);
	headersPre.style.display = form.ExportHeaders.checked ? '' : 'none';
	
	for (var i=1; i<=headers.length; i++)
	{
		var item = enclosure + 'Value ' + i + enclosure;
		previewArray.push(item);
	}
	
	rowPre.innerHTML = previewArray.join(delimiter);
}

function toggleCheckColumns()
{
	var tocheck = document.getElementById('checkColumns').checked;
	var totalHeaders = <?php echo count($this->previewArray); ?>;
	
	for (var i=1; i<=totalHeaders; i++)
		document.getElementById('header' + i).checked = tocheck;
	
	updateCSVPreview();
}
</script>

<form action="index.php?option=com_rsform" method="post" id="adminForm" name="adminForm">
	<?php echo $this->tabs->startPane('export'); ?>
	<?php echo $this->tabs->startPanel(JText::_('RSFP_EXPORT_SELECT_FIELDS'), 'export-fields'); ?>
	<table class="adminform" border="0">
	<tr>
		<td>
			<input type="radio" name="ExportRows" id="ExportRowsAll" value="0" <?php echo $this->exportAll ? 'checked="checked"' : ''; ?> />
			<label for="ExportRowsAll"><?php echo JText::_('RSFP_EXPORT_ALL_ROWS'); ?></label>
			<input type="radio" name="ExportRows" id="ExportRowsSelected" value="<?php echo implode(',', $this->exportSelected); ?>" <?php echo !$this->exportAll ? 'checked="checked"' : ''; ?> />
			<label for="ExportRowsSelected"><?php echo JText::_('RSFP_EXPORT_SELECTED_ROWS'); ?> (<?php echo $this->exportSelectedCount; ?>) </label>
		</td>
	</tr>
	<tr>
		<td>
		<table class="adminlist" style="width: 500px" width="500">
			<tr>
				<td><input type="checkbox" onclick="toggleCheckColumns();" id="checkColumns" /></td>
				<td colspan="2"><label for="checkColumns"><strong><?php echo JText::_('RSFP_CHECK_ALL'); ?></strong></label></td>
			</tr>
			<thead>
			<tr>
				<th class="title" width="5" nowrap="nowrap"><?php echo JText::_('RSFP_EXPORT'); ?></th>
				<th class="title"><?php echo JText::_('RSFP_EXPORT_SUBMISSION_INFO'); ?></th>
				<th class="title" width="5" nowrap="nowrap"><?php echo JText::_('RSFP_EXPORT_COLUMN_ORDER'); ?></th>
			</tr>
			</thead>
			<?php $k = 0; ?>
			<?php $i = 1; ?>
			<?php foreach ($this->staticHeaders as $header) { ?>
			<tr class="row<?php echo $k; ?>">
				<td><input type="checkbox" onchange="updateCSVPreview();" name="ExportSubmission[<?php echo $header; ?>]" id="header<?php echo $i; ?>" value="<?php echo $header; ?>" <?php echo $this->isHeaderEnabled($header, 1) ? 'checked="checked"' : ''; ?> /></td>
				<td><label for="header<?php echo $i; ?>"><?php echo JText::_('RSFP_'.$header); ?></label></td>
				<td><input type="text" onkeyup="updateCSVPreview();" style="text-align: center" name="ExportOrder[<?php echo $header; ?>]" value="<?php echo $i; ?>" size="3"/></td>
			</tr>
			<?php $i++; ?>
			<?php $k=1-$k; ?>
			<?php } ?>
			<thead>
			<tr>
				<th class="title" width="5" nowrap="nowrap"><?php echo JText::_('RSFP_EXPORT'); ?></th>
				<th class="title"><?php echo JText::_('RSFP_EXPORT_COMPONENTS'); ?></th>
				<th class="title" width="5" nowrap="nowrap"><?php echo JText::_('RSFP_EXPORT_COLUMN_ORDER'); ?></th>
			</tr>
			</thead>
			<?php foreach ($this->headers as $header) { ?>
			<tr class="row<?php echo $k; ?>">
				<td><input type="checkbox" onchange="updateCSVPreview();" name="ExportComponent[<?php echo $header; ?>]" id="header<?php echo $i; ?>" value="<?php echo $header; ?>" <?php echo $this->isHeaderEnabled($header, 0) ? 'checked="checked"' : ''; ?> /></td>
				<td><label for="header<?php echo $i; ?>"><?php echo $header != '_STATUS' ? $header : JText::_('RSFP_PAYPAL_STATUS'); ?></label></td>
				<td><input type="text" onkeyup="updateCSVPreview();" style="text-align: center" name="ExportOrder[<?php echo $header; ?>]" value="<?php echo $i; ?>" size="3" /></td>
			</tr>
			<?php $i++; ?>
			<?php $k=1-$k; ?>
			<?php } ?>
		</table>
		</td>
	</tr>
	<tr>
		<td><input type="button" onclick="submitbutton('submissions.export.task');" name="Export" value="<?php echo JText::_('RSFP_EXPORT');?>" /></td>
	</tr>
	</table>
	<?php echo $this->tabs->endPanel(); ?>
	<?php echo $this->tabs->startPanel(JText::_($this->exportType == 'csv' ? 'RSFP_EXPORT_CSV_OPTIONS' : 'RSFP_EXPORT_OPTIONS'), 'export-options'); ?>
	<table class="admintable">
	<tr>
		<td width="200" style="width: 200px;" align="right" class="key">
			<span class="hasTip" title="<?php echo JText::_('RSFP_EXPORT_HEADERS_DESC'); ?>">
				<?php echo JText::_('RSFP_EXPORT_HEADERS');?>
			</span>
		</td>
		<td>
			<input type="checkbox" style="text-align: center" onchange="updateCSVPreview();" name="ExportHeaders" value="1" checked="checked" />
		</td>
	</tr>
	<?php if ($this->exportType == 'csv') { ?>
	<tr>
		<td width="200" style="width: 200px;" align="right" class="key">
			<span class="hasTip" title="<?php echo JText::_('RSFP_EXPORT_DELIMITER_DESC'); ?>">
				<?php echo JText::_('RSFP_EXPORT_DELIMITER');?>
			</span>
		</td>
		<td>
			<input type="text" style="text-align: center" onkeyup="updateCSVPreview();" name="ExportDelimiter" value="," size="5" />
		</td>
	</tr>
	<tr>
		<td width="200" style="width: 200px;" align="right" class="key">
			<span class="hasTip" title="<?php echo JText::_('RSFP_EXPORT_ENCLOSURE_DESC'); ?>">
				<?php echo JText::_('RSFP_EXPORT_ENCLOSURE');?>
			</span>
		</td>
		<td>
			<input type="text" style="text-align: center" onkeyup="updateCSVPreview();" name="ExportFieldEnclosure" value="&quot;" size="5" />
		</td>
	</tr>
	<?php } ?>
	</table>
	<?php echo $this->tabs->endPanel(); ?>
	<?php if ($this->exportType == 'csv') { ?>
		<?php echo $this->tabs->startPanel(JText::_('Preview'), 'export-preview'); ?>
		<table class="adminform" border="0">
		<tr>
			<td><?php echo JText::_('RSFP_EXPORT_PREVIEW_DESC'); ?></td>
		</tr>
		<tr>
			<td>
			<div id="previewExportDiv">
			<pre id="headersPre"><?php echo implode(',', $this->staticHeaders); ?><?php if (count($this->headers)) { ?>,<?php echo implode(',', $this->headers); ?><?php } ?></pre>
			<pre id="rowPre">&quot;<?php echo implode('&quot;,&quot;', $this->previewArray); ?>&quot;</pre>
			</div>
			</td>
		</tr>
		</table>
		<?php echo $this->tabs->endPanel(); ?>
	<?php } ?>
	<?php echo $this->tabs->endPane(); ?>
	
	<input type="hidden" name="task" value="submissions.export.task" />
	<input type="hidden" name="exportType" value="<?php echo $this->exportType; ?>" />
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="formId" value="<?php echo $this->formId; ?>" />
	<input type="hidden" name="ExportFile" value="<?php echo $this->exportFile; ?>" />
</form>

<script type="text/javascript">updateCSVPreview();</script>
<?php JHTML::_('behavior.keepalive'); ?>