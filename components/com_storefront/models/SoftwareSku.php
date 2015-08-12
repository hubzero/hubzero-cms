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
 * @author    Hubzero
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Sku.php');

/**
 *
 * Storefront SKU class
 *
 */
class StorefrontModelSoftwareSku extends StorefrontModelSku
{
	/**
	 * Contructor
	 *
	 * @param  void
	 * @return void
	 */
	public function __construct($sId)
	{
		parent::__construct($sId);
	}

	public function verify()
	{
		parent::verify();

		// Check if the download file really exists
		$params = JComponentHelper::getParams('com_storefront');
		$downloadFolder = $params->get('downloadFolder');
		$dir = JPATH_ROOT . $downloadFolder;
		$file = $dir . DS . $this->data->meta['downloadFile'];

		if (!file_exists($file))
		{
			throw new Exception(JText::_('Download file doesn\'t exist'));
		}
	}

}