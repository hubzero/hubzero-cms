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

$this->css();

// Get the name of the product downloaded
$product = $this->productInfo->pName;
$product .= ', ' . $this->productInfo->oName;

?>

<header id="content-header">
	<h2>Checkout: user agreement</h2>
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
			<div class="col span12">
				<p>In order to continue downloading <strong><?php echo $product; ?></strong> you must agree to the user agreement:</p>

				<form name="eula" class="full" method="post" id="hubForm">
					<fieldset>
						<label>Please read the user agreement:
							<div class="eula"><?php echo $this->productEula; ?></div>
						</label>

						<fieldset>
							<legend>Please confirm that you accept the user agreement</legend>
							<label for="acceptEula"><input type="checkbox" class="option" name="acceptEula" id="acceptEula" /> I Accept</label>

							<p>If you don't accept the user agreement, <a href="/cart">cancel and return to cart</a></p>
						</fieldset>

						<div class="submit">
							<input type="submit" value="Next" name="submitEula" id="submitEula" class="btn" />
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</section>