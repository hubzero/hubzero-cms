<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<?php if ($this->params->get('show_page_title', 1)) { ?>
	<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo $this->escape($this->params->get('page_title')); ?></div>
<?php } ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td><?php echo $this->template; ?></td>
	</tr>
	<tr>
		<td align="center" class="sectiontablefooter<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo $this->pagination->getPagesLinks(); ?></td>
	</tr>
	<tr>
		<td align="center"><?php echo $this->pagination->getPagesCounter(); ?></td>
	</tr>
</table>