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

$this->css()
	->js();

?>

<header id="content-header">
	<h2><?php echo  Lang::txt('COM_STOREFRONT'); ?> homepage</h2>

	<div id="content-header-extra">
		<p>
			<a class="btn" href="/cart"><?php echo  Lang::txt('COM_STOREFRONT_CART'); ?></a>
		</p>
	</div>
</header>

<section class="section">
	<div class="section-inner">
		<?php

		if (sizeof($this->categories))
		{
			//echo '<h3>Product categories</h3>';

			echo '<ul class="rres cf">';
			foreach ($this->categories as $category)
			{
				echo '<li class="';
				if (isset($category->imgName) && $category->imgName)
				{
					echo 'with-img';
				}
				echo '">';
				if (isset($category->imgName) && $category->imgName)
				{
					echo '<div class="img" style="background-image: url(';
					$imgPath = DS . trim($this->config->get('collectionsImagesFolder', '/app/site/storefront/collections'), DS) . DS . $category->cId . DS;
					echo "'" . $imgPath . $category->imgName . "'";
					echo ')"></div>';
				}

				echo '<a href="';

				// Use alias if exists, otherwise use pId
				$categoryId = $category->cId;
				if (!empty($category->cAlias))
				{
					$categoryId = $category->cAlias;
				}

				echo Route::url('index.php?option=' . $this->option) . 'browse/' . $categoryId;
				echo '">';
				echo '<div class="content">';
				echo '<h3>' . $category->cName . '</h3>';
				echo '</div>';
				echo '</a>';
				echo '</li>';
			}
			echo '<li class="stub"><div class="a"><div class="content"><p>&nbsp;</p></div></div></li>';
			echo '</ul>';
		}
		else
		{
			echo '<p>' . Lang::txt('COM_STOREFRONT_NO_CATEGORIES_SETUP') . '</p>';
		}

		?>
	</div>
</section>