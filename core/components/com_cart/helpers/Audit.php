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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

require_once(PATH_CORE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_cart' . DS . 'lib' . DS . 'auditors' . DS . 'BaseAuditor.php');

class Audit
{

	/**
	 * Constructor
	 * @param 	Object  Product info
	 * @param   int     Cart ID
	 * @param   int     User ID
	 * @return 	Void
	 */
	public static function getAuditor($pInfo, $crtId)
	{
		$pId = $pInfo->pId;

		$warehouse = new StorefrontModelWarehouse();
		// Get product type
		$pType = $warehouse->getProductTypeInfo($pInfo->ptId);

		$type = $pType['ptName'];
		$model = $pType['ptModel'];

		// Find if there are auditors for this product's type and model
		$auditorsPath = PATH_CORE . DS . 'components' . DS . 'com_cart' . DS . 'lib' . DS . 'auditors';

		$auditorClass = str_replace(' ', '_', ucwords(strtolower($model))) . '_Auditor';
		if (file_exists($auditorsPath . DS . $auditorClass . '.php'))
		{
			// Include the auditor file
			include_once($auditorsPath . DS . $auditorClass . '.php');
			return new $auditorClass($type, $pId, $crtId);
		}
		else
		{
			return new BaseAuditor($type);
		}
	}
}