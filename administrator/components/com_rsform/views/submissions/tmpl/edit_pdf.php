<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<table class="admintable" cellpadding="4" cellspacing="1">
	<?php foreach ($this->staticHeaders as $header) { ?>
	<tr>
		<td><b><?php echo JText::_('RSFP_'.$header); ?></b></td>
	</tr>
	<tr>
		<td><?php echo $this->staticFields->$header; ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<?php } ?>
	<?php foreach ($this->fields as $field) { ?>
	<tr>
		<td><b><?php echo $field[0]; ?></b></td>
	</tr>
	<tr>
		<td><?php echo $field[1]; ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<?php } ?>
</table>