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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Projects Links plugin
 */
class plgProjectsLinks extends JPlugin
{	
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function plgProjectsLinks(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin 		= JPluginHelper::getPlugin( 'projects', 'links' );
		$this->_params 		= new JParameter($this->_plugin->params);
		
		// Load component configs
		$this->_config 		= JComponentHelper::getParams('com_projects');
		$this->_pubconfig 	= JComponentHelper::getParams('com_publications');
		
		// Output collectors
		$this->_referer 	= '';
		$this->_message 	= array();
	}
	
	/**
	 * Event call to determine if this plugin should return data
	 * 
	 * @return     array   Plugin name and title
	 */	
	public function &onProjectAreas( $all = false) 
	{
		// Not showing side panel
		$area = array();
		
		if ($all == true)
		{
			$area = array(
				'name' => 'links',
				'title' => 'Links'
			);
		}
		
		return $area;		
	}
	
	/**
	 * Event call to return count of items
	 * 
	 * @param      object  $project 		Project
	 * @param      integer &$counts 		
	 * @return     array   integer
	 */	
	public function onProjectCount( $project, &$counts ) 
	{
		// Not counting
		return false;
	}
	
	/**
	 * Event call to return data for a specific project
	 * 
	 * @param      object  $project 		Project
	 * @param      string  $option 			Component name
	 * @param      integer $authorized 		Authorization
	 * @param      integer $uid 			User ID
	 * @param      integer $msg 			Message
	 * @param      integer $error 			Error
	 * @param      string  $action			Plugin task
	 * @param      string  $areas  			Plugins to return data
	 * @return     array   Return array of html
	 */	
	public function onProject ( $project, $option, $authorized, 
		$uid, $msg = '', $error = '', $action = '', $areas = NULL)
	{
				
		// What's the task?						
		$this->_task = $action ? $action : JRequest::getVar('action');
		
		// Get this area details
		$this->_area = $this->onProjectAreas(true);
		
		// Load language file
		$this->loadLanguage();
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) 
		{
			if (empty($this->_area) || !in_array($this->_area['name'], $areas)) 
			{
				return;
			}
		}
		
		$tasks = array('browser', 'parseurl', 'parsedoi', 'addcitation', 'deletecitation');
						
		// Publishing?
		if ( in_array($this->_task, $tasks) )
		{
			$this->_project = $project;
			
			// Set vars
			$this->_database 	= JFactory::getDBO();
			$this->_option 		= $option;
			$this->_authorized 	= $authorized;
			$this->_uid 		= $uid;

			if (!$this->_uid) 
			{
				$juser = JFactory::getUser();
				$this->_uid = $juser->get('id');
			}
			
			// Actions				
			switch ($this->_task) 
			{
				case 'browser':
				default: 			
					$html = $this->browser(); 		
					break;
					
				case 'parseurl':
					$html = $this->parseUrl();
					break;
					
				case 'parsedoi':
					$html = $this->parseDoi();
					break;	
					
				case 'addcitation':
					$html = $this->addCitation();
					break;
					
				case 'deletecitation':
					$html = $this->deleteCitation();
					break;				
			}
			
			$arr = array(
				'html' 		=> $html,
				'metadata' 	=> '',
				'msg' 		=> $this->_message,
				'referer' 	=> $this->_referer
			);

			return $arr;			
		}		
		
		// Nothing to return
		return false;
	}
	
	/**
	 * Delete a citation from a publication
	 * 
	 * @return     string
	 */
	public function deleteCitation() 
	{
		// Incoming
		$cid 		= JRequest::getInt('cid', 0);
		$version 	= JRequest::getVar('version', 'dev');
		$pid 		= JRequest::getInt('pid', 0);
		
		if (!$cid || !$pid)
		{
			$this->setError( JText::_('PLG_PROJECTS_LINKS_ERROR_CITATION_DELETE') );
		}
		
		// Make sure this publication belongs to this project
		$objP = new Publication( $this->_database );
		if (!$objP->load($pid) || $objP->project_id != $this->_project->id)
		{
			$this->setError( JText::_('PLG_PROJECTS_LINKS_ERROR_CITATION_DELETE') );
		}
				
		// Remove citation
		if (!$this->getError())
		{
			include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' 
				. DS . 'com_citations' . DS . 'tables' . DS . 'citation.php' );
			include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' 
				. DS . 'com_citations' . DS . 'tables' . DS . 'association.php' );
			include_once( JPATH_ROOT . DS . 'components' . DS . 'com_citations' 
				. DS . 'helpers' . DS . 'format.php' );	
				
			$c 		= new CitationsCitation( $this->_database );
			$assoc 	= new CitationsAssociation($this->_database);
			
			// Fetch all associations
			$aPubs = $assoc->getRecords(array('cid' => $cid));
			
			// Remove citation if only one association
			if (count($aPubs) == 1)
			{
				// Delete the citation
				$c->delete($cid);
			}
			
			// Remove association
			foreach ($aPubs as $aPub)
			{
				if ($aPub->oid == $pid && $aPub->tbl = 'publication')
				{
					$assoc->delete($aPub->id);	
				}
			}
			
			$this->_msg = JText::_('PLG_PROJECTS_LINKS_CITATION_DELETED');
		}
		
		// Pass success or error message
		if ($this->getError()) 
		{
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
		}
		elseif (isset($this->_msg) && $this->_msg) 
		{
			$this->_message = array('message' => $this->_msg, 'type' => 'success');
		}
		
		// Build pub url
		$route = $this->_project->provisioned 
			? 'index.php?option=com_publications' . a . 'task=submit'
			: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias 
				. a . 'active=publications';
		$url = JRoute::_($route . a . 'pid=' . $pid).'/?version=' . $version . '&section=citations';

		$this->_referer = $url;
		return;
	}
	
	/**
	 * Attach a citation to a publication
	 * 
	 * @return     string
	 */
	public function addCitation() 
	{
		// Incoming
		$url 		= JRequest::getVar('citation-doi', '');
		$vid 		= JRequest::getInt('versionid', 0);
		$version 	= JRequest::getVar('version', 'dev');
		$pid 		= JRequest::getInt('pid', 0);
		
		if (!$url || !$pid)
		{
			$this->setError( JText::_('PLG_PROJECTS_LINKS_NO_DOI') );
		}
		
		$parts 		= explode("doi:", $url);
		$doi   		= count($parts) > 1 ? $parts[1] : $url;
		$format		= $this->_pubconfig->get('citation_format', 'apa');
		
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' 
			. DS . 'com_citations' . DS . 'tables' . DS . 'citation.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' 
			. DS . 'com_citations' . DS . 'tables' . DS . 'association.php' );
		include_once( JPATH_ROOT . DS . 'components' . DS . 'com_citations' 
			. DS . 'helpers' . DS . 'format.php' );
				
		$c 		= new CitationsCitation( $this->_database );
		
		if ($c->loadPubCitation($doi, $pid)) 
		{
			$this->setError( JText::_('PLG_PROJECTS_LINKS_CITATION_ALREADY_ATTACHED') );
		}
		else
		{	
			// Get DOI preview
			$output = $this->parseUrl($doi, true, true, $format);
			$output = json_decode($output);
			
			if (isset($output->error) && $output->error)
			{
				$this->setError( $output->error );
			}
			elseif (isset($output->preview) && $output->preview)
			{								
				// Load citation record with the same DOI if present
				if (!$c->loadByDoi($doi))
				{
					$c->created 	= JFactory::getDate()->toSql();
					$c->title   	= $doi;
					$c->uid			= $this->_uid;
					$c->affiliated 	= 1;
				}
				$c->formatted 		= $output->preview;
				$c->format			= $format;
				$c->doi				= $doi;
				
				// Try getting more metadata
				$data = self::getDoiMetadata($doi, false, $url = '');
				
				// Save available data
				if ($data)
				{
					foreach ($c as $key => $value)
					{
						$column = strtolower($key);
						if (isset($data->$column))
						{
							$c->$column = $data->$column;
						}
					}
					
					// Some extra mapping hacks
					$c->pages = $data->page;
				}
				
				if (!$c->store())
				{
					$this->setError( JText::_('PLG_PROJECTS_LINKS_CITATION_ERROR_SAVE') );
				}
				
				// Create association
				if ($c->id)
				{
					$assoc 		 = new CitationsAssociation($this->_database);
					$assoc->oid  = $pid;
					$assoc->tbl  = 'publication';
					$assoc->type = 'owner';
					$assoc->cid  = $c->id;
					
					// Store new content
					if (!$assoc->store()) 
					{
						JError::raiseError(500, $assoc->getError());
						return;
					}							
				}
								
				$this->_msg = JText::_('PLG_PROJECTS_LINKS_CITATION_SAVED');								
			}
			else
			{
				$this->setError( JText::_('PLG_PROJECTS_LINKS_CITATION_COULD_NOT_LOAD') );
			}			
		}	
				
		// Pass success or error message
		if ($this->getError()) 
		{
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
		}
		elseif (isset($this->_msg) && $this->_msg) 
		{
			$this->_message = array('message' => $this->_msg, 'type' => 'success');
		}
		
		// Build pub url
		$route = $this->_project->provisioned 
			? 'index.php?option=com_publications' . a . 'task=submit'
			: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias 
				. a . 'active=publications';
		$url = JRoute::_($route . a . 'pid=' . $pid).'/?version=' . $version . '&section=citations';

		$this->_referer = $url;
		return;
	}
		
	/**
	 * Browser for within publications
	 * 
	 * @return     string
	 */
	public function browser() 
	{		
		// Incoming
		$ajax 		= JRequest::getInt('ajax', 0);
		$primary 	= JRequest::getInt('primary', 1);
		$versionid  = JRequest::getInt('versionid', 0);
										
		if (!$ajax) 
		{
			return false;
		}
				
		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'=>'projects',
				'element'=>'links',
				'name'=>'browser'
			)
		);
				
		// Get current attachments
		$pContent = new PublicationAttachment( $this->_database );
		$role 	= $primary ? '1' : '0';
		$other 	= $primary ? '0' : '1';
		
		$view->attachments = $pContent->getAttachments($versionid, 
			$filters = array('role' => $role, 'type' => 'link'));
		
		// Output HTML
		$view->params 		= new JParameter( $this->_project->params );
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->config 		= $this->_config;	
		$view->primary		= $primary;
		$view->versionid	= $versionid;
		
		// Get messages	and errors	
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		
		return  $view->loadTemplate();
		
	}
	
	/**
	 * Parse DOI
	 * 
	 * @return     string
	 */
	public function parseDoi() 
	{		
		// Incoming
		$url = JRequest::getVar('url', '');
		
		// Is this a DOI?
		$parts 		= explode("doi:", $url);
		$doi   		= count($parts) > 1 ? $url : 'doi:' . $url;
		
		// Get format from config
		$format 	= $this->_pubconfig->get('citation_format', 'apa');
				
		return $this->parseUrl($doi, true, true, $format);
	}
	
	/**
	 * Parse input
	 * 
	 * @return     string
	 */
	public function parseUrl($url = '', $citation = true, $incPreview = true, $format = 'apa') 
	{		
		// Incoming
		$url 			= $url ? $url : JRequest::getVar('url', $url);
		$output 		= array('rtype' => 'url');
		
		if (!$url)
		{
			$output['error'] = JText::_('PLG_PROJECTS_LINKS_EMPTY_URL');
			return json_encode($output);
		}

		// Is this a DOI?
		$parts 		= explode("doi:", $url);
		$doi   		= count($parts) > 1 ? $parts[1] : NULL;
		
		// Treat url starting with numbers as DOI
		if (preg_match('#[0-9]#', substr($url, 0, 2)))
		{
			$doi = $url;
		}
		
		$data = NULL;
		
		// Pull DOI metadata
		if ($doi)
		{
			$output['rtype'] = 'doi';
			$data = self::getDoiMetadata($doi, $citation, $url, $format);
			
			if ($this->getError())
			{
				$output['error'] = $this->getError();
				return json_encode($output);
			}			
		}
		
		// DOI metadata
		if ($data)
		{
			$output['url'] 	= $url;
			
			if ($incPreview)
			{
				$output['preview'] 	= $data;
			}
			
			if ($citation == false && is_object($data))
			{
				$output['data'] = array();
				foreach ($data as $key => $value)
				{
					$output['data'][$key] = $value;
				}
			}
		}
		else
		{
			$ch 	 = curl_init( $url );
			$options = array(
			    CURLOPT_RETURNTRANSFER => true,     // return web page
			    CURLOPT_HEADER         => false,    // don't return headers
			    CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			    CURLOPT_ENCODING       => "",       // handle all encodings
			    CURLOPT_USERAGENT      => "spider", // who am i
			    CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			    CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
			    CURLOPT_TIMEOUT        => 120,      // timeout on response
			    CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
			);

			curl_setopt_array( $ch, $options );
			curl_setopt($ch, CURLOPT_FAILONERROR, true);

			$content 	= curl_exec( $ch );
		    $finalUrl 	= curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		    $finalUrl 	= str_replace("HTTP", "http", $finalUrl);
			$finalUrl 	= str_replace("HTTPS", "https", $finalUrl);
		    curl_close( $ch );

			if (!$finalUrl || !$content)
			{
				$output['error'] = JText::_('PLG_PROJECTS_LINKS_INVALID_URL');
				return json_encode($output);
			}
			else
			{
				$output['url'] 	= $finalUrl;
			}

			if ($content)
			{
				require_once( JPATH_ROOT . DS . 'plugins' . DS . 'projects' . DS . 'links' 
							. DS . 'helpers' . DS . 'simple_html_dom.php');

				$out = '';

				// Create DOM from URL or file
				$html 	= file_get_html($finalUrl);

				$title 	= $html->find('title',0)->innertext; //Title Of Page

				$out .= $title ? stripslashes('<h5>' . addslashes($title) . '</h5>') 
						: '<h5>' . ProjectsHtml::shortenText($finalUrl, 100) . '</h5>';

				//Get all images found on this page
			   	$jpgs = $html->find('img[src$=jpg],img[src$=png]');
				$images = array();
				if ($jpgs)
				{
					foreach ($jpgs as $jpg) 
					{
				        $src 			= $jpg->getAttribute('src');
						$width 			= $jpg->getAttribute('width');
						
						$pathCounter 	= substr_count($src, "../");
						$src 			= self::getImgSrc($src);
						
						// Must be larger than 25px
						if ($width && $width <= 25)
						{
							continue;
						}
						
						if (!$src)
						{
							continue;
						}

						if (!preg_match("/https?\:\/\//i", $src)) 
						{
							$src = self::getImageUrl($pathCounter, self::getLink($src, $finalUrl));
						}
												
						// Can only show images served via https
						//$src = str_replace('http://', 'https://', $src);
						if (preg_match("/https/i", $src)) 
						{
							$images[] = $src;	
						}
				   	}		   
				}

				if ($images)
				{
					$out .= '<div id="link-image"><img src="' . $images[0] . '" alt="" /></div>';
				}

				// Set description if desc meta tag found else grab a little plain text of the page
			    if ($html->find('meta[name="description"]',0)) 
				{
			        $description = $html->find('meta[name="description"]',0)->content;
			    } 
				else 
				{
			        $description = $html->find('body',0)->plaintext;
			    }

				$out .= $description ? stripslashes('<p>' . ProjectsHtml::shortenText(addslashes($description), 300) . '</p>')
						: '<p>' . ProjectsHtml::shortenText($finalUrl, 300) . '</p>';
				
				if ($images)
				{
					$out .= '<span class="clear"></span>';
				}

				// Preview of the url
				if ($incPreview)
				{
					$output['preview'] 		= $out;
				}
				$output['description']	= $description;
				$output['title']		= $title;
			}
			else
			{
				$output['error'] 	= JText::_('PLG_PROJECTS_LINKS_FAILED_TO_LOAD_URL');
				return json_encode($output);
			}
		}
								
		return json_encode($output);
	}
	
	/**
	 * Get DOI Metadata
	 * 
	 * @return     string
	 */
	public function getDoiMetadata($doi, $citation = false, &$url, $rawData = false, $format = 'apa') 
	{
		// Include metadata model
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications' 
			. DS . 'models' . DS . 'metadata.php');
		
		$format = in_array($format, array('apa', 'ieee')) ? $format : 'apa';
		
		$ch 	= curl_init();
		$url 	= 'http://dx.doi.org/doi:' . $doi;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
		$data   = new PublicationsMetadata();

		if ($citation == true)
		{
			curl_setopt ($ch, CURLOPT_HTTPHEADER,
		    	array (
				"Accept: text/x-bibliography; style=" . $format
		    ));
		}
		else
		{
			curl_setopt ($ch, CURLOPT_HTTPHEADER,
		    	array (
				"Accept: application/x-datacite+xml;q=0.9, application/citeproc+json;q=1.0"
			));
		}

		$metadata 		= curl_exec($ch);
		$status 		= curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$contenttype 	= curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		curl_close($ch);

		// Error
		if ( $status != 200 ) 
		{
			$this->setError(JText::_('PLG_PROJECTS_LINKS_DOI_NOT_FOUND'));
			return;
		}

		// Error - redirected instead of printing metadata
		if ($contenttype == "text/html; charset=utf-8")
		{
			return;
		}
		
		// Error - redirected instead of printing metadata
		if ($citation == true)
		{
			return $metadata;
		}

		// JSON
		if ( $contenttype == "application/citeproc+json" )
		{
		   	if ($rawData == true)
			{
				return $metadata;
			}
					
			// crossref DOI
		   	$metadata = json_decode($metadata, true);
			
			// Parse data
			$data = self::parseDoiData($data, $metadata);
		}
		else
		{
			// XML
			if ($rawData == true)
			{
				return $metadata;
			}
		}
		
		return $data;
		
	}
	
	/**
	 * Parse DOI metadata
	 * 
	 * @return     string
	 */
	public function parseDoiData( $data, $metadata) 
	{
		// Pull applicable data
		foreach ($data as $key => $value)
		{
			$altKey = strtoupper($key);
			if (isset($metadata[$key]) || isset($metadata[$altKey]))
			{
				$which = isset($metadata[$key]) ? $key : $altKey;
				$data->$key = $metadata[$which];
			}
		
			// Parse authors
			if ($key == 'author' && isset($metadata['author']))
			{
				$authors = $metadata['author'];
				$authString = '';
				if (is_array($authors) && !empty($authors))
				{
					foreach ($authors as $author)
					{							
						if (isset($author['family']) && isset($author['given']))
						{
							$authString .=  $author['family'] . ', ' . $author['given'] . ', ';
						}
						elseif (isset($author['literal']))
						{
							$authString .=  $author['literal'] . ', ';
						}
					}
					$authString = substr($authString,0,strlen($authString) - 2);	
				}
				$data->author = $authString;
			}
		
			// Parse date
			if ($key == 'issued')
			{
				if (isset($metadata['issued']) && isset($metadata['issued']['date-parts']))
				{
					$data->year = $metadata['issued']['date-parts'][0][0];
				}
			}
		
			// More custom parsing
			if (isset($metadata['container-title']))
			{
				$data->journal = $metadata['container-title'];
			}
		}
		
		return $data;
	}
	
	/**
	 * Parse image source
	 * 
	 * @return     string
	 */
	public function getImgSrc($imgSrc) 
	{
		$imgSrc = str_replace("../", "", $imgSrc);
		$imgSrc = str_replace("./", "", $imgSrc);
		$imgSrc = str_replace(" ", "%20", $imgSrc);
		
		return $imgSrc;
	}
	
	/**
	 * Get image url
	 * 
	 * @return     string
	 */
	public function getImageUrl($pathCounter, $url) 
	{
		$src = "";
		if ($pathCounter > 0) 
		{
			$urlBreaker = explode('/', $url);
			for ($j = 0; $j < $pathCounter + 1; $j++) 
			{
				if (isset($urlBreaker[$j]))
				{
					$src .= $urlBreaker[$j] . '/';
				}
				
			}
		} 
		else 
		{
			$src = $url;
		}
		return $src;
	}
	
	/**
	 * Get link
	 * 
	 * @return     string
	 */
	public function getLink($imgSrc, $referer) 
	{
		if (strpos($imgSrc, "//") === 0)
		{
			$imgSrc = "https:" . $imgSrc;
		}
		elseif (strpos($imgSrc, "/") === 0)
		{
			$imgSrc = "https://" . $this->getPage($referer) . $imgSrc;
		}
		else
		{
			$imgSrc = "https://" . $this->getPage($referer) . '/' . $imgSrc;
		}
		return $imgSrc;
	}
	
	/**
	 * Get page
	 * 
	 * @return     string
	 */
	public function getPage($url) 
	{
		$cannonical = "";

		if (substr_count($url, 'http://') > 1 || substr_count($url, 'https://') > 1 
			|| (strpos($url, 'http://') !== false && strpos($url, 'https://') !== false))
		{
			return $url;
		}

		if (strpos($url, "http://") !== false)
		{
			$url = substr($url, 7);
		}			
		elseif (strpos($url, "https://") !== false)
		{
			$url = substr($url, 8);
		}

		for ($i = 0; $i < strlen($url); $i++) 
		{
			if ($url[$i] != "/")
			{
				$cannonical .= $url[$i];
			}				
			else
			{
				break;
			}
		}

		return $cannonical;
	}
}
