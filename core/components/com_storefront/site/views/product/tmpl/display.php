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

setlocale(LC_MONETARY, 'en_US.UTF-8');

?>

<header id="content-header">
	<h2><?php echo $this->product->pName; ?></h2>
</header>

<?php

if (!empty($this->notifications))
{
	$view = new \Hubzero\Component\View(array('name'=>'shared', 'layout' => 'notifications'));
	$view->notifications = $this->notifications;
	$view->display();
}

?>

<section class="section">
	<div class="section-inner">

		<div class="grid">

			<div class="col span6">

				<?php

				$imgPath = DS . trim($this->config->get('imagesFolder', '/site/storefront/products'), DS) . DS . $this->pId . DS;

				if (empty($this->product->images))
				{
					$imgPath = dirname(dirname(dirname(str_replace(PATH_ROOT, '', __DIR__)))) . DS . 'assets' . DS . 'img' . DS;
					$image = new \stdClass();
					$image->imgName = 'noimage.png';
					$this->product->images[0] = $image;
				}

				echo '<div class="productImages">';
				if (!strstr($this->product->images[0]->imgName, 'noimage'))
				{
					echo '<a href="' . $imgPath . $this->product->images[0]->imgName . '"';
					echo ' rel="lightbox">';
				}
				echo '<img src="' . $imgPath . $this->product->images[0]->imgName . '" />';
				if (!strstr($this->product->images[0]->imgName, 'noimage'))
				{
					echo '</a>';
				}
				echo '</div>';

				?>

				</form>

			</div>

			<div class="col span6 omega">

				<?php
				// format price/price range
				$price = $this->price;
				$priceRange = '';

				if ($price['high'] == $price['low'])
				{
					$priceRange .= money_format('%n', $price['high']);
				}
				else {
					$priceRange .= money_format('%n', $price['low']) . ' &ndash; ' . money_format('%n', $price['high']);
				}

				$out = false;
				if (!$this->inStock)
				{
					$priceRange = 'Out of stock';
					$out = true;
				}

				?>

				<div id="price" class="<?php echo $out ? 'outofstock' : ''; ?>"><?php echo $priceRange; ?></div>

				<form id="productInfo" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
					<input type="hidden" name="pId" value="<?php echo $this->pId; ?>" />

					<?php
					if (isset($this->options) && count($this->options))
					{
						?>

						<!--h3>Product options</h3-->

						<div id="productOptions">

							<?php

							foreach ($this->options as $optionGroupId => $info)
							{
								echo '<p class="option-label">' . $info['info']->ogName . ':</p>';
								echo '<ul class="product-options">';

								foreach ($info['options'] as $opt)
								{
									echo '<li><input type="radio" name="og[' . $optionGroupId . ']" value="' . $opt->oId . '" id="option_' . $opt->oId . '">';
									echo '<label for="option_' . $opt->oId . '">' . $opt->oName . '</label></li>';
								}

								echo '</ul>';
							}

							?>

						</div>

					<?php
					}
					?>

					<div id="qtyWrap">
						<?php

						$addToCartEnabled = false;
						if ($this->qtyDropDown)
						{
							$addToCartEnabled = true;
							if ($this->qtyDropDown > 1)
							{
								echo '<div class="inner">';
								echo '<label>Quantity </label>';

								echo '<select name="qty" id="qty">';
								for ($i = 1; $i <= $this->qtyDropDown; $i++)
								{
									echo '<option value="' . $i . '">' . $i . '</option>';
								}
								echo '</select>';
								echo '</div>';
							}
						}
						?>
					</div>

					<?php
					if ($this->inStock && $this->productAvailable)
					{
						?>
						<p class="submit">
							<input type="submit" value="Add to cart"
								   class="btn <?php  echo($addToCartEnabled ? 'enabled' : 'disabled'); ?>"
								   name="addToCart" id="addToCart" />
						</p>
					<?php
					}
					?>

				</form>


				<?php

				//echo '<h3>' . $this->product->pName . '</h3>';
				echo '<h3>' . $this->product->pTagline . '</h3>';

				echo '<div class="description">';
				echo $this->product->pDescription;
				echo '</div>';

				if (!empty($this->product->pFeatures))
				{
					echo '<div class="features">';
					echo $this->product->pFeatures;
					echo '</div>';
				}

				foreach ($this->product as $k => $val)
				{
					//echo '<p>' . $k . ': ' . $val . '</p>';
				}
				?>

			</div>
		</div>
	</div>
</section>