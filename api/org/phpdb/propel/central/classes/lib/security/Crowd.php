<?php
  /**
   * PHP Client Library for Atlassian Crowd
   *
   * Copyright (C) 2008 Infinite Campus, Inc.
   *
   * Licensed under the Apache License, Version 2.0 (the "License");
   * you may not use this file except in compliance with the License.
   * You may obtain a copy of the License at
   *
   *     http://www.apache.org/licenses/LICENSE-2.0
   *
   * Unless required by applicable law or agreed to in writing, software
   * distributed under the License is distributed on an "AS IS" BASIS,
   * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   * See the License for the specific language governing permissions and
   * limitations under the License.
   */

  /**
   * Crowd SecurityServer SOAP Client
   */
class Crowd {

  private $crowd_client;
  private $crowd_config;
  private $crowd_app_token;

  /**
   * Create an application client using the passed in configuration parameters.
   * 
   * @param [string] crowd configuration map
   * 					service_url: location of Crowd wsdl (e.g., https://neesdev.sdsc.edu/crows/services/CrowdService?wsdl)
   * 					app_credential: application password to pass to crowd
   * 					app_name: application name (e.g., 'neescentral')
   */
  function Crowd($config) {
    $this->crowd_config = $config;

    // Create the Crowd SOAP client
    try {
      $this->crowd_client = new SoapClient($this->crowd_config['service_url']);
    } catch (SoapFault $fault) {
      echo "SOAP Fault: faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring}"; 
      throw new CrowdConnectionException("Unable to connect to Crowd.  Verify the service_url property is defined and Crowd is running.");
    }
  }

  /**
   * Authenticates an application client to the Crowd security server.
   * 
   * Uses the crowd configuration to send an AuthenticateApplication message to the
   * crowd server.
   * 
   * @return string	Application Token
   */
  function authenticateApplication() {
    $param = array('in0' => array('credential' => array('credential' => $this->crowd_config['app_credential']),
				  'name'       => $this->crowd_config['app_name']));
    try {
      $resp = $this->crowd_client->authenticateApplication($param);
    } catch (SoapFault $fault) {
      echo "SOAP Fault: faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring}"; 
    }

    $this->crowd_app_token = $resp->out->token;

    if ( empty($this->crowd_app_token) ) {
      throw new CrowdLoginException("Unable to login to Crowd.  Verify the app_name and app_credential properties are defined and valid.");
    }
    else {
      return $this->crowd_app_token;
    }
  }

  /**
   * Authenticates a principal to the Crowd security server for the application client.
   * 
   * @param string name -- user's id
   * @param string credential -- user's password
   * @param string user_agent -- further identifying information
   * @param string remote_address -- further identiying information
   * @return string principal token
   */
  function authenticatePrincipal($name, $credential, $user_agent, $remote_address) {

    // Build the parameter used to authenticate the principal
    $param = array('in0' => array('name'        => $this->crowd_config['app_name'],
				  'token'       => $this->crowd_app_token),
		   'in1' => array('application' => $this->crowd_config['app_name'],
				  'credential'  => array('credential' => $credential),
				  'name'        => $name,
				  'validationFactors' => array( array('name'  => 'User-Agent',
								      'value' => $user_agent),
								array('name'  => 'remote_address', 
								      'value' => $remote_address) ) ) );

    // Attempt to authenticate the user (principal) via Crowd.
    try {
      $resp = $this->crowd_client->authenticatePrincipal($param);
    } catch (SoapFault $fault) {
      echo "SOAP Fault: faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring}"; 
      return null;
    }

    // Get the principal's token
    $princ_token = $resp->out;

    return $princ_token;
  }

  /**
   * Determines if the principal's current token is still valid in Crowd.
   * 
   * @param string principal_token
   * @param string user_agent
   * @param string remote_address
   * @return string valid_token or '' if invalid
   */
  function isValidPrincipalToken($princ_token, $user_agent, $remote_address) {

    // Determine if the principal is still valid in Crowd
    $param = array('in0' => array('name'        => $this->crowd_config['app_name'],
				  'token'       => $this->crowd_app_token),
		   'in1' => $princ_token,
		   'in2' => array( array('name'  => 'User-Agent',
					 'value' => $user_agent),
				   array('name'  => 'remote_address', 
					 'value' => $remote_address) ) );

    try {
      $resp = $this->crowd_client->isValidPrincipalToken($param);
    } catch (SoapFault $fault) {
      echo "SOAP Fault: faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring}"; 
      return '';
    }

    $valid_token = $resp->out;
        
    return $valid_token;
  }

  /**
   * Invalidates a token for for this princpal for all application clients in Crowd.
   * 
   * @param string principal_token
   * @return  bool true if principal was invalidated, else false
   */
  function invalidatePrincipalToken($princ_token) {

    // Invalidate the principal's token in Crowd
    $param = array('in0' => array('name'  => $this->crowd_config['app_name'],
				  'token' => $this->crowd_app_token),
		   'in1' => $princ_token);

    try {
      $resp = $this->crowd_client->invalidatePrincipalToken($param);
      return true;
    } catch (SoapFault $fault) {
      echo "SOAP Fault: faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring}"; 
    }
    return false;
  }

  /**
   * Finds a principal by token.
   * 
   * @param string principal_token
   * @return 
   */
  function findPrincipalByToken($princ_token) {

    $param = array('in0' => array('name'  => $this->crowd_config['app_name'],
				  'token' => $this->crowd_app_token),
		   'in1' => $princ_token);

    try {
      $resp = $this->crowd_client->findPrincipalByToken($param);
      return $resp->out;
    } catch (SoapFault $fault) {
      echo "SOAP Fault: faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring}"; 
      return null;
    }
  }

  /**
   * Finds all of the groups the specified principal is in.
   * 
   * @param string principal_token
   * @return 
   */
  function findGroupMemberships($princ_name) {

    $param = array('in0' => array('name'  => $this->crowd_config['app_name'],
				  'token' => $this->crowd_app_token),
		   'in1' => $princ_name);

    try {
      $resp = $this->crowd_client->findGroupMemberships($param);
      return $resp->out;
    } catch (SoapFault $fault) {
      echo "SOAP Fault: faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring}"; 
      return null;
    }
  }
  }

/**
 * Exception used to indicate a problem connecting to the Crowd server.
 */
class CrowdConnectionException extends Exception
{

}

/**
 * Exception used to incidate a problem authenticating to the Crowd server.
 */
class CrowdLoginException extends Exception
{

}

?>
