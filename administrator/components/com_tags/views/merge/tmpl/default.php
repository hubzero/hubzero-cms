<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'TAGS' ).': <small><small>[ '.JText::_('MERGE').' ]</small></small>', 'addedit.png' );
JToolBarHelper::save('merge');
JToolBarHelper::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm" class="editform">
	<p><?php echo JText::_('MERGED_EXPLANATION'); ?></p>
	
	<div class="col width-50">
		<fieldset class="adminform">
			<legend><?php echo JText::_('MERGING'); ?></legend>
			
			<ul>
			<?php
			foreach ($this->tags as $tag) 
			{
				echo '<li>'.stripslashes($tag->raw_tag).' ('.$tag->tag.' - '.$tag->total.')</li>'."\n";
			}
			?>
			</ul>
		</fieldset>
	</div>
	<div class="col width-50">
		<fieldset class="adminform">
			<legend><?php echo JText::_('MERGE_TO'); ?></legend>
			
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="existingtag"><?php echo JText::_('EXISTING_TAG'); ?>:</label></td>
						<td>
							<select name="existingtag" id="existingtag">
								<option value=""><?php echo JText::_('OPT_SELECT'); ?></option>
								<?php
								foreach ($this->rows as $row)
								{
									echo '<option value="'.$row->id.'">'.stripslashes($row->raw_tag).'</option>'."\n";
								}
								?>
							</select>
						</td>
					</tr>
<?php
	if (count($this->tags) > 1) {
?>
					<tr>
						<td colspan="2"><?php echo JText::_('OR'); ?></td>
					</tr>
					<tr>
						<td class="key"><label for="newtag"><?php echo JText::_('NEW_TAG'); ?>:</label></td>
						<td><input type="text" name="newtag" id="newtag" size="25" value="" /></td>
					</tr>
				</tbody>
			</table>
<?php
	} else {
?>
				</tbody>
			</table>
			<input type="hidden" name="newtag" id="newtag" value="" />
<?php
	}
?>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="ids" value="<?php echo $this->idstr; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="step" value="<?php echo $this->step; ?>" />
	<input type="hidden" name="task" value="merge" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>