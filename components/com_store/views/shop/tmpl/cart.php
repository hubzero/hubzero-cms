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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<ul id="useroptions">
		<li><a href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" class="icon-store storefront btn"><?php echo JText::_('COM_STORE_STOREFRONT'); ?></a></li>
		<li class="last"><a class="icon-points mypoints btn" href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->juser->get('id') . '&active=points'); ?>"><?php echo JText::_('COM_STORE_MY_POINTS'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>

	<div id="cartcontent">
<?php if ($this->msg) { ?>
		<p class="passed"><?php echo $this->msg; ?></p>
<?php } ?>
<?php if ($this->rows) { ?>
		<p>
			<?php echo JText::sprintf('COM_STORE_THERE_ARE_ITEMS_IN_CART', count($this->rows)); ?>
			<a href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" ><?php echo JText::_('COM_STORE_CONTINUE'); ?></a>
		</p>
		
		<form id="myCart" method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=cart'); ?>">
			<input type="hidden" name="action" value="" />
			<input type="hidden" name="task" value="checkout" />
			<input type="hidden" name="funds" id="funds" value="<?php echo $this->funds; ?>" />
			<input type="hidden" name="cost" id="cost" value="<?php echo $this->cost; ?>" />

			<table id="tktlist">
				<thead>
					<tr>
						<th><?php echo ucfirst(JText::_('COM_STORE_ITEM')); ?></th>
						<th><?php echo JText::_('COM_STORE_AVAILABILITY'); ?></th>
						<th><?php echo JText::_('COM_STORE_QUANTITY'); ?>*</th>
						<th><?php echo JText::_('COM_STORE_SIZE'); ?></th>
						<th><a href="<?php echo $this->infolink; ?>" title="<?php echo JText::_('COM_STORE_WHAT_ARE_POINTS'); ?>" class="coin"><?php echo JText::_('COM_STORE_WHAT_ARE_POINTS'); ?></a></th>
					</tr>
				</thead>
			<tbody>
<?php
	$total = 0;
	foreach ($this->rows as $row)
	{
		$price = $row->price*$row->quantity;
		if ($row->available) 
		{ // do not add if not available
			$total = $total+$price;
		}
		$sizes = array(); // build size options
		if ($row->sizes && count($row->sizes) > 0) 
		{
			foreach ($row->sizes as $rs)
			{
				if (trim($rs) != '') 
				{
					$sizes[$rs] = $rs;
				}
			}
			$selectedsize = ($row->selectedsize) ? $row->selectedsize : $row->sizes[0];
		}
?>
					<tr>
						<td><?php echo $this->escape($row->title); ?></td>
						<td>
<?php
if ($row->category!='service') {
	if ($row->available) {
?>
							<span class="yes"><?php echo JText::_('COM_STORE_INSTOCK'); ?></span>

<?php } else { ?>
							<span class="no"><?php echo JText::_('COM_STORE_SOLDOUT'); ?></span>
<?php
	}
}
?>
						</td>
						<td class="quantityspecs">
<?php if ($row->category!='service') { ?>
							<input type="text" name="num<?php echo $row->itemid; ?>" id="num<?php echo $row->itemid; ?>" value="<?php echo $row->quantity; ?>" size="1" maxlength = "1" class="quantity" />
<?php } else { ?>
							1 
<?php } ?>
							<span class="removeitem"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=cart&action=remove&item=' . $row->itemid); ?>" title="<?php echo JText::_('COM_STORE_REMOVE_FROM_CART'); ?>"><?php echo JText::_('COM_STORE_REMOVE_FROM_CART'); ?></a></span>
						</td>
						<td>
<?php if (count($sizes)>0) { ?>
							<select name="size<?php echo $row->itemid; ?>" id="size<?php echo $row->itemid; ?>">
<?php
							foreach ($sizes as $anode)
							{
?>
								<option value="<?php echo $this->escape(stripslashes($anode)); ?>"<?php echo ($anode == $selectedsize) ? ' selected="selected"' : ''; ?>><?php echo $this->escape(stripslashes($anode)); ?></option>
<?php
							}
?>
							</select>
<?php } else { ?>
							N/A
<?php } ?>
						</td>
						<td><?php echo $price; ?></td>
					</tr>
<?php
	}
?>
					<tr class="totals">
						<td><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=cart&action=empty'); ?>" class="actionlink" title="<?php echo JText::_('COM_STORE_EMPTY_CART'); ?>"><?php echo JText::_('COM_STORE_EMPTY_CART'); ?></a></td>
						<td></td>
						<td><a href="javascript:void(0);" class="actionlink" id="updatecart" title="<?php echo JText::_('COM_STORE_TITLE_UPDATE'); ?>"><?php echo JText::_('COM_STORE_UPDATE'); ?></a></td>
						<td><?php echo JText::_('COM_STORE_TOTAL'); ?></td>
						<td><?php echo $total; ?></td>
					</tr>
				</tbody>
			</table>

			<p class="process">
<?php if ($this->funds >= $total && intval($total) > 0) { ?>
				<input type="submit" class="button checkout" value="checkout" /></p>
				<span class="reassure">(<?php echo JText::_('COM_STORE_NOTE_NOCHARGE'); ?>)</span>
<?php } else { ?>
				<span class="button checkout_disabled">&nbsp;</span>
<?php } ?>
			</p>
		</form>
		
		<div class="footernotes">
			<p>* <?php echo JText::_('COM_STORE_CART_NOTES'); ?></p>
		</div>
<?php } else { ?>
		<p><?php echo JText::_('COM_STORE_CART_IS_EMPTY'); ?> <a href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" ><?php echo JText::_('COM_STORE_START_SHOPPING'); ?></a>.</p>
<?php } ?>
	</div><!-- / #cartcontent -->
	<div id="balanceupdate">
		<p class="point-balance"><small><?php echo JText::_('COM_STORE_YOU_HAVE') . '</small> ' . $this->funds . '<small> '.JText::_('COM_STORE_POINTS') . ' ' . JText::_('COM_STORE_TO_SPEND'); ?></small></p>
<?php if ($this->funds < $this->cost && $this->cost != 0) { ?>
		<p class="error"><?php echo JText::_('COM_STORE_MSG_NO_FUNDS') . ' ' . JText::_('COM_STORE_LEARN_HOW') . ' <a href="' . $this->infolink . '">' . strtolower(JText::_('COM_STORE_EARN')) . '</a>'; ?></p>
<?php } ?>
	</div>
	<div class="clear"></div>
</div><!-- / .main section -->
