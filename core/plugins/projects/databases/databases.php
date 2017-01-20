<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

require_once PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'database.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'database.version.php';

/**
 * Projects - Databases plugin
 */
class plgProjectsDatabases extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Store output message
	 *
	 * @var	 array
	 */
	public $dataviewer = 'dataviewer';

	/**
	 * Component name
	 *
	 * @var  string
	 */
	protected $_option = 'com_projects';

	/**
	 * Store internal message
	 *
	 * @var  array
	 */
	protected $_msg = NULL;

	/**
	 * Event call after databases initialized
	 *
	 * @return  array  Plugin name and title
	 */
	public function onAfterInitialise()
	{
		// Databases initialized
	}

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param   string  $alias
	 * @return  array   Plugin name and title
	 */
	public function &onProjectAreas($alias = null)
	{
		// default areas returned to nothing
		$area = array();

		// Check if plugin is restricted to certain projects
		$projects = $this->params->get('restricted') ? \Components\Projects\Helpers\Html::getParamArray($this->params->get('restricted')) : array();

		if (!empty($projects) && $alias)
		{
			if (!in_array($alias, $projects))
			{
				return $area;
			}
		}

		// Hide section completely if not configured
		if ($this->_checkConfig() == false)
		{
			return $area;
		}

		$area = array(
			'name'    => 'databases',
			'title'   => 'Databases',
			'submenu' => 'Assets',
			'show'    => true,
			'icon'    => 'f001'
		);

		return $area;
	}

	/**
	 * Event call to return count of items
	 *
	 * @param   object  $model   Project
	 * @return  array   integer
	 */
	public function &onProjectCount($model)
	{
		$counts['databases'] = 0;

		// Get this area details
		$this->_area = $this->onProjectAreas();
		if (empty($this->_area) || !$model->exists())
		{
			return $counts;
		}

		$database = App::get('db');

		$objPD = new \Components\Projects\Tables\Database($database);
		$total = $objPD->getItems($model->get('id'), array('count' => 1));

		$counts['databases'] = $total;

		return $counts;
	}

	/**
	 * Check if plugin is configured
	 *
	 * @return  boolean
	 */
	protected function _checkConfig()
	{
		if (isset($this->_configured))
		{
			return $this->_configured;
		}

		try
		{
			// Check if the plugin parameters the two mysql accounts are properly set
			$db_opt_rw['driver']   = 'mysqli';
			$db_opt_rw['host']     = $this->params->get('db_host');
			$db_opt_rw['user']     = $this->params->get('db_user');
			$db_opt_rw['password'] = $this->params->get('db_password');
			$db_opt_rw['prefix']   = '';
			$db_rw = JDatabase::getInstance($db_opt_rw);

			$db_opt_ro['driver']   = 'mysqli';
			$db_opt_ro['host']     = $this->params->get('db_host');
			$db_opt_ro['user']     = $this->params->get('db_ro_user');
			$db_opt_ro['password'] = $this->params->get('db_ro_password');
			$db_opt_ro['prefix']   = '';
			$db_ro = JDatabase::getInstance($db_opt_ro);

			if ($db_rw->getErrorNum() > 0 || $db_ro->getErrorNum() > 0)
			{
				$this->_configured = false;
			}
			else
			{
				$this->_configured = true;
			}
		}
		catch (Exception $e)
		{
			$this->_configured = false;
		}

		return $this->_configured;
	}

	/**
	 * Event call to return data for a specific project
	 *
	 * @param   object  $model   Project model
	 * @param   string  $action  Plugin task
	 * @param   string  $areas   Plugins to return data
	 * @return  array   Return array of html
	 */
	public function onProject($model, $action = 'view', $areas = null)
	{
		$arr = array(
			'html'     => '',
			'metadata' => '',
			'message'  => '',
			'error'    => ''
		);

		// Get this area details
		$this->_area = $this->onProjectAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (empty($this->_area) || !in_array($this->_area['name'], $areas))
			{
				return;
			}
		}

		// Check authorization
		if ($model->exists() && !$model->access('member'))
		{
			return $arr;
		}

		// Model
		$this->model = $model;

		// Load component configs
		$this->_config = $model->config();
		$this->gitpath = $this->_config->get('gitpath', '/opt/local/bin/git');

		// Incoming
		$raw_op = Request::getInt('raw_op', 0);
		$action = $action ? $action : Request::getVar('action', 'list');

		// Get this area details
		$this->_area = $this->onProjectAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (empty($this->_area) || !in_array($this->_area['name'], $areas))
			{
				return $arr;
			}
		}

		$this->_database = App::get('db');
		$this->_uid      = User::get('id');

		// Publishing?
		if ($action == 'browser')
		{
			return $this->browser();
		}
		if ($action == 'select')
		{
			return $this->select();
		}

		$act_func = 'act_' . $action;

		if (!method_exists($this, $act_func))
		{
			if ($raw_op)
			{
				print json_encode(array('status' => 'success', 'data' => $table));
				exit();
			}
			else
			{
				$act_func = 'act_list';
			}
		}

		// detect CR as new line
		ini_set('auto_detect_line_endings', true);

		if ($raw_op)
		{
			$this->$act_func();
			exit();
		}
		else
		{
			//Document::addScript('//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
			//Document::addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js');
			Document::addStyleSheet('//ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/themes/smoothness/jquery-ui.css');

			Document::addScript('/core/plugins/projects/databases/res/main.js');
			Document::addStyleSheet('/core/plugins/projects/databases/res/main.css');

			if (file_exists(__DIR__ . '/res/ds.' . $action . '.js'))
			{
				Document::addScript('/core/plugins/projects/databases/res/ds.' . $action . '.js');
			}

			if (file_exists(__DIR__ . '/res/ds.' . $action . '.css'))
			{
				Document::addStyleSheet('/core/plugins/projects/databases/res/ds.' . $action . '.css');
			}

			return $this->$act_func();
		}
	}

	/**
	 * Browser within publications NEW
	 *
	 * @return  string
	 */
	public function select()
	{
		// Incoming
		$props  = Request::getVar('p', '');
		$ajax   = Request::getInt('ajax', 0);
		$pid    = Request::getInt('pid', 0);
		$vid    = Request::getInt('vid', 0);
		$filter = urldecode(Request::getVar('filter', ''));

		// Parse props for curation
		$parts   = explode('-', $props);
		$block   = isset($parts[0]) ? $parts[0] : 'content';
		$step    = (isset($parts[1]) && is_numeric($parts[1]) && $parts[1] > 0) ? $parts[1] : 1;
		$element = (isset($parts[2]) && is_numeric($parts[2]) && $parts[2] > 0) ? $parts[2] : 1;

		// Provisioned project?
		$prov   = $this->model->isProvisioned() ? 1 : 0;

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'databases',
				'name'    =>'selector',
				'layout'  =>'default'
			)
		);

		$view->publication = new \Components\Publications\Models\Publication($pid, null, $vid);

		// On error
		if (!$view->publication->exists())
		{
			// Output error
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  =>'projects',
					'element' =>'files',
					'name'    =>'error'
				)
			);

			$view->title  = '';
			$view->option = $this->_option;
			$view->setError(Lang::txt('PLG_PROJECTS_DATABASES_SELECTOR_ERROR_NO_PUBID'));
			return $view->loadTemplate();
		}

		$view->publication->attachments();

		// Get curation model
		$view->publication->setCuration();

		// Make sure block exists, else use default
		$view->publication->_curationModel->setBlock($block, $step);

		// Set pub assoc and load curation
		$view->publication->_curationModel->setPubAssoc($view->publication);

		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'databases','selector');
		if (!$ajax)
		{
			\Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications','selector');
		}

		$view->option   = $this->_option;
		$view->database = $this->_database;
		$view->model    = $this->model;
		$view->uid      = $this->_uid;
		$view->ajax     = $ajax;
		$view->element  = $element;
		$view->block    = $block;
		$view->step     = $step;
		$view->props    = $props;
		$view->filter   = $filter;

		// Get databases to choose from
		$objPD = new \Components\Projects\Tables\Database($this->_database);
		$view->items = $objPD->getItems($this->model->get('id'), array());

		// Get messages	and errors
		if ($this->getError())
		{
			$view->setError($this->getError());
		}

		$arr = array(
			'html'     => $view->loadTemplate(),
			'metadata' => '',
			'msg'      => '',
			'referer'  => ''
		);

		return $arr;
	}

	/**
	 * List project databases available for publishing
	 *
	 * @return  array
	 */
	public function browser()
	{
		// Incoming
		$ajax      = Request::getInt('ajax', 0);
		$primary   = Request::getInt('primary', 1);
		$versionid = Request::getInt('versionid', 0);

		if (!$ajax)
		{
			return false;
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => 'databases',
				'name'    => 'browser'
			)
		);

		// Get current attachments
		$pContent = new \Components\Publications\Tables\Attachment($this->_database);
		$role  = $primary ? '1' : '0';
		$other = $primary ? '0' : '1';

		$view->attachments = $pContent->getAttachments(
			$versionid,
			$filters = array('role' => $role, 'type' => 'data')
		);

		// Output HTML
		$view->params    = $this->model->params;
		$view->option    = $this->_option;
		$view->database  = $this->_database;
		$view->model     = $this->model;
		$view->uid       = $this->_uid;
		$view->config    = $this->_config;
		$view->title     = $this->_area['title'];
		$view->primary   = $primary;
		$view->versionid = $versionid;

		// Get messages	and errors
		if ($this->getError())
		{
			$view->setError($this->getError());
		}

		$arr = array(
			'html'     => $view->loadTemplate(),
			'metadata' => '',
			'msg'      => '',
			'referer'  => ''
		);

		return $arr;
	}

	/**
	 * List project databases
	 *
	 * @return  array
	 */
	public function act_list()
	{
		// Get project path
		$path = \Components\Projects\Helpers\Html::getProjectRepoPath($this->model->get('alias'));

		// Get project database object
		$objPD = new \Components\Projects\Tables\Database($this->_database);

		// Get database list
		$list  = $objPD->getList($this->model->get('id'));

		if (is_dir($path))
		{
			chdir($path);
			exec($this->gitpath . ' ls-files --exclude-standard |grep ".csv"', $files);
		}

		$list_u = array();
		foreach ($list as $l)
		{
			$info = array();

			if ($l['source_dir'] != '')
			{
				$file = $l['source_dir'] . DS . $l['source_file'];
			}
			else
			{
				$file = $l['source_file'];
			}

			exec($this->gitpath . ' log --pretty=format:%f"|#|"%H"|#|"%cr%n -- "' . $file . '"|head -1', $info);

			if (isset($info[0]))
			{
				$info = explode('|#|', $info[0]);
				$l['source_revision_curr'] = $info[1];
				$l['source_revision_date'] = $info[2];
			}
			else
			{
				$l['source_revision_curr'] = '';
				$l['source_revision_date'] = '';
			}

			$l['source_available'] = file_exists($path . DS . $file) ? true : false;

			if ($l['created_by'] == $this->_uid)
			{
				$l['name'] = 'me';
			}

			$list_u[] = $l;
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder' => 'projects',
				'element'=> 'databases',
				'name'   => 'list'
			)
		);
		$view->model      = $this->model;
		$view->option     = $this->_option;
		$view->dataviewer = $this->dataviewer;
		$view->list       = $list_u;

		return array('html' => $view->loadTemplate());
	}

	/**
	 * Create database
	 *
	 * @return  array
	 */
	public function act_create()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Incoming
		$db_id = Request::getInt('db_id', false);

		// Get project path
		$path = \Components\Projects\Helpers\Html::getProjectRepoPath($this->model->get('alias'));

		$list = array();
		$error = false;

		if (file_exists($path) && is_dir($path))
		{
			chdir($path);
			exec($this->gitpath . ' ls-files --exclude-standard |grep ".csv"', $list);
			sort($list);
		}
		else
		{
			$error = Lang::txt('PLG_PROJECTS_DATABASES_MISSING_REPO');
		}

		// Get project database object
		$objPD = new \Components\Projects\Tables\Database($this->_database);

		// Get database list
		$used_files  = $objPD->getUsedItems($this->model->get('id'));

		$files = array();
		foreach ($list as $l)
		{
			$info = array();
			chdir($path);

			exec($this->gitpath . ' log --date=local --pretty=format:%f"|#|"%H"|#|"%ad%n "' . $l . '"|head -1', $info);
			$info = explode('|#|', $info[0]);

			$file = pathinfo($l);

			if (!in_array($l, $used_files))
			{
				$files[$file['dirname']][] = array(
					'name' => $file['basename'],
					'hash' => $info[1],
					'date' => $info[2]
				);
			}
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder' => 'projects',
				'element'=> 'databases',
				'name'   => 'create'
			)
		);

		$view->model   = $this->model;
		$view->option  = $this->_option;
		$view->files   = $files;
		$view->msg     = NULL;

		if ($error)
		{
			$view->setError($error);
		}

		// Get project database object
		$objPD = new \Components\Projects\Tables\Database($this->_database);

		if ($objPD->loadRecord($db_id))
		{
			$view->db_id = $db_id;
			$view->dir   = trim($objPD->source_dir, '/');
			$view->file  = trim($objPD->source_file, '/');
			$view->title = $objPD->title;
			$view->desc  = $objPD->description;
		}

		return array('html' => $view->loadTemplate());
	}

	/**
	 * Preview data
	 *
	 * @return  void
	 */
	public function act_preview_data()
	{
		// Incoming
		$file = Request::getVar('file', false);
		$dir  = Request::getVar('dir', '');

		if (!$file)
		{
			print json_encode(array('status' => 'failed', 'msg' => Lang::txt('PLG_PROJECTS_DATABASES_INVALID_FILE')));
			return;
		}

		// Get project path
		$path = \Components\Projects\Helpers\Html::getProjectRepoPath($this->model->get('alias'));

		if ($dir != '')
		{
			$path .= DS . $dir;
		}

		if (file_exists($path . DS . $file) && ($handle = fopen($path . '/' . $file, "r")) !== FALSE)
		{
			$table = array();
			$dd = array();

			$sub_dirs = array();
			$list = array();
			chdir($path);
			exec('find . -type d -not \(-name ".?*" -prune \)', $list);
			foreach ($list as $d)
			{
				$d = ltrim($d, './');
				if ($d != '.' && $d != '')
				{
					$sub_dirs[] = $d;
				}
			}

			$table['repo'] = array(
				'prj_alias' => $this->model->get('alias'),
				'wd'        => trim($dir, '/'),
				'base'      => '/projects/' . $this->model->get('alias') . '/files/?action=download&subdir=' . trim($dir, '/'),
				'sub_dirs'  => $sub_dirs
			);

			// Check if expert mode CSV
			$expert_mode  = false;
			$col_labels   = fgetcsv($handle);
			$col_prop     = fgetcsv($handle);
			$data_start   = fgetcsv($handle);

			if (isset($data_start[0]) && $data_start[0] == 'DATASTART')
			{
				$expert_mode = true;
			}

			$count = 0;
			$display_count = 0;
			$limit = 100;

			// Non expert mode
			if (!$expert_mode)
			{
				$handle = fopen($path . '/' . $file, "r");
				$col_labels = fgetcsv($handle);

				$type_info = array();
				while ($r = fgetcsv($handle))
				{
					$col_vals = array();

					for ($i = 0; $i < count($col_labels); $i++)
					{
						$val = isset($r[$i]) ? mb_convert_encoding(trim($r[$i]), "UTF-8") : '';

						$col_vals[] = $val;

						$type_info[$i]['type'] = isset($type_info[$i]['type']) ? $type_info[$i]['type'] : false;
						if (isset($type_info[$i]['max_len']))
						{
							$type_info[$i]['max_len'] = $type_info[$i]['max_len'] > strlen($val) ? $type_info[$i]['max_len'] : strlen($val);
						}
						else
						{
							$type_info[$i]['max_len'] = strlen($val);
						}

						$type_info[$i]['type'] = $this->_guess_data_type($val, $type_info[$i]['type'], $type_info[$i]['max_len']);
					}

					if ($count < $limit)
					{
						$table['data'][] = $col_vals;
						$display_count++;
					}

					$count++;
				}

				for ($i = 0; $i < count($col_labels); $i++)
				{
					$label = trim($col_labels[$i]);
					if ($label == '')
					{
						$label = 'Column-' . $i;
					}

					$dd[$i]['type'] = $type_info[$i]['type'];
					switch ($dd[$i]['type'])
					{
						case 'numeric':
						case 'float':
						case 'int':
						case 'date':
							$dd[$i]['align'] = 'right';
							break;
						default:
							$dd[$i]['align'] = 'left';
					}

					$table['header'][] = array('sTitle' => $label, 'sClass' => $dd[$i]['align']);
					$dd[$i]['label'] = $label;
					$dd[$i]['idx'] = $i;
				}
			}
			else
			{
				while ($r = fgetcsv($handle))
				{
					if ($count < $limit)
					{
						$col_vals = array();

						for ($i = 0; $i < count($col_labels); $i++)
						{
							$val = isset($r[$i]) ? mb_convert_encoding(trim($r[$i]), 'UTF-8') : '';

							$col_vals[] = $val;
						}

						$table['data'][] = $col_vals;

						$display_count++;
					}

					$count++;
				}

				for ($i = 0; $i < count($col_labels); $i++)
				{
					$label = trim($col_labels[$i]);
					if ($label == '')
					{
						$label = 'Column-' . $i;
					}

					$prop = isset($col_prop[$i]) ? '{' . trim($col_prop[$i]) . '}': '{}';
					$dd[$i] = json_decode($prop, true);
					$dd[$i]['type'] = isset($dd[$i]['type']) ? $dd[$i]['type'] : 'text_large';

					if (!isset($dd[$i]['align']))
					{
						switch ($dd[$i]['type'])
						{
							case 'numeric':
							case 'float':
							case 'int':
							case 'date':
								$dd[$i]['align'] = 'right';
								break;
							default:
								$dd[$i]['align'] = 'left';
						}
					}

					$table['header'][] = array('sTitle' => $label, 'sClass' => $dd[$i]['align']);
					$dd[$i]['label'] = $label;
					$dd[$i]['idx'] = $i;
				}
			}

			$table['dd'] = $dd;
			$table['rec_total'] = $count;
			$table['rec_display'] = $display_count;

			print json_encode(array('status' => 'success', 'data' => $table));
		}
	}

	/**
	 * Guess data type
	 *
	 * @param   object   $data
	 * @param   string   $type
	 * @param   integer  $max_len
	 * @return  string
	 */
	protected function _guess_data_type($data, $type, $max_len)
	{
		$data = trim($data);

		if ($type == 'text_large' || $data == '')
		{
			return $type;
		}

		if (!$type)
		{
			if (is_numeric($data))
			{
				if (strpos($data, '.') === false)
				{
					$type = 'int';
				}
				else
				{
					$type = 'float';
				}
			}
			else
			{
				if (filter_var($data, FILTER_VALIDATE_EMAIL) !== false)
				{
					$type = 'email';
				}
				elseif (filter_var($data, FILTER_VALIDATE_URL) !== false)
				{
					$type = 'link';
				}
				elseif ($max_len < 50)
				{
					$type = 'text_small';
				}
				else
				{
					$type = 'text_large';
				}
			}
		}
		else
		{
			if ($type == 'int' || $type == 'float' && is_numeric($data))
			{
				if ($type == 'int' && strpos($data, '.') === false)
				{
					$type = 'int';
				}
				else
				{
					$type = 'float';
				}
			}
			else
			{
				if ($type == 'text_small' && $max_len < 50)
				{
					$type = 'text_small';
				}
				elseif (filter_var($data, FILTER_VALIDATE_EMAIL) !== false)
				{
					$type = 'email';
				}
				elseif (filter_var($data, FILTER_VALIDATE_URL) !== false)
				{
					$type = 'link';
				}
				else
				{
					$type = 'text_large';
				}
			}
		}

		return $type;
	}

	/**
	 * act_add_new_finish
	 *
	 * @return  void
	 */
	public function act_create_database()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Incoming
		$file  = Request::getVar('file', false);
		$dir   = Request::getVar('dir', '');
		$title = Request::getVar('title', '');
		$desc  = Request::getVar('desc', '');
		$db_id = Request::getVar('db_id', '');
		$d     = Request::getVar('dd', false);
		$d     = json_decode($d, true);

		$db    = $this->get_ds_db($this->model->get('id'));
		$table = array();

		// Add new or Recreate
		$recreate = ($db_id != '') ? true : false;

		if (!$file)
		{
			print json_encode(array('status' => 'failed', 'msg' => Lang::txt('PLG_PROJECTS_DATABASES_INVALID_FILE')));
			return;
		}

		$repo_base = '/projects/' . $this->model->get('alias') . '/files/?action=download&subdir=' . trim($dir, '/');

		// Get project path
		$path = \Components\Projects\Helpers\Html::getProjectRepoPath($this->model->get('alias'));
		$path .= DS;

		if ($dir != '')
		{
			$path .= $dir . DS;
		}

		$table['name'] = 'prj_db_' . $this->model->get('id') . '_' . sha1($dir . DS . $file);

		if ($recreate)
		{
			$sql = "DROP TABLE IF EXISTS `" . $table['name'] . "` ";
			$db->setQuery($sql);
			$db->query();
		}

		$sql = "CREATE TABLE `" . $table['name'] . "` ";

		$table['cols'][] = '__ds_rec_id int(11) NOT NULL AUTO_INCREMENT NOT NULL';

		if (file_exists($path . DS . $file) && ($handle = fopen($path . DS . $file, "r")) !== false)
		{
			// Get commit hash
			chdir($path);
			exec($this->gitpath . ' log --pretty=format:%H ' . escapeshellarg($file) . '|head -1', $hash);
			$hash = $hash[0];

			// Check if expert mode CSV
			$expert_mode = false;
			$col_labels  = fgetcsv($handle);
			$col_prop    = fgetcsv($handle);
			$data_start  = fgetcsv($handle);

			if (isset($data_start[0]) && $data_start[0] == 'DATASTART')
			{
				$expert_mode = true;
			}

			// Non expert mode
			if (!$expert_mode) {
				$handle = fopen($path . '/' . $file, "r");
				$col_labels = fgetcsv($handle);
			}

			$dd = array();

			for ($i = 0; $i < count($col_labels); $i++)
			{
				$label = trim($col_labels[$i]);
				if ($label == '')
				{
					$label = 'Column-' . $i;
				}

				$col_name = strtolower(preg_replace('/\W/', '_', $label));
				$col_name = substr($col_name, 0, (63 - strlen($i))) . '_' . $i;

				switch ($d[$i]['type'])
				{
					case 'text_small';
						$col_type = 'VARCHAR(64)';
						break;
					case 'text_large';
						$col_type = 'TEXT';
						break;
					case 'numeric';
						$col_type = 'NUMERIC (65,4)';
						break;
					case 'float';
						$col_type = 'DOUBLE';
						break;
					case 'int';
						$col_type = 'BIGINT';
						break;
					case 'link';
					case 'image';
						$col_type = 'VARCHAR(2083)';
						break;
					case 'email';
						$col_type = 'VARCHAR(256)';
						break;
					case 'date';
						$col_type = 'DATE';
						break;
					case 'datetime';
						$col_type = 'DATETIME';
						break;
					default:
						$col_type = 'text';
				}

				$table['cols'][] ='`' . $col_name . '` ' . $col_type . ' DEFAULT NULL';
				if (isset($d[$i]))
				{
					$dd['cols'][$table['name'] . '.' . $col_name] = $d[$i];
				}
			}

			$sql .= '(' . implode(', ', $table['cols']) . ', PRIMARY KEY (`__ds_rec_id`))
				ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT=' . $db->quote($file);
			$db->setQuery($sql);
			$db->query();

			$count = 0;
			$vals = array();

			while ($r = fgetcsv($handle))
			{
				$vals = array();
				for ($i = 0; $i < count($col_labels); $i++)
				{
					$vals[] = (isset($r[$i]) && $r[$i] != '') ? $db->quote(trim($r[$i])) : 'NULL';
				}

				$sql = "INSERT INTO " . $table['name'] . " VALUES (NULL, " . implode(", ", $vals) . ")";

				$db->setQuery($sql);
				$db->query();
			}

			$dd['project']   = $this->model->get('id');
			$dd['database']  = 'prj_db_' . $this->model->get('id');
			$dd['table']     = $table['name'];
			$dd['title']     = $title;
			$dd['pk']        = $table['name'] . '.__ds_rec_id';
			$dd['repo_base'] = $repo_base;
			$dd = json_encode($dd);

			// Get project database object
			$objPD = new \Components\Projects\Tables\Database($this->_database);

			// Recreate or Expert mode
			if ($recreate)
			{
				$objPD->loadRecord($db_id);

				if ($objPD->project != $this->model->get('id'))
				{
					exit;
				}

				$objPD->title           = $title;
				$objPD->source_revision = $hash;
				$objPD->description     = $desc;
				$objPD->data_definition = $dd;
				$objPD->updated         = Date::toSql();
				$objPD->updated_by      = $this->_uid;
				$msg = Lang::txt('PLG_PROJECTS_DATABASES_UPDATED_DATABASE') . ' "' . $title . '"' . Lang::txt('PLG_PROJECTS_DATABASES_IN_PROJECT');
			}
			else
			{
				$objPD->project         = $this->model->get('id');
				$objPD->database_name   = $table['name'];
				$objPD->title           = $title;
				$objPD->source_file     = $file;
				$objPD->source_dir      = $dir;
				$objPD->source_revision = $hash;
				$objPD->description     = $desc;
				$objPD->data_definition = $dd;
				$objPD->created         = Date::toSql();
				$objPD->created_by      = $this->_uid;

				$msg = Lang::txt('PLG_PROJECTS_DATABASES_CREATED_DATABASE') . ' "' . $title . '"' . Lang::txt('PLG_PROJECTS_DATABASES_IN_PROJECT');
			}

			// Store new/update record
			$objPD->store();

			// Update source CSV file
			$this->_save_csv($objPD->id);

			// Success
			if ($objPD->id)
			{
				// Record project activity
				$this->model->recordActivity(
					str_replace("'", "\'", $msg),
					$objPD->id,
					'databases',
					Route::url('index.php?option=' . $this->_option . '&alias=' . $this->model->get('alias') . '&active=databases'),
					'databases',
					1
				);
				ob_clean();
				$this->_msg = Lang::txt('PLG_PROJECTS_DATABASES_CREATED');
			}
		}

		$url = str_replace($_SERVER['SCRIPT_URL'], '', $_SERVER['SCRIPT_URI']) . "/projects/" . $this->model->get('alias') . "/databases/";

		print json_encode(array('status' => 'success', 'data' => $url));

		// Success message
		if (!empty($this->_msg))
		{
			\Notify::message($this->_msg, 'success', 'projects');
		}

		return;
	}

	/**
	 * Save updated CSV file with headers
	 *
	 * @param   integer  $id  Database ID
	 * @return  void
	 */
	public function _save_csv($id)
	{
		$db = $this->get_ds_db($this->model->get('id'));

		// Get project database object
		$objPD = new \Components\Projects\Tables\Database($this->_database);

		// Get project path
		$path  = \Components\Projects\Helpers\Html::getProjectRepoPath($this->model->get('alias'));
		$path .= DS;

		if ($objPD->loadRecord($id))
		{
			$db = $this->get_ds_db($objPD->project);
			$table = $objPD->database_name;
			$title = $objPD->title;
			$file  = $objPD->source_file;
			$dir   = ($objPD->source_dir != '') ? $objPD->source_dir . DS : '';
			$dd    = json_decode($objPD->data_definition, true);

			$header = array();
			$field_list = array();
			foreach ($dd['cols'] as $field => $prop)
			{
				$field_list[] = $field;

				$header[0][] = $prop['label'];

				unset($prop['label']);
				unset($prop['idx']);
				unset($prop['styles']);

				$header[1][] = rtrim(ltrim(str_replace('","', "\",\r\n\"", json_encode($prop)), '{'), '}');
			}

			$header[2] = array('DATASTART');

			$path .= $dir;
			$fp = fopen($path . $file, 'w+');
			foreach ($header as $h)
			{
				fputcsv($fp, $h);
			}

			$sql = "SELECT " . implode(', ', $field_list) . " FROM $table";
			$db->setQuery($sql);
			$res = $db->query();

			while ($row = $res->fetch_array(MYSQLI_NUM))
			{
				fputcsv($fp, $row);
			}

			fclose($fp);

			// Commit update file
			$commit_message = Lang::txt('PLG_PROJECTS_DATABASES_UPDATED_FILE') . ' ' . escapeshellarg($file);
			$author = escapeshellarg(User::get('name') . ' <' . User::get('email') . '> ');

			chdir($path);
			exec($this->gitpath . ' add ' . escapeshellarg($file));
			exec($this->gitpath . ' commit ' . escapeshellarg($file)
				. ' -m "' . $commit_message . '"'
				. ' --author="' . $author . '" 2>&1');

			// Update source_revision with the current commit hash
			chdir($path);
			exec($this->gitpath . ' log --pretty=format:%H ' . escapeshellarg($file) . '|head -1', $hash);
			$hash = isset($hash[0]) ? $hash[0] : 0;
			$objPD->source_revision = $hash;
			$objPD->store();

			$msg = Lang::txt('PLG_PROJECTS_DATABASES_UPDATED_FILE') . ' "' . $file . '"' . Lang::txt('PLG_PROJECTS_DATABASES_IN_PROJECT');
			$this->model->recordActivity(
				str_replace("'", "\'", $msg),
				$file,
				'files',
				Route::url('index.php?option=' . $this->_option . '&alias=' . $this->model->get('alias') . '&active=files'),
				'files',
				1
			);
			ob_clean();
		}
	}

	/**
	 * Delete database
	 *
	 * @return  array
	 */
	public function act_delete()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Incoming
		$id = Request::getVar('db_id', false);
		$ds_db = $this->get_ds_db($this->model->get('id'));

		// Get project database object
		$objPD = new \Components\Projects\Tables\Database($this->_database);

		if ($objPD->loadRecord($id))
		{
			$table = $objPD->database_name;
			$title = $objPD->title;

			if ($table && $table != ''&& $objPD->project == $this->model->get('id'))
			{
				// Removing the record for this database
				$objPD->delete();

				// Removing mysql table for this database
				$sql = "DROP TABLE $table";
				$ds_db->setQuery($sql);
				$ds_db->query();

				$this->_msg = Lang::txt('PLG_PROJECTS_DATABASES_DELETED');
			}

			// Record project activity
			$msg = Lang::txt('PLG_PROJECTS_DATABASES_REMOVED_DATABASE') . ' "' . $title . '"' . Lang::txt('PLG_PROJECTS_DATABASES_FROM_PROJECT');
			$this->model->recordActivity(
				str_replace("'", "\'", $msg),
				$id,
				'databases',
				Route::url('index.php?option=' . $this->_option . '&alias=' . $this->model->get('alias') . '&active=databases'),
				'databases',
				1
			);
		}

		$url = str_replace($_SERVER['SCRIPT_URL'], '', $_SERVER['SCRIPT_URI']) . "/projects/" . $this->model->get('alias') . "/databases/";

		// Pass success message
		if (!empty($this->_msg))
		{
			\Notify::message($this->_msg, 'success', 'projects');
		}

		// Redirect
		App::redirect($url);
		return;
	}

	/**
	 * Update database
	 *
	 * @return  array
	 */
	public function act_update()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Incoming
		$id          = Request::getVar('db_id', false);
		$title       = Request::getVar('db_title', false);
		$description = Request::getVar('db_description', false);

		// Get project database object
		$objPD = new \Components\Projects\Tables\Database($this->_database);

		if ($objPD->loadRecord($id))
		{
			$dd = json_decode($objPD->data_definition, true);

			if ($title != '' && $objPD->project == $this->model->get('id'))
			{
				// Setting title and description
				$objPD->title           = $title;
				$dd['title']            = $title;
				$objPD->description     = $description;
				$objPD->data_definition = json_encode($dd);
				$objPD->store();

				$this->_msg = Lang::txt('PLG_PROJECTS_DATABASES_UPDATED');
			}
		}

		$url = str_replace($_SERVER['SCRIPT_URL'], '', $_SERVER['SCRIPT_URI']) . "/projects/" . $this->model->get('alias') . "/databases/";

		// Pass success message
		if (!empty($this->_msg))
		{
			\Notify::message($this->_msg, 'success', 'projects');
		}

		// Redirect
		App::redirect($url);
		return;
	}

	/**
	 * Making a copy of the database for publications
	 *
	 * Function to be called outside of the databases plugin
	 *
	 * @param   integer  $identifier  Database ID or name
	 * @param   object   $project     Project object
	 * @param   string   $base_path   File-repository base path
	 * @return  integer
	 */
	public function clone_database($identifier = 0, $project = null, $base_path = null)
	{
		if (!$identifier || $project == null)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_DATABASES_ERROR_MISSING_ID'));
			return false;
		}

		$db    = App::get('db');
		$ds_db = $this->get_ds_db($project->get('id'));

		// Load database record
		$objPD = new \Components\Projects\Tables\Database($db);
		if (!$objPD->loadRecord($identifier))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_DATABASES_ERROR_LOAD_RECORD'));
			return false;
		}

		$dd = json_decode($objPD->data_definition, true);

		// Get last version
		$objPDV  = new \Components\Projects\Tables\DatabaseVersion($db);
		$version = $objPDV->getMaxVersion($objPD->database_name) + 1;

		// Start cloning
		$orig_table = $dd['table'];
		$new_table  = $orig_table . '_' . $version;

		$dd['table'] = $new_table;

		$new_cols = array();
		foreach ($dd['cols'] as $col => $prop)
		{
			$col = explode('.', $col);
			$new_cols[$new_table . '.' . $col[1]] = $prop;
		}

		$dd['cols'] = $new_cols;
		$dd['pk']   = $new_table . '.__ds_rec_id';

		if ($base_path != null)
		{
			$dd['repo_base'] = $base_path;
		}

		$dd = json_encode($dd);

		// Make new version record
		$objPDV->database_name   = $objPD->database_name;
		$objPDV->version         = $version;
		$objPDV->data_definition = $dd;

		$objPDV->store();

		// Clone original table
		$sql = "CREATE TABLE $new_table LIKE $orig_table";
		$ds_db->setQuery($sql);
		$ds_db->query();

		// Copy table contents
		$sql = "INSERT INTO $new_table SELECT * FROM $orig_table";
		$ds_db->setQuery($sql);
		$ds_db->query();

		return $version;
	}

	/**
	 * Remove databases used in a draft publications
	 *
	 * @param   integer  $identifier  Database ID or name
	 * @param   object   $project     Project object
	 * @param   integer  $version     Database version
	 * @return  bool
	 */
	public function remove_database($identifier = 0, $project = null, $version = null)
	{
		if (!$identifier || $project == null)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_DATABASES_ERROR_MISSING_ID'));
			return false;
		}

		if ($version === null || trim($version) == '')
		{
			$this->setError(Lang::txt('PLG_PROJECTS_DATABASES_ERROR_INVALID_VERSION'));
			return false;
		}

		$db    =  App::get('db');
		$ds_db = $this->get_ds_db($project->get('id'));

		// Load database record
		$objPD = new \Components\Projects\Tables\Database($db);
		if (!$objPD->loadRecord($identifier))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_DATABASES_ERROR_LOAD_RECORD'));
			return false;
		}

		// Remove record from database versions table
		$sql = 'DELETE FROM `#__project_database_versions` WHERE'
			. ' database_name = ' . $db->quote($objPD->database_name)
			. ' AND version = ' . $db->quote($version);

		$db->setQuery($sql);
		$db->query();

		// Remove database table
		$table_name = $objPD->database_name . '_' . $version;
		$sql = 'DROP TABLE ' . $table_name;
		$ds_db->setQuery($sql);
		return $ds_db->query();
	}

	/**
	 * get_ds_db
	 *
	 * @param   string  $id
	 * @return  object
	 */
	public function get_ds_db($id)
	{
		$opt = array();

		// Create database if it doesn't exist
		$sql = "CREATE DATABASE IF NOT EXISTS " . 'prj_db_' . $id;

		$opt['driver']   = 'mysqli';
		$opt['host']     = $this->params->get('db_host');
		$opt['user']     = $this->params->get('db_user');
		$opt['password'] = $this->params->get('db_password');
		$opt['prefix']   = '';

		$db = JDatabase::getInstance($opt);

		$db->setQuery($sql);
		$db->query();

		$opt['database'] = 'prj_db_' . $id;

		$db = JDatabase::getInstance($opt);

		return $db;
	}
}
