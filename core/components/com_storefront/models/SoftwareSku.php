<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Models;

use Components\Storefront\Helpers\Serials;
use Component;
use Lang;

require_once __DIR__ . DS . 'Sku.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'Serials.php';

/**
 *
 * Storefront SKU class
 *
 */
class SoftwareSku extends Sku
{
	/**
	 * Contructor
	 *
	 * @param   int   $sId
	 * @return  void
	 */
	public function __construct($sId)
	{
		parent::__construct($sId);
	}

	/**
	 * Reserve inventory
	 *
	 * @param   int   $qty
	 * @return  void
	 */
	public function reserveInventory($qty)
	{
		parent::reserveInventory($qty);

		// Mark the serials as reserved
		Serials::reserveSerials($this->getId(), $qty);
	}

	/**
	 * Release inventory
	 *
	 * @param   int   $qty
	 * @return  void
	 */
	public function releaseInventory($qty)
	{
		parent::releaseInventory($qty);

		// Mark the reserved serials as available
		Serials::releaseSerials($this->getId(), $qty);
	}

	/**
	 * Verify
	 *
	 * @return  void
	 */
	public function verify()
	{
		parent::verify();

		// Check if the download file is set
		if (empty($this->data->meta['downloadFile']) || !$this->data->meta['downloadFile'])
		{
			throw new \Exception(Lang::txt('Download file must be set'));
		}

		// Check if the download file really exists
		$storefrontConfig = Component::params('com_storefront');
		$dir = $storefrontConfig->get('downloadFolder', '/site/protected/storefront/software');
		$file = PATH_APP . $dir . DS . $this->data->meta['downloadFile'];

		if (!file_exists($file))
		{
			throw new \Exception(Lang::txt('Download file doesn\'t exist'));
		}
	}

	/**
	 * Save
	 *
	 * @return  void
	 */
	public function save()
	{
		// Update the inventory level for those SKUs that have multiple managed Serial Numbers.
		// The inventory should be tracked
		// The inventory level should always be equal to the number of available not-reserved serial numbers.

		$serialManagement = $this->getMeta('serialManagement');
		if ($serialManagement && $serialManagement == 'multiple')
		{
			$totalSerials = Serials::countAvailableSerials($this->getId());
			$this->setTrackInventory(true);
			$this->setInventoryLevel($totalSerials);
		}

		parent::save();
	}
}
