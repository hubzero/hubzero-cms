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
defined('_JEXEC') or die('Restricted access');

/**
 * Sync helper
 */
class Sync extends \Hubzero\Base\Object
{
	/**
	 * Constructor
	 *
	 * @param	   object	$connect
	 * @return	   void
	 */
	public function __construct($connect = NULL )
	{
		if (empty($connect))
		{
			return false;
		}
		$this->_db      = \JFactory::getDBO();
		$this->_connect = $connect;
		$this->model    = $connect->model;
		$this->_uid     = User::get('id');

		$this->params   = Plugin::params( 'projects', 'files' );
		$this->_logPath = \Components\Projects\Helpers\Html::getProjectRepoPath($this->model->get('alias'), 'logs');
		$this->_path    = $this->model->repo()->get('path');
	}

	/**
	 * Get status srray
	 *
	 * @return array
	 */
	public function getStatus()
	{
		$props = array(
			'service' => $this->get('service'),
			'status'  => $this->get('status'),
			'message' => $this->get('message'),
			'error'   => $this->getError(),
			'output'  => $this->get('output'),
			'auto'    => $this->get('auto')
		);
		return $props;
	}

	/**
	 * Sync local and remote changes since last sync
	 *
	 * @param    string		$service	Remote service name
	 * @param    boolean	$queue	    Remote service name
	 * @return   void
	 */
	public function sync ($service = 'google', $queue = false, $auto = false)
	{
		// Lock sync
		if (!$this->lockSync($service, false, $queue))
		{
			// Return error
			if ($auto == false)
			{
				$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_DELAYED'));
			}

			return false;
		}

		if (!isset($this->_git))
		{
			$this->_git = new \Components\Projects\Helpers\Git($this->_path);
		}

		// Clean up status
		$this->writeToFile('');

		// Record sync status
		$this->writeToFile(ucfirst($service) . ' '. Lang::txt('PLG_PROJECTS_FILES_SYNC_STARTED') );

		// Get time of last sync
		$synced = $this->model->params->get($service . '_sync', 1);

		// Get disk usage
		$diskUsage = $this->model->repo()->call('getDiskUsage',
			$params = array(
				'working' => true,
				'history' => $this->params->get('disk_usage')
			)
		);

		$quota 	   = $this->model->params->get('quota')
					? $this->model->params->get('quota')
					: \Components\Projects\Helpers\Html::convertSize( floatval($this->model->config()->get('defaultQuota', '1')), 'GB', 'b');
		$avail 	   = $quota - $diskUsage;

		// Last synced remote/local change
		$lastRemoteChange = $this->model->params->get($service . '_last_remote_change', NULL);
		$lastLocalChange  = $this->model->params->get($service . '_last_local_change', NULL);

		// Get last change ID for project creator
		$lastSyncId = $this->model->params->get($service . '_sync_id', NULL);
		$prevSyncId = $this->model->params->get($service . '_prev_sync_id', NULL);

		// User ID of project owner
		$projectOwner = $this->model->get('owned_by_user');

		// Are we syncing project home directory or other?
		$localDir   = $this->_connect->getConfigParam($service, 'local_dir');
		$localDir   = $localDir == '#home' ? '' : $localDir;

		$localPath  = $this->_path;
		$localPath .= $localDir ? DS . $localDir : '';

		// Record sync status
		$this->writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_ESTABLISH_REMOTE_CONNECT') );

		// Get service API - always project owner!
		$this->_connect->getAPI($service, $projectOwner);

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
		$passed    = $synced != 1 ? \Components\Projects\Helpers\Html::timeDifference(strtotime($startTime) - strtotime($synced)) : 'N/A';

		// Start debug output
		$output  = ucfirst($service) . "\n";
		$output .= $synced != 1 ? 'Last sync (local): ' . $synced
				. ' | (UTC): ' . gmdate('Y-m-d H:i:s', strtotime($synced)) . "\n" : "";
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
		$this->writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_STRUCTURE_REMOTE') );

		// Get stored remote connections
		$objRFile = new \Components\Projects\Tables\RemoteFile ($this->_db);
		$connections = $objRFile->getRemoteConnections($this->model->get('id'), $service);

		// Get remote folder structure (to find out remote ids)
		$this->_connect->getFolderStructure($service, $projectOwner, $remoteFolders);

		// Record sync status
		$this->writeToFile( Lang::txt('PLG_PROJECTS_FILES_SYNC_COLLECT_LOCAL') );

		// Collector for local renames
		$localRenames = array();

		$fromLocal = ($synced == $lastLocalChange || !$lastLocalChange) ? $synced : $lastLocalChange;

		// Get all local changes since last sync
		$locals = $this->_git->getChanges($localPath, $fromLocal, $localDir, $localRenames, $connections );

		// Record sync status
		$this->writeToFile( Lang::txt('PLG_PROJECTS_FILES_SYNC_COLLECT_REMOTE') );

		// Get all remote files that changed since last sync
		$newSyncId  = 0;
		$nextSyncId = 0;
		if ($lastSyncId > 1)
		{
			// Via Changes feed
			$newSyncId = $this->_connect->getChangedItems(
				$service, $projectOwner,
				$lastSyncId, $remotes,
				$deletes, $connections
			);
		}
		else
		{
			// Via List feed
			$remotes = $this->_connect->getRemoteItems($service, $projectOwner, '', $connections);
			$newSyncId = 1;
		}

		// Record sync status
		$this->writeToFile( Lang::txt('PLG_PROJECTS_FILES_SYNC_VERIFY_REMOTE') );

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
			$timedRemotes = $this->_connect->getRemoteItems($service, $projectOwner, $from, $connections);
		}

		// Record timed remote changes (for debugging)
		if (!empty($timedRemotes))
		{
			$output .= 'Timed remote changes since ' . $from . ' (' . count($timedRemotes) . '):' . "\n";
			foreach ($timedRemotes as $tr => $trinfo)
			{
				$output .= $tr . ' changed ' . date("c", $trinfo['time'])
						. ' status ' . $trinfo['status'] . ' ' . $trinfo['fileSize'] . "\n";
			}

			// Pick up missed changes
			if ($remotes != $timedRemotes)
			{
				$output .= empty($remotes)
					? 'Using exclusively timed changes ' . "\n"
					: 'Mixing in timed changes ' . "\n";

				$remotes = array_merge($timedRemotes, $remotes);
			}
		}
		else
		{
			$output .= 'No timed changes since ' . $from . "\n";
		}

		if ($this->_connect->getError())
		{
			$this->writeToFile( '' );
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_SYNC_ERROR_OUPS') . ' ' . $this->_connect->getError());
			$this->lockSync($service, true);
			return false;
		}

		// Collector arrays for processed files
		$processedLocal 	= array();
		$processedRemote 	= array();
		$conflicts			= array();

		// Record sync status
		$this->writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_EXPORTING_LOCAL'));

		$output .= 'Local changes:' . "\n";

		// Go through local changes
		if (count($locals) > 0)
		{
			$lChange = NULL;
			foreach ($locals as $filename => $local)
			{
				$output .= ' * Local change ' . $filename . ' - ' . $local['status'] . ' - ' . $local['modified'] . ' - ' . $local['time'] . "\n";

				// Get latest change
				$lChange = $local['time'] > $lChange ? $local['time'] : $lChange;

				// Skip renamed files (local renames are handled later)
				if (in_array($filename, $localRenames) && !file_exists($local['fullPath']))
				{
					$output .= '## skipped rename from ' . $filename . "\n";
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
				// Record sync status
				$this->writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_SYNCING') . ' '
					. \Components\Projects\Helpers\Html::shortenFileName($filename, 30) );

				// Item renamed
				if ($local['status'] == 'R')
				{
					if ($local['remoteid'])
					{
						// Rename remote item
						$renamed = $this->_connect->renameRemoteItem(
							$this->model->get('id'), $service, $projectOwner,
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
						$parentId = $this->_connect->prepRemoteParent($this->model->get('id'), $service, $projectOwner, $local, $remoteFolders);

						if ($parentId != $local['rParent'])
						{
							// Move to new parent
							$moved = $this->_connect->moveRemoteItem(
								$this->model->get('id'), $service, $projectOwner,
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
								$this->model->get('id'),
								$service,
								$projectOwner,
								$local['remoteid'],
								false
							);

							// Delete from remote
							$output .= '-- deleted from remote: ' . $filename . "\n";
						}
						else
						{
							// skip (deleted non-synced file)
							$output .= '## skipped deleted non-synced item: ' . $filename . "\n";
							$deleted = 1;
						}

						if ($local['type'] == 'folder')
						{
							$this->_connect->fixConvertedItems($service, $this->_uid, $filename, 'D');
						}

						// Delete connection record if exists
						if ($deleted)
						{
							$objRFile = new \Components\Projects\Tables\RemoteFile ($this->_db);
							$objRFile->deleteRecord( $this->model->get('id'), $service, $local['remoteid'], $filename);
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
							$parentId = $this->_connect->prepRemoteParent($this->model->get('id'), $service, $projectOwner, $local, $remoteFolders);

							// Add/update operation
							if ($local['remoteid'])
							{
								// Update remote file
								$updated = $this->_connect->updateRemoteFile(
									$this->model->get('id'), $service, $projectOwner,
									$local['remoteid'], $local, $parentId
								);

								$output .= '++ sent update from local to remote: ' . $filename . "\n";
							}
							else
							{
								// Add item from local to remote (new)
								if ($local['type'] == 'folder')
								{
									// Create remote folder
									$created = $this->_connect->createRemoteFolder(
										$this->model->get('id'), $service, $projectOwner,
										basename($filename), $filename,  $parentId, $remoteFolders
									);

									$output .= '++ created remote folder: ' . $filename . "\n";

								}
								elseif ($local['type'] == 'file')
								{
									// Create remote file
									$created = $this->_connect->addRemoteFile(
										$this->model->get('id'), $service, $projectOwner,
										$local, $parentId
									);

									$output .= '++ added new file to remote: ' . $filename . "\n";
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
			$output .= 'No local changes since last sync' . "\n";
		}

		$newRemotes   = array();

		// Record sync status
		$this->writeToFile( Lang::txt('PLG_PROJECTS_FILES_SYNC_REFRESHING_REMOTE') );

		// Get new change ID after local changes got sent to remote
		if (!empty($locals))
		{
			$newSyncId = $this->_connect->getChangedItems(
				$service,
				$projectOwner,
				$newSyncId,
				$newRemotes,
				$deletes,
				$connections
			);
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

		// Make sure we have thumbnails for updates from local repo
		if (!empty($newRemotes) && $synced != 1)
		{
			$tChange = strtotime($lastRemoteChange);
			foreach ($newRemotes as $filename => $nR)
			{
				// Generate local thumbnail
				if ($nR['thumb'])
				{
					$this->writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_GET_THUMB') . ' ' . \Components\Projects\Helpers\Html::shortenFileName($filename, 15) );

					$this->_connect->generateThumbnail(
						$service,
						$projectOwner,
						$nR,
						$this->model->config(),
						$this->model->get('alias')
					);
				}

				$tChange = $nR['time'] > $tChange ? $nR['time'] : $tChange;
			}

			// Pick up last remote change
			$lastRemoteChange = $tChange ? date('c', $tChange) : NULL;
		}

		// Record sync status
		$this->writeToFile( Lang::txt('PLG_PROJECTS_FILES_SYNC_IMPORTING_REMOTE') );

		$output .= 'Remote changes:' . "\n";

		// Go through remote changes
		if (count($remotes) > 0 && $synced != 1)
		{
			// Get email/name pairs of connected project owners
			$objO = $this->model->table('Owner');
			$connected = $objO->getConnected($this->model->get('id'), $service);

			// Examine each change
			foreach ($remotes as $filename => $remote)
			{
				$output .= ' * Remote change ' . $filename . ' - '
					. $remote['status'] . ' - ' . $remote['modified'];
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
					$email = $objO->getProfileEmail($name, $this->model->get('id'));
				}
				$author = $this->_git->getGitAuthor($name, $email);

				// Change acting user to whoever did the remote change
				$uid = $objO->getProfileId( $email, $this->model->get('id'));
				if ($uid)
				{
					$this->_uid = $uid;
				}

				// Set Git author date (GIT_AUTHOR_DATE)
				$cDate = date('c', $remote['time']); // Important! Needs to be local time, NOT UTC

				// Record sync status
				$this->writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_SYNCING') . ' ' . \Components\Projects\Helpers\Html::shortenFileName($filename, 30) );

				// Item in directory? Make sure we have correct local dir structure
				$local_dir = dirname($filename) != '.' ? dirname($filename) : '';
				if ($remote['status'] != 'D' && $local_dir && !is_dir( $this->_path . DS . $local_dir ))
				{
					if (Filesystem::makeDirectory( $this->_path . DS . $local_dir ))
					{
						$created = $this->_git->makeEmptyFolder($local_dir, false);
						$commitMsg = Lang::txt('PLG_PROJECTS_FILES_CREATED_DIRECTORY')
							. '  ' . escapeshellarg($local_dir);
						$this->_git->gitCommit($commitMsg, $author, $cDate);
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
					if (file_exists($this->_path . DS . $filename))
					{
						// Delete in Git
						$deleted = $this->_git->gitDelete($filename, $remote['type'], $commitMsg);
						if ($deleted)
						{
							$this->_git->gitCommit($commitMsg, $author, $cDate);

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
						$objRFile = new \Components\Projects\Tables\RemoteFile ($this->_db);
						$objRFile->deleteRecord( $this->model->get('id'), $service, $remote['remoteid']);
					}
				}
				elseif ($remote['status'] == 'R' || $remote['status'] == 'W')
				{
					// Rename/move in Git
					if (file_exists($this->_path . DS . $remote['rename']))
					{
						$output .= '>> rename from: '. $remote['rename'] . ' to ' . $filename . "\n";

						if ($this->_git->gitMove($remote['rename'], $filename, $remote['type'], $commitMsg))
						{
							$this->_git->gitCommit($commitMsg, $author, $cDate);
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
					elseif (file_exists($this->_path . DS . $filename))
					{
						// Update
						if ($remote['type'] == 'file')
						{
							// Check md5 hash - do we have identical files?
							$md5Checksum = hash_file('md5', $this->_path . DS . $filename);
							if ($remote['md5'] == $md5Checksum)
							{
								// Skip update
								$output .= '## update skipped: local and remote versions identical: '
										. $filename . "\n";
								$updated = 1;
							}
							else
							{
								// Download remote file
								if ($this->_connect->downloadFileCurl(
									$service,
									$projectOwner,
									$remote['url'],
									$this->_path . DS . $remote['local_path'])
								)
								{
									// Checkin into repo
									$this->model->repo()->call('checkin', array(
										'file'   => $this->model->repo()->getMetadata($filename, 'file', array()),
										'author' => $author,
										'date'   => $cDate
										)
									);

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
							if (Filesystem::makeDirectory( $this->_path . DS . $filename, 0755, true, true ))
							{
								$created = $this->_git->makeEmptyFolder($filename, false);
								$commitMsg = Lang::txt('PLG_PROJECTS_FILES_CREATED_DIRECTORY')
									. '  ' . escapeshellarg($filename);
								$this->_git->gitCommit($commitMsg, $author, $cDate);
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
								continue;
							}
							else
							{
								$avail   = $checkAvail;
								$output .= 'file size ok: ' . $remote['fileSize'] . ' bytes ' . "\n";
							}

							// Download remote file
							if ($this->_connect->downloadFileCurl(
								$service,
								$projectOwner,
								$remote['url'],
								$this->_path . DS . $remote['local_path'])
							)
							{
								// Git add & commit
								$this->_git->gitAdd($filename, $commitMsg);
								$this->_git->gitCommit($commitMsg, $author, $cDate);

								$output .= '++ added new file to local: '. $filename . "\n";
								$updated = 1;

								// Store in session
								$this->registerUpdate('uploaded', $filename);
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
					$objRFile = new \Components\Projects\Tables\RemoteFile ($this->_db);
					$objRFile->updateSyncRecord(
						$this->model->get('id'), $service, $this->_uid,
						$remote['type'], $remote['remoteid'], $filename,
						$match, $remote
					);

					$lastLocalChange = date('c', time() + 1);

					// Generate local thumbnail
					if ($remote['thumb'] && $remote['status'] != 'D')
					{
						$this->writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_GET_THUMB') . ' '
						. \Components\Projects\Helpers\Html::shortenFileName($filename, 15) );
						$this->_connect->generateThumbnail($service, $projectOwner, $remote,
							$this->model->config(), $this->model->get('alias'));
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
		$length  = \Components\Projects\Helpers\Html::timeDifference(strtotime($endTime) - strtotime($startTime));

		$output .= 'Sync complete:' . "\n";
		$output .= 'Local time: '. $endTime . "\n";
		$output .= 'UTC time: '.  Date::toSql() . "\n";
		$output .= 'Sync completed in: '.  $length . "\n";

		// Determine next sync ID
		if (!$nextSyncId)
		{
			$nextSyncId  = ($newSyncId > $lastSyncId || count($remotes) > 0) ? ($newSyncId + 1) : $lastSyncId;
		}

		// Save sync time
		$this->model->saveParam($service . '_sync', $endTime);

		// Save change id for next sync
		$this->model->saveParam($service . '_sync_id', ($nextSyncId));
		$output .= 'Next sync ID: ' . $nextSyncId . "\n";

		$this->model->saveParam($service . '_prev_sync_id', $lastSyncId);

		$output .= 'Saving last synced remote change at: ' . $lastRemoteChange . "\n";
		$this->model->saveParam($service . '_last_remote_change', $lastRemoteChange);

		$output .= 'Saving last synced local change at: ' . $lastLocalChange . "\n";
		$this->model->saveParam($service . '_last_local_change', $lastLocalChange);

		// Debug output
		$this->writeToFile($output, $this->_logPath . DS . 'sync.' . Date::of('now')->format('Y-m') . '.log', true);

		// Record sync status
		$this->writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_COMPLETE_UPDATE_VIEW') );

		// Unlock sync
		$this->lockSync($service, true);

		// Clean up status
		$this->writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_COMPLETE'));

		$this->set('status', 'success');
		return true;
	}

	/**
	 * Lock/unlock sync operation
	 *
	 * @param    string		$service	Remote service name
	 * @return   void
	 */
	public function lockSync ($service = 'google', $unlock = false, $queue = 0 )
	{
		$pparams 	= $this->model->params;
		$synced 	= $pparams->get($service . '_sync');
		$syncLock 	= $pparams->get($service . '_sync_lock');
		$syncQueue 	= $pparams->get($service . '_sync_queue', 0);

		// Request to unlock sync
		if ($unlock == true)
		{
			$this->model->saveParam($service . '_sync_lock', '');
			$this->set('status', 'complete');

			// Clean up status
			$this->writeToFile(Lang::txt('PLG_PROJECTS_FILES_SYNC_COMPLETE'));

			// Repeat sync? (another request in queue)
			if ($syncQueue > 0)
			{
				// Clean up queue
				$this->model->saveParam($service . '_sync_queue', 0);
			}

			return true;
		}

		// Is there time lock?
		$timeLock = $this->params->get('sync_lock', 0);
		if ($timeLock)
		{
			$timecheck = date('c', time() - (1 * $timeLock * 60));
		}

		// Can't run sync - too soon
		if ($timeLock && $synced && $synced > $timecheck && !$queue)
		{
			$this->set('status', 'locked');
			return false;
		}
		elseif ($syncLock)
		{
			// Add request to queue
			if ($queue && $syncQueue == 0)
			{
				$this->model->saveParam($service . '_sync_queue', 1);
				return false;
			}

			// Past hour - sync should have been complete, unlock
			$timecheck = date('c', time() - (1 * 60 * 60));

			if ($synced && $synced >= $timecheck)
			{
				$this->set('status', 'locked');
				return false;
			}
		}

		// Lock sync
		$this->model->saveParam($service . '_sync_lock', $this->_uid);
		$this->set('status', 'progress');
		return true;
	}

	/**
	 * Write sync status to file
	 *
	 * @return   void
	 */
	public function writeToFile($content = '', $filename = '', $append = false )
	{
		// Get temp path
		if (!$filename)
		{
			if (empty($this->_logPath))
			{
				return false;
			}
			if (!is_dir($this->_logPath))
			{
				Filesystem::makeDirectory($this->_logPath);
			}
			$sfile = $this->_logPath . DS . 'sync_' . $this->model->get('alias') . '.hidden';
		}
		else
		{
			$sfile = $filename;
		}

		$place   = $append == true ? 'a' : 'w';
		$content = $append ? $content . "\n" : $content;

		$handle  = fopen($sfile, $place);
		fwrite($handle, $content);
		fclose($handle);
	}

	/**
	 * Read sync status from file (last line)
	 *
	 * @return   void
	 */
	public function readFile($filename = '', $readAll = false)
	{
		// Get temp path
		if (!$filename)
		{
			$sfile = $this->_logPath . DS . 'sync_' . $this->model->get('alias') . '.hidden';
		}
		else
		{
			$sfile = $filename;
		}

		if (is_file($sfile))
		{
			if ($readAll == true)
			{
				return file_get_contents($sfile);
			}
			else
			{
				// Return one line
				$file = fopen($sfile, 'r');
				$line = fgets($file);
				fclose($file);
				return $line;
			}
		}

		return NULL;
	}
}
