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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$productsCount = 0;
if (!empty($this->products))
{
	$productsCount = sizeof($this->products);
}

$skusCount = 0;
if (!empty($this->skus))
{
	$skusCount = sizeof($this->skus);
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
?>