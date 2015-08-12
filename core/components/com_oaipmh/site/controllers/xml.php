<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Oaipmh\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Oaipmh\Models\Service;
use Request;
use Session;
use Lang;

/**
 * OAIPMH controller for XML output
 */
class Xml extends SiteController
{
	/**
	 * Pull records and build the XML
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$metadata   = Request::getVar('metadataPrefix', 'oai_dc');
		$from       = Request::getVar('from');
		$until      = Request::getVar('until');
		$set        = Request::getVar('set');
		$resumption = Request::getVar('resumptionToken');

		$igran  = "YYYY-MM-DD";
		$igran .= $this->config->get('gran', 'c') == 'c' ? "Thh:mm:ssZ" : '';

		$hubname = rtrim($this->config->get('base_url', str_replace('https', 'http', \Request::base())), '/');

		$edate = $this->config->get('edate');
		$edate = ($edate ? strtotime($edate) : time());

		$service = new Service($metadata, rtrim(Request::base(), '/') . Route::url('index.php?option=' . $this->_option . '&task=stylesheet&metadataPrefix=' . $metadata));
		$service->set('metadataPrefix', $metadata)
				->set('repositoryName', $this->config->get('repository_name', \Config::get('sitename')))
				->set('baseURL', $hubname)
				->set('protocolVersion', '2.0')
				->set('adminEmail', $this->config->get('email', \JFactory::getConfig()->get('mailfrom')))
				->set('earliestDatestamp', gmdate('Y-m-d\Th:i:s\Z', $edate))
				->set('deletedRecord', $this->config->get('del'))
				->set('granularity', $igran)
				->set('max', $this->config->get('max', 500))
				->set('limit', $this->config->get('limig', 50))
				->set('allow_ore', $this->config->get('allow_ore', 0))
				->set('gran', $this->config->get('gran', 'c'))
				->set('resumption', $resumption);

		$verb = Request::getVar('verb');
		switch ($verb)
		{
			case 'GetRecord':
				$service->record(Request::getVar('identifier'));
			break;

			case 'Identify':
				$service->identify();
			break;

			case 'ListMetadataFormats':
				$service->formats();
			break;

			case 'ListIdentifiers':
				$service->identifiers($from, $until, $set);
			break;

			case 'ListRecords':
				$sessionTokenResumptionTemp = Session::get($resumption);

				if (!empty($resumption) && empty($sessionTokenResumptionTemp))
				{
					$service->error($service::ERROR_BAD_RESUMPTION_TOKEN);
				}

				$service->records($from, $until, $set);
			break;

			case 'ListSets':
				$sessionTokenResumptionTemp = Session::get($resumption);

				if (!empty($resumption) && empty($sessionTokenResumptionTemp))
				{
					$service->error($service::ERROR_BAD_RESUMPTION_TOKEN);
				}

				$service->sets();
			break;

			default:
				$service->error($service::ERROR_BAD_VERB, Lang::txt('COM_OAIPMH_ILLEGAL_VERB'));
			break;
		}

		Document::setType('xml');

		echo $service;
	}

	/**
	 * Output an XSL template
	 *
	 * @return  void
	 */
	public function stylesheetTask()
	{
		Document::setType('xml');

		$this->view
			->setLayout(Request::getVar('stylesheet', 'stylesheet'))
			->display();
	}
}
