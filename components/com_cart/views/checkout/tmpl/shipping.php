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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$states = Cart_Helper::getUsStates();

$this->css();
?>

<header id="content-header">
	<h2>Checkout: shipping information</h2>
</header>

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

				<form name="cartShippingInfo" id="cartShippingInfo" method="post" class="hubForm">
					<label for="shippingToFirst">First name:
						<input type="text" name="shippingToFirst" id="shippingToFirst" value="<?php echo JRequest::getVar('shippingToFirst', false, 'post'); ?>" />
					</label>

					<label for="shippingToLast">Last name:
						<input type="text" name="shippingToLast" id="shippingToLast" value="<?php echo JRequest::getVar('shippingToLast', false, 'post'); ?>" />
					</label>

					<label for="shippingAddress">Shipping address:
						<input type="text" name="shippingAddress" id="shippingAddress" value="<?php echo JRequest::getVar('shippingAddress', false, 'post'); ?>" />
					</label>

					<label for="shippingCity">City:
						<input type="text" name="shippingCity" id="shippingCity" value="<?php echo JRequest::getVar('shippingCity', false, 'post'); ?>" />
					</label>

					<label for="shippingZip">Zip:
						<input type="text" name="shippingZip" id="shippingZip" value="<?php echo JRequest::getVar('shippingZip', false, 'post'); ?>" />
					</label>

					<label for="shippingState">State:
						<select name="shippingState" id="shippingState">
							<option value=""> -- please select -- </option>
							<?php
								foreach ($states as $abbr => $state)
								{
									echo '<option value="' . $abbr . '"';
									if (JRequest::getVar('shippingState', false, 'post') == $abbr)
									{
										echo ' selected';
									}
									echo '>' . $state . '</option>';
								}
							?>
						</select>
					</label>

					<label for="saveAddress"><input type="checkbox" name="saveAddress" id="saveAddress" /> Save this address?</label>

					<p class="submit">
						<input type="submit" value="Next" name="submitShippingInfo" id="submitShippingInfo" class="btn" />
					</p>
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
						echo JRoute::_('index.php?option=com_cart/checkout/shipping/select/' . $address->saId);
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