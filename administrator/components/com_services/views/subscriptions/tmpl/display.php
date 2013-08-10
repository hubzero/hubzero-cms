<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$dateFormat = '%d %b, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$tz = false;
}

JToolBarHelper::title( '<a href="index.php?option=com_services">'.JText::_( 'Services &amp; Subscriptions Manager' ).'</a>: <small><small>[ Subscriptions ]</small></small>', 'addedit.png' );
JToolBarHelper::preferences('com_services', '550');

$now = date( 'Y-m-d H:i:s', time() );
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<?php echo $this->total; ?> <?php echo JText::_('total subscriptions'); ?>.
		<label>
			<?php echo JText::_('Filter by'); ?>:
			<select name="filterby" onchange="document.adminForm.submit( );">
				<option value="pending"<?php if ($this->filters['filterby'] == 'pending') { echo ' selected="selected"'; } ?>><?php echo JText::_('Pending'); ?> <?php echo ucfirst(JText::_('Subscriptions')); ?></option>
				<option value="active"<?php if ($this->filters['filterby'] == 'processed') { echo ' selected="selected"'; } ?>><?php echo JText::_('Active'); ?> <?php echo ucfirst(JText::_('Subscriptions')); ?></option>
				<option value="cancelled"<?php if ($this->filters['filterby'] == 'cancelled') { echo ' selected="selected"'; } ?>><?php echo JText::_('Cancelled'); ?> <?php echo ucfirst(JText::_('Subscriptions')); ?></option>
				<option value="all"<?php if ($this->filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>><?php echo JText::_('ALL'); ?> <?php echo ucfirst(JText::_('Subscriptions')); ?></option>
			</select>
		</label>
		
		<label>
			<?php echo JText::_('Sort by'); ?>:
			<select name="sortby" onchange="document.adminForm.submit( );">
				<option value="date"<?php if ($this->filters['sortby'] == 'date') { echo ' selected="selected"'; } ?>><?php echo JText::_('Date Added'); ?></option>
				<option value="date_updated"<?php if ($this->filters['sortby'] == 'date_updated') { echo ' selected="selected"'; } ?>><?php echo JText::_('Last Updated'); ?></option>
				<option value="date_expires"<?php if ($this->filters['sortby'] == 'date_expires') { echo ' selected="selected"'; } ?>><?php echo JText::_('Soon to Expire'); ?></option>
				<option value="pending"<?php if ($this->filters['sortby'] == 'pending') { echo ' selected="selected"'; } ?>><?php echo ucfirst(JText::_('Pending Admin Action')); ?></option>	
				<option value="status"<?php if ($this->filters['sortby'] == 'status') { echo ' selected="selected"'; } ?>><?php echo ucfirst(JText::_('Status')); ?></option>					
			</select>
		</label> 
	</fieldset>
	<div class="clr"></div>
	
	<table class="adminlist">
		<thead>
			<tr>
				<th><?php echo JText::_('ID -- Code'); ?></th>
				<th><?php echo JText::_('Status'); ?></th>
				<th><?php echo JText::_('Service'); ?></th>
				<th><?php echo JText::_('Pending Payment / Units'); ?></th>
				<th><?php echo JText::_('User'); ?></th>
				<th><?php echo JText::_('Added'); ?></th>
				<th><?php echo JText::_('Last Updated'); ?></th>
				<th><?php echo JText::_('Expires'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	$name = JText::_('UNKNOWN');
	$login = JText::_('UNKNOWN');
	$ruser =& JUser::getInstance($row->uid);
	if (is_object($ruser)) {
		$name = $ruser->get('name');
		$login = $ruser->get('username');
	}

	$status='';
	$pending = $row->currency.' '.$row->pendingpayment.' - '.JText::_('for').' '.$row->pendingunits.' '.JText::_('units(s)');

	$expires = (intval($row->expires) <> 0) ? JHTML::_('date', $row->expires, $dateFormat, $tz) : 'N/A';

	switch ($row->status)
	{
		case '1':
			$status = ($row->expires > $now) ? '<span style="color:#197f11;">'.strtolower(JText::_('Active')).'</span>' : '<span style="color:#ef721e;">'.strtolower(JText::_('Expired')).'</span>';
			break;
		case '0':
			$status = '<span style="color:#ff0000;">'.strtolower(JText::_('Pending')).'</span>';
			break;
		case '2':
			$status = '<span style="color:#999;">'.strtolower(JText::_('Cancelled')).'</span>';
			$pending .= $row->pendingpayment ? ' ('.JText::_('refund').')' : '';
			break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('View Subscription Details'); ?>"><?php echo $row->id,' -- '.$row->code; ?></a></td>
				<td><?php echo $status;  ?></td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('View Subscription Details'); ?>">
						<span><?php echo $this->escape($row->category) . ' -- ' . $this->escape($row->title); ?></span>
					</a>
				</td>
				<td><?php echo $row->pendingpayment &&  ($row->pendingpayment > 0 or $row->pendingunits > 0)  ? '<span style="color:#ff0000;">'.$pending.'</span>' : $pending;  ?></td>
				<td><?php echo $name.' ('.$login.')';  ?></td>
				<td><?php echo JHTML::_('date', $row->added, $dateFormat, $tz); ?></td>	   
				<td><?php echo $row->updated ? JHTML::_('date', $row->updated, $dateFormat, $tz) : 'never'; ?></td>
				<td><?php echo $expires; ?></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
