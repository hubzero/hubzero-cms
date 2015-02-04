<?php
/**
 * HUBzero CMS
 *
 * Copyright 2008-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2008-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

include_once(JPATH_COMPONENT. DS . 'models' . DS . 'info.php');

/**
 * System controller class for info
 */
class SystemControllerInfo extends \Hubzero\Component\AdminController
{
	/**
	 * Outputs a list of available scripts
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		if (!$this->juser->authorise('core.admin'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$model = new SystemModelInfo();

		$this->view
			->set('php_settings', $model->getPhpSettings())
			->set('config', $model->getConfig())
			->set('info', $model->getInfo())
			->set('php_info', $model->getPhpInfo())
			->set('directory', $model->getDirectory())
			->setLayout('default')
			->display();
	}
}
