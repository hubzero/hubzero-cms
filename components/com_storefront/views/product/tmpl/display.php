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

?>

<?php

$errors = $this->getError();
if (!empty($errors))
{
	echo '<div class="messages errors">';
		foreach ($errors as $error)
		{
			echo '<p>' . $error . '</p>';
		}
	echo '</div>';
}

?>

<header id="content-header">
	<h2><?php echo $this->product->pName; ?></h2>
</header>

<section class="main section">
	<div class="section-inner">

	<?php
		// format price/price range
		$price = $this->price;
		$priceRange = '$';

		if ($price['high'] == $price['low'])
		{
			$priceRange .= $price['high'];
		}
		else {
			$priceRange .= $price['low'] . ' &ndash; $' . $price['high'];
		}

		if(!$this->inStock)
		{
			$priceRange = 'Out of stock';
		}

	?>

	<div id="price"><?php echo $priceRange; ?></div>

	<form id="productInfo" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
	<input type="hidden" name="pId" value="<?php echo $this->pId; ?>" />

	<?php
	if (count($this->options))
	{
	?>

	<h3>Product options</h3>

	<div id="productOptions">

	<?php

		foreach ($this->options as $optionGroupId => $info)
		{
			echo '<p>' . $info['info']->ogName . '</p>';
			echo '<ul>';

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

	<h3>Product info</h3>

	<?php

		foreach ($this->product as $k => $val)
		{
			echo '<p>' . $k . ': ' . $val . '</p>';
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
				echo '<select name="qty" id="qty">';
					for ($i = 1; $i <= $this->qtyDropDown; $i++)
					{
						echo '<option value="' . $i . '">' . $i . '</option>';
					}
				echo '</select>';
			}
		}
	?>
	</div>

	<?php
	if($this->inStock)
	{
	?>
	<input type="submit" value="Add to cart" class="btn <?php  echo($addToCartEnabled ? 'enabled' : 'disabled'); ?>" name="addToCart" id="addToCart" />
	<?php
	}
	?>
	</form>

	</div>
</section>