<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

if ($this->getError()) {
	echo ResourcesHtml::error($this->getError());
}
?>
<script type="text/javascript">
function dirup()
{
	var urlquery = frames['imgManager'].location.search.substring(1);
	var curdir = urlquery.substring(urlquery.indexOf('listdir=')+8);
	var listdir = curdir.substring(0,curdir.lastIndexOf('/'));
	frames['imgManager'].location.href='index3.php?option=com_resources&task=listfiles&listdir=' + listdir;
}

function goUpDir()
{
	var listdir = document.getElementById('listdir');
	var selection = document.forms[0].dirPath;
	var dir = selection.options[selection.selectedIndex].value;
	frames['imgManager'].location.href='index3.php?option=com_resources&task=listfiles&listdir=' + listdir.value +'&subdir='+ dir;
}
</script>

<form action="index.php" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">
	<p>path = <?php echo $this->path; ?></p>
	
	<fieldset>
		<label>
			Directory
			<?php echo $this->dirPath; ?>
		</label>

		<div id="themanager" class="manager">
			<iframe src="index.php?option=<?php echo $this->option; ?>&amp;task=listfiles&amp;tmpl=component&amp;listdir=<?php echo $this->listdir; ?>&amp;subdir=<?php echo $this->subdir; ?>" name="imgManager" id="imgManager" width="98%" height="180"></iframe>
		</div>
	</fieldset>
	
	<fieldset>
		<table>
			<tbody>
				<tr>
					<td><label for="upload"><?php echo JText::_('Upload'); ?></label></td>
					<td><input type="file" name="upload" id="upload" /></td>
				</tr>
				<tr>
					<td> </td>
					<td><input type="checkbox" name="batch" id="batch" value="1" /> <label for="batch"><?php echo JText::_('Unpack file (.zip, .tar, etc)'); ?></label></td>
				</tr>
				<tr>
					<td><label for="foldername"><?php echo JText::_('Create Directory'); ?></label></td>
					<td><input type="text" name="foldername" id="foldername" /></td>
				</tr>
				<tr>
					<td> </td>
					<td><input type="submit" value="<?php echo JText::_('Create or Upload'); ?>" /></td>
				</tr>
			</tbody>
		</table>

		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="listdir" id="listdir" value="<?php echo $this->listdir; ?>" />
		<input type="hidden" name="task" value="upload" />
	</fieldset>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>