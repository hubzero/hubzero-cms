<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
	->js();

?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_STOREFRONT'); ?> homepage</h2>

	<div id="content-header-extra">
		<p>
			<a class="btn" href="/cart"><?php echo Lang::txt('COM_STOREFRONT_CART'); ?></a>
		</p>
	</div>
</header>

<section class="section">
	<div class="section-inner">
		<form action="/storefront/search/" method="get">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="Search">
				<fieldset class="entry-search">
					<label for="entry-search-field">Search</label>
					<input type="text" name="q" id="entry-search-field" value="" placeholder="Search">
				</fieldset>
			</div>
		</form>

		<?php

		if (count($this->categories))
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
					$imgPath = '/app/' . trim($this->config->get('collectionsImagesFolder', '/site/storefront/collections'), DS) . DS . $category->cId . DS;
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