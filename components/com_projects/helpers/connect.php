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

/**
 * Projects Connect helper class
 */
class ProjectsConnectHelper extends JObject {
		
	/**
	 * Project
	 * 
	 * @var object
	 */
	private $_project 		= NULL;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	private $_db 			= NULL;
	
	/**
	 * User ID
	 * 
	 * @var integer
	 */
	private $_uid 			= NULL;
	
	/**
	 * Connection configuration
	 * 
	 * @var array
	 */
	private $_connect 		= array();
	
	/**
	 * Service configuration
	 * 
	 * @var array
	 */
	private $_service 		= array();
	
	/**
	 * Available services
	 * 
	 * @var array
	 */
	private $_services 		= array('google', 'dropbox');
	
	/**
	 * Service client
	 * 
	 * @var array
	 */
	public $_client 		= array();
	
	/**
	 * Service API
	 * 
	 * @var array
	 */
	public $_api 			= array();
	
	/**
	 * URL to revoke Google token
	 */
	const GOOGLE_OAUTH2_REVOKE_URI 	= 'https://accounts.google.com/o/oauth2/revoke';
	
	/**
	 * URL to refresh Google token
	 */
  	const GOOGLE_OAUTH2_TOKEN_URI 	= 'https://accounts.google.com/o/oauth2/token';

	/**
	 * Google auth URL
	 */
  	const GOOGLE_OAUTH2_AUTH_URL 	= 'https://accounts.google.com/o/oauth2/auth';

	/**
	 * Constructor
	 * 
	 * @param      object 	&$db 		JDatabase
	 * @param      object 	$project 	Project
	 * @param      integer 	$userid 	User ID
	 * @param      string 	$zone 		Default time zone
	 * @return     void
	 */	
	public function __construct( &$db, $project = NULL, $userid = 0, $zone = 'UTC' )
	{
		$this->_db 		=& $db;
		$this->_project = $project;
		$this->_uid 	= $userid;
		
		if (!$userid)
		{
			$juser =& JFactory::getUser();
			$this->_uid = $juser->get('id');
		}
		
		// Set configurations
		$this->setConfigs();
		
		// Load library for active service
		foreach ($this->_services as $servicename)
		{
			if ($this->_connect[$servicename]['on'])
			{
				$this->loadLibrary($servicename);
			}
		}
		
		// Set local timezone after loading library (so that local and remote sync)
		date_default_timezone_set($zone);	
	}
	
	/**
	 * Get variable
	 * 
	 * @param      string	$var	Variable name
	 *
	 * @return     false or array with configs
	 */
	public function getVar ($var = '') 
	{
		return $this->$var;
	}
	
	/**
	 * Set user
	 * 
	 * @param      integer	$uid				User ID
	 *
	 * @return     void
	 */
	public function setUser ($uid = 0) 
	{
		$this->_uid = $uid;
	}
	
	/**
	 * Is any service active?
	 * 
	 *
	 * @return     false or array with configs
	 */
	public function isActive() 
	{
		$active = false;
		foreach ($this->_services as $servicename)
		{
			$configs = $this->getConfigs($servicename, true);
			
			if ($configs && $configs['active'])
			{
				$active = true;
			}
		}
		
		return $active;
	}
	
	/**
	 * Get active services
	 * 
	 *
	 * @return     array
	 */
	public function getActive() 
	{
		$active = array();
		foreach ($this->_services as $servicename)
		{
			$configs = $this->getConfigs($servicename, true);
			
			if ($configs && $configs['active'])
			{
				$active[] = $servicename;
			}
		}
		
		return $active;
	}
	
	/**
	 * Get service config param
	 * 
	 * @param      string	$service	Service name (google or dropbox)
	 * @param      string	$param		Param name
	 *
	 * @return     string or NULL
	 */
	public function getConfigParam ($service = 'google', $param = '') 
	{
		if (!$param || !isset($this->_connect[$service][$param]))
		{
			return NULL;
		}
		return $this->_connect[$service][$param];
	}	
	
	/**
	 * Get connections
	 * 
	 * @param      integer	$uid	User ID
	 *
	 * @return     array
	 */
	public function getConnections($uid = 0) 
	{
		$connections = array();
		$uid = $uid ? $uid : $this->_uid;
		
		foreach ($this->_services as $servicename)
		{
			$connections[$servicename] = $this->getStoredParam($servicename . '_token', $uid);	
		}
		
		return $connections;
	}
			
	/**
	 * Set configuration to connect with outside services
	 * 
	 * @return     void
	 */
	public function setConfigs() 
	{
		// Make up redirection URL for Google service
		$juri =& JURI::getInstance();
		$redirectUri = trim($juri->base(), DS) . DS . 'projects' . DS . 'auth';
		
		// Scope for Google service
		$scope = array (
			'https://www.googleapis.com/auth/drive',
			'https://www.googleapis.com/auth/userinfo.profile',
			'https://www.googleapis.com/auth/userinfo.email'
		);
		
		// We will use files plugin params
		$plugin 			= JPluginHelper::getPlugin( 'projects', 'files' );
		$filesParams 		= new JParameter($plugin->params);
				
		// Get project params
		$pparams = new JParameter( $this->_project->params );
				
		$connect = array(
			'google' => array(
				'servicename' 	=> 'Google Drive',
				'on' 			=> $filesParams->get('enable_google', 0),
				'clientId' 		=> $filesParams->get('google_clientId', 0),
				'clientSecret' 	=> $filesParams->get('google_clientSecret', 0),
				'appKey' 		=> $filesParams->get('google_appKey', 0),
				'redirectUri' 	=> $redirectUri,
				'scope'			=> $scope,
				'approvalPrompt'=> 'force',
				'accessType'	=> 'offline',
				'local_dir'		=> $pparams->get('google_local_dir', '#home'),
				'remote_dir'	=> $pparams->get('google_dir', 'Project :: ' . $this->_project->alias),
				'remote_dir_id'	=> $pparams->get('google_dir_id', 1),
				'active'		=> $pparams->get('google_token', 0)
			),
			'dropbox' => array(
				'servicename' 	=> 'Dropbox',
				'on' 			=> $filesParams->get('enable_dropbox', 0),
				'key' 			=> $filesParams->get('dropbox_key', 0),
				'secret' 		=> $filesParams->get('dropbox_secret', 0),
				'local_dir'		=> $pparams->get('dropbox_local_dir', '#home'),
				'remote_dir'	=> $pparams->get('dropbox_dir', 'project_' . $this->_project->alias),
				'remote_dir_id'	=> $pparams->get('dropbox_dir_id', 1),
				'active'		=> $pparams->get('dropbox_service', 0)
			)
		);
				
		$this->_connect		=  $connect;				
	}
	
	/**
	 * Get configuration to connect with outside services
	 * 
	 * @param      string	$service	Service name (google or dropbox)
	 * @param      boolean	$active		Only get active service
	 *
	 * @return     false or array with configs
	 */
	public function getConfigs ($service = 'google', $active = true) 
	{
		if (!isset($this->_connect[$service]))
		{
			$this->setError('Missing or invalid service name');
			return false;
		}
		
		foreach ($this->_connect[$service] as $key => $value)
		{
			if (!$value && $key != 'active')
			{
				// Return only active service?
				if ($active == true)
				{
					return false;
				}
				else
				{
					$this->_connect[$service]['on'] = 0;
				}
			}
		}
		
		return $this->_connect[$service];
	}
	
	/**
	 * Load library for service
	 * 
	 * @param      string	$service	Service name (google or dropbox)
	 *
	 * @return     void
	 */
	public function loadLibrary ($service = 'google') 
	{
		if ($service == 'google')
		{
			$srcDir = 'src';
			require_once(JPATH_SITE . DS . 'libraries' . DS . 'google-api-php-client' 
				. DS . $srcDir . DS . 'Google_Client.php');
			require_once(JPATH_SITE . DS . 'libraries' . DS . 'google-api-php-client' 
				. DS . $srcDir . DS . 'contrib' . DS . 'Google_DriveService.php');
			require_once(JPATH_SITE . DS . 'libraries' . DS . 'google-api-php-client' 
				. DS . $srcDir . DS . 'contrib' . DS . 'Google_Oauth2Service.php');
		
			/*require_once(JPATH_SITE . DS . 'libraries' . DS . 'google-api-php-client' 
				. DS . $srcDir . DS . 'io' . DS . 'Google_HttpRequest.php');*/
		}
	}
	
	/**
	 * Establish connection
	 * 
	 * @param      string	$service	Service name (google or dropbox)
	 * @param      boolean	$reauth		Re-authenticate user?
	 * @param      string	$return		URL to return to after authorization
	 *
	 * @return     false or array with configs
	 */
	public function makeConnection ($service = 'google', $reauth = false, $return = '') 
	{
		if (!in_array($service, $this->_services))
		{
			$this->setError('Missing or invalid service name');
			return false;
		}		
		
		$config = $this->_connect[$service];
		$jsession =& JFactory::getSession();
		
		// Make sure we have service
		if (!isset($config) || !$config)
		{
			$this->setError('Missing service configuration or connection service turned off');
			return false;
		}
		
		// Connect to google (OAUTH2)
		if ($service == 'google')
		{
			$code  = JRequest::getVar( 'code', '', 'get' );
			$refresh_token = '';
			
			// We got here from the redirect from a successful authorization grant, fetch the access token
			if ($code) 
			{
				$postvals = array(
					'grant_type' => 'authorization_code',
			        'client_id' => $config['clientId'],
					'client_secret' => $config['clientSecret'],
					'code' => $code,
					'redirect_uri' => $config['redirectUri']
				);
				
				// get JSON access token object (with refresh_token parameter)
				$token = json_decode($this->runCurl(self::GOOGLE_OAUTH2_TOKEN_URI, 'POST', $postvals));
	
				// Store refresh token in the database
				if (isset($token->refresh_token))
				{
					$refresh_token = $token->refresh_token;
					$this->storeParam($service . '_token', $token->refresh_token);
					$this->storeParam($service . '_token_created', time());
					
					// Store master token for project
					if ($this->_uid == $this->_project->created_by_user)
					{
						$obj = new Project( $this->_db );
						$obj->saveParam($this->_project->id, $service . '_token', $token->refresh_token);
					}
				}								
		    }
		
			// We got a token stored in session
			$access_token  = json_decode($jsession->get('projects.' . $service . '.token'));
			$refresh_token = $refresh_token ? $refresh_token : $this->getStoredParam($service . '_token');
			
			if ($refresh_token && $access_token && !$reauth) 
			{
				// Make sure it's not expired, otherwise need to refresh
				$expired = ($access_token->created + ($access_token->expires_in - 30)) < time();
				if (!$expired)
				{
					return true;
				}
			}
		
			// Refresh token
			if ($refresh_token && !$reauth)
			{
				$token = $this->refreshToken($service, $refresh_token);
				
				// Store access token in session
				if (isset($token->access_token))
				{
					$token->created = time();
					$jsession->set('projects.' . $service . '.token', json_encode($token));	
					
					return true;	
				}
			}

			// First-time or forced authentication
			if (!$refresh_token || $reauth)
			{
				$authUrl = $this->createAuthUrl('google', $return);
			    header('Location: ' . $authUrl);
				return;
			}
		}
		
		// Connect to dropbox (OAUTH1)
		if ($service == 'dropbox')
		{
			// TBD
			$this->setError('Dropbox service is not yet available');
			return false;
		}
	}
	
	/**
	 * Start service client
	 * 
	 * @param      string	$service	Service name (google or dropbox)
	 * @param      integer	$uid		User ID
	 *
	 * @return     object
	 */
	public function startClient ($service = 'google', $uid = 0) 
	{
		$config = $this->_connect[$service];
		$uid = $uid ? $uid : $this->_uid;
		$access_token = 0;
		
		// Get access token
		if ($uid == $this->_uid)
		{
			// Do we have a client started already?
			if (isset($this->_client[$service]) && $this->_client[$service])
			{
				return $this->_client[$service];
			}
			
			// Acting user - get token from session
			$jsession =& JFactory::getSession();
			$access_token = $jsession->get('projects.' . $service . '.token');
		}

		// Check that access token is valid
		if ($access_token)
		{
			$token = json_decode($access_token);
			if (($token->created + ($token->expires_in - 30)) < time())
			{
				$access_token = 0;
			}
		}
		
		// Refresh token if access token is invalid
		if (!$access_token)
		{
			// Offline user - get token from stored refresh token
			$refresh_token = $this->getStoredParam($service . '_token', $uid);

			if (!$refresh_token)
			{
				return false;
			}

			$token = $this->refreshToken($service, $refresh_token);

			// Store access token in session
			if (isset($token->access_token))
			{
				$token->created = time();
				$access_token = json_encode($token);
			}
		}
				
		// Need access token to proceed
		if (!$access_token)
		{
			$this->setError('Oups! Authentication error. Please try again. If the error persists, contact support');
			return false;
		}
		
		if ($service == 'google')
		{
			// Set up the config for the google api instance
			$client = new Google_Client();
			$client->setClientId($config['clientId']);
			$client->setClientSecret($config['clientSecret']);
			$client->setAccessToken($access_token);
		}
		
		if ($uid == $this->_uid)
		{
			$this->_client[$service] = $client;
		}
		
		return $client;
	}
	
	/**
	 * Get service API
	 * 
	 * @param      string	$service	Service name (google or dropbox)
	 * @param      integer	$uid		User ID
	 *
	 * @return     object
	 */
	public function getAPI ($service = 'google', $uid = 0) 
	{
		// Do we have API started already?
		if (isset($this->_api[$service]) && $this->_api[$service])
		{
			return $this->_api[$service];
		}
		
		$uid = $uid ? $uid : $this->_uid;
		
		// Is user connected?
		$connected = $this->getStoredParam($service . '_token', $uid);
		$uid = $connected ? $uid : $this->_project->created_by_user;
		
		if (!in_array($service, $this->_services))
		{
			$this->setError('Missing or invalid service name');
			return false;
		}
		
		$config = $this->_connect[$service];

		// Make sure we have service
		if (!isset($config) || !$config)
		{
			$this->setError('Missing service configuration or connection service turned off');
			return false;
		}
		
		// Need service client
		$client = $this->startClient($service, $uid);
		if (!$client)
		{
			return false;
		}
		
		// Get API
		if ($service == 'google')
		{
			$this->_api[$service] = new Google_DriveService($client);			
		}
		else
		{
			$this->_api[$service] = NULL;
		}
		
		return $this->_api[$service];
		
	}
	
	/**
	 * Prepare service for use or confirm it's ready
	 * 
	 * @param      string	$service	Service name (google or dropbox)
	 *
	 * @return     void
	 */
	public function afterConnect ($service = 'google') 
	{
		if (!in_array($service, $this->_services))
		{
			$this->setError('Missing or invalid service name');
			return false;
		}
		
		$config = $this->_connect[$service];
		
		// Make sure we have service
		if (!isset($config) || !$config)
		{
			$this->setError('Missing service configuration or connection service turned off');
			return false;
		}
		
		// Get and save acting user access profile info
		$profile = $this->getAccessProfile($service);
										
		$objO = new ProjectOwner( $this->_db );
		
		// Get email/name pairs of connected project owners
		$connected = $objO->getConnected($this->_project->id, $service, $exclude = array($this->_project->created_by_user));
		
		// Setup remote directory & update permissions		
		$dir = $this->getRemoteDirectory($service, $this->_project->created_by_user, $connected);
									
		return true;
	}
	
	/**
	 * Get ID of remote directory and store it
	 * 
	 * @param      string	$service	Service name (google or dropbox)
	 * @param      integer	$uid		User ID
	 * @param      array	$shared		Array of emails with whom master remote folder is shared
	 *
	 * @return     String
	 */
	public function getRemoteDirectory ($service = 'google', $uid = 0, $shared = array()) 
	{
		// Get api
		$apiService = $this->getAPI($service, $uid);
		
		if (!$apiService)
		{
			$this->setError('API service unavailable');
			return false;
		}
		
		// Service config
		$config = $this->_connect[$service];
		
		// Get ID of user's remote project folder
		$folderID = $config['remote_dir_id'];
		
		// Is this project creator?
		$creator = $uid == $this->_project->created_by_user ? 1 : 0;
		
		if ($service == 'google')
		{																		
			// Make sure we still have the folder
			if ($folderID != 1)
			{
				try
				{
					$folder = $apiService->files->get($folderID);
				}
				catch (Exception $e)
				{
					$folderID = 1;
				}
			}
			
			// Create remote project folder if not found (project creator)
			if ($folderID == 1  && $creator)
			{
				$file = new Google_DriveFile;
				$file->setMimeType('application/vnd.google-apps.folder');
				$file->setTitle($config['remote_dir']);
				
				$createdFolder = $apiService->files->insert($file, array(
				      'mimeType' => 'application/vnd.google-apps.folder'
				));
			
				$folderID = $createdFolder['id'];
				if ($folderID)
				{
					$obj 	= new Project( $this->_db );
					$obj->saveParam($this->_project->id, $service . '_dir_id', $folderID);
				}
			}
			
			// Update permissions
			if ($folderID != 1 && !empty($shared))
			{				
				// Get current permissions
				$permlist = $apiService->permissions->listPermissions($folderID);
				$permNames = array();
				
				// Collect permission names
				foreach ($permlist['items'] as $p)
				{
					if (isset($p['name']))
					{
						$permNames[] = $p['name'];
					}
				}
				
				// Go through array of connected users
				foreach ($shared as $name => $email)
				{
					// Need to add permission
					if (!in_array($name, $permNames))
					{
						$permission = new Google_Permission;
						$permission->setRole('writer');
						$permission->setType('user');
						$permission->setValue($email);
						$params = array('sendNotificationEmails' => 'false');
						
						try 
						{
							$perm = $apiService->permissions->insert($folderID, $permission, $params);
						} 
						catch (Exception $e) 
						{
							$this->setError('Failed to set correct permissions');
						}
					}
				}
			}
			
			return $folderID;
		}
				
		return true;
	}
		
	/**
	 * Get and store access profile information
	 * 
	 * @param      string	$service	Service name (google or dropbox)
	 * @param      integer	$uid		User ID
	 *
	 * @return    boolean
	 */
	public function getAccessProfile ($service = 'google', $uid = 0) 
	{
		$config = $this->_connect[$service];
		$uid = $uid ? $uid : $this->_uid;
		$email = $this->getStoredParam($service . '_email', $uid);

		if (!$email)
		{
			if ($service == 'google')
			{
				// Start service client
				$client = $this->startClient($service, $uid);
				if (!$client)
				{
					return false;
				}
				
				$oauth2 = new Google_Oauth2Service($client);
				try
				{
					$user = $oauth2->userinfo->get();
					$email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
					$name = utf8_encode($user['name']);
					
					// Save params for project team member
					$this->storeParam($service . '_email', $email, $uid);
					$this->storeParam($service . '_name', $name, $uid);
					$this->storeParam($service . '_userid', $user['id'], $uid);					
				}
				catch (Exception $e)
				{
					$this->setError('Failed to retrieve remote service profile information');
					return false;
				}				
			}
		}
		
		return true;		
	}
	
	/**
	 * Load remote file metadata
	 * 
	 * @param      string	$service	Service name (google or dropbox)
	 * @param      integer	$uid		User ID
	 * @param      string	$id			Remote ID
	 *
	 * @return     string
	 */
	public function loadRemoteResource ($service = 'google', $uid = 0, $id = 0) 
	{
		if (!$id)
		{
			return false;
		}
		
		$resource = NULL;
		
		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError('API service unavailable');
			return false;
		}
		
		if ($service == 'google')
		{
			$resource = ProjectsGoogleHelper::loadFile ($apiService, $id);			
		}
		
		return $resource;
	}
	
	/**
	 * Add remote file
	 * 
	 * @param      integer	$projectid	Project ID
	 * @param      string	$service	Service name (google or dropbox)
	 * @param      integer	$uid		User ID
	 * @param      array	$local		Array of local file info
	 * @param      string	$parentId	Parent folder ID
	 * @param      boolean	$convert	Convert for remote editing? (Google only)
	 *
	 * @return     array
	 */
	public function addRemoteFile (
		$projectid = NULL, $service = 'google', $uid = 0, 
		$local,  $parentId = 0, $convert = false)
	{		
		if (!$projectid || !$parentId || empty($local))
		{
			return false;
		}
		
		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError('API service unavailable');
			return false;
		}
		
		$newItemId = 0;
		
		// Parse incoming
		$title 		= $local['title'];
		$localPath 	= $local['fullPath'];
		$fpath		= $local['local_path'];
		
		if (file_exists($localPath))
		{
			$data = file_get_contents($localPath);
		}
		else
		{
			return false;
		}
		
		$mimeType 	= $local['mimeType'];
		
		// File must have data
		if (!$data)
		{
			return false;
		}
		
		// Collector for created item metadata
		$metadata = array();
		
		// Perform request
		if ($service == 'google')
		{
			$newItemId = ProjectsGoogleHelper::insertFile ($apiService, $title, $data, $mimeType, $parentId, $metadata, $convert);					
		}
		
		// Error!
		if (!$newItemId)
		{
			return NULL;
		}
				
		$remote_md5 		= isset($metadata) && isset($metadata['md5Checksum']) ? $metadata['md5Checksum'] : NULL;
		$remote_modified 	= isset($metadata) && isset($metadata['modifiedDate']) 
							? gmdate('Y-m-d H:i:s', strtotime($metadata['modifiedDate'])) : NULL;
		$remote_format 		= isset($metadata) && isset($metadata['mimeType']) ? $metadata['mimeType'] : NULL;
		$converted			= isset($metadata) 
								&& preg_match("/google-apps/", $remote_format) 
								&& !preg_match("/.folder/", $remote_format) ? 1 : 0;
								
		if ($convert == true && !$converted)
		{
			// Retreat
			ProjectsGoogleHelper::deleteItem ($apiService, $newItemId, true);
			return false;
		}
								
		if ($converted)
		{
			$g_ext = ProjectsGoogleHelper::getGoogleConversionFormat($metadata['mimeType'], false, true);
			
			$dir = dirname($fpath) != '.' ? dirname($fpath) : '';
			$name = basename($fpath);
			
			// Get file extention
			$parts = explode('.', $name);
			$ext   = count($parts) > 1 ? array_pop($parts) : '';
			 
			if ($g_ext && $ext != $g_ext)
			{
				$fpath = $dir ? $dir . DS : '';
				$fpath.= $name . '.' . $g_ext;
			}
		}

		// Update connection record
		$objRFile = new ProjectRemoteFile ($this->_db);
		
		$update = $objRFile->updateRecord( $projectid, $service, $newItemId,
		 	$fpath, 'file', $this->_uid, $parentId, $title, $remote_md5,
			$local['md5'], $converted, $remote_format, $local['mimeType'], $remote_modified
		);
		
		return $newItemId;		
	}
	
	/**
	 * Update remote file
	 *
	 * @param      integer	$projectid	Project ID
	 * @param      string	$service	Service name (google or dropbox)
	 * @param      integer	$uid		User ID
	 * @param      string	$remoteid	Remote resource ID
	 * @param      array	$local		Array of local file info
	 * @param      string	$parentId	Parent folder ID
	 * @param      boolean	$convert	Convert for remote editing? (Google only)
	 *
	 * @return     array
	 */
	public function updateRemoteFile (
		$projectid = NULL, $service = 'google', $uid = 0, 
		$remoteid = 0, $local = array(),  $parentId = 0, $convert = false)
	{		
		if (!$projectid || !$remoteid || empty($local) || !$parentId)
		{
			return false;
		}
		
		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError('API service unavailable');
			return false;
		}
		
		// Parse incoming
		$title 		= basename($local['local_path']);
		$localPath 	= $local['fullPath'];
		
		if (file_exists($localPath))
		{
			$data = file_get_contents($localPath);
		}
		else
		{
			return false;
		}
				
		$mimeType 	= $local['mimeType'];
		
		// File must have data
		if (!$data)
		{
			return false;
		}
		
		// Collector for created item metadata
		$metadata = array();
		$success  = 0;

		// Perform request
		if ($service == 'google')
		{
			// Check if content really changed
			try
			{
				$check = $apiService->files->get($remoteid);
				
				if ($check['labels']['trashed'])
				{
					$success = ProjectsGoogleHelper::untrashItem ($apiService, $remoteid);
				}				
				elseif ($check && isset($check['md5Checksum']) && isset($local['md5Checksum']) && $check['md5Checksum'] == $local['md5Checksum'])
				{
					$success = $remoteid;
				}
			}
			catch (Exception $e)
			{
				return false;
			}
			
			// There was a change in content, update			
			if (!$success)
			{
				$success = ProjectsGoogleHelper::updateFile ($apiService, $remoteid, $title, $data, $mimeType, $parentId, $metadata, $convert);					
			}
		}

		if (!$success)
		{
			return false;
		}

		$remote_md5 		= isset($metadata) && isset($metadata['md5Checksum']) ? $metadata['md5Checksum'] : NULL;				
		$remote_modified 	= isset($metadata) && isset($metadata['modifiedDate']) 
				? gmdate('Y-m-d H:i:s', strtotime($metadata['modifiedDate'])) : NULL;
		$remote_format 		= isset($metadata) && isset($metadata['mimeType']) ? $metadata['mimeType'] : NULL;
		$converted			= isset($metadata) 
								&& preg_match("/google-apps/", $remote_format) 
								&& !preg_match("/.folder/", $remote_format) ? 1 : 0;
				
		// Update connection record
		$objRFile = new ProjectRemoteFile ($this->_db);
		
		$update = $objRFile->updateRecord( $projectid, $service, $remoteid,
		 	$local['local_path'], 'file', $uid, $parentId, $title, $remote_md5,
			$local['md5'], $converted, $remote_format, $local['mimeType'], $remote_modified
		);
	
		return $success;		
	}
	
	/**
	 * Rename remote file
	 * 
	 * @param      integer	$projectid	Project ID
	 * @param      string	$service	Service name (google or dropbox)
	 * @param      integer	$uid		User ID
	 * @param      string	$remoteid	Remote resource ID
	 * @param      array	$local		Array of local file info
	 * @param      string	$parentId	Parent folder ID
	 *
	 * @return     array
	 */
	public function renameRemoteItem (
		$projectid = NULL, $service = 'google', $uid = 0, 
		$remoteid = 0, $local = array(),  $parentId = 0)
	{		
		if (!$projectid || !$remoteid || empty($local))
		{
			return false;
		}
		
		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError('API service unavailable');
			return false;
		}
		
		// Collector for created folder metadata
		$metadata 	= array();
		$success 	= 0;
		$newTitle 	= basename($local['local_path']);
		
		// Perform request
		if ($service == 'google')
		{
			$success = ProjectsGoogleHelper::patchFile ($apiService, $remoteid, $newTitle, '', $metadata);					
		}
		
		if (!$success)
		{
			return false;
		}
		
		$remote_md5 		= isset($metadata) && isset($metadata['md5Checksum']) ? $metadata['md5Checksum'] : NULL;				
		$remote_modified 	= isset($metadata) && isset($metadata['modifiedDate']) 
				? gmdate('Y-m-d H:i:s', strtotime($metadata['modifiedDate'])) : NULL;
		$remote_format 		= isset($metadata) && isset($metadata['mimeType']) ? $metadata['mimeType'] : NULL;
				
		// Update connection record
		$objRFile = new ProjectRemoteFile ($this->_db);
		
		$update = $objRFile->updateRecord( $projectid, $service, $remoteid,
		 	$local['local_path'], $local['type'], $uid, $parentId, $newTitle, $remote_md5,
			$local['md5'], $local['converted'], $remote_format, $local['mimeType'], $remote_modified
		);
		
		return $success;
	}
	
	/**
	 * Move remote file to another parent
	 *
	 * @param      integer	$projectid	Project ID
	 * @param      string	$service	Service name (google or dropbox)
	 * @param      integer	$uid		User ID
	 * @param      string	$remoteid	Remote resource ID
	 * @param      array	$local		Array of local file info
	 * @param      string	$parentId	Parent folder ID
	 *
	 * @return     array
	 */
	public function moveRemoteItem (
		$projectid = NULL, $service = 'google', $uid = 0, 
		$remoteid = 0, $local = array(),  $parentId = 0)
	{		
		if (!$projectid || !$remoteid || empty($local) || !$parentId)
		{
			return false;
		}
		
		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError('API service unavailable');
			return false;
		}
		
		// Collector for created folder metadata
		$metadata 	= array();
		$success 	= 0;
		
		// Perform request
		if ($service == 'google')
		{
			$success = ProjectsGoogleHelper::patchFile ($apiService, $remoteid, '', $parentId, $metadata);					
		}
		
		if (!$success)
		{
			return false;
		}
		
		$remote_md5 		= isset($metadata) && isset($metadata['md5Checksum']) ? $metadata['md5Checksum'] : NULL;				
		$remote_modified 	= isset($metadata) && isset($metadata['modifiedDate']) 
				? gmdate('Y-m-d H:i:s', strtotime($metadata['modifiedDate'])) : NULL;
		$remote_format 		= isset($metadata) && isset($metadata['mimeType']) ? $metadata['mimeType'] : NULL;
				
		// Update connection record
		$objRFile = new ProjectRemoteFile ($this->_db);
		
		$update = $objRFile->updateRecord( $projectid, $service, $remoteid,
		 	$local['local_path'], $local['type'], $uid, $parentId, $metadata['title'], $remote_md5,
			$local['md5'], $local['converted'], $remote_format, $local['mimeType'], $remote_modified
		);
				
		return $success;
	}
	
	/**
	 * Delete remote item
	 * 
	 * @param      integer	$projectid	Project ID
	 * @param      string	$service	Service name (google or dropbox)
	 * @param      integer	$uid		User ID
	 * @param      string	$remoteid	Remote resource ID
	 * @param      boolean	$permanent	Delete permanently? (or trash)
	 *
	 * @return     array
	 */
	public function deleteRemoteItem ($projectid = NULL, $service = 'google', $uid = 0, $remoteid = 0, $permanent = false)
	{
		if (!$projectid || !$remoteid)
		{
			return false;
		}
		
		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError('API service unavailable');
			return false;
		}
		
		$success = 0;
		
		// Perform request
		if ($service == 'google')
		{
			$success = ProjectsGoogleHelper::deleteItem ($apiService, $remoteid, $permanent);					
		}
				
		// Delete connection record
		//if ($permanent == true)
		//{
			$objRFile = new ProjectRemoteFile ($this->_db);
			if ($objRFile->loadItem( $projectid, $remoteid, $service)) 
			{
				$objRFile->delete();
			}
		//}
								
		return $success;
	}
	
	/**
	 * Create remote folder
	 * 
	 * @param      integer	$projectid		Project ID
	 * @param      string	$service		Service name (google or dropbox)
	 * @param      integer	$uid			User ID
	 * @param      string	$title			Folder name
	 * @param      string	$path			Path to folder
	 * @param      string	$parentId		Parent folder ID
	 * @param      array	&$remoteFolders	Collector array
	 *
	 * @return     integer (folder ID)
	 */
	public function createRemoteFolder (
		$projectid = NULL, $service = 'google', $uid = 0, 
		$title = '', $path = '',  $parentId = 0, 
		&$remoteFolders = array())
	{
	
		if (!$projectid)
		{
			$projectid = $this->_project->id;
		}
			
		if (!$projectid || !$title || !$parentId || !$path)
		{
			return false;
		}
		
		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError('API service unavailable');
			return false;
		}
		
		// Check once again that we don't have this folder already
		if (isset($remoteFolders[$path]))
		{
			if ($remoteFolders[$path]['status'] == 'D')
			{
				// Untrash
				if ($service == 'google')
				{
					ProjectsGoogleHelper::untrashItem($apiService, $remoteFolders[$path]['remoteid']);					
				}
			}
			
			$parentId = $remoteFolders[$path]['remoteid'];
		}
		else
		{
			// We do need to create a new folder
			$newParentId = 0;
			
			// Collector for created folder metadata
			$metadata = array();
			
			// Perform request
			if ($service == 'google')
			{
				$newParentId = ProjectsGoogleHelper::createFolder ($apiService, $title, $parentId, $metadata);					
			}
			
			// Error!
			if (!$newParentId)
			{
				return NULL;
			}	
							
			// Add newly create folder to remoteFolders array
			$remoteFolders[$path] = array(
				'remoteid' => $newParentId, 
				'status' => 'A',
				'parentid'=> $parentId
			);
			
			// Update connection record
			$objRFile = new ProjectRemoteFile ($this->_db);

			$update = $objRFile->updateRecord( $projectid, $service, $newParentId,
			 	$path, 'folder', $this->_uid, $parentId, $title );			
			
			return $newParentId;
		}
		
		return $parentId;	
	}
	
	/**
	 * Prep remote directory structure for syncing file
	 * 
	 * @param      integer	$projectid		Project ID
	 * @param      string	$service		Service name (google or dropbox)
	 * @param      integer	$uid			User ID
	 * @param      array	$local			Array of local file info
	 * @param      array 	&$remoteFolders	Collector array
	 *
	 * @return     integer
	 */
	public function prepRemoteParent ($projectid = NULL, $service = 'google', $uid = 0, $local = array(), &$remoteFolders = array())
	{
		if (!$projectid)
		{
			$projectid = $this->_project->id;
		}
		
		if (empty($local))
		{
			return false;
		}
				
		// Get remote master folder ID
		$parentId = $this->getConfigParam($service, 'remote_dir_id');	
		
		$localPath = $local['local_path'];
		$dirname   = dirname($localPath);
		
		// No sub directory - parent is home project folder
		if (!$dirname || $dirname == '.')
		{
			return $parentId;
		}		
		$pathBits = explode(DS, $dirname);
				
		$cur = '';
		foreach ($pathBits as $title)
		{
			if (!trim($title))
			{
				continue;
			}
			
			$cur .= DS . $title;
			$path = trim($cur, DS);
			
			$parentId = $this->createRemoteFolder( 
				$projectid, $service, $uid, 
				$title, $path,  $parentId, $remoteFolders
			);
		}
		
		return $parentId;				
	}
	
	/**
	 * Get remote file history
	 * 
	 * @param      string	$id			Remote ID
	 * @param      integer	$uid		User ID
	 * @param      string	$service	Service name (google)
	 *
	 * @return     array
	 */
	public function getFileHistory ($id = 0, $uid = 0, $service = 'google') 
	{
		// Get api
		$apiService = $this->getAPI($service, $uid);
		
		if (!$apiService)
		{
			$this->setError('API service unavailable');
			return false;
		}
		
		$config = $this->_connect[$service];
		$history = array();
		
		// Get remote folder ID
		$folderID = $config['remote_dir_id'];	
		
		// Check for required configs
		if (!$folderID || !$id)
		{
			return false;
		}
		
		// Fetch content, scenario varies depending on service
		if ($service == 'google')
		{						
			try
			{
				$data = $apiService->revisions->listRevisions($id);
				
				if (!empty($data['items']))
				{
					return $data['items'];
				}
			}
			catch (Exception $e)
			{
				$this->setError('Failed to retrieve revisions for remote file ID ' . $id);
				return false;
			}		
		}
		
		return $history;		
	}
	
	/**
	 * Get remote file revision
	 * 
	 * @param      string	$id			Remote ID
	 * @param      integer	$revision	remote revision ID
	 * @param      integer	$uid		User ID
	 * @param      string	$service	Service name (google)
	 *
	 * @return     array
	 */
	public function getFileRevision ($id = 0, $revision = 0, $uid = 0, $service = 'google') 
	{		
		// Get api
		$apiService = $this->getAPI($service, $uid);
		
		if (!$apiService)
		{
			$this->setError('API service unavailable');
			return false;
		}
				
		// Check for required configs
		if (!$id || !$revision)
		{
			return false;
		}
		
		// Fetch content, scenario varies depending on service
		if ($service == 'google')
		{			
			try
			{
				$data = $apiService->revisions->get($id, $revision);
				if ($data)
				{
					$entry = array();
					$entry['url']			= isset($data['downloadUrl']) ? $data['downloadUrl'] : '';
					$entry['export']		= isset($data['exportLinks']) ? $data['exportLinks'] : array();
					$entry['mimeType']		= $data['mimeType'];
					return json_decode(json_encode($entry));
				}
				
			}
			catch (Exception $e)
			{
				$this->setError('Failed to retrieve revisions for remote file ID ' . $id);
				return false;
			}		
		}
		
		return false;
	}
	
	/**
	 * Sort remote revisions
	 * 
	 * @param      integer	$id					Remote resource ID
	 * @param      integer	$converted			Converted for remote editing?
	 * @param      string	$lastModifiedBy		Last modified by (name)
	 * @param      integer	$uid				User ID
	 * @param      string	$service			Service name (google or dropbox)
	 * @param      string	$file				File path
	 * @param      array 	&$versions			Versions collector array
	 * @param      array 	&$timestamps		Collector array
	 * @param      integer	$original			Source file?
	 *
	 * @return     array
	 */
	public function sortRemoteRevisions(
		$id, $converted = 0, $lastModifiedBy = '', $uid , $service, $file, 
		&$versions = array(), &$timestamps = array(), $original = 0
	)
	{		
		// Get remote revisions
		$revisions = $this->getFileHistory($id, $uid, $service );
		
		// Go through remote revisions
		if (!empty($revisions))
		{					
			$timestampsG = array();
			foreach ($revisions as $rev)
			{
				$timestampsG[]  = strtotime($rev['modifiedDate']);						
			}
			array_multisort($timestampsG, SORT_DESC, $revisions);	
			
			$r = 1;
			foreach ($revisions as $rev)
			{																
				$order 			= $r == count($revisions) ? 'first' : '';
				$order			= $r == 1 ? 'last' : $order;					

				// If remote, make sure current version is remote
				if ($converted == 1 && $order == 'last')
				{
					$timestamps[]  	= strtotime(date( 'Y-m-d H:i:s' ));
				}
				else
				{
					$timestamps[]  	= strtotime($rev['modifiedDate']);
				}	
				$author = isset($rev['lastModifyingUser']['displayName']) 
					? strtolower($rev['lastModifyingUser']['displayName']) 
					: NULL;
				$author = !$author && isset($rev['lastModifyingUserName']) ? strtolower($rev['lastModifyingUserName']) : NULL;
					
				if (!$author && $order == 'last')
				{
					$author = utf8_decode($lastModifiedBy);
				}
								
				$revision = array(
					'date' 			=> $rev['modifiedDate'],
					'author' 		=> $author,
					'email' 		=> NULL,
					'hash' 			=> $rev['id'],
					'file'			=> $file,
					'base'			=> '',
					'remote'		=> $id,
					'local'			=> false,
					'content'		=> NULL,
					'preview'		=> NULL,
					'original'		=> $original,
					'hide'			=> 0,
					'message'		=> '',
					'rename'		=> '',
					'change'		=> '',
					'movedTo'		=> '',
					'size'			=> '',
					'name'			=> JText::_('COM_PROJECTS_FILES_REMOTE_FILE_' . strtoupper($service)),
					'order'			=> $order,
					'count'			=> count($revisions),
					'commitStatus'	=> $r == count($revisions) ? 'A' : 'M'
				);

				$versions[] = $revision;
				$r++;					
			}
		}
	}
	
	/**
	 * Save connection of local file after being exported 
	 * 
	 * @param      integer 	$projectid			Project ID
	 * @param      string	$service			Service name (google)
	 * @param      string 	$id					Remote ID
	 * @param      string	$path				File path
	 * @param      integer 	$paired		    	Paired record ID
	 * @param      string	$original_path		Original path
	 * @param      string	$original_format	Original MIME type
	 * @param      string	$original_id		Original ID
	 * 
	 * @return     mixed False if error, Object on success
	 */	
	public function savePairing ( 
		$projectid = NULL, $service = '', $id = NULL, $path = '',
		$paired = 0, $original_path = '', $original_format = '', 
		$original_id = ''
	) 
	{
		if (!$projectid || !$id) 
		{
			return false;
		}
		
		$objRFile = new ProjectRemoteFile ($this->_db);
		if ($objRFile->loadItem( $projectid, $id, $service, $path)) 
		{
			$objRFile->paired 			= $paired;
			$objRFile->original_path 	= $original_path;
			$objRFile->original_format 	= $original_format;
			$objRFile->original_id 		= $original_id;
			$objRFile->store();
		}
		
		return true;
	}	
	
	/**
	 * Fix connection record(s) after local change to parent folder
	 * 
	 * @param      string	$service	Service name (google)
	 * @param      integer	$uid		User ID
	 * @param      string	$dir		Directory path
	 * @param      string	$action		Action
	 * @param      string	$newdir		New directory path
	 * @param      string	$parentId	Parent ID
	 *
	 * @return     array
	 */
	public function fixConvertedItems ($service = 'google', $uid = 0, $dir = '', $action = '', $newdir = '', $parentId = '') 
	{
		if (!$dir || !$action)
		{
			return false;
		}
		
		$objRFile = new ProjectRemoteFile ($this->_db);
		$converted = $objRFile->getRemoteConnections($this->_project->id, $service, $dir, 1);
		
		if (!empty($converted['paths']))
		{
			foreach ($converted['paths'] as $c)
			{
				// Delete record
				if ($action == 'D')
				{
					$objRFile->deleteRecord( $this->_project->id, $service, $c['remote_id']);
				}
				elseif ($newdir)
				{
					// Update dir path
					$fpath = $newdir . DS . basename($c['path']);
					
					$update = $objRFile->updateRecord( $this->_project->id, $service, $c['remote_id'],
					 	$fpath, $c['type'] , $uid, $parentId
					);
				}
			}
		}
	}
	
	/**
	 * Get remote changes
	 * 
	 * @param      string	$service		Service name (google or dropbox)
	 * @param      integer	$uid			User ID
	 * @param      string	$startChangeId	Last Change ID
	 * @param      array	$remotes		Collector array for active items
	 * @param      array	$deletes		Collector array for deleted items
	 * @param      array	$connections	Array of local-remote connections
	 * @param      string	$path			Path
	 *
	 * @return     integer - change ID
	 */
	public function getChangedItems ($service = 'google', $uid = 0, $startChangeId = NULL, &$remotes, &$deletes, $connections = array(), $path = '' ) 
	{
		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError('API service unavailable');
			return false;
		}
				
		$changeID = NULL;
				
		if ($service == 'google')
		{
			// Get remote folder ID
			$folderID = $this->getConfigParam($service, 'remote_dir_id');	
			
			$changeID = ProjectsGoogleHelper::collectChanges ($apiService, $folderID, $remotes, $deletes, $path, $startChangeId, $connections);			
		}
				
		return $changeID;		
	}
	
	/**
	 * Get all remote files
	 * 
	 * @param      string	$service		Service name (google or dropbox)
	 * @param      integer	$uid			User ID
	 * @param      string	$since			last sync date
	 * @param      array	$connections	Array of local-remote connections
	 *
	 * @return     array
	 */
	public function getRemoteItems ($service = 'google', $uid = 0, $since = '', $connections = array() ) 
	{
		// Get api
		$apiService = $this->getAPI($service, $uid);
		$remotes = array();

		if (!$apiService)
		{
			$this->setError('API service unavailable');
			return false;
		}
		
		if ($service == 'google')
		{
			// Get remote folder ID
			$folderID = $this->getConfigParam($service, 'remote_dir_id');	

			// Need remote folder
			if (!$folderID)
			{
				$this->setError('Sync failed: remote service currently unavailable');
				return false;
			}

			// Make sure master folder is there and not trashed
			try
			{
				$folder = $apiService->files->get($folderID);
				if ($folder && $folder['labels']['trashed'] == 1)
				{
					// Untrash
					ProjectsGoogleHelper::untrashItem($apiService, $folderID);					
				}
			}
			catch (Exception $e)
			{
				$this->setError('Sync failed: remote folder unavailable');
				return false;
			}
			
			// Collect remote items with duplicate names
			$duplicates = array();		
			
			// Get files in main project remote directory
			$remotes = ProjectsGoogleHelper::getFolderContent ($apiService, $folderID, 
				$remotes, '', $since, $connections, $duplicates);			
		}
		
		return $remotes;
		
	}
	
	/**
	 * Get remote folder structure
	 * 
	 * @param      string	$service		Service name (google or dropbox)
	 * @param      integer	$uid			User ID
	 * @param      array	&$remoteFolders	Collector array
	 * @param      string	$path			
	 *
	 * @return     NULL
	 */
	public function getFolderStructure ($service = 'google', $uid = 0, &$remoteFolders, $path = '' ) 
	{		
		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError('API service unavailable');
			return false;
		}
				
		if ($service == 'google')
		{
			// Get remote folder ID
			$folderID   = $this->getConfigParam($service, 'remote_dir_id');
			$folderName = $this->getConfigParam($service, 'remote_dir');	
			
			// Check that master folder is there
			try
			{
				$folder = $apiService->files->get($folderID);
				if ($folder && $folder['labels']['trashed'] == 1)
				{
					// Untrash
					ProjectsGoogleHelper::untrashItem($apiService, $folderID);					
				}
				if ($folder && $folder['title'] != $folderName)
				{
					// Save renamed reference
					$obj 	= new Project( $this->_db );
					$obj->saveParam($this->_project->id, $service . '_dir', $folder['title']);					
				}
			}
			catch (Exception $e)
			{
				$this->setError('Remote service folder unavailable');
				return false;
			}
			
			ProjectsGoogleHelper::getFolders ($apiService, $folderID, $remoteFolders, $path);			
		}
				
		return true;		
	}
	
	/**
	 * Send httpRequest
	 * 
	 * @param      string	$service		Service name (google or dropbox)
	 * @param      integer	$uid			User ID
	 * @param      string	$url			Request URL
	 *
	 * @return     string
	 */
	public function sendHttpRequest ($service = 'google', $uid = 0, $url = '') 
	{
		$content = NULL;
		
		if (!$url)
		{
			return false;
		}
		
		// Get api
		$apiService = $this->getAPI($service, $uid);
		
		if (!$apiService)
		{
			$this->setError('API service unavailable');
			return false;
		}
		
		if ($service == 'google')
		{
			// Make Http request
			$request = new Google_HttpRequest($url, 'GET', null, null);
			
			// Get remote content
			$httpRequest = Google_Client::$io->authenticatedRequest($request);
			
			if ($httpRequest->getResponseHttpCode() == 200) 
			{ 
				return $httpRequest->getResponseBody();
			} 
			else 
			{
			      // An error occurred.
			      return null;
			}
		}
		
		return $content;
	}
	
	/**
	 * Download remote content via cURL
	 * 
	 * @param      string	$service		Service name (google or dropbox)
	 * @param      string	$remote			Remote resource array
	 * @param      string	$path			Project repo path
	 * @return     string or boolean
	 */
	public function downloadFileCurl ($service = 'google', $url = '', $path = '') 
	{
		if (!$url || !$path)
		{
			return false;
		}
		
		// Get api
		$apiService = $this->getAPI($service);
		
		if (!$apiService)
		{
			$this->setError('API service unavailable');
			return false;
		}
		
		$fp = fopen($path, 'w');
		
		// Make Http request
		$request = new Google_HttpRequest($url, 'GET', null, null);
		$request = Google_Client::$auth->sign($request);
		
		//Initialize the Curl Session.
		$ch = curl_init();
		
		// Set the Curl url.
		curl_setopt ($ch, CURLOPT_URL, 	$request->getUrl());
		
		// Set headers
		$requestHeaders = $request->getRequestHeaders();
	    if ($requestHeaders && is_array($requestHeaders)) 
		{
	      	$parsed = array();
	      	foreach ($requestHeaders as $k => $v) 
			{
	        	$parsed[] = "$k: $v";
	      	}
	      
			curl_setopt($ch, CURLOPT_HTTPHEADER, $parsed);
	    }
		
		// Download to file stream
		curl_setopt($ch, CURLOPT_FILE, $fp);

		$data = curl_exec($ch);

		curl_close($ch);
		fclose($fp);

		return true;
	}
	
	/**
	 * Download remote content
	 * 
	 * @param      string	$service		Service name (google or dropbox)
	 * @param      integer	$uid			User ID
	 * @param      array	$remote			Remote resource array
	 * @param      string	$path			Project repo path
	 * @param      boolean	$getContent		Output file content? Or write to file
	 * @return     string or boolean
	 */
	public function downloadFile($service = 'google', $uid = 0, $remote = array(), $path = '', $getContent = false) 
	{
		if (empty($remote) || !$remote['url'] || !$path || !is_dir($path))
		{
			return false;
		}
		$fpath 	= $remote['local_path'];
		$url  	= $remote['url'];
		
		// Get content
		$fc = $this->sendHttpRequest($service, $uid, $url);
		
		if ($getContent == true)
		{
			return $fc;
		}
		
		if ($this->fetchFile($fc, $fpath, $path))
		{
			return true;
		}		
		
		return false;		
	}
	
	/**
	 * Import remote file
	 * 
	 * @param      string	$service		Service name (google or dropbox)
	 * @param      integer	$uid			User ID
	 * @param      array	$remote			Remote resource array
	 * @param      string	$fpath			File path relative to repo path
	 * @param      string	$path			Project repo path
	 * @param      string	$ext			Extension
	 * @param      boolean	$getContent		Output file content? Or write to file
	 *
	 * @return     string or boolean
	 */
	public function importFile($service = 'google', $uid = 0, $remote = array(), $fpath = '', $path = '', $ext = '', $getContent = false) 
	{
		if (empty($remote) || !$fpath || !is_dir($path) || !isset($remote['exportLinks']))
		{
			return false;
		}
		
		$url = '';
		$default_type = ProjectsGoogleHelper::getGoogleExportType($ext);
		foreach ($remote['exportLinks'] as $type => $link)
		{
			if ($type == $default_type)
			{
				$url = $link;
			}
		}
		
		if (!$url)
		{
			return false;
		}
		
		// Get content
		$fc = $this->sendHttpRequest($service, $uid, $url);
		
		if ($getContent == true)
		{
			return $fc;
		}
		
		// Clean up data from Windows characters - important!
		if ($ext == 'tex')
		{
			$fc = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $fc);
		}
		
		if ($this->fetchFile($fc, $fpath, $path))
		{
			return true;
		}		
		
		return false;		
	}
	
	/**
	 * Fetch file
	 * 
	 * @param      string	$fc				File content
	 * @param      string	$fpath			File path relative to repo path
	 * @param      string	$path			Project repo path
	 *
	 * @return    void
	 */
	public function fetchFile($fc = '', $fpath = '', $path = '') 
	{
		if (!$fc)
		{
			return false;
		}
		
		$handle = fopen($path . DS . $fpath, 'w');
		fwrite($handle, $fc);
		fclose($handle);
		chmod($path . DS . $fpath, 0775);
		
		if (file_exists($path . DS . $fpath))
		{
			return true;	
		}
		
		return false;
	}
	
	/**
	 * Generate local thumbnail from remote
	 * 
	 * @param      string	$service		Service name (google or dropbox)
	 * @param      integer	$uid			User ID
	 * @param      array	$remote			Remote resource array
	 * @param      array	$config			Configuration array
	 * @param      string	$alias			Project alias name
	 * @param      object	$ih				Image handler object
	 *
	 * @return     void
	 */
	public function generateThumbnail($service = 'google', $uid = 0, $remote = array(), $config = array(), $alias = NULL, $ih = NULL) 
	{
		if (empty($remote) || !$remote['thumb'] || empty($config) || !$ih || !$alias)
		{
			return false;
		}		
		
		$thumb = substr($remote['remoteid'], 0, 20) . '_' . strtotime($remote['modified']) . '.png';
		
		$imagepath = trim($config->get('imagepath', '/site/projects'), DS);
		$to_path = DS . $imagepath . DS . strtolower($alias) . DS . 'preview';
		
		if (is_dir(JPATH_ROOT . $to_path) && !is_file(JPATH_ROOT. $to_path . DS . $thumb)) 
		{			
			// Get thumnail
			$fc = $this->sendHttpRequest($service, $uid, $remote['thumb']);
			
			if ($fc && $this->fetchFile($fc, $thumb, JPATH_ROOT . $to_path))
			{
				$handle = @fopen(JPATH_ROOT . $to_path . DS . $thumb, 'w');
				
				if ($handle)
				{
					fwrite($handle, $fc);
					fclose($handle);

					// Resize the image if necessary
					$ih->set('image',$thumb);
					$ih->set('overwrite',true);
					$ih->set('path',JPATH_ROOT. $to_path . DS);
					$ih->set('maxWidth', 180);
					$ih->set('maxHeight', 180);
					$ih->process();
				}			
			}					
		}			
	}
	
	/**
	 * Get stored auth token for service
	 * 
	 * @param      string	$param	Param name
	 * @param      integer	$uid	User ID
	 *
	 * @return     string
	 */
	public function getStoredParam ($param, $uid = 0) 
	{				
		$uid = $uid ? $uid : $this->_uid;		
		$objO = new ProjectOwner( $this->_db );
		$objO->loadOwner ($this->_project->id, $uid);
		$params = new JParameter( $objO->params );
		return $params->get($param);
	}
	
	/**
	 * Store auth token for service
	 * 
	 * @param      string	$param		Param name	
	 * @param      string	$value		Param value
	 * @param      integer	$uid		User ID
	 *
	 * @return     void
	 */	
	public function storeParam ($param = '', $value = '', $uid = 0)
	{		
		$uid = $uid ? $uid : $this->_uid;	
		
		$objO = new ProjectOwner( $this->_db );
		$objO->saveParam ( 
			$this->_project->id, 
			$uid, 
			$param, 
			$value
		);
	}
	
	/**
	 * Run cURL
	 * 
	 * @param      string	$url
	 * @param      string	$method
	 * @param      array	$postvals	
	 *
	 * @return     response string
	 */	
	public function runCurl($url, $method = 'GET', $postvals = null)
	{
	    $ch = curl_init($url);

	    //GET request: send headers and return data transfer
	    if ($method == 'GET')
		{
	        $options = array(
	            CURLOPT_URL => $url,
	            CURLOPT_RETURNTRANSFER => 1
	        );
	        curl_setopt_array($ch, $options);
	    } 
		else 
		{
	        $options = array(
	            CURLOPT_URL => $url,
	            CURLOPT_POST => 1,
	            CURLOPT_POSTFIELDS => $postvals,
	            CURLOPT_RETURNTRANSFER => 1
	        );
	        curl_setopt_array($ch, $options);
	    }

	    $response = curl_exec($ch);
	    curl_close($ch);

	    return $response;
	}
	
	/**
	 * Create authentication URL
	 * 
	 * @param      string	$service	Service name (google or dropbox)	
	 * @param      string	$return		URL to return to after authorization
	 *
	 * @return    URL string
	 */	
	public function createAuthUrl($service, $return = '')
	{
	    if (!$service || !in_array($service, $this->_services))
		{
			$this->setError('Missing or invalid service name');
			return false;
		}
		
		$config = $this->_connect[$service];
		
		// Make sure we have service
		if (!isset($config) || !$config)
		{
			$this->setError('Missing service configuration or connection service turned off');
			return false;
		}
		
		// Google service
		if ($service == 'google')
		{
			$scope = implode(' ', $config['scope']);
			$params = array(
		        'response_type=code',
		        'redirect_uri=' . urlencode($config['redirectUri']),
		        'client_id=' . urlencode($config['clientId']),
		        'scope=' . urlencode($scope),
		        'access_type=' . urlencode($config['accessType']),
		        'approval_prompt=' . urlencode($config['approvalPrompt'])
		    );

			$return = $return ? $return : JRoute::_('index.php?option=com_projects' . a 
				. 'alias=' . $this->_project->alias . a . 'active=files');
			
			$array = array(
				'alias'  => $this->_project->alias,
				'return' => $return,
				'service'=> $service
			);
			$json = json_encode($array);
			$json = base64_encode($json);
			
			$params[] = 'state=' . urlencode($json);

		    $params = implode('&', $params);
		    return self::GOOGLE_OAUTH2_AUTH_URL . "?$params";
		}
	}
	
	/**
	 * Refresh service token
	 * 
	 * @param      string	$service		Service name (google or dropbox)
	 * @param      string	$refreshToken	Stored refresh token	
	 *
	 * @return     JSON string or false
	 */	
	public function refreshToken($service, $refreshToken = '')
	{
	    if (!$service || !in_array($service, $this->_services))
		{
			$this->setError('Missing or invalid service name');
			return false;
		}
		
		$config = $this->_connect[$service];
		if (!$refreshToken)
		{
			$this->setError('Missing refresh token for service');
			return false;
		}
		
		// Make sure we have service
		if (!isset($config) || !$config)
		{
			$this->setError('Missing service configuration or connection service turned off');
			return false;
		}
		
		// Google service
		if ($service == 'google')
		{
			// construct POST object required for refresh token fetch
		    $postvals = array('grant_type' 		=> 'refresh_token',
		                      'client_id' 		=> $config['clientId'],
		                      'client_secret' 	=> $config['clientSecret'],
		                      'refresh_token' 	=> $refreshToken);

		    // return JSON refreshed access token object
			try
			{
				return json_decode($this->runCurl(self::GOOGLE_OAUTH2_TOKEN_URI, 'POST', $postvals));
			}
			catch (Exception $e)
			{
				$this->setError('Oups! There was an authentication error with the remote service.');
				return false;
			}
		}
		
		return false;
	}
	
	/**
	 * Disconnect user from service
	 * 
	 * @param      string	$service	Service name (google or dropbox)
	 * @param      boolean	$removeData	Remove remote data (when project creator)	
	 *
	 * @return     JSON string or false
	 */	
	public function disconnect($service, $removeData = false)
	{
	    if (!$service || !in_array($service, $this->_services))
		{
			$this->setError('Missing or invalid service name');
			return false;
		}
		
		$config = $this->_connect[$service];
		
		// Make sure we have service
		if (!isset($config) || !$config)
		{
			$this->setError('Missing service configuration or connection service turned off');
			return false;
		}
		
		$paramname = $service . '_token';
				
		// Clean up stored values
		$objO = new ProjectOwner( $this->_db );
		$objO->loadOwner ($this->_project->id, $this->_uid);
		$oparams = new JParameter( $objO->params );
		
		// Remove token
		$objO->saveParam ( 
			$this->_project->id, 
			$this->_uid, 
			$param = $paramname, 
			''
		);
		
		// Remove time
		$objO->saveParam ( 
			$this->_project->id, 
			$this->_uid, 
			$param = $paramname . '_created', 
			''
		);
		
		// Project creator?
		$creator = ($this->_project->owned_by_user == $this->_uid) ? 1 : 0;
				
		// Disconnect service if user is project creator
		if ($creator)
		{
			$obj = new Project( $this->_db );
			if (!$obj->loadProject($this->_project->id)) 
			{
				$this->setError( JText::_('Oups! There was a problem loading project data.') );
				return false;
			}
			
			// Get project params
			$pparams = new JParameter( $this->_project->params );
			
			// Remove all connection info and remote data
			$remoteid = $pparams->get('google_dir_id');
			if ($removeData == true && $remoteid)
			{
				$this->deleteRemoteItem($this->_project->id, $service, $this->_uid, $remoteid, $permanent = true);
				
				$obj->saveParam($this->_project->id, $service . '_sync', '');
				$obj->saveParam($this->_project->id, $service . '_last_remote_change', '');
				$obj->saveParam($this->_project->id, $service . '_last_local_change', '');
				$obj->saveParam($this->_project->id, $service . '_sync_id', '');
				$obj->saveParam($this->_project->id, $service . '_prev_sync_id', '');
				$obj->saveParam($this->_project->id, $service . '_sync_lock', '');
				$obj->saveParam($this->_project->id, $service . '_sync_queue', '');
			}
			
			// Clean up token
			$obj->saveParam($this->_project->id, $service . '_token', 0);
		}
								
		return true;
	}
}
