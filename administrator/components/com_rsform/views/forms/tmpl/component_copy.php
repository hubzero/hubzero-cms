<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php?option=com_rsform" method="post" name="adminForm">
	<p><?php echo JText::_('RSFP_COPY_COMPONENTS_WHERE'); ?></p>
	<?php echo $this->lists['forms']; ?>
	<button type="submit"><?php echo JText::_('COPY');?></button>
	
	<input type="hidden" name="formId" value="<?php echo $this->formId; ?>" />
	<input type="hidden" name="task" value="components.copy.process" />
	<?php foreach ($this->cids as $cid) { ?>
	<input type="hidden" name="cid[]" value="<?php echo $cid; ?>" />
	<?php } ?>
</form>