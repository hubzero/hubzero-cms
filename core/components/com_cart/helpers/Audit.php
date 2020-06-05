<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Helpers;

use Components\Storefront\Models\Warehouse;

require_once \Component::path('com_storefront') . DS . 'models' . DS . 'Warehouse.php';

class Audit
{
	/**
	 * Constructor
	 * @param   object  Product info
	 * @param   int     Cart ID
	 * @param   int     User ID
	 * @return  void
	 */
	public static function getAuditor($pInfo, $crtId)
	{
		$pId = $pInfo->pId;

		$warehouse = new Warehouse();
		// Get product type
		$pType = $warehouse->getProductTypeInfo($pInfo->ptId);

		$type = $pType['ptName'];
		$model = $pType['ptModel'];

		// Find if there are auditors for this product's type and model
		$auditorsPath = dirname(__DIR__) . DS . 'lib' . DS . 'auditors';

		$auditorClass = str_replace(' ', '_', ucwords(strtolower($model))) . '_Auditor';
		if (file_exists($auditorsPath . DS . $auditorClass . '.php'))
		{
			// Include the auditor file
			require_once $auditorsPath . DS . $auditorClass . '.php';
			$className = "\\Components\\Cart\\Lib\\Auditors\\" . $auditorClass;
			return new $className($type, $pId, $crtId);
		}
		else
		{
			require_once $auditorsPath . DS . 'BaseAuditor.php';
			return new \Components\Cart\Lib\Auditors\BaseAuditor($type);
		}
	}
}
