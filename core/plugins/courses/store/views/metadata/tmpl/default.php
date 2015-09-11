<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$offerings = $this->course->offerings(array('available' => true, 'sort' => 'publish_up'));
if ($offerings)
{
	$offering = $offerings->fetch('first');
}
else
{
	$offering = new \Components\Courses\Models\Offering(0, $this->course->get('id'));
}

if ($offering->exists())
{
	$params = new \Hubzero\Config\Registry($offering->get('params'));

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
						<th scope="row"><?php echo Lang::txt('Offering'); ?></th>
						<td>
							<?php echo $this->escape(stripslashes($offering->get('title'))); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('Section'); ?></th>
						<td>
							<?php echo $this->escape(stripslashes($offering->section()->get('title'))); ?>
						</td>
					</tr>
				</tbody>
			</table>
		<?php if ($this->course->isManager() || $this->course->isStudent()) { ?>
			<p>
				<a class="outline btn" href="<?php echo Route::url($offering->link()); ?>">
					<?php echo Lang::txt('Enter course'); ?>
				</a>
			</p>
		<?php } else { ?>
			<?php if ($product) { ?>
			<form action="<?php echo $url; ?>" id="frm" method="post">
				<input type="hidden" name="pId[<?php echo $product->data->id; ?>]" value="1" />
				<input type="hidden" name="updateCart" value="updateCart" />
				<p>
					<input type="submit" class="enroll btn" value="<?php echo Lang::txt('Enroll in course'); ?>" />
				</p>
			</form>
			<?php } else { ?>
			<p>
				<a class="enroll btn" href="<?php echo Route::url($url); ?>">
					<?php echo Lang::txt('Enroll'); ?>
				</a>
			</p>
			<?php } ?>
		<?php } ?>
<?php }