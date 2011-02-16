<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
function rsform_close_window()
{
	<?php if (RSFormProHelper::isJ16()) { ?>
	window.parent.SqueezeBox.close();
	<?php } else { ?>
	window.parent.document.getElementById( 'sbox-window' ).close();
	<?php } ?>
}
</script>

<div id="rsmembership_explorer">
	<form action="index.php?option=com_rsform&amp;controller=files&amp;task=display" method="post" name="adminForm" enctype="multipart/form-data">
	<button type="button" onclick="rsform_close_window();"><?php echo JText::_('CLOSE'); ?></button>
		<?php if ($this->canUpload) { ?>
		<table class="adminform">
		<tr>
			<th colspan="2"><?php echo JText::_('Upload File'); ?></th>
		</tr>
		<tr>
			<td width="120">
				<label for="upload"><?php echo JText::_('File'); ?>:</label>
			</td>
			<td>
				<input class="input_box" id="upload" name="upload" type="file" size="57" />
				<input class="button" type="button" value="<?php echo JText::_('Upload File'); ?>" onclick="submitbutton('upload')" />
			</td>
		</tr>
		</table>
		<?php } else { ?>
		<div class="rsform_error">
			<?php echo JText::_('RSFP_CANT_UPLOAD'); ?>
		</div>
		<?php } ?>
		
		<div id="editcell1">
			<table class="adminlist">
				<thead>
				<tr>
					<th><strong><?php echo JText::_('RSFP_CURRENT_LOCATION'); ?></strong>
						<?php foreach ($this->elements as $folder) { ?>
							<a href="index.php?option=com_rsform&amp;controller=files&amp;task=display&amp;folder=<?php echo urlencode($folder->fullpath); ?>&amp;tmpl=component"><?php echo $folder->name; ?></a> <?php echo DS; ?>
						<?php } ?>
					</th>
				</tr>
				</thead>
				<tr>
					<td><a class="folder" href="index.php?option=com_rsform&amp;controller=files&amp;task=display&amp;folder=<?php echo urlencode($this->previous); ?>&amp;tmpl=component">..<?php echo JHTML::_('image', 'administrator/components/com_rsform/assets/images/up.gif', JText::_('BACK')); ?></a></td>
				</tr>
		<?php
		$j = 0;
		foreach ($this->folders as $folder)
		{
		?>
			<tr>
				<td><a class="folder" href="index.php?option=com_rsform&amp;controller=files&amp;task=display&amp;folder=<?php echo urlencode($folder->fullpath); ?>&amp;tmpl=component"><?php echo $folder->name; ?></a></td>
			</tr>
		<?php
			$j++;
		}
			$i = $j;
			foreach ($this->files as $file)
			{
			?>
				<tr>
					<td><a class="file" href="javascript: void(0);" onclick="window.parent.document.getElementById('UserEmailAttachFile').value = '<?php echo addcslashes($file->fullpath, '\\\''); ?>'; rsform_close_window();"><?php echo $file->name; ?></a></td>
				</tr>
			<?php
				$i++;
			}
		?>
			</table>
		</div>
		
		<?php echo JHTML::_( 'form.token' ); ?>
		<input type="hidden" name="option" value="com_rsform" />
		<input type="hidden" name="controller" value="files" />
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="folder" value="<?php echo $this->current; ?>" />
		<input type="hidden" name="task" value="display" />
	</form>
</div>