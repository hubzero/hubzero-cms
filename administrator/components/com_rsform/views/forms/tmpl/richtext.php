<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form method="post" action="index.php?option=com_rsform" name="adminForm">
	<p>
		<button type="button" onclick="submitform('richtext.apply');"><?php echo JText::_('APPLY'); ?></button>
		<button type="button" onclick="submitform('richtext.save');"><?php echo JText::_('SAVE'); ?></button>
		<button type="button" onclick="window.close();"><?php echo JText::_('CLOSE'); ?></button>
	</p>
	
	<?php if ($this->noEditor) { ?>
		<textarea cols="70" rows="10" style="width: 500px; height: 320px;" name="<?php echo $this->editorName; ?>"><?php echo $this->editorText; ?></textarea>
	<?php } else { ?>
		<?php echo $this->editor->display($this->editorName, $this->editorText, 500, 320, 70, 10); ?>
	<?php } ?>
	
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="opener" value="<?php echo $this->editorName; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="formId" value="<?php echo $this->formId; ?>" />
</form>

<?php JHTML::_('behavior.keepalive'); ?>