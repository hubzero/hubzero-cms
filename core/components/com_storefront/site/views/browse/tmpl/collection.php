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

<header id="content-header">
	<h2><?php echo $this->collectionName; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="btn" href="/cart"><?php echo  Lang::txt('COM_STOREFRONT_CART'); ?></a>
		</p>
	</div>

</header>

<section class="section">
	<div class="section-inner">

	<?php

		if (!empty($this->products))
		{
			echo '<ul class="rres cf">';
			foreach ($this->products as $product)
			{
				// find if there is an alias
				$productIdentificator = $product->pId;
				if (!empty($product->pAlias))
				{
					$productIdentificator = $product->pAlias;
				}

				echo '<li class="';
				if ($product->imgName)
				{
					echo 'with-img';
				}
				echo '">';
					if ($product->imgName)
					{
						echo '<div class="img" style="background-image: url(';
						$imgPath = DS . trim($this->config->get('imagesFolder', '/app/site/storefront/products'), DS) . DS . $product->pId . DS;
						echo "'" . $imgPath . $product->imgName . "'";
						echo ')"></div>';
					}
					else
					{
						echo '<div class="img" style="background-image: url(';
						$imgPath = dirname(dirname(dirname(str_replace(PATH_ROOT, '', __DIR__)))) . DS . 'assets' . DS . 'img' . DS;
						echo "'" . $imgPath . "noimage.png'";
						echo ')"></div>';
					}
					echo '<a href="';
					echo Route::url('index.php?option=' . Request::getVar('option')) . 'product/' . $productIdentificator;
					echo '">';
					echo '<div class="content">';
					echo '<h3>' . $product->pName . '</h3>';
					echo '</div>';
					echo '</a>';
				echo '</li>';
			}
			echo '<li class="stub"><div class="a"><div class="content"><p>&nbsp;</p></div></div></li>';
			echo '</ul>';
		}
		else {
			echo Lang::txt('COM_STOREFRONT_NO_PRODUCTS');
		}

	?>

	</div>
</section>