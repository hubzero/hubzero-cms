<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

require_once dirname(dirname(dirname(dirname(__DIR__)))) . DS . 'helpers' . DS . 'Helper.php';
$states = \Components\Cart\Helpers\CartHelper::getUsStates();

$this->css();
?>

<header id="content-header">
	<h2>Checkout: shipping information</h2>
</header>

<?php

if (!empty($this->notifications))
{
	$view = new \Hubzero\Component\View(array('name'=>'shared', 'layout' => 'notifications'));
	$view->notifications = $this->notifications;
	$view->display();
}

?>

<section class="main section">
	<div class="section-inner">
		<?php
		$errors = $this->getError();
		if (!empty($errors))
		{
			foreach ($errors as $error)
			{
				echo '<p class="error">' . $error . '</p>';
			}
		}
		?>
		<div class="grid">
			<div class="col span6">
				<form name="cartShippingInfo" class="cartShippingInfo full" method="post" id="hubForm">
					<fieldset>
						<legend><?php echo Lang::txt('Shipping address'); ?></legend>

						<label for="shippingToFirst">
							<?php echo Lang::txt('First name:'); ?>
							<input type="text" name="shippingToFirst" id="shippingToFirst" value="<?php echo $this->escape(Request::getString('shippingToFirst', User::get('givenName'), 'post')); ?>" />
						</label>

						<label for="shippingToLast">
							<?php echo Lang::txt('Last name:'); ?>
							<input type="text" name="shippingToLast" id="shippingToLast" value="<?php echo $this->escape(Request::getString('shippingToLast', User::get('surname'), 'post')); ?>" />
						</label>

						<label for="shippingAddress">
							<?php echo Lang::txt('Shipping address:'); ?>
							<input type="text" name="shippingAddress" id="shippingAddress" value="<?php echo $this->escape(Request::getString('shippingAddress', false, 'post')); ?>" />
						</label>

						<label for="shippingCity">
							<?php echo Lang::txt('City:'); ?>
							<input type="text" name="shippingCity" id="shippingCity" value="<?php echo $this->escape(Request::getString('shippingCity', false, 'post')); ?>" />
						</label>

						<label for="shippingZip">
							<?php echo Lang::txt('Zip:'); ?>
							<input type="text" name="shippingZip" id="shippingZip" value="<?php echo $this->escape(Request::getString('shippingZip', false, 'post')); ?>" />
						</label>

						<label for="shippingState">
							<?php echo Lang::txt('State:'); ?>
							<select name="shippingState" id="shippingState">
								<option value=""><?php echo Lang::txt(' -- please select -- '); ?></option>
								<?php
									foreach ($states as $abbr => $state)
									{
										echo '<option value="' . $abbr . '"';
										if (Request::getString('shippingState', false, 'post') == $abbr)
										{
											echo ' selected';
										}
										echo '>' . $state . '</option>';
									}
								?>
							</select>
						</label>

						<fieldset>
							<legend><?php echo Lang::txt('Save this address'); ?></legend>
							<label for="saveAddress">
								<input type="checkbox" class="option" name="saveAddress" id="saveAddress" />
								<?php echo Lang::txt('Save this address for future use'); ?>
							</label>
						</fieldset>

						<p class="submit">
							<input type="submit" value="<?php echo Lang::txt('Next'); ?>" name="submitShippingInfo" id="submitShippingInfo" class="btn" />
						</p>
					</fieldset>
				</form>
			</div>
			<div class="col span6 omega">
				<?php
				if (!empty($this->savedShippingAddresses))
				{
					echo '<h2>' . Lang::txt('Select saved address') . '</h2>';

					foreach ($this->savedShippingAddresses as $address)
					{
						echo '<div class="cartSection">';
						echo '<p>';
						echo $address->saToFirst . ' ' . $address->saToLast . '<br />';
						echo $address->saAddress . '<br />';
						echo $address->saCity . ', ' . $address->saState . ' ' . $address->saZip;
						echo '</p>';

						echo '<a href="' . Route::url('index.php?option=com_cart&controller=checkout/shipping/select/' . $address->saId) . '">';
						echo Lang::txt('Ship to this address');
						echo '</a>';

						echo '</div>';
					}
				}
				?>
			</div>
		</div>
	</div>
</section>