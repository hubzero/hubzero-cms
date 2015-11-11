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

namespace Components\Cart\Lib\Auditors;

use Components\Storefront\Models\Product;
use Components\Storefront\Models\Sku;
use Components\Cart\Helpers\CartDownload;

require_once('BaseAuditor.php');
require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Product.php';
require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Sku.php';
require_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'Download.php');

class Software_Auditor extends BaseAuditor
{
	/**
	 * Constructor
	 *
	 * @param 	void
	 * @return 	void
	 */
	public function __construct($type, $pId, $crtId)
	{
		parent::__construct($type, $pId, $crtId);
	}

	/**
	 * Main handler. Does all the checks
	 *
	 * @param 	void
	 * @return 	void
	 */
	public function audit()
	{
		/* If no user, some checks may be skipped... */
		// Get user
		$jUser = User::getRoot();
		// User specific checks
		if (!$jUser->get('guest'))
		{
			if ($sId = $this->getSku())
			{
				// Check if the current user reached the max count of downloads for this SKU
				$sku = new Sku($sId);
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
				return ($this->getResponse());
			}
		}

		// Check SKU-related stuff if this is a SKU
		if ($sId = $this->getSku())
		{
			// Check if SKU is reached the download max count
			$sku = new Sku($sId);
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
			return($this->getResponse());
		}

		// Get product download limit
		$productDownloadLimit = Product::getMeta($this->pId, 'globalDownloadLimit');
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
		return($this->getResponse());
	}
}