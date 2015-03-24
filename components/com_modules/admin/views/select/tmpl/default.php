<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
?>

<h2 class="modal-title"><?php echo JText::_('COM_MODULES_TYPE_CHOOSE')?></h2>

<table id="new-modules-list" class="adminlist">
	<thead>
		<tr>
			<th scope="col"><?php echo JText::_('JGLOBAL_TITLE'); ?></th>
			<th scope="col"><?php echo JText::_('COM_MODULES_HEADING_MODULE'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->items as &$item) : ?>
		<tr>
			<?php
			// Prepare variables for the link.

			$link	= 'index.php?option=com_modules&task=module.add&eid='. $item->extension_id;
			$name	= $this->escape($item->name);
			$desc	= $this->escape($item->desc);
			?>
			<td>
				<span class="editlinktip hasTip" title="<?php echo $name.' :: '.$desc; ?>"><a href="<?php echo JRoute::_($link);?>" target="_top"><?php echo $name; ?></a></span>
			</td>
			<td>
				<?php echo $this->escape($item->module); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<div class="clr"></div>
