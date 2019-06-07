<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
	->js();

?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_STOREFRONT'); ?> search</h2>

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
					<input type="text" name="q" id="entry-search-field" value="<?php echo $this->search; ?>" placeholder="Search">
				</fieldset>
			</div>
		</form>

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
					$imgPath = trim($this->config->get('imagesFolder', '/site/storefront/products'), DS) . DS . $product->pId . DS;
					echo "'/app/" . $imgPath . $product->imgName . "'";
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
				echo Route::url('index.php?option=' . Request::getCmd('option')) . 'product/' . $productIdentificator;
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
		else
		{
			echo '<p>No results found</p>';
		}
		?>
	</div>
</section>