<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

require_once dirname(__DIR__) . DS . 'models' . DS . 'Warehouse.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'Collection.php';

class Integrity
{
	public static function skuIntegrityCheck($sku)
	{
		$return = new \stdClass();
		$return->status = 'ok';
		$return->errors = array();

		//print_r($sku);

		$options = $sku->getOptions();

		// Check if there are other SKUs that have the same set of options
		$warehouse = new \Components\Storefront\Models\Warehouse();
		$skuMatch = $warehouse->mapSku($sku->getProductId(), $options, false);

		if ($skuMatch && $skuMatch != $sku->getId())
		{
			$return->status = 'error';
			// If there are no options, no multiple SKUs can be published
			if (empty($options))
			{
				$return->errors[] = 'There is already another SKU published for this product. A product without product options can only have one SKU.';
			}
			else
			{
				$return->errors[] = 'There is already a SKU with the identical set of options. Each SKU must have a unique set of options.';
			}
		}

		// If allowing multiple check if the parent product allows it. If not -- parent must be set to allow it first.
		if ($sku->getAllowMultiple())
		{
			$pInfo = $warehouse->getProductInfo($sku->getProductId());
			if (!$pInfo->pAllowMultiple)
			{
				$return->status = 'error';
				$return->errors[] = 'Cannot allow multiple, the parent product is not set to allow multiple.';
			}
		}

		return $return;
	}

	public static function collectionIntegrityCheck($collection)
	{
		$return = new \stdClass();
		$return->status = 'ok';
		$return->errors = array();

		// Check if there are other collections that have the same alias
		try
		{
			$conflictingCollectionId = \Components\Storefront\Models\Collection::findActiveCollectionByAlias($collection->getAlias());
			if ($conflictingCollectionId && $conflictingCollectionId != $collection->getId())
			{
				$return->status = 'error';
				$return->errors[] = 'There is already another collection published with the same alias. Alias must be unique.';
			}
		}
		catch (\Exception $e)
		{
			// No conflicting product found (hence, the Exception), good to go.
			return $return;
		}

		return $return;
	}
}
