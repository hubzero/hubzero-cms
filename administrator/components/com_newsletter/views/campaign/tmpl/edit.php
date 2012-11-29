<?php
$text = ($this->task == 'edit' ? JText::_('Edit') : JText::_('New'));
JToolBarHelper::title(JText::_('Newsletter Campaign') . ': <small><small>[ ' . $text . ' ]</small></small>', 'addedit.png');
JToolBarHelper::save();
JToolBarHelper::cancel();

$primary = $this->campaignPrimary;
$secondary = $this->campaignSecondary; 

// Instantiate the sliders object
jimport('joomla.html.pane');
$tabs =& JPane::getInstance('sliders');

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();
?>

<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm">
	<div class="col width-50">
		<fieldset class="adminform">
			<legend>Campaign Details</legend>
			<table class="admintable">
				<tbody>
					<?php if($this->campaign['id']) : ?>
						<tr>
							<td class="key">ID</td>
							<td>
								<?php echo $this->campaign['id']; ?>
								<input type="hidden" name="campaign[id]" value="<?php echo $this->campaign['id']; ?>" />
							</td>
						</tr>
					<?php endif; ?>
					<tr>
						<td class="key">Name</td>
						<td>
							<input type="text" name="campaign[name]" value="<?php echo $this->campaign['name']; ?>" />
						</td>
					</tr>
					<tr>
						<td class="key">Issue</td>
						<td>
							<input type="text" name="campaign[issue]" value="<?php echo $this->campaign['issue']; ?>" />
						</td>
					</tr>	
					<tr>
						<td class="key">Template</td>
						<td>
							<select name="campaign[template]">
								<option value="">- Select a Template &mdash;</option>
								<?php foreach($this->templates as $t) : ?>
									<?php echo $sel = ($t->id == $this->campaign['template']) ? 'selected="selected"' : '' ; ?>
									<option <?php echo $sel; ?> value="<?php echo $t->id; ?>"><?php echo $t->name; ?></option>
								<?php endforeach; ?> 
							</select>
						</td>
					</tr>
					<tr>
						<td class="key">Date</td>
						<td>
							<input type="text" name="campaign[date]" value="<?php echo $this->campaign['date']; ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<?php if($this->campaign['id'] != null) : ?>
			<fieldset class="adminform">
				<legend>Campaign Distribution</legend>
				<table class="admintable">
					<tbody>
						<tr>
							<td class="key">Campaign Sent?</td>
							<td>
								<?php if($this->campaign['sent']) : ?>
									<font color="green">Yes</font>
								<?php else : ?>
									<font color="red">No</font>
								<?php endif; ?>
							</td>
						</tr> 
						<tr>
							<td class="key">Send Test</td>
							<td><input type="text" name="test" placeholder="Enter Emails to Send Test" /><button onclick="javascript: submitbutton('sendTest')">Test</button></td>
						</tr>
						<tr>
							<td class="key">Send Campaign</td>
							<td><button onclick="javascript: submitbutton('send')">Send!!</button></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		<?php endif; ?>	
	</div>
	
	<div class="col width-50">
		<?php if($this->campaign['id'] == null) : ?>
			<p class="info">You must create the campaign before adding stories.</p>
		<?php else : ?>
			<fieldset class="adminform">
				<legend>Campaign Primary Stories  <a class="fltrt" style="padding-right:15px" href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->campaign['id'].'&task=add&type=primary'); ?>">Add Story</a></legend>
				<?php echo $tabs->startPane("content-pane"); ?>
					<?php for($i=0,$n=count($primary); $i<$n; $i++) : ?>
						<?php echo $tabs->startPanel("Story ".($i+1), "story-".($i+1)."") ; ?>
							<table class="admintable">
								<tbody>
									<tr>
										<td colspan="2">
											<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->campaign['id'].'&task=edit&type=primary&sid='.$primary[$i]->id); ?>">Edit Story</a>
										</td>
									</tr>
									<tr>
										<td class="key" width='20%'>Title</td>
										<td><?php echo $primary[$i]->title; ?></td>
									</tr>
									<tr>
										<td class="key">Story</td>
										<td><?php echo stripslashes($primary[$i]->story); ?></td>
									</tr>
								</tbody>
							</table>
						<?php echo $tabs->endPanel(); ?>
					<?php endfor; ?>
				<?php echo $tabs->endPane(); ?>
			</fieldset>
			<hr />
			<fieldset class="adminform">
				<legend>Campaign Secondary Stories  <a class="fltrt" style="padding-right:15px" href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->campaign['id'].'&task=add&type=secondary'); ?>">Add Story</a></legend>
				<?php echo $tabs->startPane("content-pane2"); ?>
					<?php for($i=0,$n=count($secondary); $i<$n; $i++) : ?>
						<?php echo $tabs->startPanel("Story ".($i+1), "story-".($i+1)."") ; ?>
							<table class="admintable">
								<tbody>
									<tr>
										<td colspan="2">
											<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->campaign['id'].'&task=edit&type=secondary&sid='.$secondary[$i]->id); ?>">Edit Story</a>
										</td>
									</tr>
									<tr>
										<td class="key">Title</td>
										<td><?php echo $secondary[$i]->title; ?></td>
									</tr>
									<tr>
										<td class="key">Story</td>
										<td><?php echo $secondary[$i]->story; ?></td>
									</tr>
								</tbody>
							</table>
						<?php echo $tabs->endPanel(); ?>
					<?php endfor; ?>
				<?php echo $tabs->endPane(); ?>
			</fieldset>
		<?php endif; ?>	
	</div>
	<input type="hidden" name="option" value="com_newsletter" />
	<input type="hidden" name="controller" value="campaign" />
	<input type="hidden" name="task" value="save" />
</form>	