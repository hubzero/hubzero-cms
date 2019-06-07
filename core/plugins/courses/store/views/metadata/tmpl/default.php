<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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