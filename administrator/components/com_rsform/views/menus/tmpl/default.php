<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm">
	<p><?php JText::sprintf('RSFP_ADD_TO_MENU', $this->formTitle); ?></p>
	<table class="adminlist">
	<thead>
		<tr>
			<th width="20"><?php echo JText::_('#'); ?></th>
			<th class="title" nowrap="nowrap"><?php echo JText::_('Title'); ?></th>
			<th class="title" nowrap="nowrap"><?php echo JText::_('Type'); ?></th>
			<th width="3%"><?php echo JText::_('ID'); ?></th>
		</tr>
	</thead>
	<?php
	$i = 0;
	$k = 0;
	foreach ($this->menus as $menu) { ?>
		<tr class="row<?php echo $k; ?>">
			<td align="center" width="30"><?php echo $this->pagination->getRowOffset($i); ?></td>
			<?php if (RSFormProHelper::isJ16()) { ?>
			<td><a href="index.php?option=com_rsform&amp;task=setmenu&amp;menutype=<?php echo $menu->menutype; ?>&amp;formId=<?php echo $this->formId; ?>"><?php echo $menu->title; ?></a></td>
			<?php } else { ?>
			<td><a href="index.php?option=com_menus&amp;task=edit&amp;type=component&amp;url[option]=com_rsform&amp;menutype=<?php echo $menu->menutype; ?>&amp;cid[]=&amp;formId=<?php echo $this->formId; ?>"><?php echo $menu->title; ?></a></td>
			<?php } ?>
			<td><?php echo $menu->menutype; ?></td>
			<td align="center"><?php echo $menu->id; ?></td>
		</tr>
	<?php
		$i++;
		$k=1-$k;
	}
	?>
	<tfoot>
		<tr>
			<td colspan="4">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	</table>

	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="task" value="" />
</form>