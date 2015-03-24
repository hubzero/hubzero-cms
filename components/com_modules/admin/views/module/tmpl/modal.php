<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
?>
<div class="configuration">
	<div class="configuration-options">
		<button type="button" onclick="Joomla.submitbutton('module.save');"><?php echo JText::_('JSAVE');?></button>
		<button type="button" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('JCANCEL');?></button>
	</div>
	<?php echo JText::sprintf('COM_MODULES_MANAGER_MODULE', JText::_($this->item->module)) ?>
</div>

<?php
$this->setLayout('edit');
echo $this->loadTemplate();
