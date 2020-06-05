<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Site\Controllers;

use Hubzero\Component\SiteController;
use Filesystem;
use Component;
use Request;
use Route;
use Lang;
use User;
use Log;
use App;

include_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'tool.php';
include_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'version.php';
include_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'author.php';

/**
 * Controller class for contributing a tool
 */
class Admin extends SiteController
{
	private $_toolid = 0;
	private $_admin = 0;
	private $_messages = array();

	/**
	 * Add a message
	 *
	 * @param	string $message Message
	 */
	public function setMessage($msg, $type='message')
	{
		array_push($this->_messages, $msg);
	}

	/**
	 * Get the most recent message
	 *
	 * @param      integer $i Option message index
	 * @return     string  Error message
	 */
	public function getMessage($i = null)
	{
		// Find the message
		if ($i === null)
		{
			// Default, return the last message
			$message = end($this->_messages);
		}
		else if (!array_key_exists($i, $this->_messages))
		{
			// If $i has been specified but does not exist, return false
			return false;
		}
		else
		{
			$message = $this->_messages[$i];
		}

		return $message;
	}

	/**
	 * Return all messages, if any
	 *
	 * @return     array Array of messages
	 */
	public function getMessages()
	{
		return $this->_messages;
	}

	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->_authorize();

		// needs to be admin
		if (!$this->config->get('access-manage-component'))
		{
			App::redirect(
				$this->config->get('contribtool_redirect', '/home')
			);
			return;
		}

		// Load the com_resources component config
		$rconfig = Component::params('com_resources');
		$this->rconfig = $rconfig;

		parent::execute();
	}

	/**
	 * Add repo
	 *
	 * @return     void
	 */
	public function addrepoTask()
	{
		// Set the layout (note: all the views for this controller use the same layout)
		$this->view->setLayout('display');

		// Create a Tool object
		$obj = new \Components\Tools\Tables\Tool($this->database);

		// Do we have an alias?
		if (($alias = Request::getString('app', '')))
		{
			$this->_toolid = $obj->getToolId($alias);
		}
		// Do we have a tool ID
		if (!$this->_toolid)
		{
			App::abort(403, Lang::txt('COM_TOOLS_ERROR_TOOL_NOT_FOUND'));
			return;
		}

		// Get the tool status
		$obj->getToolStatus(
			$this->_toolid,
			$this->_option,
			$status,
			'dev'
		);
		// Check for a status
		if (count($status) <= 0)
		{
			App::abort(500, Lang::txt('COM_TOOLS_ERR_CANNOT_RETRIEVE'));
			return;
		}

		$ldap_params = Component::params('com_system');
		$pw = $ldap_params->get('ldap_searchpw', '');

		if (!file_exists('/usr/bin/addrepo.sh'))
		{
			$command  = '/usr/bin/addrepo ' . $status['toolname'];
			$command .= ' -title ' . escapeshellarg($status['title']);
			$command .= ' -description ' . escapeshellarg($status['description']);
			$command .= ' -password "' . $pw . '"';
			$command .= ' -hubdir ' . PATH_ROOT;
		}
		else
		{
			$command  = '/usr/bin/addrepo.sh ' . $status['repohost'];
			$command .= ' --project ' . $status['toolname'];
			$command .= ' --title ' . escapeshellarg($status['title']);
			$command .= ' --description ' . escapeshellarg($status['description']);
			$command .= ' --hubdir ' . PATH_ROOT;
			if ($status['repohost'] == 'gitExternal')
			{
				if ($status['github'])
				{
					$command .= ' --gitURL ' . $status['github'];
				}
			}
		}

		$this->_invokeScript($command, Lang::txt('COM_TOOLS_NOTICE_PROJECT_AREA_CREATED'));

		// Set errors to view
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		// Set messages to view
		$this->view->messages = $this->getMessages();

		// Output HTML
		if (!($no_html = Request::getInt('no_html', 0)))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=pipeline&task=status&app=' . $alias)
			);
			return;
		}

		$this->view->display();
	}

	/**
	 * Install the tool
	 *
	 * @return     void
	 */
	public function installTask()
	{
		// Set the layout (note: all the views for this controller use the same layout)
		$this->view->setLayout('display');

		// Create a Tool object
		$obj = new \Components\Tools\Tables\Tool($this->database);

		// Do we have an alias?
		if (($alias = Request::getString('app', '')))
		{
			$this->_toolid = $obj->getToolId($alias);
		}
		// Do we have a tool ID
		if (!$this->_toolid)
		{
			App::abort(403, Lang::txt('COM_TOOLS_ERROR_TOOL_NOT_FOUND'));
			return;
		}

		// Get the tool status
		$obj->getToolStatus(
			$this->_toolid,
			$this->_option,
			$status,
			'dev'
		);
		// Check for a status
		if (count($status) <= 0)
		{
			App::abort(500, Lang::txt('COM_TOOLS_ERR_CANNOT_RETRIEVE'));
			return;
		}

		// Github connection?

		if (!file_exists('/usr/bin/git2git.sh'))
		{
			if ($status['github'])
			{
				if (!file_exists('/usr/bin/git2svn.sh'))
				{
					$this->setError(Lang::txt('COM_TOOLS_GITHUB_REPO_GIT2SVN_MISSING'));
				}
				else
				{
					$command = '/usr/bin/sudo -u apps '
							. '/usr/bin/git2svn.sh -g ' . $status['github']
							. ' -s ' . $status['toolname']
							. ' -c ' . PATH_ROOT;

					if (!$this->_invokeScript($command, Lang::txt('COM_TOOLS_NOTICE_GIT_REPOSITORY_CONNECTION')))
					{
						$this->setError(Lang::txt('COM_TOOLS_ERROR_GIT_REPOSITORY_CONNECTION'));
					}
				}
			}
		}
		else
		{
		if ($status['repohost'] == 'gitExternal')
			{
				if ($status['github'])
				{
					$command  = '/usr/bin/git2git.sh ' . $status['repohost'];
					$command .= ' --project ' . $status['toolname'];
					$command .= ' --gitURL ' . $status['github'];

					if (!$this->_invokeScript($command, Lang::txt('COM_TOOLS_NOTICE_GIT_REPOSITORY_CONNECTION')))
					{
						$this->setError(Lang::txt('COM_TOOLS_ERROR_GIT_REPOSITORY_CONNECTION'));
					}
				}
			}
		}

		// Build the exec command

		if (!file_exists('/usr/bin/installtool.sh'))
		{
			$command  = '/usr/bin/sudo -u apps ';
			$command .=    '/usr/bin/installtool';
			$command .=       ' -type raw';
			$command .=       ' -hubdir ' . PATH_ROOT . '/ ';
			$command .=       $status['toolname'];
			$commitHashRequired = false;
		}
		else
		{
			$command  = '/usr/bin/sudo -u apps ';
			$command .=    '/usr/bin/installtool.sh ' . $status['repohost'];
			$command .=       ' --type raw';
			$command .=       ' --hubdir ' . PATH_ROOT;
			$command .=       ' --project ' . $status['toolname'];
			if ($status['repohost'] == 'gitExternal')
			{
				if ($status['github'])
				{
					$command .= ' --gitURL ' . $status['github'];
				}
			}
			$commitHashRequired = true;
		}

		// Invoke the script
		if (!$this->getError() && $this->_invokeScript($command, Lang::txt('COM_TOOLS_NOTICE_REV_INSTALLED')))
		{
			// Extract revision number and optional commitHash
			$rev = '';
			$commitHash = '';
			$nRead = sscanf($this->getMessage(), "installed revision: %s commitHash: %s", $rev, $commitHash);

			if (!isset($rev) || !intval($rev))
			{
				$this->setError(Lang::txt('COM_TOOLS_ERR_CANNOT_SAVE_REVISION_INFO'));
			}
			else
			{
				// Update the revision number
				$hztv = \Components\Tools\Helpers\Version::getDevelopmentToolVersion($this->_toolid);
				$hztv->revision = intval($rev);
				if ($status['repohost'] == 'gitLocal' || $status['repohost'] == 'gitExternal')
				{
					if ($commitHash != '')
					{
						$txt = new \Hubzero\Config\Registry($hztv->params);
						$txt->set('commitHash', $commitHash);
						$hztv->params = $txt->toString();
					}
					else if ($commitHashRequired)
					{
						$this->setError(Lang::txt('COM_TOOLS_ERR_CANNOT_SAVE_REVISION_INFO'));
					}
				}
				if (!$hztv->update())
				{
					$this->setError(Lang::txt('COM_TOOLS_ERROR_SAVING_REVISION_UPDATE'));
				}
			}
		}

		// Set errors to view
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Set messages to view
		$this->view->messages = $this->getMessages();

		// Output HTML
		if (!($no_html = Request::getInt('no_html', 0)))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=pipeline&task=status&app=' . $alias)
			);
			return;
		}

		$this->view->display();
	}

	/**
	 * Retire a tool
	 *
	 * @return     void
	 */
	public function retireTask()
	{
		// Set the layout (note: all the views for this controller use the same layout)
		$this->view->setLayout('display');

		// Create a Tool object
		$obj = new \Components\Tools\Tables\Tool($this->database);

		// Do we have an alias?
		if (($alias = Request::getString('app', '')))
		{
			$this->_toolid = $obj->getToolId($alias);
		}
		// Do we have a tool ID
		if (!$this->_toolid)
		{
			App::abort(403, Lang::txt('COM_TOOLS_ERROR_TOOL_NOT_FOUND'));
			return;
		}

		// Get the tool status
		$obj->getToolStatus(
			$this->_toolid,
			$this->_option,
			$status,
			'dev'
		);
		// Check for a status
		if (count($status) <= 0)
		{
			App::abort(500, Lang::txt('COM_TOOLS_ERR_CANNOT_RETRIEVE'));
			return;
		}

		// Create a Tool Version object
		$objV = new \Components\Tools\Tables\Version($this->database);

		// Unpublish all previous versions
		if (!$objV->unpublish($this->_toolid))
		{
			$this->setError(Lang::txt('COM_TOOLS_ERR_FAILED_TO_UNPUBLISH_PREV_VERSIONS'));
		}
		else
		{
			$this->setMessage(Lang::txt('COM_TOOLS_NOTICE_UNPUBLISHED_PREV_VERSIONS'));
		}

		// Set errors to view
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		// Set messages to view
		$this->view->messages = $this->getMessages();

		// Output HTML
		if (!($no_html = Request::getInt('no_html', 0)))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=pipeline&task=status&app=' . $alias)
			);
			return;
		}

		$this->view->display();
	}

	/**
	 * Publish a tool
	 *
	 * @return     void
	 */
	public function publishTask()
	{
		// Set the layout (note: all the views for this controller use the same layout)
		$this->view->setLayout('display');

		// Create a Tool object
		$obj = new \Components\Tools\Tables\Tool($this->database);

		// Do we have an alias?
		if (($alias = Request::getString('app', '')))
		{
			$this->_toolid = $obj->getToolId($alias);
		}
		// Do we have a tool ID
		if (!$this->_toolid)
		{
			App::abort(403, Lang::txt('COM_TOOLS_ERROR_TOOL_NOT_FOUND'));
			return;
		}

		// Get the tool status
		$obj->getToolStatus(
			$this->_toolid,
			$this->_option,
			$status,
			'dev'
		);
		// Check for a status
		if (count($status) <= 0)
		{
			App::abort(500, Lang::txt('COM_TOOLS_ERR_CANNOT_RETRIEVE'));
			return;
		}

		$result = true;

		Log::debug("publish(): checkpoint 1:$result");

		// get config

		// Create a Tool Version object
		$objV = new \Components\Tools\Tables\Version($this->database);
		$objV->getToolVersions(
			$this->_toolid,
			$tools,
			'',
			1
		);

		// make checks
		if (!is_numeric($status['revision']))
		{
			// bad format
			$result = false;
			$this->setError(Lang::txt('COM_TOOLS_ERR_MISSING_REVISION_OR_BAD_FORMAT'));
		}
		else if (count($tools) > 0 && $status['revision'])
		{
			// check for duplicate revision
			foreach ($tools as $t)
			{
				if ($t->revision == $status['revision'])
				{
					$result = false;
					$this->setError(Lang::txt('COM_TOOLS_ERR_REVISION_EXISTS') . ' ' . $status['revision']);
				}
			}
			// check that revision number is greater than in previous version
			$currentrev = $objV->getCurrentVersionProperty($status['toolname'], 'revision');
			if ($currentrev && (intval($currentrev) > intval($status['revision'])))
			{
				$result = false;
				$this->setError(Lang::txt('COM_TOOLS_ERR_REVISION_GREATER'));
			}
		}

		// Log checkpoint
		Log::debug("publish(): checkpoint 2:$result, check revision");

		// check if version is valid
		if (!\Components\Tools\Models\Tool::validateVersion($status['version'], $error_v, $this->_toolid))
		{
			$result = false;
			$this->setError($error_v);
		}

		// Log checkpoint
		Log::debug("publish(): checkpoint 3:$result, running finalize tool");

		// Run finalizetool
		if (!$this->getError())
		{
			if ($this->_finalizeTool($out))
			{
				$this->setMessage(Lang::txt('COM_TOOLS_Version finalized.'));
			}
			else
			{
				$this->setError($out);
				$result = false;
			}
		}

		Log::debug("publish(): checkpoint 4:$result, running doi stuff");

		// Register DOI handle
		if ($result && $this->config->get('new_doi', 0))
		{
			include_once \Component::path('com_resources') . DS . 'models' . DS . 'doi.php';

			// Collect metadata
			$url = Request::base() . ltrim(Route::url('index.php?option=com_resources&id=' . $status['resourceid'] . '&rev=' . $status['revision']), DS);

			// Check if DOI exists for this revision
			$objDOI = \Components\Resources\Models\Doi::all()
				->whereEquals('rid', $status['resourceid']);
			if ($status['revision'])
			{
				$objDOI->whereEquals('local_revision', $status['revision']);
			}
			else
			{
				$objDOI->whereEquals('versionid', 0);
			}

			$bingo = $objDOI->row()->get('doi');

			// DOI already exists for this revision
			if ($bingo)
			{
				$this->setError(Lang::txt('COM_TOOLS_ERR_DOI_ALREADY_EXISTS') . ': ' . $bingo);
			}
			else
			{
				// Get latest DOI label
				$latestdoi = \Components\Resources\Models\Doi::all()
					->whereEquals('rid', $status['resourceid'])
					->order('doi_label', 'desc')
					->limit(1)
					->row()
					->get('doi');

				$newlabel   = ($latestdoi) ? (intval($latestdoi) + 1): 1;

				// Collect metadata
				$metadata = array(
					'targetURL' => $url,
					'title'     => htmlspecialchars(stripslashes($status['title'])),
					'version'   => $status['version'],
					'abstract'  => htmlspecialchars(stripslashes($status['description'])),
					'license'   => $status['license']
				);

				// Get authors
				$objA = new \Components\Tools\Tables\Author($this->database);
				$authors = $objA->getAuthorsDOI($status['resourceid']);

				// Register DOI
				$doiSuccess = $objDOI->register($authors, $this->config, $metadata);

				// Save [new] DOI record
				if ($doiSuccess)
				{
					$objDOI = \Components\Resources\Models\Doi::oneByResource($status['resourceid'], $status['revision']);
					if (!$objDOI->get('id'))
					{
						$objDOI = \Components\Resources\Models\Doi::blank()
							->set(array(
								'local_revision' => $status['revision'],
								'doi_label' => $newlabel,
								'rid' => $status['resourceid'],
								'alias' => $status['toolname'],
								'versionid' => 0,
								'doi' => $doiSuccess
							));
						if ($objDOI->save())
						{
							$this->setMessage(Lang::txt('COM_TOOLS_SUCCESS_DOI_CREATED') . ' ' . $doiSuccess);
						}
						else
						{
							$this->setError(Lang::txt('COM_TOOLS_ERR_DOI_STORE_FAILED'). ' ' . $objDOI->getError());
							$result = false;
						}
					}
					else
					{
						$this->setError(Lang::txt('COM_TOOLS_DOI already exists: ') . $objDOI->doi);
					}
				}
				else
				{
					$this->setError(Lang::txt('COM_TOOLS_ERR_DOI_STORE_FAILED'));
					$this->setError($doierr);
					$result = false;
				}
			}
		}

		if ($result)
		{
			$invokedir = rtrim($this->config->get('invokescript_dir', DS . 'apps'), "\\/");

			$hzt = \Components\Tools\Models\Tool::getInstance($this->_toolid);
			$hztv_cur = $hzt->getCurrentVersion();
			$hztv_dev = $hzt->getDevelopmentVersion();

			Log::debug("publish(): checkpoint 6:$result, running database stuff");

			// create tool instance in the database
			$newtool = $status['toolname'] . '_r' . $status['revision'];

			// get version id
			$currentid = (is_object($hztv_cur) ? $hztv_cur->id : null);
			$new       = ($currentid) ? 0 : 1;
			$devid     = $hztv_dev->id;

			$exportmap = array(
				'@OPEN'  => null,
				'@GROUP' => null,
				'@US'    => 'us',
				'@PU'    => 'pu',
				'@D1'    => 'd1'
			);

			$new_hztv = \Components\Tools\Models\Version::createInstance($status['toolname'], $newtool);
			$new_hztv->toolname      = $status['toolname'];
			$new_hztv->instance      = $newtool;
			$new_hztv->toolid        = $this->_toolid;
			$new_hztv->state         = 1;
			$new_hztv->title         = $status['title'];
			$new_hztv->version       = $status['version'];
			$new_hztv->revision      = $status['revision'];
			$new_hztv->description   = $status['description'];
			$new_hztv->toolaccess    = $status['exec'];
			$new_hztv->codeaccess    = $status['code'];
			$new_hztv->wikiaccess    = $status['wiki'];
			$new_hztv->vnc_geometry  = $status['vncGeometry'];
			$new_hztv->vnc_command   = $invokedir . DS . $status['toolname'] . DS . 'r' . $status['revision'] . DS . 'middleware' . DS . 'invoke -T r' . $status['revision'];
			$new_hztv->mw            = $status['mw'];
			$new_hztv->released      = Date::toSql();
			$new_hztv->released_by   = User::get('username');
			$new_hztv->license       = $status['license'];
			$new_hztv->fulltxt       = $status['fulltxt'];
			$new_hztv->exportControl = $exportmap[strtoupper($status['exec'])];
			$new_hztv->owner         = $hztv_dev->owner;
			$new_hztv->member        = $hztv_dev->member;
			$new_hztv->vnc_timeout   = $hztv_dev->vnc_timeout;
			$new_hztv->hostreq       = $hztv_dev->hostreq;
			$new_hztv->params        = $status['params'];

			if (!$new_hztv->update())
			{
				$this->setError(Lang::txt('COM_TOOLS_ERROR_UPDATING_INSTANCE'));
				$result = false;
			}
			else
			{
				$this->_setTracAccess($new_hztv->toolname, $new_hztv->codeaccess, $new_hztv->wikiaccess);

				// update tool entry
				$hzt = \Components\Tools\Models\Tool::getInstance($this->_toolid);
				$hzt->add('version', $new_hztv->instance);
				$hzt->update();

				if ($hzt->published != 1)
				{
					$hzt->published = 1;
					// save tool info
					if (!$hzt->update())
					{
						$this->setError(Lang::txt('COM_TOOLS_ERROR_UPDATING_INSTANCE'));
					}
					else
					{
						$this->setMessage(Lang::txt('COM_TOOLS_NOTICE_TOOL_MARKED_PUBLISHED'));
					}
				}

				// unpublish previous version
				if (!$new)
				{
					if ($hzt->unpublishVersion($hztv_cur->instance))
					{
						$this->setMessage(Lang::txt('COM_TOOLS_NOTICE_UNPUBLISHED_PREV_VERSION_DB'));
					}
					else
					{
						$this->setError(Lang::txt('COM_TOOLS_ERR_FAILED_TO_UNPUBLISH_PREV_VERSION_DB'));
					}
				}

				// get version id
				$currentid = $new_hztv->id;

				// save authors for this version
				$objA = new \Components\Tools\Tables\Author($this->database);
				if ($objA->saveAuthors($status['developers'], $currentid, $status['resourceid'], $status['revision'], $status['toolname']))
				{
					$this->setMessage(Lang::txt('COM_TOOLS_AUTHORS_SAVED'));
				}
				else
				{
					$this->setError(Lang::txt('COM_TOOLS_ERROR_SAVING_AUTHORS', $currentid));
				}

				// transfer screenshots
				if ($devid && $currentid)
				{
					include_once __DIR__ . DS . 'screenshots.php';

					$screenshots = new Screenshots();
					if ($screenshots->transfer($devid, $currentid, $status['resourceid']))
					{
						$this->setMessage(Lang::txt('COM_TOOLS_SCREENSHOTS_TRANSFERRED'));
					}
					else
					{
						$this->setError(Lang::txt('COM_TOOLS_ERROR_TRANSFERRING_SCREENSHOTS'));
					}
				}

				include_once __DIR__ . DS . 'resources.php';

				$resource = new Resources();
				// update and publish resource page
				$resource->updatePage($status['resourceid'], $status, '1', $new);
			}
		}

		Log::debug("publish(): checkpoint 7:$result, gather output");

		// Set errors to view
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Set messages to view
		$this->view->messages = $this->getMessages();

		// Output HTML
		if (!($no_html = Request::getInt('no_html', 0)))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=pipeline&task=status&app=' . $alias)
			);
			return;
		}

		$this->view->display();
	}

	/**
	 * Finalize a tool
	 *
	 * @param      string &$out Output messages container
	 * @return     boolean True on success, False if errors
	 */
	protected function _finalizeTool(&$out = '')
	{
		Log::debug("finalizeTool(): checkpoint 1");

		if (!$this->_toolid)
		{
			return false;
		}

		// We need to make sure we don't prepend with PATH_APP if we already have a root-relative path
		$tarball_path = $this->config->get('sourcecodePath', 'site/protected/source');
		if (substr($tarball_path, 0, 1) != DS)
		{
			$tarball_path = PATH_APP . DS . trim($this->config->get('sourcecodePath', 'site/protected/source'), DS);
		}

		Log::debug("finalizeTool(): checkpoint 2");

		// Create a Tool object
		$obj = new \Components\Tools\Tables\Tool($this->database);
		$obj->getToolStatus($this->_toolid, $this->_option, $status, 'dev');

		if (count($status) > 0)
		{
			// Make sure the path exist
			if (!is_dir('/tmp'))
			{
				if (!Filesystem::makeDirectory('/tmp'))
				{
					$out .= Lang::txt('COM_TOOLS_ERR_UNABLE_TO_CREATE_PATH') . ' /tmp';
					return false;
				}
			}

			$token = md5(uniqid());
			$fname = '/tmp/license' . $this->_toolid . '-r' . $status['revision'] . '-' . $token . '.txt';
			$handle = fopen($fname, "w");
			fwrite($handle, $status['license']);
			fclose($handle);
			chmod($fname, 0664);

			if (!file_exists('/usr/bin/finalizetool.sh'))
			{
				$command  = '/usr/bin/sudo -u apps ';
				$command .=    '/usr/bin/finalizetool';
				$command .=       ' -hubdir ' . PATH_ROOT . '/';
				$command .=       ' -title "' . $status['title'] . '"';
				$command .=       ' -version "' . $status['version'] . '"';
				$command .=       ' -license ' . $fname;
				$command .=       ' ' . $status['toolname'];

			}
			else
			{
				$command  = '/usr/bin/sudo -u apps ';
				$command .=    '/usr/bin/finalizetool.sh ' . $status['repohost'];
				$command .=       ' --hubdir ' . PATH_ROOT;
				$command .=       ' --title ' . escapeshellarg($status['title']);
				$command .=       ' --version ' . escapeshellarg($status['version']);
				$command .=       ' --license ' . $fname;
				$command .=       ' --project ' . $status['toolname'];
			}

			Log::debug("finalizeTool(): checkpoint 3: $command");

			if (!$this->_invokescript($command, Lang::txt('COM_TOOLS_NOTICE_VERSION_FINALIZED')))
			{
				$out .= " invoke script failure";
				unlink($fname);
				return false;
			}

			unlink($fname);

			if ($this->getError())
			{
				$out .= " invoke script failure";
				return false;
			}
			// get tarball
			$tar = explode("source tarball: /tmp/", $this->getMessage());
			$tar = $tar[1];

			$file_path = $tarball_path . DS . $status['toolname'];

			// Make sure the upload path exist
			if (!is_dir($file_path))
			{
				if (!Filesystem::makeDirectory($file_path))
				{
					Log::debug("findalizeTool(): failed to create tarball path $file_path");
					$out .= Lang::txt('COM_TOOLS_ERR_UNABLE_TO_CREATE_TAR_PATH');
					return false;
				}
			}

			Log::debug("finalizeTool(): checkpoint 4: " . DS . 'tmp' . DS . $tar . " to " . $file_path . '/' . $tar);

			if (!@copy(DS . 'tmp' . DS . $tar, $file_path . '/' . $tar))
			{
				$out .= " failed to copy $tar to $file_path";
				Log::debug("findalizeTool(): failed tarball copy");
				return false;
			}
			else
			{
				Log::debug("findalizeTool(): deleting tmp files");
				exec ('sudo -u apps rm -f /tmp/' . $tar, $out, $result);
			}
			return true;
		}
		else
		{
			$out = Lang::txt('COM_TOOLS_ERR_CANNOT_RETRIEVE');
			return false;
		}
		return true;
	}

	/**
	 * Set the access for TRAC
	 *
	 * @param      string  $toolname   Tool name
	 * @param      string  $codeaccess Code access level
	 * @param      string  $wikiaccess Wiki access level
	 * @return     boolean True on success
	 */
	protected function _setTracAccess($toolname, $codeaccess, $wikiaccess)
	{
		if (!($hztrac = \Hubzero\Trac\Project::find_or_create('app:' . $toolname)))
		{
			return false;
		}

		switch ($codeaccess)
		{
			case '@OPEN':
				$hztrac->add_user_permission(0, array(
					'BROWSER_VIEW',
					'LOG_VIEW',
					'FILE_VIEW'
				));
			break;

			case '@DEV':
				$hztrac->remove_user_permission(0, array(
					'BROWSER_VIEW',
					'LOG_VIEW',
					'FILE_VIEW'
				));
			break;

			default:
				$this->setError(Lang::txt('COM_TOOLS_WARNING_WIKI_ACCESS_UNKNOWN') . ': ' . $wikiaccess);
			break;
		}

		switch ($wikiaccess)
		{
			case '@OPEN':
				$hztrac->add_user_permission(0, array(
					'WIKI_VIEW',
					'MILESTONE_VIEW',
					'ROADMAP_VIEW',
					'SEARCH_VIEW'
				));
			break;

			case '@DEV':
				$hztrac->remove_user_permission(0, array(
					'WIKI_VIEW',
					'MILESTONE_VIEW',
					'ROADMAP_VIEW',
					'SEARCH_VIEW'
				));
			break;

			default:
				$this->setError(Lang::txt('COM_TOOLS_WARNING_WIKI_ACCESS_UNKNOWN') . ': ' . $wikiaccess);
			break;
		}

		return true;
	}

	/**
	 * Execute a script
	 *
	 * @param      string  $command    Command to execute
	 * @param      string  $successmsg Message to set upon success
	 * @return     boolean True if command executed without errors
	 */
	private function _invokeScript($command, $successmsg)
	{
		$success = true;

		exec($command . ' 2>&1 </dev/null', $rawoutput, $status);

		if ($status != 0)
		{
			$this->setError(Lang::txt('COM_TOOLS_ERR_OPERATION_FAILED'));
			$success = false;
		}

		if ($success)
		{
			$this->setMessage(Lang::txt('COM_TOOLS_SUCCESS') . ': ' . $successmsg);
			// Print out results or errors
			foreach ($rawoutput as $line)
			{
				$this->setMessage($line);
			}
		}
		else
		{
			// Print out results or errors
			foreach ($rawoutput as $line)
			{
				$this->setError($line);
			}
		}

		return $success;
	}

	/**
	 * Authorization checks
	 *
	 * @param      string $assetType Asset type
	 * @param      string $assetId   Asset id to check against
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if (User::get('guest'))
		{
			return;
		}

		// if no admin group is defined, allow superadmin to act as admin
		// otherwise superadmins can only act if they are also a member of the component admin group
		if ($admingroup = trim($this->config->get('admingroup', '')))
		{
			// Check if they're a member of admin group
			$ugs = \Hubzero\User\Helper::getGroups(User::get('id'));
			if ($ugs && count($ugs) > 0)
			{
				$admingroup = strtolower($admingroup);
				foreach ($ugs as $ug)
				{
					if (strtolower($ug->cn) == $admingroup)
					{
						$this->config->set('access-manage-' . $assetType, true);
						$this->config->set('access-admin-' . $assetType, true);
						$this->config->set('access-create-' . $assetType, true);
						$this->config->set('access-delete-' . $assetType, true);
						$this->config->set('access-edit-' . $assetType, true);
					}
				}
			}
		}
		else
		{
			$asset  = $this->_option;
			if ($assetId)
			{
				$asset .= ($assetType != 'component') ? '.' . $assetType : '';
				$asset .= ($assetId) ? '.' . $assetId : '';
			}

			$at = '';
			if ($assetType != 'component')
			{
				$at .= '.' . $assetType;
			}

			// Admin
			$this->config->set('access-admin-' . $assetType, User::authorise('core.admin', $asset));
			$this->config->set('access-manage-' . $assetType, User::authorise('core.manage', $asset));
			// Permissions
			$this->config->set('access-create-' . $assetType, User::authorise('core.create' . $at, $asset));
			$this->config->set('access-delete-' . $assetType, User::authorise('core.delete' . $at, $asset));
			$this->config->set('access-edit-' . $assetType, User::authorise('core.edit' . $at, $asset));
			$this->config->set('access-edit-state-' . $assetType, User::authorise('core.edit.state' . $at, $asset));
			$this->config->set('access-edit-own-' . $assetType, User::authorise('core.edit.own' . $at, $asset));
		}
	}
}
