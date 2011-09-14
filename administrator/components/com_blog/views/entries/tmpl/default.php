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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'Blog Manager' ),'generic.png' );
JToolBarHelper::preferences($this->option, '550');
JToolBarHelper::spacer();
JToolBarHelper::publishList();
JToolBarHelper::unpublishList();
JToolBarHelper::spacer();
JToolBarHelper::deleteList('', 'delete');
JToolBarHelper::editList();
JToolBarHelper::addNew();

JHTML::_('behavior.tooltip');
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

<form action="index.php" method="post" name="adminForm">

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JText::_('ID'); ?></th>
				<th><?php echo JText::_('Title'); ?></th>
				<th><?php echo JText::_('Author'); ?></th>
				<th><?php echo JText::_('State'); ?></th>
				<th><?php echo JText::_('Created'); ?></th>
				<th colspan="2"><?php echo JText::_('Comments'); ?></th>
				<th><?php echo JText::_('Hits'); ?></th>
			</tr>
		</thead>
		<tfoot>
 			<tr>
 				<td colspan="9"><?php echo $this->pageNav->getListFooter(); ?></td>
 			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
$config	=& JFactory::getConfig();
$now	=& JFactory::getDate();
$db		=& JFactory::getDBO();

$nullDate = $db->getNullDate();
$rows = $this->rows;
for ($i=0, $n=count( $rows ); $i < $n; $i++)
{
	$row =& $rows[$i];

	$publish_up =& JFactory::getDate($row->publish_up);
	$publish_down =& JFactory::getDate($row->publish_down);
	$publish_up->setOffset($config->getValue('config.offset'));
	$publish_down->setOffset($config->getValue('config.offset'));
	if ( $now->toUnix() <= $publish_up->toUnix() && $row->state == 1 ) {
		$img = 'publish_y.png';
		$alt = JText::_( 'Published' );
	} else if ( ( $now->toUnix() <= $publish_down->toUnix() || $row->publish_down == $nullDate ) && $row->state == 1 ) {
		$img = 'publish_g.png';
		$alt = JText::_( 'Published' );
	} else if ( $now->toUnix() > $publish_down->toUnix() && $row->state == 1 ) {
		$img = 'publish_r.png';
		$alt = JText::_( 'Expired' );
	} else if ( $row->state == 0 ) {
		$img = 'publish_x.png';
		$alt = JText::_( 'Unpublished' );
	} else if ( $row->state == -1 ) {
		$img = 'disabled.png';
		$alt = JText::_( 'Archived' );
	}
	$times = '';
	if (isset($row->publish_up)) {
		if ($row->publish_up == $nullDate) {
			$times .= JText::_( 'Start: Always' );
		} else {
			$times .= JText::_( 'Start' ) .": ". $publish_up->toFormat();
		}
	}
	if (isset($row->publish_down)) {
		if ($row->publish_down == $nullDate) {
			$times .= "<br />". JText::_( 'Finish: No Expiry' );
		} else {
			$times .= "<br />". JText::_( 'Finish' ) .": ". $publish_down->toFormat();
		}
	}

	if ($row->allow_comments == 0 ) {
		$cimg = 'publish_x.png';
		$calt = JText::_( 'Off' );
	} else {
		$cimg = 'publish_g.png';
		$calt = JText::_( 'On' );
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td style="text-align:center;"><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
				<td style="text-align:center;"><?php echo $row->id; ?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;id[]=<?php echo $row->id; ?>"><?php echo stripslashes($row->title); ?></a></td>
				<td><?php echo $row->name; ?></td>
				<td style="text-align:center;">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'Publish Information' );?>::<?php echo $times; ?>">
						<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $row->state ? 'unpublish' : 'publish' ?>')">
							<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" />
						</a>
					</span>
				</td>
				<td style="whitespace:nowrap;">
					<?php echo JHTML::_('date', $row->created, JText::_('DATE_FORMAT_LC4') ); ?>
				</td>
				<td style="text-align:center;">
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $row->allow_comments ? 'disallow' : 'allow' ?>')">
						<img src="images/<?php echo $cimg;?>" width="16" height="16" border="0" alt="<?php echo $calt; ?>" />
					</a>
				</td>
				<td style="text-align:center;">
					<?php echo $row->comments.' '.JText::_('comment(s)'); ?>
				</td>
				<td style="text-align:right;"><?php echo $row->hits; ?></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
</form>

