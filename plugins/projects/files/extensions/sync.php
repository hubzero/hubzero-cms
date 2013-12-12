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

require_once( JPATH_ROOT . DS . 'plugins' . DS . 'projects' . DS . 'files' . DS . 'files.php');	

/**
 * Extension to Projects Files plugin for sync with external services
 */
class plgProjectsFilesSync extends plgProjectsFiles
{	
	/**
	 * Manage connections to outside services
	 * 
	 * @param      string	$service	Service name (google/dropbox)
	 * @param      string	$callback	URL to return to after authorization
	 * @return     string
	 */	
	public function connect($service = '', $callback = '') 
	{			
		// Incoming
		$service 	= $service ? $service : JRequest::getVar('service', '');
		$reauth 	= JRequest::getInt('reauth', 0);
		$removeData = JRequest::getInt('removedata', 0);
		
		// Build pub url
		$route = 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias;							
		$url = JRoute::_($route . a . 'active=files');
						
		// Build return URL
		$return = $callback ? $callback : $url . '?action=connect';
		
		// Handle authentication request for service
		if ($service)
		{
			$configs = $this->_connect->getConfigs($service, false);
			
			if ($this->_task == 'disconnect')
			{
				if ($this->_connect->disconnect($service, $removeData))
				{
					$this->_msg = JText::_('You got disconnected from ') . $configs['servicename'];
				}
				else 
				{
					$this->setError($this->_connect->getError());
				}
				
				// Redirect to connect screen
				$this->_referer = $url . '?action=connect';
				return;
			}			
			elseif (!$this->_connect->makeConnection($service, $reauth, $return))
			{
				$this->setError($this->_connect->getError());
			}
			else
			{								
				// Successful authentication				
				if (!$this->_connect->afterConnect($service))
				{
					$this->setError($this->_connect->getError());
				}
				else
				{
					$this->_msg = JText::_('Successfully connected');
				}
			}
			
			// Refresh info
			$this->_connect->setConfigs();
		}
			
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'files',
				'name'=>'connect'
			)
		);
		
		$view->params 		= new JParameter($this->_project->params);
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->route		= $route;
		$view->url 			= $url;
		$view->title		= $this->_area['title'];
		$view->services		= $this->_connect->getVar('_services');
		$view->connect		= $this->_connect;
		
		// Get connection details for user
		$objO = new ProjectOwner( $this->_database );
		$objO->loadOwner ($this->_project->id, $this->_uid);
		$view->oparams = new JParameter( $objO->params );
		
		// Get messages	and errors	
		$view->msg = $this->_msg;
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();		
	}
	
	/**
	 * Initiate sync
	 * 
	 * @return   void
	 */
	public function iniSync() 
	{				
		// Get path
		$path = $this->getProjectPath();
		
		// Incoming
		$ajax 	 = JRequest::getInt('ajax', 0);
		$auto 	 = JRequest::getInt('auto', 0);
		$queue 	 = JRequest::getInt('queue', 0);
		
		$pparams = new JParameter( $this->_project->params );		
		
		// Timed sync?
		$autoSync = $this->_params->get('auto_sync', 0);
						
		// Remote service(s) active?
		if (!empty($this->_rServices) && $this->_case == 'files')
		{
			// Get remote files for each active service
			foreach ($this->_rServices as $servicename)
			{							
				// Set syncing service
				$this->_rSync['service'] = $servicename;
				
				// Get time of last sync
				$synced = $pparams->get($servicename . '_sync');
				
				// Stop if auto sync request and not enough time passed
				if ($auto && $autoSync && !$queue)
				{
					if ($autoSync < 1)
					{
						$hr = 60 * $autoSync;
						$timecheck = JFactory::getDate(time() - (1 * $hr * 60));
					}
					else
					{
						$timecheck = JFactory::getDate(time() - ($autoSync * 60 * 60));
					}

					if ($synced > $timecheck)
					{
						return json_encode(array('status' => 'waiting'));					
					}
				}
				
				// Send sync request
				$success = plgProjectsFilesSync::_sync( $servicename, $path, $queue, $auto);
				
				// Unlock sync
				if ($success)
				{
					plgProjectsFilesSync::lockSync($servicename, true);
				}
				
				// Success message
				$this->_rSync['message'] = JText::_('Successfully synced');
			}		
		}
		
		$this->_rSync['auto'] = $auto;
										
		if (!$ajax)
		{
			return $this->view();	
		}
		else
		{			
			$this->_rSync['output'] = $this->view();
			return json_encode($this->_rSync);
		}		
	}
		
	/**
	 * Sync local and remote changes since last sync
	 * 
	 * @param    string		$service	Remote service name
	 * @param    string		$path		Local project path
	 * @return   void
	 */
	protected function _sync ($service = 'google', $path = '', $queue = false, $auto = false) 
	{
		$path = $path ? $path : $this->getProjectPath();
										
		// Lock sync
		if (!plgProjectsFilesSync::lockSync($service, false, $queue))
		{	
			// Return error
			if ($auto == false) 
			{
				$this->_rSync['error'] = JText::_('Sync in progress or delayed. Please wait several minutes for a new sync request.');
			}
			
			return false;
		}
		
		// Clean up status
		$this->_writeToFile('');
				
		// Record sync status
		$this->_writeToFile(ucfirst($service) . ' '. JText::_('sync started') );		
								
		// Get time of last sync
		$obj = new Project( $this->_database );
		$obj->load($this->_project->id);
		$pparams = new JParameter( $obj->params );		
		$synced = $pparams->get($service . '_sync', 1);
		
		// Get disk usage
		$diskUsage = $this->getDiskUsage($path, $this->prefix, $this->_usageGit);
		$quota 	   = $pparams->get('quota')
					? $pparams->get('quota')
					: ProjectsHtml::convertSize( floatval($this->_config->get('defaultQuota', '1')), 'GB', 'b');
		$avail 	   = $quota - $diskUsage;
		
		// Last synced remote/local change
		$lastRemoteChange = $pparams->get($service . '_last_remote_change', NULL);
		$lastLocalChange  = $pparams->get($service . '_last_local_change', NULL);
		
		// Get last change ID for project creator
		$lastSyncId = $pparams->get($service . '_sync_id', NULL);
		$prevSyncId = $pparams->get($service . '_prev_sync_id', NULL);	
		
		// User ID of project creator
		$projectCreator = $this->_project->created_by_user;
										
		// Are we syncing project home directory or other?
		$localDir   = $this->_connect->getConfigParam($service, 'local_dir');
		$localDir   = $localDir == '#home' ? '' : $localDir;
		
		$localPath  = $this->prefix . $path;
		$localPath .= $localDir ? DS . $localDir : '';
		
		// Record sync status
		$this->_writeToFile(JText::_('Establishing remote connection') );		
		
		// Get service API - allways project creator!
		$this->_connect->setUser($projectCreator);
		$this->_connect->getAPI($service, $projectCreator);
									
		// Collector arrays
		$locals 		= array();
		$remotes 		= array();
		$localFolders 	= array();
		$remoteFolders 	= array();
		$failed			= array();
		$deletes		= array();
		$timedRemotes	= array();
						
		// Sync start time
		$startTime =  date('c');
		$passed    = $synced != 1 ? ProjectsHtml::timeDifference(strtotime($startTime) - strtotime($synced)) : 'N/A';
				
		// Start debug output
		$output  = ucfirst($service) . "\n";
		$output .= $synced != 1 ? 'Last sync (local): ' . $synced . ' | (UTC): ' . gmdate('Y-m-d H:i:s', strtotime($synced)) . "\n" : "";
		$output .= 'Previous sync ID: ' . $prevSyncId . "\n";
		$output .= 'Current sync ID: ' . $lastSyncId . "\n";
		$output .= 'Last synced remote change: '.  $lastRemoteChange . "\n";
		$output .= 'Last synced local change: '.  $lastLocalChange . "\n";
		$output .= 'Time passed since last sync: ' . $passed . "\n";		
		$output .= 'Local sync start time: '.  $startTime . "\n";
		$output .= 'Initiated by (user ID): '.  $this->_uid . ' [';
		$output .= ($auto == true) ? 'Auto sync' : 'Manual sync request';
		$output .= ']' . "\n";
		
		// Record sync status
		$this->_writeToFile(JText::_('Getting remote directory structure') );
				
		// Get stored remote connections
		$objRFile = new ProjectRemoteFile ($this->_database);	
		$connections = $objRFile->getRemoteConnections($this->_project->id, $service);
				
		// Get remote folder structure (to find out remote ids)
		$this->_connect->getFolderStructure($service, $projectCreator, $remoteFolders);
				
		// Record sync status
		$this->_writeToFile( JText::_('Collecting local changes') );
						
		// Collector for local renames
		$localRenames = array();
		
		$fromLocal = ($synced == $lastLocalChange || !$lastLocalChange) ? $synced : $lastLocalChange;
		
		// Get all local changes since last sync
		$locals = $this->_git->getChanges($path, $localPath, $fromLocal, $localDir, $localRenames, $connections );
											
		// Record sync status
		$this->_writeToFile( JText::_('Collecting remote changes') );
						
		// Get all remote files that changed since last sync
		$newSyncId  = 0;	
		$nextSyncId = 0;		
		if ($lastSyncId > 1)
		{
			// Via Changes feed
			$newSyncId = $this->_connect->getChangedItems($service, $projectCreator, 
				$lastSyncId, $remotes, $deletes, $connections);
		}
		else
		{
			// Via List feed
			$remotes = $this->_connect->getRemoteItems($service, $projectCreator, '', $connections);
			$newSyncId = 1;
		}
		
		// Record sync status
		$this->_writeToFile( JText::_('Verifying remote changes') );
		
		// Possible that we've missed a change?
		if ( $newSyncId > $lastSyncId )
		{
			$output .= '!!! Changes detected - new change ID: ' . $newSyncId . "\n";
		}
		else
		{
			$output .= '>>> Returned change ID: ' . $newSyncId . "\n";
		}
		
		$output .= empty($remotes) 
				? 'No changes brought in by Changes feed' . "\n"
				: 'Changes feed has ' . count($remotes) . ' changes' . "\n";
		
		$from = ($synced == $lastRemoteChange || !$lastRemoteChange) 
				? date("c", strtotime($synced) - (1)) : date("c", strtotime($lastRemoteChange));
		
		// Get changes via List feed (to make sure we get ALL changes)
		// We need this because Changes feed is not 100% reliable
		if ( $newSyncId > $lastSyncId)
		{
			$timedRemotes = $this->_connect->getRemoteItems($service, $projectCreator, $from, $connections);
		}		
		
		// Record timed remote changes (for debugging)
		if (!empty($timedRemotes))
		{
			$output .= 'Timed remote changes since ' . $from . ' (' . count($timedRemotes) . '):' . "\n";
			foreach ($timedRemotes as $tr => $trinfo)
			{
				$output .= $tr . ' changed ' . date("c", $trinfo['time']) . ' status ' . $trinfo['status'] . ' ' . $trinfo['fileSize'] . "\n";
			}
			
			// Pick up missed changes			
			if ($remotes != $timedRemotes)
			{
				$output .= empty($remotes) 
					? 'Using exclusively timed changes ' . "\n"
					: 'Mixing in timed changes ' . "\n";
				
				$remotes = array_merge($remotes, $timedRemotes);
				array_unique($remotes);
			}
		}
		else
		{
			$output .= 'No timed changes since ' . $from . "\n";
		}
						
		// Error!
		if ($lastSyncId > 1 && !$newSyncId)
		{
			$this->_rSync['error'] = 'Oups! Unknown sync error. Please try again at a later time.';
			return false;
		}
		
		if ($this->_connect->getError())
		{
			$this->_rSync['error'] = 'Oups! Sync error: ' . $this->_connect->getError();
			return false;
		}
								
		// Collector arrays for processed files
		$processedLocal 	= array();
		$processedRemote 	= array();
		$conflicts			= array();
		
		// Record sync status
		$this->_writeToFile( JText::_('Exporting local changes') );
		
		$output .= 'Local changes:' . "\n";
				
		// Go through local changes
		if (count($locals) > 0)
		{	
			$lChange = NULL;
			foreach ($locals as $filename => $local)
			{							
				// Record sync status
				$this->_writeToFile(JText::_('Syncing ') . ' ' . ProjectsHTML::shortenFileName($filename, 30) );
				
				$output .= ' * Local change ' . $filename . ' - ' . $local['status'] . ' - ' . $local['modified'] . ' - ' . $local['time'] . "\n";
				
				// Get latest change
				$lChange = $local['time'] > $lChange ? $local['time'] : $lChange;
				
				// Skip renamed files (local renames are handled later)
				if (in_array($filename, $localRenames) && !file_exists($local['fullPath']))
				{
					$output .= '## skipped rename from '. $filename . "\n";
					continue;
				}
				
				// Do we have a matching remote change?
				$match = !empty($remotes) 
					&& isset($remotes[$filename]) 
					&& $local['type'] == $remotes[$filename]['type'] 
					? $remotes[$filename] : NULL;
					
				// Check against individual item sync time (to avoid repeat sync)
				if ($local['synced'] && ($local['synced']  > $local['modified']))
				{
					$output .= '## item in sync: '. $filename . ' local: ' 
						. $local['modified'] . ' synced: ' . $local['synced'] . "\n";
					$processedLocal[$filename] = $local;
					continue;	
				}
												
				// Item renamed
				if ($local['status'] == 'R')
				{					
					if ($local['remoteid'])
					{
						// Rename remote item
						$renamed = $this->_connect->renameRemoteItem( 
							$this->_project->id, $service, $projectCreator, 
							$local['remoteid'], $local,  $local['rParent']
						);
						
						$output .= '>> renamed ' . $local['rename'] . ' to ' . $filename . "\n";
						$processedLocal[$filename] = $local;
						
						if ($local['type'] == 'folder')
						{
							$this->_connect->fixConvertedItems($service, $this->_uid, $local['rename'], 'R', $filename);
						}
						
						continue;
					}					
				}				
				// Item moved
				if ($local['status'] == 'W')
				{
					if ($local['remoteid'])
					{
						// Determine new remote parent
						$parentId = $this->_connect->prepRemoteParent($this->_project->id, $service, $projectCreator, $local, $remoteFolders);
						
						if ($parentId != $local['rParent'])
						{
							// Move to new parent
							$moved = $this->_connect->moveRemoteItem( 
								$this->_project->id, $service, $projectCreator, 
								$local['remoteid'], $local,  $parentId
							);	
							
							$output .= '>> moved ' . $local['rename'] . ' to ' . $filename . ' (new parent id ' 
								. $parentId . ')' . "\n";
							$processedLocal[$filename] = $local;
							
							if ($local['type'] == 'folder')
							{
								$this->_connect->fixConvertedItems($service, $this->_uid, $local['rename'], 'W', $filename, $parentId);
							}
							
							continue;
						}
					}										
				}
				
				// Check for match in remote changes
				if ($match && (($match['time'] - strtotime($from)) > 0))
				{					
					// skip - remote change prevails
					$output .= '== local and remote change match (choosing remote over local): '. $filename . "\n";
					$conflicts[$filename] = $local['remoteid'];
				}
				else
				{				
					// Local change needs to be transferred
					if ($local['status'] == 'D')
					{
						$deleted   = 0;
						
						// Delete operation
						if ($local['remoteid'])
						{													
							// Delete remote file						
							$deleted = $this->_connect->deleteRemoteItem( 
								$this->_project->id, $service, $projectCreator, 
								$local['remoteid'], false
							);
							
							// Delete from remote
							$output .= '-- deleted from remote: '. $filename . "\n";
						}
						else
						{
							// skip (deleted non-synced file)
							$output .= '## skipped deleted non-synced item: '. $filename . "\n";
							$deleted = 1;
						}
						
						if ($local['type'] == 'folder')
						{
							$this->_connect->fixConvertedItems($service, $this->_uid, $filename, 'D');
						}
						
						// Delete connection record if exists
						if ($deleted)
						{
							$objRFile = new ProjectRemoteFile ($this->_database);
							$objRFile->deleteRecord( $this->_project->id, $service, $local['remoteid'], $filename);
						}						
					}
					else
					{
						// Not updating converted files via sync
						if ($local['converted'] == 1)
						{
							$output .= '## skipped locally changed converted file: '. $filename . "\n";
						}
						else
						{
							// Item in directory? Make sure we have correct remote dir structure in place
							$parentId = $this->_connect->prepRemoteParent($this->_project->id, $service, $projectCreator, $local, $remoteFolders);

							// Add/update operation
							if ($local['remoteid'])
							{															
								// Update remote file						
								$updated = $this->_connect->updateRemoteFile( 
									$this->_project->id, $service, $projectCreator, 
									$local['remoteid'], $local, $parentId
								);

								$output .= '++ sent update from local to remote: '. $filename . "\n";
							}
							else
							{
								// Add item from local to remote (new)
								if ($local['type'] == 'folder')
								{																
									// Create remote folder
									$created = $this->_connect->createRemoteFolder( 
										$this->_project->id, $service, $projectCreator, 
										basename($filename), $filename,  $parentId, $remoteFolders
									);

									$output .= '++ created remote folder: '. $filename . "\n";

								}
								elseif ($local['type'] == 'file')
								{								
									// Create remote file
									$created = $this->_connect->addRemoteFile( 
										$this->_project->id, $service, $projectCreator, 
										$local,  $parentId
									);

									$output .= '++ added new file to remote: '. $filename . "\n";
								}
							}						
						}
					}																	
				}
				
				$processedLocal[$filename] = $local;
				$lastLocalChange = $lChange ? date('c', $lChange + 1) : NULL;
			}
		}
		else
		{
			$output .= 'No local changes since last sync'. "\n";
		}
		
		$newRemotes   = array();
		
		// Record sync status
		$this->_writeToFile( JText::_('Refreshing remote file list') );
		
		// Get new change ID after local changes got sent to remote
		if (!empty($locals))
		{
			$newSyncId = $this->_connect->getChangedItems($service, $projectCreator, 
				$newSyncId, $newRemotes, $deletes, $connections);
		}
		
		// Get very last received remote change
		if (!empty($remotes))
		{
			$tChange = strtotime($lastRemoteChange);
			foreach ($remotes as $r => $ri)
			{
				$tChange = $ri['time'] > $tChange ? $ri['time'] : $tChange;
			}

			$lastRemoteChange = $tChange ? date('c', $tChange) : NULL;
		}
		
		// Image handler for generating thumbnails
		$ih = new ProjectsImgHandler();	
		
		// Make sure we have thumbnails for updates from local repo
		if (!empty($newRemotes) && $synced != 1)
		{
			$tChange = strtotime($lastRemoteChange);
			foreach ($newRemotes as $filename => $nR)
			{
				// Generate local thumbnail
				if ($nR['thumb'])
				{
					$this->_writeToFile(JText::_('Getting thumbnail for ') . ' ' . ProjectsHTML::shortenFileName($filename, 15) );
					$this->_connect->generateThumbnail($service, $projectCreator, 
						$nR, $this->_config, $this->_project->alias, $ih);																			
				}
				
				$tChange = $nR['time'] > $tChange ? $nR['time'] : $tChange;
			}
			
			// Pick up last remote change
			$lastRemoteChange = $tChange ? date('c', $tChange) : NULL;
		}
												
		// Record sync status
		$this->_writeToFile( JText::_('Importing remote changes') );

		$output .= 'Remote changes:' . "\n";
		
		// Go through remote changes
		if (count($remotes) > 0 && $synced != 1)
		{						
			// Get email/name pairs of connected project owners
			$objO = new ProjectOwner( $this->_database );
			$connected = $objO->getConnected($this->_project->id, $service);			
			
			// Examine each change
			foreach ($remotes as $filename => $remote)
			{												
				// Record sync status
				$this->_writeToFile(JText::_('Syncing ') . ' ' . ProjectsHTML::shortenFileName($filename, 30) );
				
				$output .= ' * Remote change ' . $filename . ' - ' . $remote['status'] . ' - ' . $remote['modified'];
				$output .= $remote['fileSize'] ? ' - ' . $remote['fileSize'] . ' bytes' : '';
				$output .= "\n";
				
				// Do we have a matching local change?
				$match = !empty($locals) 
					&& isset($locals[$filename]) 
					&& $remote['type'] == $locals[$filename]['type'] 
					? $locals[$filename] : array();	
					
				// Check for match in local changes
				// Remote usually prevails, unless it's older than last synced remote change
				if ($match && (($match['modified'] > $remote['modified']) > 0))
				{					
					// skip
					$output .= '== local and remote change match, but remote is older, picking local: '. $filename . "\n";
					$conflicts[$filename] = $local['remoteid'];
					continue;
				}
						
				$updated 	= 0;
				$deleted   	= 0;
				$commitMsg 	= 'Sync with ' . $service . ' (from change ID ' . $lastSyncId . ')' . "\n";				
				
				// Get change author for Git
				$email = 'sync@sync.org';
				$name = utf8_decode($remote['author']);
				if ($connected && isset($connected[$name]))
				{
					$email = $connected[$name];
				}
				else
				{
					// Email from profile?
					$email = $objO->getProfileEmail($name, $this->_project->id);
				}
				$author = $this->_git->getGitAuthor($name, $email);
				
				// Set Git author date (GIT_AUTHOR_DATE)
				$cDate = date('c', $remote['time']); // Important! Needs to be local time, NOT UTC
				
				// Item in directory? Make sure we have correct local dir structure
				$local_dir = dirname($filename) != '.' ? dirname($filename) : '';
				if ($remote['status'] != 'D' && $local_dir && !JFolder::exists( $this->prefix . $path . DS . $local_dir ))
				{
					if (JFolder::create( $this->prefix . $path . DS . $local_dir, 0777 )) 
					{
						$created = $this->_git->makeEmptyFolder($path, $local_dir);				
						$commitMsg = JText::_('COM_PROJECTS_CREATED_DIRECTORY') . '  ' . escapeshellarg($local_dir);
						$this->_git->gitCommit($path, $commitMsg, $author, $cDate);
					}
					else
					{
						// Error
						$output .= '[error] failed to provision local directory for: '. $filename . "\n";
						$failed[] = $filename;
						continue;
					}
				}
	
				// Send remote change to local (whether or not there is local change)
				// Remote version always prevails
				if ($remote['status'] == 'D')
				{
					if (file_exists($this->prefix . $path . DS . $filename))
					{
						// Delete in Git
						$deleted = $this->_git->gitDelete($path, $filename, $remote['type'], $commitMsg);				
						if ($deleted)
						{
							$this->_git->gitCommit($path, $commitMsg, $author, $cDate);
							
							// Delete local file or directory
							$output .= '-- deleted from local: '. $filename . "\n";
						}
						else
						{
							// Error
							$output .= '[error] failed to delete from local: '. $filename . "\n";
							$failed[] = $filename;
							continue;
						}						
					}
					else
					{
						// skip (deleted non-synced file)
						$output .= $remote['converted'] == 1 
									? '-- deleted converted: '. $filename . "\n"
									: '## skipped deleted non-synced item: '. $filename . "\n";
						$deleted = 1;		
					}
					
					// Delete connection record if exists
					if ($deleted)
					{
						$objRFile = new ProjectRemoteFile ($this->_database);
						$objRFile->deleteRecord( $this->_project->id, $service, $remote['remoteid']);
					}
				}
				elseif ($remote['status'] == 'R' || $remote['status'] == 'W')
				{
					// Rename/move in Git	
					if (file_exists($this->prefix . $path . DS . $remote['rename']))
					{
						$output .= '>> rename from: '. $remote['rename'] . ' to ' . $filename . "\n";
						
						if ($this->_git->gitMove($path, $remote['rename'], $filename, $remote['type'], $commitMsg))
						{
							$this->_git->gitCommit($path, $commitMsg, $author, $cDate);
							$output .= '>> renamed/moved item locally: '. $filename . "\n";
							$updated = 1;
						}
						else
						{
							// Error
							$output .= '[error] failed to rename/move item locally: '. $filename . "\n";
							$failed[] = $filename;
							continue;
						}
					}
					
					if ($remote['converted'] == 1)
					{
						$output .= '>> renamed/moved item locally converted: '. $filename . "\n";
						$updated = 1;
					}
				}
				else
				{
					if ($remote['converted'] == 1)
					{			
						// Not updating converted files via sync
						$output .= '## skipped converted remotely changed file: '. $filename . "\n";
						$updated = 1;						
					}					
					elseif (file_exists($this->prefix . $path . DS . $filename))
					{
						// Update
						if ($remote['type'] == 'file')
						{								
							// Check md5 hash - do we have identical files?
							$md5Checksum = hash_file('md5', $this->prefix . $path . DS . $filename);
							if ($remote['md5'] == $md5Checksum)
							{
								// Skip update
								$output .= '## update skipped: local and remote versions identical: '
										. $filename . "\n";
								$updated = 1;
							}
							else
							{
								// Check file size against quota ??
								
								// Download remote file								
								if ($this->_connect->downloadFileCurl($service, $remote['url'], $this->prefix . $path . DS . $remote['local_path']))
								//if ($this->_connect->downloadFile($service, $projectCreator, $remote, $this->prefix . $path ))
								{
									// Git add & commit
									$this->_git->gitAdd($path, $filename, $commitMsg);
									$this->_git->gitCommit($path, $commitMsg, $author, $cDate);
									
									$output .= ' ! versions differ: remote md5 ' . $remote['md5'] . ', local md5' . $md5Checksum . "\n";
									$output .= '++ sent update from remote to local: '. $filename . "\n";
									$updated = 1;
								}
								else
								{
									// Error
									$output .= '[error] failed to update local file with remote change: '. $filename . "\n";
									$failed[] = $filename;
									continue;
								}								
							}							
						}
						else
						{
							$output .= '## skipped folder in sync: '. $filename . "\n";
							$updated = 1;
						}
					}
					else
					{
						// Add item from remote to local (new)
						if ($remote['type'] == 'folder')
						{
							if (JFolder::create( $this->prefix . $path . DS . $filename, 0777 )) 
							{
								$created = $this->_git->makeEmptyFolder($path, $filename);				
								$commitMsg = JText::_('COM_PROJECTS_CREATED_DIRECTORY') . '  ' . escapeshellarg($filename);
								$this->_git->gitCommit($path, $commitMsg, $author, $cDate);
								$output .= '++ created local folder: '. $filename . "\n";
								$updated = 1;
							}
							else
							{
								// error
								$output .= '[error] failed to create local folder: '. $filename . "\n";
								$failed[] = $filename;
								continue;
							}
						}
						else
						{
							// Check against quota
							$checkAvail = $avail - $remote['fileSize'];
							if ($checkAvail <= 0)
							{
								// Error
								$output .= '[error] not enough space for '. $filename . ' (' . $remote['fileSize'] 
										. ' bytes) avail space:' . $checkAvail . "\n";
								$failed[] = $filename;
								
								// Record sync status
								$this->_writeToFile(JText::_('Skipping (size over limit)') . ' ' . ProjectsHTML::shortenFileName($filename, 30) );
								
								continue;
							}
							else
							{
								$avail   = $checkAvail; 
								$output .= 'file size ok: ' . $remote['fileSize'] . ' bytes ' . "\n";
							}
							
							// Download remote file
							if ($this->_connect->downloadFileCurl($service, $remote['url'], $this->prefix 
								. $path . DS . $remote['local_path']))
							//if ($this->_connect->downloadFile($service, $projectCreator, $remote, $this->prefix . $path ))
							{
								// Git add & commit
								$this->_git->gitAdd($path, $filename, $commitMsg);
								$this->_git->gitCommit($path, $commitMsg, $author, $cDate);
								
								$output .= '++ added new file to local: '. $filename . "\n";
								$updated = 1;
							}
							else
							{
								// Error
								$output .= '[error] failed to add new local file: '. $filename . "\n";
								$failed[] = $filename;
								continue;
							}
						}
					}									
				}
				
				// Update connection record	
				if ($updated)
				{
					$objRFile = new ProjectRemoteFile ($this->_database);
					$objRFile->updateSyncRecord( 
						$this->_project->id, $service, $this->_uid, 
						$remote['type'], $remote['remoteid'], $filename, 
						$match, $remote
					);
					
					$lastLocalChange = date('c', time() + 1);
					
					// Generate local thumbnail
					if ($remote['thumb'] && $remote['status'] != 'D')
					{						
						$this->_writeToFile(JText::_('Getting thumbnail for ') . ' ' . ProjectsHTML::shortenFileName($filename, 15) );
						$this->_connect->generateThumbnail($service, $projectCreator, $remote, $this->_config, $this->_project->alias, $ih);																			
					}
				}		
				
				$processedRemote[$filename] = $remote;
			}
		}
		else
		{
			$output .= 'No remote changes since last sync' . "\n";
		}
		
		// Hold on by one second (required as a forced breather before next sync request)
		sleep(1);
		
		// Log time
		$endTime = date('c');
		$length  = ProjectsHtml::timeDifference(strtotime($endTime) - strtotime($startTime));
		
		$output .= 'Sync complete:' . "\n";
		$output .= 'Local time: '. $endTime . "\n";
		$output .= 'UTC time: '.  JFactory::getDate()->toSql() . "\n";
		$output .= 'Sync completed in: '.  $length . "\n";
		
		// Determine next sync ID
		if (!$nextSyncId)
		{
			$nextSyncId  = ($newSyncId > $lastSyncId || count($remotes) > 0) ? ($newSyncId + 1) : $lastSyncId;
		}
														
		// Save sync time and last sync ID
		$obj = new Project( $this->_database );
		
		// Save sync time
		$obj->saveParam($this->_project->id, $service . '_sync', $endTime);
		
		// Save change id for next sync
		$obj->saveParam($this->_project->id, $service . '_sync_id', ($nextSyncId));
		$output .= 'Next sync ID: ' . $nextSyncId . "\n";
		
		$obj->saveParam($this->_project->id, $service . '_prev_sync_id', $lastSyncId);
		
		$output .= 'Saving last synced remote change at: ' . $lastRemoteChange . "\n";
		$obj->saveParam($this->_project->id, $service . '_last_remote_change', $lastRemoteChange);

		$output .= 'Saving last synced local change at: ' . $lastLocalChange . "\n";
		$obj->saveParam($this->_project->id, $service . '_last_local_change', $lastLocalChange);			
				
		// Debug output
		$temp = $this->getProjectPath ($this->_project->alias, 'logs');
		$this->_writeToFile($output, $this->prefix . $temp . DS . 'sync.' . JFactory::getDate()->format('Y-m') . '.log', true);
				
		// Record sync status
		$this->_writeToFile( JText::_('Sync complete! Updating view...') );
				
		// Unlock sync
		plgProjectsFilesSync::lockSync($service, true);
		
		// Clean up status
		$this->_writeToFile('Sync complete');
		
		$this->_rSync['status'] = 'success';
		return true;
	}
	
	/**
	 * Get sync status (AJAX call)
	 * 
	 * @return     string
	 */
	public function syncStatus() 
	{
		// Incoming
		$pid 		= JRequest::getInt('id', 0);
		$service 	= JRequest::getVar('service', 'google');
		$status 	= array('status' => '', 'msg' => time(), 'output' => '');
		
		// Read status file
		$rFile = $this->_readFile();
		
		// Report sync progress
		if ($rFile && $rFile != 'Sync complete')
		{
			$status = array('status' => 'progress', 'msg' => $rFile, 'output' => '');
		}
		elseif ($service)
		{
			// Get time of last sync
			$obj = new Project( $this->_database );
			$obj->load($pid);
			$pparams 	= new JParameter( $obj->params );	
			$synced 	= $pparams->get($service . '_sync');
			$syncLock 	= $pparams->get($service . '_sync_lock', '');
			
			// Report last sync time
			$msg = $synced && $synced != 1 
				? '<span class="faded">Last sync: ' . ProjectsHtml::timeAgo(strtotime($synced), false) 
				. ' ' . JText::_('COM_PROJECTS_AGO') . '</span>' 
				: '';
			$status = array('status' => 'complete', 'msg' => $msg);
			
			// Refresh view if sync happened recently
			$timecheck = date('c', time() - (1 * 1 * 60));
			if ($synced >= $timecheck)
			{
				$status['output'] = $this->view(2);
			}
			
			// Timed sync?
			$autoSync = $this->_params->get('auto_sync', 0);
			if ($autoSync > 0)
			{
				if ($autoSync < 1)
				{
					$hr = 60 * $autoSync;
					$timecheck = date('c', time() - (1 * $hr * 60));
				}
				else
				{
					$timecheck = date('c', time() - ($autoSync * 60 * 60));
				}

				if ($synced <= $timecheck)
				{
					$status['auto'] = 1;					
				}
			}
		}
		
		return json_encode($status);
	}
	
	/**
	 * Check if sync operation is in progress
	 * 
	 * @param    string		$service	Remote service name
	 * @return   Boolean
	 */
	protected function checkSyncLock ($service = 'google') 
	{
		$pparams 	= new JParameter( $this->_project->params );		
		$syncLock 	= $pparams->get($service . '_sync_lock', '');
		
		return $syncLock ? true : false;
	}
	
	/**
	 * Lock/unlock sync operation 
	 * 
	 * @param    string		$service	Remote service name
	 * @return   void
	 */
	protected function lockSync ($service = 'google', $unlock = false, $queue = 0 ) 
	{				
		$obj = new Project( $this->_database );
		$obj->load($this->_project->id);
		
		$pparams 	= new JParameter( $obj->params );
		$synced 	= $pparams->get($service . '_sync');		
		$syncLock 	= $pparams->get($service . '_sync_lock');
		$syncQueue 	= $pparams->get($service . '_sync_queue', 0);
		
		// Request to unlock sync
		if ($unlock == true)
		{
			$obj->saveParam($this->_project->id, $service . '_sync_lock', '');
			$this->_rSync['status'] = 'complete';
			
			// Clean up status
			$this->_writeToFile('Sync complete');
			
			// Repeat sync? (another request in queue)
			if ($syncQueue > 0)
			{
				// Clean up queue
				$obj->saveParam($this->_project->id, $service . '_sync_queue', 0);
				
				// Sync request
				//$this->_sync( $service, '', false, true);
			}
			
			return true;
		}
		
		// Is there time lock?
		$timeLock = $this->_params->get('sync_lock', 0);
		if ($timeLock)
		{
			$timecheck = date('c', time() - (1 * $timeLock * 60));
		}
		
		// Can't run sync - too soon
		if ($timeLock && $synced && $synced > $timecheck && !$queue)
		{
			$this->_rSync['status'] = 'locked';
			return false;
		}
		elseif ($syncLock)
		{
			// Add request to queue
			if ($queue && $syncQueue == 0)
			{
				$obj->saveParam($this->_project->id, $service . '_sync_queue', 1);
				return false;	
			}
				
			// Past hour - sync should have been complete, unlock
			$timecheck = date('c', time() - (1 * 60 * 60));
			
			if ($synced && $synced >= $timecheck)
			{
				$this->_rSync['status'] = 'locked';
				return false;	
			}
		}
		
		// Lock sync
		$obj->saveParam($this->_project->id, $service . '_sync_lock', $this->_uid);
		$this->_rSync['status'] = 'progress';
		return true;	
	}	
}
