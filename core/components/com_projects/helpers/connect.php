<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.	 If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package	  hubzero-cms
 * @author	  Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license	  http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Helpers;

use Exception;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;
use Google_Service_Oauth2;
use Google_Http_Request;
use Hubzero\Base\Object;

require_once PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'remote' . DS . 'google.php';

/**
 * Projects Connect helper class
 */
class Connect extends Object
{
	/**
	 * Project
	 *
	 * @var object
	 */
	public $model		   = null;

	/**
	 * JDatabase
	 *
	 * @var object
	 */
	private $_db			= null;

	/**
	 * User ID
	 *
	 * @var integer
	 */
	private $_uid			= null;

	/**
	 * Connection configuration
	 *
	 * @var array
	 */
	private $_connect		= array();

	/**
	 * Service configuration
	 *
	 * @var array
	 */
	private $_service		= array();

	/**
	 * Available services
	 *
	 * @var array
	 */
	private $_services		= array('google', 'dropbox');

	/**
	 * Service client
	 *
	 * @var array
	 */
	public $_client			= array();

	/**
	 * Service API
	 *
	 * @var array
	 */
	public $_api			= array();

	/**
	 * URL to revoke Google token
	 */
	const GOOGLE_OAUTH2_REVOKE_URI	= 'https://accounts.google.com/o/oauth2/revoke';

	/**
	 * URL to refresh Google token
	 */
	const GOOGLE_OAUTH2_TOKEN_URI	= 'https://accounts.google.com/o/oauth2/token';

	/**
	 * Google auth URL
	 */
	const GOOGLE_OAUTH2_AUTH_URL	= 'https://accounts.google.com/o/oauth2/auth';

	/**
	 * Constructor
	 *
	 * @param   object   $model   Project
	 * @param   integer  $userid  User ID
	 * @param   string   $zone    Default time zone
	 * @return  void
	 */
	public function __construct($model = null, $userid = 0, $zone = 'UTC')
	{
		$this->_db   = \App::get('db');
		$this->model = $model;
		$this->_uid  = $userid ? $userid : User::get('id');

		// Set configurations
		$this->setConfigs();

		$this->getActive();

		// Set local timezone after loading library (so that local and remote sync)
		date_default_timezone_set($zone);
	}

	/**
	 * Set user
	 *
	 * @param   integer  $uid  User ID
	 * @return  void
	 */
	public function setUser($uid = 0)
	{
		$this->_uid = $uid;
	}

	/**
	 * Is any service active?
	 *
	 * @return  bool  false or array with configs
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
	 * @return  array
	 */
	public function getActive()
	{
		if (isset($this->_active))
		{
			return $this->_active;
		}

		$this->_active = array();

		foreach ($this->_services as $servicename)
		{
			$configs = $this->getConfigs($servicename, true);

			if ($configs && $configs['active'])
			{
				$this->_active[] = $servicename;
			}
		}

		return $this->_active;
	}

	/**
	 * Get services
	 *
	 * @return  array
	 */
	public function getServices()
	{
		return $this->_services;
	}

	/**
	 * Get service config param
	 *
	 * @param   string  $service  Service name (google or dropbox)
	 * @param   string  $param    Param name
	 * @return  mixed   string or null
	 */
	public function getConfigParam($service = 'google', $param = '')
	{
		if (!$param || !isset($this->_connect[$service][$param]))
		{
			return null;
		}
		return $this->_connect[$service][$param];
	}

	/**
	 * Get connections
	 *
	 * @param   integer  $uid  User ID
	 * @return  array
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
	 * @return  void
	 */
	public function setConfigs()
	{
		// Make up redirection URL for Google service
		$redirectUri = trim(Request::base(), DS) . DS . 'projects' . DS . 'auth';

		// Scope for Google service
		$scope = array (
			'https://www.googleapis.com/auth/drive',
			'https://www.googleapis.com/auth/userinfo.profile',
			'https://www.googleapis.com/auth/userinfo.email'
		);

		// We will use files plugin params
		$filesParams = Plugin::params('projects', 'files');

		// Get project params
		$pparams = $this->model->params;

		$connect = array(
			'google' => array(
				'servicename'    => 'Google Drive',
				'on'             => $filesParams->get('enable_google', 0),
				'clientId'       => $filesParams->get('google_clientId', 0),
				'clientSecret'   => $filesParams->get('google_clientSecret', 0),
				'appKey'         => $filesParams->get('google_appKey', 0),
				'redirectUri'    => $redirectUri,
				'scope'          => $scope,
				'approvalPrompt' => 'force',
				'accessType'     => 'offline',
				'local_dir'      => $pparams->get('google_local_dir', '#home'),
				'remote_dir'     => $pparams->get('google_dir', 'Project :: ' . $this->model->get('alias')),
				'remote_dir_id'  => $pparams->get('google_dir_id', 1),
				'active'         => $pparams->get('google_token', 0)
			),
			'dropbox' => array(
				'servicename'   => 'Dropbox',
				'on'            => $filesParams->get('enable_dropbox', 0),
				'key'           => $filesParams->get('dropbox_key', 0),
				'secret'        => $filesParams->get('dropbox_secret', 0),
				'local_dir'     => $pparams->get('dropbox_local_dir', '#home'),
				'remote_dir'    => $pparams->get('dropbox_dir', 'project_' . $this->model->get('alias')),
				'remote_dir_id' => $pparams->get('dropbox_dir_id', 1),
				'active'        => $pparams->get('dropbox_service', 0)
			)
		);

		$this->_connect = $connect;
	}

	/**
	 * Get configuration to connect with outside services
	 *
	 * @param   string   $service  Service name (google or dropbox)
	 * @param   boolean  $active   Only get active service
	 * @return  boolean  false or array with configs
	 */
	public function getConfigs($service = 'google', $active = true)
	{
		if (!isset($this->_connect[$service]))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_INVALID_SERVICE'));
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
	 * Establish connection
	 *
	 * @param   string   $service  Service name (google or dropbox)
	 * @param   boolean  $reauth   Re-authenticate user?
	 * @param   string   $return   URL to return to after authorization
	 * @return  false or array with configs
	 */
	public function makeConnection($service = 'google', $reauth = false, $return = '')
	{
		if (!in_array($service, $this->_services))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_INVALID_SERVICE'));
			return false;
		}

		$config = $this->_connect[$service];
		$jsession = App::get('session');

		// Make sure we have service
		if (!isset($config) || !$config)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_MISSING_CONFIG'));
			return false;
		}

		// Connect to google (OAUTH2)
		if ($service == 'google')
		{
			$code  = Request::getVar('code', '', 'get');
			$refresh_token = '';

			// We got here from the redirect from a successful authorization grant, fetch the access token
			if ($code)
			{
				$postvals = array(
					'grant_type'    => 'authorization_code',
					'client_id'     => $config['clientId'],
					'client_secret' => $config['clientSecret'],
					'code'          => $code,
					'redirect_uri'  => $config['redirectUri']
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
					if ($this->_uid == $this->model->get('owned_by_user'))
					{
						$this->model->saveParam($service . '_token', $token->refresh_token);
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
				//header('Location: ' . $authUrl);
				App::redirect($authUrl);
				return;
			}
		}

		// Connect to dropbox (OAUTH1)
		if ($service == 'dropbox')
		{
			// TBD
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_DROPBOX_UNAVAIL'));
			return false;
		}
	}

	/**
	 * Start service client
	 *
	 * @param   string   $service  Service name (google or dropbox)
	 * @param   integer  $uid      User ID
	 * @param   boolean  $force
	 * @return  object
	 */
	public function startClient($service = 'google', $uid = 0, $force = false)
	{
		$config = $this->_connect[$service];
		$uid = $uid ? $uid : $this->_uid;
		$access_token = 0;

		// Get access token
		if ($uid == $this->_uid && $force == false)
		{
			// Do we have a client started already?
			if (isset($this->_client[$service]) && $this->_client[$service])
			{
				return $this->_client[$service];
			}

			// Acting user - get token from session
			$jsession = App::get('session');
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
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_AUTH_ERROR'));
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
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_AUTH_ERROR_DETAILED'));
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

		$this->_client[$service] = $client;

		return $client;
	}

	/**
	 * Get service API
	 *
	 * @param   string   $service  Service name (google or dropbox)
	 * @param   integer  $uid      User ID
	 * @return  object
	 */
	public function getAPI ($service = 'google', $uid = 0)
	{
		// Do we have API started already?
		if (isset($this->_api[$service]) && $this->_api[$service] && $uid == $this->_uid)
		{
			return $this->_api[$service];
		}

		$uid = $uid ? $uid : $this->_uid;

		// Is user connected?
		$connected = $this->getStoredParam($service . '_token', $uid);
		$uid = $connected ? $uid : $this->model->get('owned_by_user');

		if (!in_array($service, $this->_services))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_INVALID_SERVICE'));
			return false;
		}

		$config = $this->_connect[$service];

		// Make sure we have service
		if (!isset($config) || !$config)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_MISSING_CONFIG'));
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
			$this->_api[$service] = new Google_Service_Drive($client);
		}
		else
		{
			$this->_api[$service] = null;
		}

		return $this->_api[$service];

	}

	/**
	 * Prepare service for use or confirm it's ready
	 *
	 * @param   string   $service  Service name (google or dropbox)
	 * @param   integer  $uid      User ID
	 * @return  void
	 */
	public function afterConnect($service = 'google', $uid = 0)
	{
		if (!in_array($service, $this->_services))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_INVALID_SERVICE'));
			return false;
		}

		$config = $this->_connect[$service];

		// Make sure we have service
		if (!isset($config) || !$config)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_MISSING_CONFIG'));
			return false;
		}

		// Get and save acting user access profile info
		$profile = $this->getAccessProfile($service, $uid);

		$objO = $this->model->table('Owner');

		// Get email/name pairs of connected project owners
		$connected = $objO->getConnected($this->model->get('id'), $service, $exclude = array($this->model->get('owned_by_user')));

		// Setup remote directory & update permissions
		$dir = $this->getRemoteDirectory($service, $this->model->get('owned_by_user'), $connected);

		return true;
	}

	/**
	 * Get ID of remote directory and store it
	 *
	 * @param   string   $service  Service name (google or dropbox)
	 * @param   integer  $uid      User ID
	 * @param   array    $shared   Array of emails with whom master remote folder is shared
	 * @return  String
	 */
	public function getRemoteDirectory($service = 'google', $uid = 0, $shared = array())
	{
		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_API_UNAVAILABLE'));
			return false;
		}

		// Service config
		$config = $this->_connect[$service];

		// Get ID of user's remote project folder
		$folderID = $config['remote_dir_id'];

		// Is this project owner?
		$owner = $uid == $this->model->get('owned_by_user') ? 1 : 0;

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

			// Create remote project folder if not found (project owner)
			if ($folderID == 1	&& $owner)
			{
				$file = new Google_Service_Drive_DriveFile;
				$file->setMimeType('application/vnd.google-apps.folder');
				$file->setName($config['remote_dir']);

				$createdFolder = $apiService->files->create($file, array(
					'mimeType' => 'application/vnd.google-apps.folder'
				));

				$folderID = $createdFolder['id'];
				if ($folderID)
				{
					$obj = new \Components\Projects\Tables\Project($this->_db);
					$obj->saveParam($this->model->get('id'), $service . '_dir_id', $folderID);
				}
			}

			// Update permissions
			if ($folderID != 1 && !empty($shared))
			{
				// Get current permissions
				$permlist = $apiService->permissions->listPermissions($folderID);
				$permEmails = array();

				// Collect permission names
				foreach ($permlist as $p)
				{
					if ($email = $p->getEmailAddress())
					{
						$permEmails[] = $email;
					}
				}

				// Go through array of connected users
				foreach ($shared as $email => $name)
				{
					// Need to add permission
					if (!in_array($email, $permEmails))
					{
						$permission = new Google_Service_Drive_Permission;
						$permission->setRole('writer');
						$permission->setType('user');
						$permission->setEmailAddress($email);
						$params = array('sendNotificationEmails' => 'false');

						try
						{
							$perm = $apiService->permissions->create($folderID, $permission, $params);
						}
						catch (Exception $e)
						{
							$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_FAILED_SET_PERMISSIONS'));
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
	 * @param   string   $service  Service name (google or dropbox)
	 * @param   integer  $uid      User ID
	 * @return  boolean
	 */
	public function getAccessProfile($service = 'google', $uid = 0)
	{
		$config = $this->_connect[$service];
		$uid = $uid ? $uid : $this->_uid;
		$email = $this->getStoredParam($service . '_email', $uid);

		if ($service == 'google')
		{
			// Start service client
			$client = $this->startClient($service, $uid, true);
			if (!$client)
			{
				return false;
			}

			$oauth2 = new Google_Service_Oauth2($client);

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
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_FAILED_RETRIEVE_PROFILE'));
				return false;
			}
		}

		return true;
	}

	/**
	 * Load remote file metadata
	 *
	 * @param   string   $service  Service name (google or dropbox)
	 * @param   integer  $uid      User ID
	 * @param   string   $id       Remote ID
	 * @return  string
	 */
	public function loadRemoteResource($service = 'google', $uid = 0, $id = 0)
	{
		if (!$id)
		{
			return false;
		}

		$resource = null;

		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_API_UNAVAILABLE'));
			return false;
		}

		if ($service == 'google')
		{
			$resource = Google::loadFile($apiService, $id);
		}

		return $resource;
	}

	/**
	 * Add remote file
	 *
	 * @param   integer  $projectid  Project ID
	 * @param   string   $service    Service name (google or dropbox)
	 * @param   integer  $uid        User ID
	 * @param   object   $file       Models\File
	 * @param   boolean  $convert    Convert for remote editing? (Google only)
	 * @return  array
	 */
	public function addRemoteFile($projectid = null, $service = 'google', $uid = 0, $file = null, $parentId = 0, $convert = false)
	{
		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_API_UNAVAILABLE'));
			return false;
		}

		$newItemId = 0;

		// Parse incoming
		if ($file instanceof \Components\Projects\Models\File)
		{
			$title     = $file->get('name');
			$localPath = $file->get('fullPath');
			$fpath     = $file->get('localPath');
			$mimeType  = $file->getMimeType();
			$md5       = $file->getMd5Hash();
			$parentId  = $parentId ? $parentId : $file->get('remoteParent');
		}
		else
		{
			$title     = $file['title'];
			$localPath = $file['fullPath'];
			$fpath     = $file['local_path'];
			$mimeType  = $file['mimeType'];
			$md5       = $file['md5'];
		}
		if (!$parentId)
		{
			return false;
		}

		// Collector for created item metadata
		$metadata = array();

		// Perform request
		if ($service == 'google')
		{
			$newItemId = Google::insertFile($apiService, $this->_client[$service], $title, $localPath, $mimeType, $parentId, $metadata, $convert);
		}

		// Error!
		if (!$newItemId)
		{
			return null;
		}

		$remote_md5      = isset($metadata) && isset($metadata['md5Checksum']) ? $metadata['md5Checksum'] : null;
		$remote_modified = isset($metadata) && isset($metadata['modifiedDate'])
							? gmdate('Y-m-d H:i:s', strtotime($metadata['modifiedDate'])) : null;
		$remote_format   = isset($metadata) && isset($metadata['mimeType']) ? $metadata['mimeType'] : null;
		$converted       = isset($metadata)
						&& preg_match("/google-apps/", $remote_format)
						&& !preg_match("/.folder/", $remote_format) ? 1 : 0;

		if ($convert == true && !$converted)
		{
			// Retreat
			Google::deleteItem($apiService, $newItemId, true);
			return false;
		}

		if ($converted)
		{
			$g_ext = Google::getGoogleConversionFormat($metadata['mimeType'], false, true);

			$dir = dirname($fpath) != '.' ? dirname($fpath) : '';
			$name = basename($fpath);

			// Get file extention
			$parts = explode('.', $name);
			$ext   = count($parts) > 1 ? array_pop($parts) : '';

			if ($g_ext && $ext != $g_ext)
			{
				$fpath  = $dir ? $dir . DS : '';
				$fpath .= $name . '.' . $g_ext;
			}
		}

		// Update connection record
		$objRFile = new \Components\Projects\Tables\RemoteFile($this->_db);

		$update = $objRFile->updateRecord(
			$projectid, $service, $newItemId,
			$fpath, 'file', $this->_uid, $parentId, $title, $remote_md5,
			$md5, $converted, $remote_format, $mimeType, $remote_modified
		);

		return $newItemId;
	}

	/**
	 * Update remote file
	 *
	 * @param   integer  $projectid  Project ID
	 * @param   string   $service    Service name (google or dropbox)
	 * @param   integer  $uid        User ID
	 * @param   string   $remoteid   Remote resource ID
	 * @param   array    $local      Array of local file info
	 * @param   string   $parentId   Parent folder ID
	 * @param   boolean  $convert    Convert for remote editing? (Google only)
	 * @return  array
	 */
	public function updateRemoteFile($projectid = null, $service = 'google', $uid = 0, $remoteid = 0, $local = array(),  $parentId = 0, $convert = false)
	{
		if (!$projectid || !$remoteid || empty($local) || !$parentId)
		{
			return false;
		}

		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_API_UNAVAILABLE'));
			return false;
		}

		// Parse incoming
		$title     = basename($local['local_path']);
		$localPath = $local['fullPath'];
		$mimeType  = $local['mimeType'];

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
					$success = Google::untrashItem ($apiService, $remoteid);
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
				$success = Google::updateFile($apiService, $this->_client[$service], $remoteid, $title, $localPath, $mimeType, $parentId, $metadata, $convert);
			}
		}

		if (!$success)
		{
			return false;
		}

		$remote_md5 = isset($metadata) && isset($metadata['md5Checksum']) ? $metadata['md5Checksum'] : null;
		$remote_modified = isset($metadata) && isset($metadata['modifiedDate'])
				? gmdate('Y-m-d H:i:s', strtotime($metadata['modifiedDate'])) : null;
		$remote_format = isset($metadata) && isset($metadata['mimeType']) ? $metadata['mimeType'] : null;
		$converted = isset($metadata)
							&& preg_match("/google-apps/", $remote_format)
							&& !preg_match("/.folder/", $remote_format) ? 1 : 0;

		// Update connection record
		$objRFile = new \Components\Projects\Tables\RemoteFile($this->_db);

		$update = $objRFile->updateRecord(
			$projectid, $service, $remoteid,
			$local['local_path'], 'file', $uid, $parentId, $title, $remote_md5,
			$local['md5'], $converted, $remote_format, $local['mimeType'], $remote_modified
		);

		return $success;
	}

	/**
	 * Rename remote file
	 *
	 * @param   integer  $projectid  Project ID
	 * @param   string   $service    Service name (google or dropbox)
	 * @param   integer  $uid        User ID
	 * @param   string   $remoteid   Remote resource ID
	 * @param   array    $local      Array of local file info
	 * @param   string   $parentId   Parent folder ID
	 * @return  array
	 */
	public function renameRemoteItem($projectid = null, $service = 'google', $uid = 0, $remoteid = 0, $local = array(),  $parentId = 0)
	{
		if (!$projectid || !$remoteid || empty($local))
		{
			return false;
		}

		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_API_UNAVAILABLE'));
			return false;
		}

		// Collector for created folder metadata
		$metadata = array();
		$success  = 0;
		$newTitle = basename($local['local_path']);

		// Perform request
		if ($service == 'google')
		{
			$success = Google::patchFile(
				$apiService,
				$remoteid,
				$newTitle,
				'',
				$metadata
			);
		}

		if (!$success)
		{
			return false;
		}

		$remote_md5 = isset($metadata) && isset($metadata['md5Checksum'])
					? $metadata['md5Checksum']
					: null;
		$remote_modified = isset($metadata) && isset($metadata['modifiedDate'])
						? gmdate('Y-m-d H:i:s', strtotime($metadata['modifiedDate']))
						: null;
		$remote_format = isset($metadata) && isset($metadata['mimeType'])
						? $metadata['mimeType']
						: null;

		// Update connection record
		$objRFile = new \Components\Projects\Tables\RemoteFile($this->_db);

		$update = $objRFile->updateRecord(
			$projectid,
			$service,
			$remoteid,
			$local['local_path'],
			$local['type'],
			$uid,
			$parentId,
			$newTitle,
			$remote_md5,
			$local['md5'],
			$local['converted'],
			$remote_format,
			$local['mimeType'],
			$remote_modified
		);

		return $success;
	}

	/**
	 * Move remote file to another parent
	 *
	 * @param   integer  $projectid  Project ID
	 * @param   string   $service    Service name (google or dropbox)
	 * @param   integer  $uid        User ID
	 * @param   string   $remoteid   Remote resource ID
	 * @param   array    $local      Array of local file info
	 * @param   string   $parentId   Parent folder ID
	 * @return  array
	 */
	public function moveRemoteItem($projectid = null, $service = 'google', $uid = 0, $remoteid = 0, $local = array(),  $parentId = 0)
	{
		if (!$projectid || !$remoteid || empty($local) || !$parentId)
		{
			return false;
		}

		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_API_UNAVAILABLE'));
			return false;
		}

		// Collector for created folder metadata
		$metadata = array();
		$success  = 0;

		// Perform request
		if ($service == 'google')
		{
			$success = Google::patchFile($apiService, $remoteid, '', $parentId, $metadata);
		}

		if (!$success)
		{
			return false;
		}

		$remote_md5 = isset($metadata) && isset($metadata['md5Checksum']) ? $metadata['md5Checksum'] : null;
		$remote_modified = isset($metadata) && isset($metadata['modifiedDate'])
				? gmdate('Y-m-d H:i:s', strtotime($metadata['modifiedDate'])) : null;
		$remote_format = isset($metadata) && isset($metadata['mimeType']) ? $metadata['mimeType'] : null;

		// Update connection record
		$objRFile = new \Components\Projects\Tables\RemoteFile($this->_db);

		$update = $objRFile->updateRecord(
			$projectid,
			$service,
			$remoteid,
			$local['local_path'],
			$local['type'],
			$uid,
			$parentId,
			$metadata['title'],
			$remote_md5,
			$local['md5'],
			$local['converted'],
			$remote_format,
			$local['mimeType'],
			$remote_modified
		);

		return $success;
	}

	/**
	 * Delete remote item
	 *
	 * @param	   integer	$projectid	Project ID
	 * @param	   string	$service	Service name (google or dropbox)
	 * @param	   integer	$uid		User ID
	 * @param	   string	$remoteid	Remote resource ID
	 * @param	   boolean	$permanent	Delete permanently? (or trash)
	 *
	 * @return	   array
	 */
	public function deleteRemoteItem($projectid = null, $service = 'google', $uid = 0, $remoteid = 0, $permanent = false)
	{
		if (!$projectid || !$remoteid)
		{
			return false;
		}

		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_API_UNAVAILABLE'));
			return false;
		}

		$success = 0;

		// Perform request
		if ($service == 'google')
		{
			$success = Google::deleteItem($apiService, $remoteid, $permanent);
			$success = Google::deleteAllParents($apiService, $remoteid);
		}

		// Delete connection record
		$objRFile = new \Components\Projects\Tables\RemoteFile($this->_db);

		if ($objRFile->loadItem($projectid, $remoteid, $service))
		{
			$objRFile->delete();
		}

		return $success;
	}

	/**
	 * Create remote folder
	 *
	 * @param   integer  $projectid       Project ID
	 * @param   string   $service         Service name (google or dropbox)
	 * @param   integer  $uid             User ID
	 * @param   string   $title           Folder name
	 * @param   string   $path            Path to folder
	 * @param   string   $parentId        Parent folder ID
	 * @param   array    &$remoteFolders  Collector array
	 * @return  integer (folder ID)
	 */
	public function createRemoteFolder($projectid = null, $service = 'google', $uid = 0, $title = '', $path = '',  $parentId = 0, &$remoteFolders = array())
	{
		if (!$projectid)
		{
			$projectid = $this->model->get('id');
		}

		if (!$projectid || !$title || !$parentId || !$path)
		{
			return false;
		}

		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_API_UNAVAILABLE'));
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
					Google::untrashItem($apiService, $remoteFolders[$path]['remoteid']);
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
				$newParentId = Google::createFolder($apiService, $title, $parentId, $metadata);
			}

			// Error!
			if (!$newParentId)
			{
				return null;
			}

			// Add newly create folder to remoteFolders array
			$remoteFolders[$path] = array(
				'remoteid' => $newParentId,
				'status'   => 'A',
				'parentid' => $parentId
			);

			// Update connection record
			$objRFile = new \Components\Projects\Tables\RemoteFile($this->_db);

			$update = $objRFile->updateRecord(
				$projectid,
				$service,
				$newParentId,
				$path,
				'folder',
				$this->_uid,
				$parentId,
				$title
			);

			return $newParentId;
		}

		return $parentId;
	}

	/**
	 * Prep remote directory structure for syncing file
	 *
	 * @param   integer  $projectid       Project ID
	 * @param   string   $service         Service name (google or dropbox)
	 * @param   integer  $uid             User ID
	 * @param   array    $local           Array of local file info
	 * @param   array    &$remoteFolders  Collector array
	 * @return  integer
	 */
	public function prepRemoteParent($projectid = null, $service = 'google', $uid = 0, $local = array(), &$remoteFolders = array())
	{
		if (!$projectid)
		{
			$projectid = $this->model->get('id');
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
				$title, $path, $parentId, $remoteFolders
			);
		}

		return $parentId;
	}

	/**
	 * Get remote file history
	 *
	 * @param   string   $id       Remote ID
	 * @param   integer  $uid      User ID
	 * @param   string   $service  Service name (google)
	 * @return  array
	 */
	public function getFileHistory($id = 0, $uid = 0, $service = 'google')
	{
		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_API_UNAVAILABLE'));
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
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_FAILED_RETRIEVE_REVISIONS') . ' ' . $id);
				return false;
			}
		}

		return $history;
	}

	/**
	 * Get remote file revision
	 *
	 * @param   string   $id        Remote ID
	 * @param   integer  $revision  remote revision ID
	 * @param   integer  $uid       User ID
	 * @param   string   $service   Service name (google)
	 * @return  array
	 */
	public function getFileRevision($id = 0, $revision = 0, $uid = 0, $service = 'google')
	{
		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_API_UNAVAILABLE'));
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
					$entry['url']      = isset($data['downloadUrl']) ? $data['downloadUrl'] : '';
					$entry['export']   = isset($data['exportLinks']) ? $data['exportLinks'] : array();
					$entry['mimeType'] = $data['mimeType'];
					return json_decode(json_encode($entry));
				}
			}
			catch (Exception $e)
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_FAILED_RETRIEVE_REVISIONS') . ' ' . $id);
				return false;
			}
		}

		return false;
	}

	/**
	 * Sort remote revisions
	 *
	 * @param   integer  $id              Remote resource ID
	 * @param   integer  $converted       Converted for remote editing?
	 * @param   string   $lastModifiedBy  Last modified by (name)
	 * @param   integer  $uid             User ID
	 * @param   string   $service         Service name (google or dropbox)
	 * @param   string   $file            File path
	 * @param   array    &$versions       Versions collector array
	 * @param   array    &$timestamps     Collector array
	 * @param   integer  $original        Source file?
	 * @return  array
	 */
	public function sortRemoteRevisions($id, $converted = 0, $lastModifiedBy = '', $uid , $service, $file, &$versions = array(), &$timestamps = array(), $original = 0)
	{
		// Get remote revisions
		$revisions = $this->getFileHistory($id, $uid, $service);

		// Go through remote revisions
		if (!empty($revisions))
		{
			$timestampsG = array();
			foreach ($revisions as $rev)
			{
				$timestampsG[] = strtotime($rev['modifiedDate']);
			}
			array_multisort($timestampsG, SORT_DESC, $revisions);

			$r = 1;
			foreach ($revisions as $rev)
			{
				$order = $r == count($revisions) ? 'first' : '';
				$order = $r == 1 ? 'last' : $order;

				// If remote, make sure current version is remote
				if ($converted == 1 && $order == 'last')
				{
					$timestamps[] = Date::toUnix();
				}
				else
				{
					$timestamps[] = strtotime($rev['modifiedDate']);
				}

				$author = isset($rev['lastModifyingUser']['displayName'])
					? strtolower($rev['lastModifyingUser']['displayName'])
					: null;
				$author = !$author && isset($rev['lastModifyingUserName'])
					? strtolower($rev['lastModifyingUserName'])
					: null;

				if (!$author && $order == 'last')
				{
					$author = utf8_decode($lastModifiedBy);
				}

				$revision = array(
					'date'         => $rev['modifiedDate'],
					'author'       => $author,
					'email'        => null,
					'hash'         => $rev['id'],
					'file'         => $file,
					'base'         => '',
					'remote'       => $id,
					'local'        => false,
					'content'      => null,
					'preview'      => null,
					'original'     => $original,
					'hide'         => 0,
					'message'      => '',
					'rename'       => '',
					'change'       => '',
					'movedTo'      => '',
					'size'         => '',
					'name'         => Lang::txt('PLG_PROJECTS_FILES_REMOTE_FILE_' . strtoupper($service)),
					'order'        => $order,
					'count'        => count($revisions),
					'commitStatus' => $r == count($revisions) ? 'A' : 'M'
				);

				$versions[] = $revision;
				$r++;
			}
		}
	}

	/**
	 * Save connection of local file after being exported
	 *
	 * @param   integer  $projectid        Project ID
	 * @param   string   $service          Service name (google)
	 * @param   string   $id               Remote ID
	 * @param   string   $path             File path
	 * @param   integer  $paired           Paired record ID
	 * @param   string   $original_path	   Original path
	 * @param   string   $original_format  Original MIME type
	 * @param   string   $original_id      Original ID
	 * @return  mixed False if error, Object on success
	 */
	public function savePairing($projectid = null, $service = '', $id = null, $path = '', $paired = 0, $original_path = '', $original_format = '', $original_id = '')
	{
		if (!$projectid || !$id)
		{
			return false;
		}

		$objRFile = new \Components\Projects\Tables\RemoteFile($this->_db);

		if ($objRFile->loadItem($projectid, $id, $service, $path))
		{
			$objRFile->paired          = $paired;
			$objRFile->original_path   = $original_path;
			$objRFile->original_format = $original_format;
			$objRFile->original_id     = $original_id;
			$objRFile->store();
		}

		return true;
	}

	/**
	 * Fix connection record(s) after local change to parent folder
	 *
	 * @param   string   $service   Service name (google)
	 * @param   integer  $uid       User ID
	 * @param   string   $dir       Directory path
	 * @param   string   $action    Action
	 * @param   string   $newdir    New directory path
	 * @param   string   $parentId  Parent ID
	 * @return  array
	 */
	public function fixConvertedItems($service = 'google', $uid = 0, $dir = '', $action = '', $newdir = '', $parentId = '')
	{
		if (!$dir || !$action)
		{
			return false;
		}

		$objRFile = new \Components\Projects\Tables\RemoteFile($this->_db);
		$converted = $objRFile->getRemoteConnections($this->model->get('id'), $service, $dir, 1);

		if (!empty($converted['paths']))
		{
			foreach ($converted['paths'] as $c)
			{
				// Delete record
				if ($action == 'D')
				{
					$objRFile->deleteRecord(
						$this->model->get('id'),
						$service,
						$c['remote_id']
					);
				}
				elseif ($newdir)
				{
					// Update dir path
					$fpath = $newdir . DS . basename($c['path']);

					$update = $objRFile->updateRecord(
						$this->model->get('id'),
						$service,
						$c['remote_id'],
						$fpath,
						$c['type'],
						$uid,
						$parentId
					);
				}
			}
		}
	}

	/**
	 * Get remote changes
	 *
	 * @param   string   $service        Service name (google or dropbox)
	 * @param   integer  $uid            User ID
	 * @param   string   $startChangeId  Last Change ID
	 * @param   array    $remotes        Collector array for active items
	 * @param   array    $deletes        Collector array for deleted items
	 * @param   array    $connections    Array of local-remote connections
	 * @param   string   $path           Path
	 * @return  integer  change ID
	 */
	public function getChangedItems($service = 'google', $uid = 0, $startChangeId = null, &$remotes, &$deletes, $connections = array(), $path = '')
	{
		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			if (!$this->getError())
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_API_UNAVAILABLE'));
			}
			return false;
		}

		$changeID = null;

		if ($service == 'google')
		{
			// Get remote folder ID
			$folderID = $this->getConfigParam($service, 'remote_dir_id');

			$changeID = Google::collectChanges($apiService, $folderID, $remotes, $deletes, $path, $startChangeId, $connections);
		}

		return $changeID;
	}

	/**
	 * Get all remote files
	 *
	 * @param   string   $service      Service name (google or dropbox)
	 * @param   integer  $uid          User ID
	 * @param   string   $since        last sync date
	 * @param   array    $connections  Array of local-remote connections
	 * @return  array
	 */
	public function getRemoteItems($service = 'google', $uid = 0, $since = '', $connections = array())
	{
		// Get api
		$apiService = $this->getAPI($service, $uid);
		$remotes = array();

		if (!$apiService)
		{
			if (!$this->getError())
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_API_UNAVAILABLE'));
			}
			return false;
		}

		if ($service == 'google')
		{
			// Get remote folder ID
			$folderID = $this->getConfigParam($service, 'remote_dir_id');

			// Need remote folder
			if (!$folderID)
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_FAILED_SERVICE_UNAVAILABLE'));
				return false;
			}

			// Make sure master folder is there and not trashed
			try
			{
				$folder = $apiService->files->get($folderID);
				if ($folder && $folder['labels']['trashed'] == 1)
				{
					// Untrash
					Google::untrashItem($apiService, $folderID);
				}
			}
			catch (Exception $e)
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_FOLDER_UNAVAILABLE'));
				return false;
			}

			// Collect remote items with duplicate names
			$duplicates = array();

			// Get files in main project remote directory
			$remotes = Google::getFolderContent(
				$apiService,
				$folderID,
				$remotes,
				'',
				$since,
				$connections,
				$duplicates
			);
		}

		return $remotes;

	}

	/**
	 * Get remote folder structure
	 *
	 * @param   string   $service         Service name (google or dropbox)
	 * @param   integer  $uid             User ID
	 * @param   array    &$remoteFolders  Collector array
	 * @param   string   $path
	 * @return  bool
	 */
	public function getFolderStructure($service = 'google', $uid = 0, &$remoteFolders, $path = '')
	{
		// Get api
		$apiService = $this->getAPI($service, $uid);

		if (!$apiService)
		{
			if (!$this->getError())
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_API_UNAVAILABLE'));
			}
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
					Google::untrashItem($apiService, $folderID);
				}
				if ($folder && $folder['title'] != $folderName)
				{
					// Save renamed reference
					$obj = new \Components\Projects\Tables\Project($this->_db);
					$obj->saveParam($this->model->get('id'), $service . '_dir', $folder['title']);
				}
			}
			catch (Exception $e)
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_SERVICE_UNAVAILABLE'));
				return false;
			}

			Google::getFolders($apiService, $folderID, $remoteFolders, $path);
		}

		return true;
	}

	/**
	 * Send httpRequest
	 *
	 * @param   string   $service  Service name (google or dropbox)
	 * @param   integer  $uid      User ID
	 * @param   string   $url      Request URL
	 * @return  string
	 */
	public function sendHttpRequest($service = 'google', $uid = 0, $url = '')
	{
		$content = null;

		if (!$url)
		{
			return false;
		}

		// Need service client
		$client = $this->startClient($service, $uid, true);

		if (!$client)
		{
			return false;
		}

		if ($service == 'google')
		{
			// Make Http request
			$request = new Google_Http_Request($url, 'GET', null, null);

			// Get remote content
			$request = $client->getAuth()->sign($request);
			$httpRequest = $client->getIo()->makeRequest($request);

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
	 * @param   string  $service  Service name (google or dropbox)
	 * @param   string  $remote   Remote resource array
	 * @param   string  $path     Project repo path
	 * @return  string or boolean
	 */
	public function downloadFileCurl($service = 'google', $uid = 0, $url = '', $path = '')
	{
		if (!$url || !$path)
		{
			return false;
		}

		if ($service =='google' || $service instanceof Google_Service_Drive)
		{
			$apiService = $this->getAPI($service, $uid);
			$response = $apiService->files->get($url, array('alt' => 'media'));
			$content = $response->getBody()->getContents();
			file_put_contents($path, $content);
			return true;
		}

		// Need service client
		$client = $this->startClient($service, $uid, true);
		if (!$client)
		{
			return false;
		}

		$fp = fopen($path, 'w');

		// Make Http request
		$request = new Google_Http_Request($url, 'GET', null, null);

		$request = $client->getAuth()->sign($request);
		$request = $client->getIo()->makeRequest($request);

		//Initialize the Curl Session.
		$ch = curl_init();

		// Set the Curl url.
		curl_setopt ($ch, CURLOPT_URL,	$request->getUrl());

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
	 * @param   string   $service     Service name (google or dropbox)
	 * @param   integer  $uid         User ID
	 * @param   array    $remote      Remote resource array
	 * @param   string   $path        Project repo path
	 * @param   boolean  $getContent  Output file content? Or write to file
	 * @return  string or boolean
	 */
	public function downloadFile($service = 'google', $uid = 0, $remote = array(), $path = '', $getContent = false)
	{
		if (empty($remote) || !$remote['url'] || !$path || !is_dir($path))
		{
			return false;
		}
		$fpath = $remote['local_path'];
		$url   = $remote['url'];

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
	 * @param   string   $service     Service name (google or dropbox)
	 * @param   integer  $uid         User ID
	 * @param   array    $remote      Remote resource array
	 * @param   string   $fpath       File path relative to repo path
	 * @param   string   $path        Project repo path
	 * @param   string   $ext         Extension
	 * @param   boolean  $getContent  Output file content? Or write to file
	 * @return  string or boolean
	 */
	public function importFile($service = 'google', $uid = 0, $remote = array(), $fpath = '', $path = '', $ext = '', $getContent = false)
	{
		if (empty($remote) || !$fpath || !is_dir($path) || !isset($remote['exportLinks']))
		{
			return false;
		}

		$url = '';
		$default_type = Google::getGoogleExportType($ext);
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
			$fc = preg_replace('/[^(\x20-\x7F)\x0A]*/', '', $fc);
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
	 * @param   string  $fc     File content
	 * @param   string  $fpath  File path relative to repo path
	 * @param   string  $path   Project repo path
	 * @return  void
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
	 * @param   string   $service  Service name (google or dropbox)
	 * @param   integer  $uid      User ID
	 * @param   array    $remote   Remote resource array
	 * @param   array    $config   Configuration array
	 * @param   string   $alias    Project alias name
	 * @return  bool
	 */
	public function generateThumbnail($service = 'google', $uid = 0, $remote = array(), $config = array(), $alias = null)
	{
		if (empty($remote) || !$remote['thumb'] || empty($config) || !$alias)
		{
			return false;
		}

		$thumb = substr($remote['remoteid'], 0, 20) . '_' . strtotime($remote['modified']) . '.png';

		$imagepath = trim($config->get('imagepath', '/site/projects'), DS);
		$to_path = DS . $imagepath . DS . strtolower($alias) . DS . 'preview';

		if (is_dir(PATH_APP . $to_path) && !is_file(PATH_APP . $to_path . DS . $thumb))
		{
			// Get thumnail
			$fc = $this->sendHttpRequest($service, $uid, $remote['thumb']);

			if ($fc && $this->fetchFile($fc, $thumb, PATH_APP . $to_path))
			{
				$handle = @fopen(PATH_APP . $to_path . DS . $thumb, 'w');

				if ($handle)
				{
					fwrite($handle, $fc);
					fclose($handle);

					// Resize image
					$hi = new \Hubzero\Image\Processor(PATH_APP . $to_path . DS . $thumb);
					if (count($hi->getErrors()) == 0)
					{
						$hi->resize(180, false, false, true);
						$hi->save(PATH_APP . $to_path . DS . $thumb);
					}
					else
					{
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Get stored auth token for service
	 *
	 * @param   string   $param  Param name
	 * @param   integer  $uid    User ID
	 * @return  string
	 */
	public function getStoredParam($param, $uid = 0)
	{
		$uid = $uid ? $uid : $this->_uid;
		$objO = $this->model->table('Owner');
		$objO->loadOwner($this->model->get('id'), $uid);
		$params = new \Hubzero\Config\Registry($objO->params);
		return $params->get($param);
	}

	/**
	 * Store auth token for service
	 *
	 * @param   string   $param  Param name
	 * @param   string   $value  Param value
	 * @param   integer  $uid    User ID
	 * @return  void
	 */
	public function storeParam($param = '', $value = '', $uid = 0)
	{
		$uid = $uid ? $uid : $this->_uid;

		$objO = $this->model->table('Owner');
		$objO->saveParam(
			$this->model->get('id'),
			$uid,
			$param,
			$value
		);
	}

	/**
	 * Run cURL
	 *
	 * @param   string  $url
	 * @param   string  $method
	 * @param   array   $postvals
	 * @return  response string
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
	 * @param   string  $service  Service name (google or dropbox)
	 * @param   string  $return   URL to return to after authorization
	 * @return  string  URL
	 */
	public function createAuthUrl($service, $return = '')
	{
		if (!$service || !in_array($service, $this->_services))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_INVALID_SERVICE'));
			return false;
		}

		$config = $this->_connect[$service];

		// Make sure we have service
		if (!isset($config) || !$config)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_MISSING_CONFIG'));
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

			$return = $return ? $return : Route::url($this->model->link('files'));

			$array = array(
				'alias'	 => $this->model->get('alias'),
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
	 * @param   string  $service       Service name (google or dropbox)
	 * @param   string  $refreshToken  Stored refresh token
	 * @return  mixed   JSON string or false
	 */
	public function refreshToken($service, $refreshToken = '')
	{
		if (!$service || !in_array($service, $this->_services))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_INVALID_SERVICE'));
			return false;
		}

		$config = $this->_connect[$service];
		if (!$refreshToken)
		{
			$this->setError(Lang::txt('Missing refresh token for service'));
			return false;
		}

		// Make sure we have service
		if (!isset($config) || !$config)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_MISSING_CONFIG'));
			return false;
		}

		// Google service
		if ($service == 'google')
		{
			// construct POST object required for refresh token fetch
			$postvals = array(
				'grant_type'    => 'refresh_token',
				'client_id'     => $config['clientId'],
				'client_secret' => $config['clientSecret'],
				'refresh_token' => $refreshToken
			);

			// return JSON refreshed access token object
			try
			{
				return json_decode($this->runCurl(self::GOOGLE_OAUTH2_TOKEN_URI, 'POST', $postvals));
			}
			catch (Exception $e)
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_AUTH_ERROR'));
				return false;
			}
		}

		return false;
	}

	/**
	 * Disconnect user from service
	 *
	 * @param   string   $service  Service name (google or dropbox)
	 * @param   integer  $uid
	 * @param   itneger  $remoteid
	 * @return  mixed    JSON string or false
	 */
	public function disconnectMember($service, $uid = 0, $remoteid = 0)
	{
		if (!$uid)
		{
			return false;
		}

		$objO = $this->model->table('Owner');
		$objO->loadOwner($this->model->get('id'), $uid);

		$creator = $uid == $this->model->get('owned_by_user') ? 1 : 0;

		// Get connection email & name
		$email = $this->getStoredParam($service . '_email', $uid);
		$name  = $this->getStoredParam($service . '_name', $uid);

		// Remove token
		$objO->saveParam(
			$this->model->get('id'),
			$uid,
			$param = $service . '_token',
			''
		);

		// Remove time
		$objO->saveParam(
			$this->model->get('id'),
			$uid,
			$param = $service . '_token_created',
			''
		);

		// Remove email
		$objO->saveParam(
			$this->model->get('id'),
			$uid,
			$param = $service . '_email',
			''
		);

		// Remove name
		$objO->saveParam(
			$this->model->get('id'),
			$uid,
			$param = $service . '_name',
			''
		);

		// Remove user id
		$objO->saveParam(
			$this->model->get('id'),
			$uid,
			$param = $service . '_userid',
			''
		);

		if (!$email || !$name || !$remoteid)
		{
			return;
		}

		// Get api
		$apiService = $this->getAPI($service, $this->model->owner('id'));

		if (!$apiService)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_API_UNAVAILABLE'));
			return false;
		}

		// Remove permission
		if ($service == 'google' && !$creator)
		{
			Google::clearPermissions($apiService, array($name => $email), $remoteid);
		}
	}

	/**
	 * Disconnect user from service
	 *
	 * @param   string   $service     Service name (google or dropbox)
	 * @param   boolean  $removeData  Remove remote data (when project creator)
	 * @return  mixed    JSON string or false
	 */
	public function disconnect($service, $removeData = false)
	{
		if (!$service || !in_array($service, $this->_services))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_INVALID_SERVICE'));
			return false;
		}

		$config = $this->_connect[$service];

		// Make sure we have service
		if (!isset($config) || !$config)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_MISSING_CONFIG'));
			return false;
		}

		// Load project
		$obj = new \Components\Projects\Tables\Project($this->_db);
		if (!$obj->loadProject($this->model->get('id')))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_PROBLEM_LOADING_DATA'));
			return false;
		}

		// Get project params
		$pparams = $this->model->params;

		// Remove all connection info and remote data
		$remoteid = $pparams->get($service . '_dir_id');

		// Project creator?
		$creator = $this->_uid == $this->model->get('owned_by_user')  ? 1 : 0;

		// Clean up stored values for the disconnecting member
		$this->disconnectMember($service, $this->_uid, $remoteid);

		// Disconnect service if user is project creator
		if ($creator)
		{
			if ($removeData == true && $remoteid)
			{
				// Disconnect all members
				$objO = $this->model->table('Owner');
				$owners = $objO->getOwners($this->model->get('id'), $filters = array('connected' => 1));

				if ($owners)
				{
					foreach ($owners as $owner)
					{
						// Clean up stored values for the disconnecting member
						$this->disconnectMember($service, $owner->userid, $remoteid);
					}
				}

				$this->deleteRemoteItem($this->model->get('id'), $service, $this->_uid, $remoteid, $permanent = true);

				$obj->saveParam($this->model->get('id'), $service . '_sync', '');
				$obj->saveParam($this->model->get('id'), $service . '_last_remote_change', '');
				$obj->saveParam($this->model->get('id'), $service . '_last_local_change', '');
				$obj->saveParam($this->model->get('id'), $service . '_sync_id', '');
				$obj->saveParam($this->model->get('id'), $service . '_prev_sync_id', '');
				$obj->saveParam($this->model->get('id'), $service . '_sync_lock', '');
				$obj->saveParam($this->model->get('id'), $service . '_sync_queue', '');
				$obj->saveParam($this->model->get('id'), $service . '_dir_id', '');
			}

			// Clean up token
			$obj->saveParam($this->model->get('id'), $service . '_token', 0);
		}

		return true;
	}
}
