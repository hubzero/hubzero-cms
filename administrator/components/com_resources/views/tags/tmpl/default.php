<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'Resource Manager' ), 'addedit.png' );
JToolBarHelper::save('savetags');
JToolBarHelper::cancel('canceltags');

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
function addtag(tag)
{
	var input = document.getElementById('tags-men');
	if(input.value == '') {
		input.value = tag;
	} else {
		input.value += ', '+tag;
	}
}
</script>

<form action="index.php" method="post" name="adminForm">
	<h2>Edit Tags for this Resource</h2>
	<p>Create new tags and assign them to this resource by entering them below separated by commas (e.g. <em>negf theory, NEMS, ion transport</em>).</p>
		<table class="adminform">
		 <thead>
		  <tr>
		   <th colspan="2"><?php echo $this->row->title; ?></th>
		  </tr>
		 </thead>
		 <tbody>
		  <tr>
		   <th><label for="tags-men">Create Tags:</label></th>
		   <td><input type="text" name="tags" id="tags-men" size="65" value="<?php //echo $objtags->tag_men; ?>" />
		   </td>
		  </tr>
		 </tbody>
		</table>

	<h3>Existing Tags</h3>
	<p>Add or remove tags assigned to this resource by checking or unchecking tags below.</p>
	<table class="adminlist" summary="A list of all tags">
	 <thead>
	  <tr>
	   <th style="width: 15px;"> </th>
	   <th>Raw Tag</th>
	   <th>Tag</th>
	   <th>Alias</th>
	   <th>Admin</th>
	  </tr>
	 </thead>
	 <tbody>
<?php
$k = 0;
for ($i=0, $n=count( $this->tags ); $i < $n; $i++) {
	$thistag = &$this->tags[$i];
	$check = '';
	if ($thistag->admin == 1) {
		$check = '<span class="check">admin</span>';
	}
?>
	  <tr class="<?php echo "row$k"; ?>">
	   <td><input type="checkbox" name="tgs[]" id="cb<?php echo $i;?>" <?php if (in_array($thistag->tag,$this->mytagarray)) { echo 'checked="checked"'; } ?> value="<?php echo stripslashes($thistag->tag); ?>" /></td>
	   <td><a href="#" onclick="addtag('<?php echo stripslashes($thistag->tag); ?>');"><?php echo stripslashes($thistag->raw_tag); ?></a></td>
	   <td><a href="#" onclick="addtag('<?php echo stripslashes($thistag->tag); ?>');"><?php echo stripslashes($thistag->tag); ?></a></td>
	   <td><a href="#" onclick="addtag('<?php echo stripslashes($thistag->tag); ?>');"><?php echo stripslashes($thistag->alias); ?></a></td>
	   <td><?php echo $check; ?></td>
	  </tr>
<?php
	$k = 1 - $k;
}
?>
	 </tbody>
	</table>
	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="savetags" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>