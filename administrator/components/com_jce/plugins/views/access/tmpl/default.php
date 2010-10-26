<?php defined('_JEXEC') or die('Restricted access'); ?>
<script language="javascript" type="text/javascript">
	var form = parent.document.adminForm;
	function submitbutton() {
		if(form.boxchecked.value === ''){
			alert('Please select items to alter!');
			return false;
		}else{
			form.task.value = 'access_save';
			form.submit();
		}
	}
</script>
<fieldset>
    <div style="float: right">
        <button type="button" onclick="submitbutton();">
            <?php echo JText::_( 'Apply' );?></button>
        <button type="button" onclick="window.parent.document.getElementById('sbox-window').close();">
            <?php echo JText::_( 'Cancel' );?></button>
    </div>
    <div class="configuration" >
        <?php echo JText::_( 'JCE Access Level' );?>
    </div>
</fieldset>
<fieldset>
    <legend><?php echo JText::_( 'Select Access Level' );?></legend>
    <table>
    <tr>
        <td style="text-align:center;"><?php echo $this->lists;?></td>
    </tr>
    </table>
</fieldset>