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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'ResourcesDoi'
 * 
 * Long description (if any) ...
 */
class ResourcesDoi extends JTable
{

	/**
	 * Description for 'local_revision'
	 * 
	 * @var unknown
	 */
	var $local_revision = NULL;  // @var int(11) Primary key


	/**
	 * Description for 'doi_label'
	 * 
	 * @var unknown
	 */
	var $doi_label      = NULL;  // @var int(11)


	/**
	 * Description for 'rid'
	 * 
	 * @var unknown
	 */
	var $rid            = NULL;  // @var int(11)


	/**
	 * Description for 'alias'
	 * 
	 * @var unknown
	 */
	var $alias          = NULL;  // @var varchar(30)

	//-----------


	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__doi_mapping', 'rid', $db );
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		if (trim( $this->rid ) == '') {
			$this->setError( JText::_('Your entry must have a resource ID.') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'getDoi'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @param      string $revision Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getDoi( $id=NULL, $revision=NULL )
	{
		if ($id == NULL) {
			$id = $this->rid;
		}
		if ($id == NULL) {
			return false;
		}
		if ($revision == NULL) {
			$revision = $this->local_revision;
		}
		if ($revision == NULL) {
			return false;
		}

		$query  = "SELECT d.doi_label as doi ";
		$query .= "FROM $this->_tbl as d ";
		$query .= "WHERE d.rid='".$id."' AND d.local_revision='".$revision."' LIMIT 1";

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getLatestDoi'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @param      unknown $revision Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getLatestDoi( $id=NULL, $revision=NULL )
	{
		if ($id == NULL) {
			$id = $this->rid;
		}
		if ($id == NULL) {
			return false;
		}

		$query  = "SELECT d.doi_label as doi ";
		$query .= "FROM $this->_tbl as d ";
		$query .= "WHERE d.rid='".$id."' ORDER BY d.doi_label DESC LIMIT 1";

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'saveDOI'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $revision Parameter description (if any) ...
	 * @param      mixed $newlabel Parameter description (if any) ...
	 * @param      string $rid Parameter description (if any) ...
	 * @param      string $alias Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function saveDOI($revision=0, $newlabel=1, $rid, $alias='')
	{
		if ($rid == NULL) {
			return false;
		}

		$query = "INSERT INTO $this->_tbl (local_revision, doi_label, rid, alias) VALUES ('".$revision."','".$newlabel."','".$rid."','".$alias."')";
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Short description for 'createDOIHandle'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $url Parameter description (if any) ...
	 * @param      unknown $handle Parameter description (if any) ...
	 * @param      unknown $doiservice Parameter description (if any) ...
	 * @param      string &$err Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function createDOIHandle($url, $handle, $doiservice, &$err='')
	{
		jimport('nusoap.lib.nusoap');

		$client = new nusoap_client($doiservice, 'wsdl', '', '', '', '');
		$err = $client->getError();
		if ($err) {
			$this->_error = 'Constructor error: '. $err;
			return false;
		}

		$param = array('in0'=>$url, 'in1'=>$handle);

		$result = $client->call('create', $param, '', '', false, true);

		// Check for a fault
		if ($client->fault) {
			//print_r ($result);
			$err = 'Fault: '.$result['faultstring'];
			return false;
		} else {
			// Check for errors
			$err = $client->getError();
			if ($err) {
				// Return the error
				//print_r($err);
				//$this->setError( 'Error: '. $err);		
				return false;
			} else {
				return $result;
			}
		}
	}

	/**
	 * Short description for 'deleteDOIHandle'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $url Parameter description (if any) ...
	 * @param      unknown $handle Parameter description (if any) ...
	 * @param      unknown $doiservice Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function deleteDOIHandle($url, $handle, $doiservice)
	{
		jimport('nusoap.lib.nusoap');

		$client = new nusoap_client($doiservice, 'wsdl', '', '', '', '');
		$err = $client->getError();
		if ($err) {
			$this->_error = 'Constructor error: '. $err;
			return false;
		}

		$param = array('in0'=>$url, 'in1'=>$handle);

		$result = $client->call('delete', $param, '', '', false, true);

		// Check for a fault
		if ($client->fault) {
			print_r ($result);
			$this->setError( 'Fault: '.$result['faultstring']);
			return false;
		} else {
			// Check for errors
			$err = $client->getError();
			if ($err) {
				// Return the error
				print_r($err);
				$this->setError( 'Error: '. $err);
				return false;
			} else {
				return $result;
			}
		}
	}

	/*
	
	//-----------
	
	public function createDOIHandle($url, $handle, $proxyclient) 
	{
		// Retrieve some plugin parameters
		$proxy = array();
		$proxy['host']     = '';
		$proxy['port']     = '';
		$proxy['username'] = '';
		$proxy['password'] = '';
		
		if ($proxyclient===NULL) {
			$this->setError( JText::_('No web service URL found') );
			return false;
		}
		if($url===NULL or $handle===NULL) {
			$this->setError( JText::_('No handle or URL. Cannot create an empty handle.') );
			return false;
		}
			
		// Try to connect to the web service
		try {
			$client = new SoapClient($proxyclient, $proxy);
		} catch (Exception $e) {
		
			$this->setError( $e->getMessage() );
			return false;
		}
			
		// Set the array of parameters we'll be passing to the web service
		$param = array('in0'=>$url,'in1'=>$handle);

		// Try to call the web service
		try {
			$result = $client->__soapCall('create', $param, '', '', false, true);
		} catch (SoapFault $e) {
			$this->setError( JText::_('WEBSERVICE_FAULT').' '.$e );
			return false;
		}
		
		// Return the result
		return $result;
	
	}
	*/
}

