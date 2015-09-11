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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$states = Cart_Helper::getUsStates();

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
				<h2>Shipping address</h2>

				<form name="cartShippingInfo" class="cartShippingInfo full" method="post" id="hubForm">
					<fieldset>
						<label for="shippingToFirst">First name:
							<input type="text" name="shippingToFirst" id="shippingToFirst" value="<?php echo Request::getVar('shippingToFirst', false, 'post'); ?>" />
						</label>

						<label for="shippingToLast">Last name:
							<input type="text" name="shippingToLast" id="shippingToLast" value="<?php echo Request::getVar('shippingToLast', false, 'post'); ?>" />
						</label>

						<label for="shippingAddress">Shipping address:
							<input type="text" name="shippingAddress" id="shippingAddress" value="<?php echo Request::getVar('shippingAddress', false, 'post'); ?>" />
						</label>

						<label for="shippingCity">City:
							<input type="text" name="shippingCity" id="shippingCity" value="<?php echo Request::getVar('shippingCity', false, 'post'); ?>" />
						</label>

						<label for="shippingZip">Zip:
							<input type="text" name="shippingZip" id="shippingZip" value="<?php echo Request::getVar('shippingZip', false, 'post'); ?>" />
						</label>

						<label for="shippingState">State:
							<select name="shippingState" id="shippingState">
								<option value=""> -- please select -- </option>
								<?php
									foreach ($states as $abbr => $state)
									{
										echo '<option value="' . $abbr . '"';
										if (Request::getVar('shippingState', false, 'post') == $abbr)
										{
											echo ' selected';
										}
										echo '>' . $state . '</option>';
									}
								?>
							</select>
						</label>

						<fieldset>
							<legend>Save this address</legend>
							<label for="saveAddress"><input type="checkbox" class="option" name="saveAddress" id="saveAddress" /> Save this address for future use</label>
						</fieldset>

						<p class="submit">
							<input type="submit" value="Next" name="submitShippingInfo" id="submitShippingInfo" class="btn" />
						</p>
					</fieldset>
				</form>
			</div>
			<div class="col span6 omega">
				<?php
				if (!empty($this->savedShippingAddresses))
				{
					echo '<h2>Select saved address</h2>';

					foreach ($this->savedShippingAddresses as $address)
					{
						echo '<div class="cartSection">';
						echo '<p>';
						echo $address->saToFirst . ' ' . $address->saToLast . '<br>';
						echo $address->saAddress . '<br>';
						echo $address->saCity . ', ' . $address->saState . ' ' . $address->saZip;
						echo '</p>';

						echo '<a href="';
						echo Route::url('index.php?option=com_cart/checkout/shipping/select/' . $address->saId);
						echo '">';
						echo 'Ship to this address';
						echo '</a>';

						echo '</div>';
					}
				}
				?>
			</div>
		</div>
	</div>
</section>