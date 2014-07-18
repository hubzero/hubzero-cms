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
defined('_JEXEC') or die('Restricted access');

$offerings = $this->course->offerings(array('available' => true, 'sort' => 'publish_up'));
if ($offerings)
{
	$offering = $offerings->fetch('first');
}
else
{
	$offering = new CoursesModelOffering(0, $this->course->get('id'));
}

if ($offering->exists())
{
	$params = new JRegistry($offering->get('params'));

	$product = null;

	if ($params->get('store_product_id', 0))
	{
		$warehouse = new StorefrontModelWarehouse();
		// Get course by pID returned with $course->add() above
		try
		{
			$product = $warehouse->getCourse($params->get('store_product_id', 0));
		}
		catch (Exception $e)
		{
			echo 'ERROR: ' . $e->getMessage();
		}
	}

	$url = $offering->link() . '&task=enroll';
	if ($product && $product->data->id)
	{
		$url = 'index.php?option=com_cart'; //index.php?option=com_storefront/product/' . $product->pId;
	}
?>
			<table>
				<tbody>
<?php if (!$this->course->isManager() && !$this->course->isStudent() && $product) { ?>
					<tr>
						<td colspan="2">
							<strong class="price">$<?php echo $product->skus[0]->data->price; ?></strong>
						</td>
					</tr>
<?php } ?>
					<tr>
						<th scope="row"><?php echo JText::_('Offering'); ?></th>
						<td>
							<?php echo $this->escape(stripslashes($offering->get('title'))); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo JText::_('Section'); ?></th>
						<td>
							<?php echo $this->escape(stripslashes($offering->section()->get('title'))); ?>
						</td>
					</tr>
				</tbody>
			</table>
		<?php if ($this->course->isManager() || $this->course->isStudent()) { ?>
			<p>
				<a class="outline btn" href="<?php echo JRoute::_($offering->link()); ?>">
					<?php echo JText::_('Enter course'); ?>
				</a>
			</p>
		<?php } else { ?>
			<?php if ($product) { ?>
			<form action="<?php echo $url; ?>" id="frm" method="post">
				<input type="hidden" name="pId[<?php echo $product->data->id; ?>]" value="1" />
				<input type="hidden" name="updateCart" value="updateCart" />
				<p>
					<input type="submit" class="enroll btn" value="<?php echo JText::_('Enroll in course'); ?>" />
				</p>
			</form>
			<?php } else { ?>
			<p>
				<a class="enroll btn" href="<?php echo JRoute::_($url); ?>">
					<?php echo JText::_('Enroll'); ?>
				</a>
			</p>
			<?php } ?>
		<?php } ?>
<?php }