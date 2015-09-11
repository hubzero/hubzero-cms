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

// No direct access.
defined('_HZEXEC_') or die();

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
					if (!empty($this->productImg))
					{
						echo '<div class="productImages">';
					}

					if (!strstr($this->productImg[0], 'noimage'))
					{
						echo '<a href="' . $this->productImg[0] . '"';
						echo ' rel="lightbox">';
					}
					echo '<img src="' . $this->productImg[0] . '" />';
					if (!strstr($this->productImg[0], 'noimage'))
					{
						echo '</a>';
					}

					if (!empty($this->productImg))
					{
						echo '</div>';
					}

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