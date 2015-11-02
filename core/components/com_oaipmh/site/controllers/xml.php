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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		if ($from)
		{
			$from = Date::of($from)->toSql();
		}
		$until      = Request::getVar('until');
		if ($until)
		{
			$until = Date::of($until)->toSql();
		}
		$set        = Request::getVar('set');
		$resumption = Request::getVar('resumptionToken');

		$igran  = "YYYY-MM-DD";
		$igran .= $this->config->get('gran', 'c') == 'c' ? "Thh:mm:ssZ" : '';

		$hubname = rtrim($this->config->get('base_url', str_replace('https', 'http', \Request::base())), '/');

		$edate = $this->config->get('edate');
		$edate = ($edate ? strtotime($edate) : time());

		$service = new Service($metadata, rtrim(Request::getSchemeAndHttpHost(), '/') . Route::url('index.php?option=' . $this->_option . '&task=stylesheet&metadataPrefix=' . $metadata));
		$service->set('metadataPrefix', $metadata)
				->set('repositoryName', $this->config->get('repository_name', \Config::get('sitename')))
				->set('baseURL', $hubname)
				->set('protocolVersion', '2.0')
				->set('adminEmail', $this->config->get('email', \Config::get('mailfrom')))
				->set('earliestDatestamp', gmdate('Y-m-d\Th:i:s\Z', $edate))
				->set('deletedRecord', $this->config->get('del'))
				->set('granularity', $igran)
				->set('limit', $this->config->get('limit', 50))
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
