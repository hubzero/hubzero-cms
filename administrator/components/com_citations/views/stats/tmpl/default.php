<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'CITATION' ).': <small><small>[ '.JText::_( 'STATS' ).' ]</small></small>', 'addedit.png' );
?>
<table class="admintable">
	<thead>
		<tr>
			<th><?php echo JText::_('YEAR'); ?></th>
			<th><?php echo JText::_('AFFILIATED'); ?></th>
			<th><?php echo JText::_('NONAFFILIATED'); ?></th>
			<th><?php echo JText::_('TOTAL'); ?></th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ($this->stats as $year=>$amt) 
	{
?>
		<tr>
			<th><?php echo $year; ?></th>
			<td><?php echo $amt['affiliate']; ?></td>
			<td><?php echo $amt['non-affiliate']; ?></td>
			<td><span style="color:#c00;"><?php echo (intval($amt['affiliate']) + intval($amt['non-affiliate'])); ?></span></td>
		</tr>
<?php
	}
?>
	</tbody>
</table>