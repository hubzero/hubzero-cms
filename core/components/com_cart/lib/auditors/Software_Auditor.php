<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Lib\Auditors;

use Components\Storefront\Models\Product;
use Components\Storefront\Models\Sku;
use Components\Cart\Helpers\CartDownload;
use User;

require_once __DIR__ . DS . 'BaseAuditor.php';
require_once \Component::path('com_storefront') . DS . 'models' . DS . 'Product.php';
require_once \Component::path('com_storefront') . DS . 'models' . DS . 'Sku.php';
require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'Download.php';

class Software_Auditor extends BaseAuditor
{
	/**
	 * Constructor
	 *
	 * @param   string   $type
	 * @param   integer  $pId
	 * @param   integer  $crtId
	 * @return  void
	 */
	public function __construct($type, $pId, $crtId)
	{
		parent::__construct($type, $pId, $crtId);
	}

	/**
	 * Main handler. Does all the checks
	 *
	 * @return  void
	 */
	public function audit()
	{
		// If no user, some checks may be skipped...
		// Get user
		$user = User::getInstance();
		// User specific checks
		if (!$user->get('guest'))
		{
			if ($sId = $this->getSku())
			{
				// Check if the current user reached the max count of downloads for this SKU
				$sku = Sku::getInstance($sId);
				$skuDownloadLimit = $sku->getMeta('downloadLimit');
				if ($skuDownloadLimit > 0)
				{
					// Get SKU download count
					$skuDownloadCount = CartDownload::countUserSkuDownloads($this->sId, $this->uId);
					// Check if the limit is reached
					if ($skuDownloadCount >= $skuDownloadLimit)
					{
						$this->setResponseStatus('error');
						$this->setResponseNotice('You have reached the maximum number of allowed downloads for this product.');
						$this->setResponseError(': you have reached the maximum number of allowed downloads for this product.');
					}
				}
				return $this->getResponse();
			}
		}

		// Check SKU-related stuff if this is a SKU
		if ($sId = $this->getSku())
		{
			// Check if SKU is reached the download max count
			$sku = Sku::getInstance($sId);
			$skuDownloadLimit = $sku->getMeta('globalDownloadLimit');
			if ($skuDownloadLimit > 0)
			{
				// Get SKU download count
				$skuDownloadCount = CartDownload::countSkuDownloads($this->sId);
				// Check if the limit is reached
				if ($skuDownloadCount >= $skuDownloadLimit)
				{
					$this->setResponseStatus('error');
					$this->setResponseNotice('This product has reached the maximum number of allowed downloads and cannot be downloaded.');
					$this->setResponseError(': this product has reached the maximum number of allowed downloads and cannot be downloaded.');
				}
			}
			return $this->getResponse();
		}

		// Get product download limit
		$productDownloadLimit = Product::getMetaValue($this->pId, 'globalDownloadLimit');
		// Get product downloads count
		if ($productDownloadLimit > 0)
		{
			$productDownloadCount = CartDownload::countProductDownloads($this->pId);
			// Check if the limit is reached
			if ($productDownloadCount >= $productDownloadLimit)
			{
				$this->setResponseStatus('error');
				$this->setResponseNotice('This product has reached the maximum number of allowed downloads and cannot be downloaded.');
				$this->setResponseError(': this product has reached the maximum number of allowed downloads and cannot be downloaded.');
			}
		}
		return $this->getResponse();
	}
}
