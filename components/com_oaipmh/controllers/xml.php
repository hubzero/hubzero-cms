<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2012 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2012 Purdue University. All rights reserved.
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
 */
defined('_JEXEC') or die('Restricted access');

/**
 * OAIPMH controller for XML output
 */
class OaipmhControllerXml extends \Hubzero\Component\SiteController
{
	/**
	 * int(11) Primary key
	 * 
	 * @var  integer
	 */
	protected $hubname;

	/**
	 * int(11) Primary key
	 * 
	 * @var  integer
	 */
	protected $gran;

	/**
	 * int(11) Primary key
	 * 
	 * @var  integer
	 */
	protected $sets;

	/**
	 * int(11) Primary key
	 * 
	 * @var  integer
	 */
	protected $metadata;

	/**
	 * int(11) Primary key
	 * 
	 * @var  integer
	 */
	protected $from;

	/**
	 * int(11) Primary key
	 * 
	 * @var  integer
	 */
	protected $until;

	/**
	 * Pull records and build the XML
	 *
	 * return  void
	 */
	public function displayTask()
	{
		// check for multiple query sets
		$query = "SELECT DISTINCT display FROM `#__oaipmh_dcspecs`";
		$this->database->setQuery($query);
		$qsets = $this->database->loadResultArray();

		// get custom queries
		$this->sets = 0;
		if (count($qsets) > 1)
		{
			for ($i=0; $i<count($qsets); $i++)
			{
				$customs[$i] = new TablesOaipmhCustom($this->database, $qsets[$i]);
			}
			$this->sets = $i;
		}
		else
		{
			$customs = new TablesOaipmhCustom($this->database, 1);
			$this->sets = 1;
		}

		// set constants
		$juri = JURI::getInstance();

		$max_records     = $this->config->get('max', 500);
		$repository_name = $this->config->get('repository_name');
		$this->hubname   = rtrim($this->config->get('base_url', str_replace('https', 'http', $juri->base())), '/');
		$allow_ore       = $this->config->get('allow_ore', 0);
		$this->gran      = $this->config->get('gran', 'c');

		// get query vars
		$verb           = JRequest::getVar('verb');
		$identifier     = JRequest::getVar('identifier');
		$this->metadata = JRequest::getVar('metadataPrefix');
		$this->from     = JRequest::getVar('from');
		$this->until    = JRequest::getVar('until');
		$set            = JRequest::getVar('set');
		$resumption     = JRequest::getVar('resumptionToken');

		// start XML
		$response = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<OAI-PMH xmlns=\"http://www.openarchives.org/OAI/2.0/\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd\">";
		$now = gmdate('c', time());
		$response .= "<responseDate>$now</responseDate>";

		// chose verb
		switch ($verb)
		{
			case 'GetRecord':
				$response .= "<request verb=\"GetRecord\" identifier=\"$identifier\" metadataPrefix=\"$this->metadata\">$this->hubname/oaipmh</request>";
				// check for errors
				$check = new TablesOaipmhResult($this->database, $customs, $identifier);
				if ($check->identifier == '')
				{
					$badID = true;
				}
				$error = $this->errorCheck('GetRecord', $identifier, $badID);
				if ($error == "")
				{
					if ($this->metadata == "oai_dc")
					{
						$response .= "<GetRecord>";
					}
					// get record
					$result = new TablesOaipmhResult($this->database,$customs,$identifier);
					$response .= $this->doRecord($result);
					if ($this->metadata == "oai_dc")
					{
						$response .= "</GetRecord>";
					}
				}
				else
				{
					$response .= $error;
				}
			break;

			case 'Identify':
				$response .= "<request verb=\"Identify\">$this->hubname/oaipmh</request>";
				// check for errors
				if (!empty($this->metadata) || !empty($identifier) || !empty($resumption))
				{
					$response .= "<error code=\"badArgument\"/>";
				}
				else
				{
					$response .= " 
					<Identify> 
					<repositoryName>$repository_name</repositoryName> 
					<baseURL>$this->hubname</baseURL>
					<protocolVersion>2.0</protocolVersion> 
					<adminEmail>{$this->config->get('email')}</adminEmail> 
					<earliestDatestamp>{$this->config->get('edate')}</earliestDatestamp> 
					<deletedRecord>{$this->config->get('del')}</deletedRecord>";
					if ($this->gran == 'c')
					{
						$igran = "YYYY-MM-DDThh:mm:ssZ";
					}
					else
					{
						$igran = "YYYY-MM-DD";
					}
					$response .= "<granularity>$igran</granularity> 
					</Identify>";
				}
			break;

			case 'ListMetadataFormats':
				$response .= "<request verb=\"ListMetadataFormats\"";
				// TODO: add ID error check
				if (!empty($identifier))
				{
					$response .= " identifier=\"$identifier\"";
				}
				$response .= ">$this->hubname</request><ListMetadataFormats><metadataFormat><metadataPrefix>oai_dc</metadataPrefix><schema>http://www.openarchives.org/OAI/2.0/oai_dc.xsd</schema><metadataNamespace>http://www.openarchives.org/OAI/2.0/oai_dc/</metadataNamespace></metadataFormat>";
				if ($allow_ore)
				{
					$response .= "<metadataFormat><metadataPrefix>oai_ore</metadataPrefix><schema>http://www.openarchives.org/OAI/2.0/rdf.xsd</schema><metadataNamespace>http://www.openarchives.org/OAI/2.0/rdf/</metadataNamespace></metadataFormat>";
				}
				$response .= "</ListMetadataFormats>";
			break;

			case 'ListIdentifiers':
			case 'ListRecords':
				$response .= "<request verb=\"$verb\" metadataPrefix=\"$this->metadata\">$this->hubname/oaipmh</request>";
				// get session
				$session = JFactory::getSession();
				$sessionTokenResumptionTemp = $session->get($resumption);
				// get IDs
				$ids = $this->getRecords($customs, $this->from, $this->until);
				// check for errors
				$error = $this->errorCheck($verb, $ids, $resumption, $sessionTokenResumptionTemp);
				if ($error == '')
				{
					// start list
					$verb == "ListIdentifiers" ? $response .= "<ListIdentifiers>" : $response .= "<ListRecords>";
					// set flow control vars
					$begin = 0;
					$toWrite = 50;
					$completed = 0;
					$resumptionToken = 0;
					$split = false;
					// check completion
					if (!empty($resumption))
					{
						$session = JFactory::getSession();
						$completed = $session->get($resumption);
						$resumptionToken = $resumption;
					}
					// set up flow vars
					if ((count($ids) - $completed) > $max_records)
					{
						$toWrite = $max_records;
						$begin = $completed;
						$split = true;
					}
					else
					{
						$toWrite = count($ids) - $completed;
						$begin = $completed;
					}
					// set resumption session
					if (empty($resumption))
					{
						$session = JFactory::getSession();
						$resumptionToken = uniqid();
					}
					$session->set($resumptionToken, $begin + $toWrite);
					// list records
					// TODO: move to function
					if (is_array($customs))
					{
						foreach ($customs as $custom)
						{
							for ($i=$begin; $i<($begin + $toWrite); $i++)
							{
								$result = new TablesOaipmhResult($this->database, $custom, $ids[$i]);
								// record or just header?
								if ($verb == "ListIdentifiers")
								{
									$response .= $this->doHeader($result);
								}
								else
								{
									$response .= $this->doRecord($result);
								}
							}
						}
					}
					else
					{
						for ($i=$begin; $i<($begin + $toWrite); $i++)
						{
							$result = new TablesOaipmhResult($this->database, $customs, $ids[$i]);
							// record or just header
							if ($verb == "ListIdentifiers")
							{
								$response .= $this->doHeader($result);
							}
							else
							{
								$response .= $this->doRecord($result);
							}
						}
					}
					// write resumption token if needed
					if ($split)
					{
						$response .= "<resumptionToken completeListSize=\"" . count($ids) . "\" cursor=\"$begin\">$resumptionToken</resumptionToken>";
					}
					// end list
					$verb == "ListIdentifiers" ? $response .= "</ListIdentifiers>" : $response .= "</ListRecords>";
				}
				else
				{
					$response .= $error;
				}
			break;

			case 'ListSets':
				$response .= "<request verb=\"ListSets\">$this->hubname/oaipmh</request>";
				// get session
				$session =  JFactory::getSession();
				$sessionTokenResumptionTemp = $session->get($resumption);
				// check for errors
				$error = $this->errorCheck('ListSets', $resumption, $sessionTokenResumptionTemp);
				if ($error == "")
				{
					$response .= "<ListSets>". $this->doSets($customs) . "</ListSets>";
				}
				else
				{
					$response .= $error;
				}
			break;

			default :
				$response .= "<request>$this->hubname/oaipmh</request><error code=\"badVerb\">" . JText::_('COM_OAIPMH_ILLEGAL_VERB') . "</error>";
			break;
		}

		// end XML
		// [!] HUBZERO - OAI-PMH is hard-coded above, no need for IF statement below

		/*if ($this->metadata == 'oai_dc' || $this->metadata == '') // || $metadataPrefix == '')  [!] HUBZERO - Can't find any other reference to $metadataPrefix 
		{
			$response .= '</OAI-PMH>';
		}
		else
		{
			$response .= '</rdf:RDF>';
		}*/
		$response .= '</OAI-PMH>';

		$response = $this->formatXmlString($response);

		// send to View
		$this->view->xml = $response;
		$this->view->display();
		exit;
	}

	/**
	 * get record IDs from custom query
	 * 
	 * @param   mixed   $records
	 * @param   string  $from
	 * @param   string  $until
	 * @return  array
	 */
	protected function getRecords($records, $from='', $until='')
	{
		if (is_array($records))
		{
			$SQL = '';
			for ($i=0;$i<count($records); $i++)
			{
				$SQL .= $this->addDateRange($records[$i]->records, $from, $until) . " UNION ";
				$i++;
				$SQL .= $this->addDateRange($records[$i]->records, $from, $until) . " ";
			}
		}
		else
		{
			$SQL = $this->addDateRange($records->records, $from, $until);
		}
		$SQL = trim($SQL);
		$this->database->setQuery($SQL);
		return $this->database->loadResultArray();
	}

	/**
	 * add date ranges to query
	 * 
	 * @param   string  $SQL
	 * @param   string  $from
	 * @param   string  $until
	 * @return  string
	 */
	protected function addDateRange($SQL, $from, $until)
	{
		if (!empty($from))
		{
			stristr($SQL, "WHERE") === false ? $SQL .= " WHERE " : $SQL .= " AND ";
			$SQL .= "created > $from";
		}
		if (!empty($until))
		{
			stristr($SQL, "WHERE") === false ? $SQL .= " WHERE " : $SQL .= " AND ";
			$SQL .=  "created < $until";
		}
		return $SQL;
	}

	/**
	 * nicely form the XML - thanks, Goog 
	 * 
	 * @param   string   $xml
	 * @param   boolean  $html_output
	 * @return  string
	 */
	protected function formatXmlString($xml, $html_output=false)
	{
		$xml_obj = new SimpleXMLElement($xml);
		$level = 4;
		$indent = 0;
		$pretty = array();
		// get an array containing each XML element
		$xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml_obj->asXML()));
		// shift off opening XML tag if present
		if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0]))
		{
			$pretty[] = array_shift($xml);
		}
		foreach ($xml as $el)
		{
			if (preg_match('/^<([\w])+[^>\/]*>$/U', $el))
			{
				// opening tag, increase indent
				$pretty[] = str_repeat(' ', $indent) . $el;
				$indent += $level;
			}
			else
			{
				if (preg_match('/^<\/.+>$/', $el))
				{
					// closing tag, decrease indent
					$indent -= $level;
				}
				if ($indent < 0)
				{
					$indent += $level;
				}
				$pretty[] = str_repeat(' ', $indent) . $el;
			}
		}
		$xml = implode("\n", $pretty);
		return ($html_output) ? htmlentities($xml) : $xml;
	}

	/**
	 * check for verb-specific errors
	 * 
	 * @return  string
	 */
	protected function errorCheck()
	{
		$error = '';

		$args = func_get_args();
		switch ($args[0])
		{
			case 'GetRecord':
				if (empty($args[1]) || empty($this->metadata))
				{
					$error = "<error code=\"badArgument\"/>";
				}
				else if ($this->metadata != "oai_dc" && $this->metadata != "oai_ore")
				{
					$error = "<error code=\"cannotDisseminateFormat\"/>";
				}
				else if ($this->metadata == "oai_ore" && !$allow_ore)
				{
					$error = "<error code=\"cannotDisseminateFormat\"/>";
				}
				else if ($args[2] == true)
				{
					$error = "<error code=\"idDoesNotExist\">" . JText::sprintf('COM_OAIPMH_NO_MATCHING_IDENTIFIER', $this->hubname) . "</error>";
				}
			break;

			case 'ListRecords':
			case 'ListIdentifiers':
				if (!empty($from) || !empty($until))
				{
					$error = "<error code=\"badArgument\"/>";
				}
				else if (!empty($args[2]) && empty($args[3]))
				{
					$error = "<error code=\"badResumptionToken\"/>";
				}
				else if ($this->metadata != "oai_dc" && $this->metadata != "oai_ore")
				{
					$error = "<error code=\"cannotDisseminateFormat\"/>";
				}
				else if ($this->metadata == "oai_ore" && !$allow_ore)
				{
					$error = "<error code=\"cannotDisseminateFormat\"/>";
				}
				else if (count($args[1]) == 0 || $args[1] == -1 || empty($args[1]))
				{
					$error = "<error code=\"noRecordsMatch\"/>";
				}
			break;

			case 'ListSets':
				if (!empty($args[1]) && empty($args[2]))
				{
					$error = "<error code=\"badResumptionToken\"/>";
				}
				else if ($this->sets == 0)
				{
					$error = "<error code=\"noSetHierarchy\"/>";
				}
			break;
		}
		return $error;
	}

	/**
	 * build XML for a single record
	 * 
	 * @param   object  $result
	 * @return  string
	 */
	protected function doRecord($result)
	{
		if ($this->metadata == 'oai_dc')
		{
			// DC
			$return  = "<record>\n";
			$return .= $this->doHeader($result);
			$return .= "<metadata><oai_dc:dc 
			 xmlns:oai_dc=\"http://www.openarchives.org/OAI/2.0/oai_dc/\" 
			 xmlns:dc= \"http://purl.org/dc/elements/1.1/\"
			 xmlns:xsi= \"http://www.w3.org/2001/XMLSchema-instance\" 
			 xsi:schemaLocation=\"http://www.openarchives.org/OAI/2.0/oai_dc/ 
			 http://www.openarchives.org/OAI/2.0/oai_dc.xsd\">";

			$dcs = array(
				'title',
				'creator',
				'subject',
				'date',
				'identifier',
				'description',
				'type',
				'publisher',
				'rights',
				'contributor',
				'relation',
				'format',
				'coverage',
				'language',
				'source'
			);

			// loop through DC elements
			for ($i=0; $i<15; $i++)
			{
				if (is_array($result->$dcs[$i]))
				{
					foreach ($result->$dcs[$i] as $sub)
					{
						$sub = html_entity_decode($sub);
						$sub = str_ireplace(array('<','>','&','\'','"'), array('&lt;','&gt;','&amp;','&apos;','&quot;'), $sub);
						$return .= "<dc:$dcs[$i]>" . $sub . "</dc:$dcs[$i]>";
					}
				}
				elseif (!empty($result->$dcs[$i]))
				{
					//check for DOI
					if ($dcs[$i] == "identifier")
					{
						$return .= $this->doDoi($result->identifier);
					}
					else
					{
						$res = html_entity_decode($result->$dcs[$i]);
						$res = str_ireplace(array('<','>','&','\'','"'), array('&lt;','&gt;','&amp;','&apos;','&quot;'), $res);
						$return .= "<dc:$dcs[$i]>" . $res . "</dc:$dcs[$i]>";
					}
				}
			}
			$return .= "</oai_dc:dc></metadata>\n</record>\n";
		}
		else
		{
			// ORE (next version)
		}
		return $return;
	}

	/**
	 * build XML header for a single record
	 * 
	 * @param   object  $result
	 * @return  string
	 */
	protected function doHeader($result)
	{
		$header = '<header>' . "\n";
		if (!empty($result->identifier))
		{
			$header .= $this->doDoi($result->identifier);
		}
		$datestamp = strtotime($result->date);
		$datestamp = date($this->gran, $datestamp);
		if (!empty($datestamp))
		{
			$header .= '<datestamp>' . $datestamp . '</datestamp>' . "\n";
		}
		if (!empty($result->type))
		{
			$header .= '<setSpec>' . $result->type . '</setSpec>' . "\n";
		}
		$header .= '</header>' . "\n";
		return $header;
	}

	/**
	 * build Identifier element 
	 * 
	 * @param   string  $id
	 * @return  string
	 */
	protected function doDoi($id)
	{
		if (preg_match("{^10\.}", $id))
		{
			$url = 'http://dx.doi.org/' . $id;
		}
		else
		{
			$url = rtrim($this->hubname, DS) . DS . ltrim(JRoute::_('index.php?option=com_resources&id=' . $id), DS);
		}
		return '<identifier>' . $url . '</identifier>';
	}

	/**
	 * build XML for sets
	 * 
	 * @param   mixed   $customs
	 * @return  string
	 */
	protected function doSets($customs)
	{
		$setlist = '';

		$total = array();
		if (is_array($customs))
		{
			// multiple groups
			foreach ($customs as $custom)
			{
				if (!empty($custom->sets))
				{
					// check for hard code
					if (stristr($custom->sets,"SELECT") === false)
					{
						$sets = array(
							0 => array(
								$type,
								$type,
								''
							)
						);
						array_push($total, $sets);
					}
					else
					{
						$this->database->setQuery($custom->sets);
						$this->database->query();
						$sets = $this->database->loadRowList();
						foreach ($sets as $set)
						{
							array_push($total, $set);
						}
					}
				}
				else
				{
					if (!empty($custom->type))
					{
						if (stristr($custom->type, "SELECT") === false)
						{
							$sets = array($custom->type, $custom->type,'');
							array_push($total, $sets);
						}
					}
				}
			}
			$msets = 1;
		}
		else
		{
			// check for hard code
			if (stristr($customs->sets, "SELECT") === false)
			{
				$setName = $customs->type;
				$msets = 0;
			}
			else
			{
				$this->database->setQuery($customs->sets);
				$this->database->query();
				$count = $this->database->getNumRows();
				$total = $this->database->loadRowList();
				$msets = 1;
			}
		}
		if ($msets == 0)
		{
			// single set
			$setlist .= "<set><setSpec>$setName</setSpec>";
			$setlist .= "<setName>$setName</setName></set>";
		}
		else
		{
			// multiple sets
			foreach ($total as $set)
			{
				$setlist .= "<set>";
				if (!empty($set[0]))
				{
					$setlist .= "<setSpec>{$set[0]}</setSpec>";
				}
				if (!empty($set[1]))
				{
					$setlist .= "<setName>{$set[1]}</setName>";
				}
				if (!empty($set[2]))
				{
					$setlist .= "<setDescription> <oai_dc:dc 
						xmlns:oai_dc=\"http://www.openarchives.org/OAI/2.0/oai_dc/\" 
						xmlns:dc=\"http://purl.org/dc/elements/1.1/\" 
						xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" 
						xsi:schemaLocation=\"http://www.openarchives.org/OAI/2.0/oai_dc/ 
						http://www.openarchives.org/OAI/2.0/oai_dc.xsd\"><dc:description>{$set[2]}</dc:description>
					</oai_dc:dc></setDescription>";
				}
				$setlist .= "</set>";
			}
		}
		return $setlist;
	}
}
