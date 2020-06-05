<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$productsCount = 0;
if (!empty($this->products))
{
	$productsCount = count($this->products);
}

$skusCount = 0;
if (!empty($this->skus))
{
	$skusCount = count($this->skus);
}

?>

Publish down notifications
--------------------------

<?php

if ($skusCount + $productsCount == 1)
{
	echo 'One item in the storefront is set to be published down soon:';
	echo "\n";
}
else
{
	echo 'Several items in the storefront are set to be published down soon:';
	echo "\n";
}

if (!empty($this->products))
{
?>
=======================

<?php
echo 'Product';
if ($productsCount > 1)
{
	echo 's';
}
echo "\n";
?>

<?php
foreach ($this->products as $product)
{
	echo "{$product->pName} will be unpublished in {$product->daysLeftUntilPublishDown} day";
	echo $product->daysLeftUntilPublishDown > 1 ? 's' : '';
	echo " (Publish down is set to {$product->publish_down})";
	echo "\n";
}
}
?>

<?php
if (!empty($this->skus))
{
?>
=======================

<?php
echo 'SKU';
if ($skusCount > 1)
{
	echo 's';
}
echo "\n";

?>

<?php
foreach ($this->skus as $sku)
{
	echo "{$sku->sSku} will be unpublished in {$sku->daysLeftUntilPublishDown} day";
	echo $sku->daysLeftUntilPublishDown > 1 ? 's' : '';
	$local = Date::of($sku->publish_down)->toLocal();
	echo " (Publish down is set to {$local})";
	echo "\n";
}
}
