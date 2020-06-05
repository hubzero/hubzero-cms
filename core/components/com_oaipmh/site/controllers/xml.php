<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Oaipmh\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Oaipmh\Models\Service;
use Document;
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
		$metadata   = Request::getString('metadataPrefix');
		$from       = Request::getString('from');
		if ($from)
		{
			$from = \Date::of($from)->toSql();
		}
		$until      = Request::getString('until');
		if ($until)
		{
			$until = \Date::of($until)->toSql();
		}
		$set        = Request::getString('set');
		$resumption = urldecode(Request::getString('resumptionToken'));
		$identifier = urldecode(Request::getString('identifier'));

		$igran  = 'YYYY-MM-DD';
		$igran .= $this->config->get('gran', 'c') == 'c' ? 'Thh:mm:ssZ' : '';

		$hubname = rtrim($this->config->get('base_url', str_replace('https', 'http', Request::base())), '/');

		$edate = $this->config->get('edate');
		$edate = ($edate ? strtotime($edate) : time());

		// Set the document type
		Document::setType('xml');

		// Initiate the service
		$service = new Service(rtrim(Request::getSchemeAndHttpHost(), '/') . Route::url('index.php?option=' . $this->_option . '&task=stylesheet&metadataPrefix=' . $metadata));

		$service->set('metadataPrefix', $metadata)
				->set('repositoryName', $this->config->get('repository_name', \Config::get('sitename')))
				->set('baseURL', $hubname)
				->set('protocolVersion', '2.0')
				->set('adminEmail', $this->config->get('email', \Config::get('mailfrom')))
				->set('earliestDatestamp', gmdate('Y-m-d\Th:i:s\Z', $edate))
				->set('deletedRecord', $this->config->get('del'))
				->set('granularity', $igran)
				->set('limit', $this->config->get('limit', 50))
				->set('start', 0)
				->set('allow_ore', $this->config->get('allow_ore', 0))
				->set('gran', $this->config->get('gran', 'c'))
				->set('resumption', $resumption);

		$verb = Request::getString('verb');
		switch ($verb)
		{
			case 'Identify':
				if (!$service->getError() && $identifier)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'identifier'), $verb);
				}

				if (!$service->getError() && $set)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'set'), $verb);
				}

				if (!$service->getError() && $from)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'from'), $verb);
				}

				if (!$service->getError() && $until)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'until'), $verb);
				}

				if (!$service->getError() && $resumption)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'resumptionToken'), $verb);
				}

				if (!$service->getError())
				{
					$service->identify();
				}
			break;

			case 'ListMetadataFormats':
				if (!$service->getError() && $set)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'set'), $verb);
				}

				if (!$service->getError() && $from)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'from'), $verb);
				}

				if (!$service->getError() && $until)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'until'), $verb);
				}

				if (!$service->getError() && $resumption)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'resumptionToken'), $verb);
				}

				if (!$service->getError())
				{
					$service->formats();
				}
			break;

			case 'ListSets':
				if (!empty($resumption))
				{
					try
					{
						$data = $service->decodeToken($resumption);
					}
					catch (\Exception $e)
					{
						$service->error($service::ERROR_BAD_RESUMPTION_TOKEN, null, $verb);
					}

					if (!$service->getError())
					{
						if (!$data || !is_array($data))
						{
							$service->error($service::ERROR_BAD_RESUMPTION_TOKEN, null, $verb);
						}
						else
						{
							$service->set('limit', isset($data['limit']) ? $data['limit'] : $service->get('limit'));
							$service->set('start', isset($data['start']) ? $data['start'] + $service->get('limit') : $service->get('start'));
							$from     = isset($data['from'])   ? $data['from']   : $from;
							$until    = isset($data['until'])  ? $data['until']  : $until;
							$set      = isset($data['set'])    ? $data['set']    : $set;
							$metadata = isset($data['prefix']) ? $data['prefix'] : $metadata;
							$service->set('metadataPrefix', $metadata);
						}
					}
				}

				if ($metadata)
				{
					$service->setSchema($metadata);
				}

				if (!$service->getError() && $identifier)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'identifier'), $verb);
				}

				if (!$service->getError() && $set)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'set'), $verb);
				}

				if (!$service->getError() && $from)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'from'), $verb);
				}

				if (!$service->getError() && $until)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'until'), $verb);
				}

				if (!$service->getError())
				{
					$service->sets();
				}
			break;

			case 'ListIdentifiers':
				if (!empty($resumption))
				{
					try
					{
						$data = $service->decodeToken($resumption);
					}
					catch (\Exception $e)
					{
						$service->error($service::ERROR_BAD_RESUMPTION_TOKEN, null, $verb);
					}

					if (!$service->getError())
					{
						if (!$data || !is_array($data))
						{
							$service->error($service::ERROR_BAD_RESUMPTION_TOKEN, null, $verb);
						}
						else
						{
							$service->set('limit', isset($data['limit']) ? $data['limit'] : $service->get('limit'));
							$service->set('start', isset($data['start']) ? $data['start'] + $service->get('limit') : $service->get('start'));
							$from     = isset($data['from'])   ? $data['from']   : $from;
							$until    = isset($data['until'])  ? $data['until']  : $until;
							$set      = isset($data['set'])    ? $data['set']    : $set;
							$metadata = isset($data['prefix']) ? $data['prefix'] : $metadata;
							$service->set('metadataPrefix', $metadata);
						}
					}
				}

				if (!$service->getError() && !$metadata)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'metadataPrefix'), $verb);
				}
				if (!$service->getError())
				{
					$service->setSchema($metadata);
				}

				if (!$service->getError() && $identifier)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'identifier'), $verb);
				}

				if (!$service->getError())
				{
					$service->identifiers($from, $until, $set);
				}
			break;

			case 'ListRecords':
				if (!empty($resumption))
				{
					try
					{
						$data = $service->decodeToken($resumption);
					}
					catch (\Exception $e)
					{
						$service->error($service::ERROR_BAD_RESUMPTION_TOKEN, null, $verb);
					}

					if (!$service->getError())
					{
						if (!$data || !is_array($data))
						{
							$service->error($service::ERROR_BAD_RESUMPTION_TOKEN, null, $verb);
						}
						else
						{
							$service->set('limit', isset($data['limit']) ? $data['limit'] : $service->get('limit'));
							$service->set('start', isset($data['start']) ? $data['start'] + $service->get('limit') : $service->get('start'));
							$from     = isset($data['from'])   ? $data['from']   : $from;
							$until    = isset($data['until'])  ? $data['until']  : $until;
							$set      = isset($data['set'])    ? $data['set']    : $set;
							$metadata = isset($data['prefix']) ? $data['prefix'] : $metadata;
							$service->set('metadataPrefix', $metadata);
						}
					}
				}

				if (!$service->getError() && !$metadata)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'metadataPrefix'), $verb);
				}
				if (!$service->getError())
				{
					$service->setSchema($metadata);
				}

				if (!$service->getError() && $identifier)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'identifier'), $verb);
				}

				if (!$service->getError())
				{
					$service->records($from, $until, $set);
				}
			break;

			case 'GetRecord':
				if (!$metadata)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'metadataPrefix'), $verb);
				}
				if (!$service->getError())
				{
					$service->setSchema($metadata);
				}

				if (!$service->getError() && $set)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'set'), $verb);
				}

				if (!$service->getError() && $from)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'from'), $verb);
				}

				if (!$service->getError() && $until)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'until'), $verb);
				}

				if (!$service->getError() && $resumption)
				{
					$service->error($service::ERROR_BAD_ARGUMENT, Lang::txt('COM_OAIPMH_ILLEGAL_ARGUMENT', 'resumptionToken'), $verb);
				}

				if (!$service->getError())
				{
					if (!$identifier)
					{
						$service->error($service::ERROR_BAD_ID);
					}
					else
					{
						$service->record($identifier);
					}
				}
			break;

			default:
				$service->error($service::ERROR_BAD_VERB, Lang::txt('COM_OAIPMH_ILLEGAL_VERB'));
			break;
		}

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
			->setLayout(Request::getString('stylesheet', 'stylesheet'))
			->display();
	}
}
