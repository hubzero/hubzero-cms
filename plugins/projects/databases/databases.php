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
 * @author    Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_JEXEC') or die('Restricted access');


error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);


jimport('joomla.plugin.plugin');

/**
 * Projects - Databases plugin
 */
class plgProjectsDatabases extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function plgProjectsDatabases(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin('projects', 'databases');
		$this->_params = new JParameter($this->_plugin->params);

		// Dataviewer
		$this->dataviewer = 'dataviewer';

		// Load component configs
		$this->_config 		=& JComponentHelper::getParams('com_projects');
		$this->gitpath 		= $this->_config->get('gitpath', '/opt/local/bin/git');
		$this->prefix 		= $this->_config->get('offroot', 0) ? '' : JPATH_ROOT ;

		// Output collectors
		$this->_referer 	= '';
		$this->_message 	= array();
	}

	/**
	 * Event call after databases initialized
	 * 
	 * @return     array   Plugin name and title
	 */
	public function onAfterInitialise()
	{
		// Databases initialized
	}

	/**
	 * Event call to determine if this plugin should return data
	 * 
	 * @return     array   Plugin name and title
	 */
	public function &onProjectAreas()
	{
		$area = array(
			'name' => 'databases',
			'title' => 'Databases'
		);

		return $area;
	}

	/**
	 * Event call to return count of items
	 * 
	 * @param      object  $project 		Project
	 * @param      integer &$counts 
	 * @return     array   integer
	 */
	public function &onProjectCount($project, &$counts)
	{
		$database =& JFactory::getDBO();

		$objPD = new ProjectDatabase($database);
		$total = $objPD->getItems($project->id, array('count' => 1));

		$counts['databases'] = $total;

		return $counts;
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
	public function onProject ($project, $option, $authorized, 
		$uid, $msg = '', $error = '', 
		$action = 'view', $areas = null, $case)
	{

		// Check if the plugin parameters the two mysql accounts are properly set
		$db_opt_rw['driver'] 		= 'mysqli';
		$db_opt_rw['host'] 		= $this->_params->get('db_host');
		$db_opt_rw['user'] 		= $this->_params->get('db_user');
		$db_opt_rw['password'] 	= $this->_params->get('db_password');
		$db_opt_rw['prefix'] 		= '';
		$db_rw = &JDatabase::getInstance($db_opt_rw);

		$db_opt_ro['driver'] 		= 'mysqli';
		$db_opt_ro['host'] 		= $this->_params->get('db_host');
		$db_opt_ro['user'] 		= $this->_params->get('db_ro_user');
		$db_opt_ro['password'] 	= $this->_params->get('db_ro_password');
		$db_opt_ro['prefix'] 		= '';
		$db_ro = &JDatabase::getInstance($db_opt_ro);

		if (get_class($db_rw) == 'JException' || get_class($db_ro) == 'JException')
		{
			// Output HTML
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'projects',
					'element'=>'databases',
					'name'=>'config_error'
				)
			);

			return array('html'=>$view->loadTemplate());
		}

		$arr = array(
			'html'=>'',
			'metadata'=>'',
			'message'=>'',
			'error'=>''
		);

		// Get this area details
		$this->_area = $this->onProjectAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) 
		{
			if (empty($this->_area) || !in_array($this->_area['name'], $areas)) 
			{
				return;
			}
		}

		// Is the user logged in?
		if ( !$authorized && !$project->owner ) 
		{
			return $arr;
		}

		// Load language file
		JPlugin::loadLanguage( 'plg_projects_databases' );

		$this->_project 	= $project;
		$this->_option 		= $option;
		$this->_database 	=& JFactory::getDBO();
		$this->_authorized  = $authorized;
		$this->_uid = $uid;
		if (!$this->_uid) 
		{
			$juser =& JFactory::getUser();
			$this->_uid = $juser->get('id');
		}

		// Enable views
		ximport('Hubzero_View_Helper_Html');
		ximport('Hubzero_Plugin_View');

		// Incoming
		$raw_op = JRequest::getInt('raw_op', 0);
		$action = $action ? $action : JRequest::getVar('action', 'list');
		
		// Publishing?
		if ($action == 'browser')
		{
			return $this->browser(); 
		}

		$act_func = 'act_' . $action;

		if (!method_exists($this, $act_func)) 
		{
			if ($raw_op) 
			{
				print json_encode(array('status'=>'success', 'data'=>$table));
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
			$this->$act_func(); exit();
		} 
		else 
		{
			$document =& JFactory::getDocument();

			$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');

			$document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js');
			$document->addStyleSheet('//ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/themes/smoothness/jquery-ui.css');

			$document->addScript('/plugins/projects/databases/res/main.js');
			$document->addStyleSheet('/plugins/projects/databases/res/main.css');

			if (file_exists(JPATH_PLUGINS . '/projects/databases/res/ds.' . $action . '.js')) 
			{
				$document->addScript('/plugins/projects/databases/res/ds.' . $action . '.js');
			}

			if (file_exists(JPATH_PLUGINS . '/projects/databases/res/ds.' . $action . '.css')) 
			{
				$document->addStyleSheet('/plugins/projects/databases/res/ds.' . $action . '.css');
			}

			return $this->$act_func();
		}
	}
	
	/**
	 * List project databases available for publishing
	 * 
	 * @return     array
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
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'databases',
				'name'=>'browser'
			)
		);
		
		// Get current attachments
		$pContent = new PublicationAttachment( $this->_database );
		$role 	= $primary ? '1' : '0';
		$other 	= $primary ? '0' : '1';
		
		$view->attachments = $pContent->getAttachments($versionid, $filters = array('role' => $role, 'type' => 'data'));
		
		// Output HTML
		$view->params 		= new JParameter( $this->_project->params );
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->config 		= $this->_config;	
		$view->title		= $this->_area['title'];
		$view->primary		= $primary;
		$view->versionid	= $versionid;
		
		// Get messages	and errors	
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		$html =  $view->loadTemplate();
		
		$arr = array(
			'html' => $html,
			'metadata' => '',
			'msg' => '',
			'referer' => ''
		);
		
		return $arr;
	}

	/**
	 * List project databases
	 * 
	 * @return     array
	 */
	public function act_list()
	{
		// Get project path
		$path = ProjectsHelper::getProjectPath($this->_project->alias, 
						$this->_config->get('webpath'), $this->_config->get('offroot', 0));

		chdir($path);
		exec($this->gitpath . ' ls-files --exclude-standard |grep ".csv"', $files);

		// Get project database object
		$objPD = new ProjectDatabase($this->_database);

		// Get database list
		$list  = $objPD->getList($this->_project->id);

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
			$info = explode('|#|', $info[0]);
			$l['source_revision_curr'] = $info[1];
			$l['source_revision_date'] = $info[2];
			$l['source_available'] = file_exists($path . DS . $file) ? true : false;

			if ($l['created_by'] == $this->_uid)
			{
				$l['name'] = 'me';
			}

			$list_u[] = $l;
		}

		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'databases',
				'name'=>'list'
			)
		);
		$view->project 		= $this->_project;
		$view->option 		= $this->_option;
		$view->dataviewer 	= $this->dataviewer;
		$view->list 		= $list_u;

		return array('html'=>$view->loadTemplate());
	}

	/**
	 * Create database
	 * 
	 * @return     array
	 */
	public function act_create()
	{
		// Incoming
		$db_id = JRequest::getInt('db_id', false);

		// Get project path
		$path = ProjectsHelper::getProjectPath($this->_project->alias, 
						$this->_config->get('webpath'), $this->_config->get('offroot', 0));

		chdir($path);
		exec($this->gitpath . ' ls-files --exclude-standard |grep ".csv"', $list);
		sort($list);

		// Get project database object
		$objPD = new ProjectDatabase($this->_database);

		// Get database list
		$used_files  = $objPD->getUsedItems($this->_project->id);

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
					'name'=>$file['basename'],
					'hash'=>$info[1],
					'date'=>$info[2]
				);
			}
		}

		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'databases',
				'name'=>'create'
			)
		);

		$view->project = $this->_project;
		$view->option = $this->_option;
		$view->files = $files;

		// Get project database object
		$objPD = new ProjectDatabase($this->_database);

		if ($objPD->loadRecord($db_id))
		{
			$view->db_id = $db_id;
			$view->dir = trim($objPD->source_dir, '/');
			$view->file = trim($objPD->source_file, '/');
			$view->title = $objPD->title;
			$view->desc = $objPD->description;
		}

		return array('html'=>$view->loadTemplate());
	}

	/**
	 * Preview data
	 * 
	 */
	public function act_preview_data()
	{
		// Incoming
		$file = JRequest::getVar('file', false);
		$dir = JRequest::getVar('dir', '');

		if (!$file) 
		{
			print json_encode(array('status'=>'failed', 'msg'=>'Invalid File'));
			return;
		}

		// Get project path
		$path = ProjectsHelper::getProjectPath($this->_project->alias, 
						$this->_config->get('webpath'), $this->_config->get('offroot', 0));
		$path .= DS;

		if ($dir != '') 
		{
			$path .= $dir . DS;
		}
		

		if (file_exists($path . DS . $file) && ($handle = fopen($path . '/' . $file, "r")) !== FALSE) 
		{
			$table = array();
			$dd = array();
			
			$sub_dirs = array();
			$list =array();
			chdir($path);
			exec('find . -type d -not \( -name ".?*" -prune \)', $list);
			foreach ($list as $d) {
				$d = ltrim($d, './');
				if ($d != '.' && $d != '') {
					$sub_dirs[] = $d;
				}
			}
			
			
			$table['repo'] = array(
				'prj_alias' => $this->_project->alias,
				'wd' => trim($dir, '/'),
				'base' => '/projects/' . $this->_project->alias . '/files/?action=download&subdir=' . trim($dir, '/'),
				'sub_dirs' => $sub_dirs
			);

			// Check if expert mode CSV
			$expert_mode = false;
			$col_labels = fgetcsv($handle);
			$col_prop = fgetcsv($handle);
			$data_start = fgetcsv($handle);
			
			if (isset($data_start[0]) && $data_start[0] == 'DATASTART') 
			{
				$expert_mode = true;
			}
			
			$count = 0;
			$display_count = 0;
			$limit = 20;
			
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
						$val = isset($r[$i]) ? trim($r[$i]) : '';

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

					$table['header'][] = array('sTitle'=>$label, 'sClass'=>$dd[$i]['align']);
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
							$val = isset($r[$i]) ? trim($r[$i]) : '';

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

					$table['header'][] = array('sTitle'=>$label, 'sClass'=>$dd[$i]['align']);
					$dd[$i]['label'] = $label;
					$dd[$i]['idx'] = $i;
				}
			}

			$table['dd'] = $dd;
			$table['rec_total'] = $count;
			$table['rec_display'] = $display_count;

			print json_encode(array('status'=>'success', 'data' => $table));
		}
	}

	/**
	 * Guess data type
	 * 
	 * @param      object  $data
	 * @param      string  $type 
	 * @param      integer $max_len 
	 * @return     string
	 */
	public function _guess_data_type($data, $type, $max_len)
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
	 * @return     void
	 */
	public function act_create_database()
	{
		// Incoming
		$file 	= JRequest::getVar('file', false);
		$dir 	= JRequest::getVar('dir', '');
		$title 	= JRequest::getVar('title', '');
		$desc 	= JRequest::getVar('desc', '');
		$db_id 	= JRequest::getVar('db_id', '');
		$d 		= JRequest::getVar('dd', false);
		$d 		= json_decode($d, true);
			
		$db 	= $this->get_ds_db($this->_project->id);
		$juser 	= &JFactory::getUser();
		$table 	= array();

		// Add new or Recreate
		$recreate = ($db_id != '') ? true : false;

		if (!$file) 
		{
			print json_encode(array('status'=>'failed', 'msg'=>'Invalid file'));
			return;
		}

		$repo_base = '/projects/' . $this->_project->alias . '/files/?action=download&subdir=' . trim($dir, '/');

		// Get project path
		$path = ProjectsHelper::getProjectPath($this->_project->alias, 
						$this->_config->get('webpath'), $this->_config->get('offroot', 0));
		$path .= DS;

		if ($dir != '') 
		{
			$path .= $dir . DS;
		}

		$table['name'] = 'prj_db_' . $this->_project->id . '_' . sha1($dir . DS . $file);

		if ($recreate) 
		{
			$sql = "DROP TABLE `" . $table['name'] . "` ";
			$db->setQuery($sql);
			$db->query();
		}

		$sql = "CREATE TABLE `" . $table['name'] . "` ";

		$table['cols'][] = '__ds_rec_id int(11) NOT NULL AUTO_INCREMENT NOT NULL';

		if (file_exists($path . DS . $file) && ($handle = fopen($path . DS . $file, "r")) !== FALSE) 
		{
			// Get commit hash
			chdir($path);
			exec($this->gitpath . ' log --pretty=format:%H ' . escapeshellarg($file) . '|head -1', $hash);
			$hash = $hash[0];

			// Check if expert mode CSV
			$expert_mode = false;
			$col_labels = fgetcsv($handle);
			$col_prop = fgetcsv($handle);
			$data_start = fgetcsv($handle);
			
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

			$dd['project']		= $this->_project->id;
			$dd['database']		= 'prj_db_' . $this->_project->id;
			$dd['table']		= $table['name'];
			$dd['title']		= $title;
			$dd['pk']			= $table['name'] . '.__ds_rec_id';
			$dd['repo_base']	= $repo_base;
			$dd = json_encode($dd);

			// Get project database object
			$objPD = new ProjectDatabase($this->_database);

			// Recreate or Expert mode
			if ($recreate) {
				$objPD->loadRecord($db_id);

				if ($objPD->project != $this->_project->id)
				{
					exit;
				}

				$objPD->title 			= $title;
				$objPD->source_revision = $hash;
				$objPD->description 	= $desc;
				$objPD->data_definition = $dd;
				$objPD->updated 		= date( 'Y-m-d H:i:s' );
				$objPD->updated_by 		= $this->_uid;
				$msg = 'updated database "' . $title . '" in project ';
			}
			else
			{
				$objPD->project 		= $this->_project->id;
				$objPD->database_name 	= $table['name'];
				$objPD->title 			= $title;
				$objPD->source_file 	= $file;
				$objPD->source_dir 		= $dir;
				$objPD->source_revision = $hash;
				$objPD->description 	= $desc;
				$objPD->data_definition = $dd;
				$objPD->created 		= date( 'Y-m-d H:i:s' );
				$objPD->created_by 		= $this->_uid;
				$msg = 'created database "' . $title . '" in project ';
			}

			// Store new/update record
			$objPD->store();

			// Update source CSV file
			$this->_save_csv($objPD->id);


			// Success
			if ($objPD->id)
			{
				// Record project activity
				$prjAct = new ProjectActivity($this->_database);
				$prjAct->recordActivity($this->_project->id, $this->_uid, str_replace("'", "\'", $msg), $objPD->id, 'databases',
					JRoute::_('index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias . a . 'active=databases'),
					'databases', 1);
				ob_clean();
				$this->_msg = 'Database successfully created';
			}

		}

		$url = str_replace($_SERVER['SCRIPT_URL'], '', $_SERVER['SCRIPT_URI']) . "/projects/" . $this->_project->alias . "/databases/";
		
		print json_encode(array('status'=>'success', 'data'=>$url));

		// Success message
		if (isset($this->_msg) && $this->_msg) {
			$this->_message = array('message' => $this->_msg, 'type' => 'success');
		}

		return;
	}

	/**
	 * Save updated CSV file with headers
	 * 
	 * @param    integer  	$id	Database ID	
	 * @return   void
	 */
	public function _save_csv($id)
	{
		$db = $this->get_ds_db($this->_project->id);

		// Get project database object
		$objPD = new ProjectDatabase($this->_database);
		
		// Get project path
		$path = ProjectsHelper::getProjectPath($this->_project->alias, 
						$this->_config->get('webpath'), $this->_config->get('offroot', 0));
		$path .= DS;

		if ($objPD->loadRecord($id))
		{
			$db = $this->get_ds_db($objPD->project);
			$table = $objPD->database_name;
			$title = $objPD->title;
			$file = $objPD->source_file;
			$dir = ($objPD->source_dir != '') ? $objPD->source_dir . DS : '';
			$dd = json_decode($objPD->data_definition, true);

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
			$commit_message = 'Updated file ' . escapeshellarg($file);

			// Modified By
			$profile =& Hubzero_Factory::getProfile();
			$profile->load($this->_uid);
		
			$name = $profile->get('name');
			$email = $profile->get('email');
			$author = escapeshellarg($name . ' <' . $email . '> ');

			chdir($path);
			exec($this->gitpath . ' add ' . escapeshellarg($file));
			exec($this->gitpath . ' commit ' . escapeshellarg($file) 
				. ' -m "' . $commit_message . '"'
				. ' --author="' . $author . '" 2>&1');


			// Update source_revision with the current commit hash
			chdir($path);
			exec($this->gitpath . ' log --pretty=format:%H ' . escapeshellarg($file) . '|head -1', $hash);
			$hash = $hash[0];
			$objPD->source_revision = $hash;
			$objPD->store();

			$prjAct = new ProjectActivity($this->_database);
			$msg = 'updated file "' . $file . '" in project ';
			$prjAct->recordActivity($this->_project->id, $this->_uid, str_replace("'", "\'", $msg), $file, 'files', 
				JRoute::_('index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias . a . 'active=files'), 
				'files', 1 );
			ob_clean();
		}
	}

	/**
	 * Delete database
	 * 
	 * @return     array
	 */
	public function act_delete()
	{
		// Incoming
		$id = JRequest::getVar('db_id', false);
		$ds_db = $this->get_ds_db($this->_project->id);

		// Get project database object
		$objPD = new ProjectDatabase($this->_database);

		if ($objPD->loadRecord($id))
		{
			$table = $objPD->database_name;
			$title = $objPD->title;

			if ($table && $table != ''&& $objPD->project == $this->_project->id) 
			{
				// Removing the record for this database
				$objPD->delete();

				// Removing mysql table for this database
				$sql = "DROP TABLE $table";
				$ds_db->setQuery($sql);
				$ds_db->query();

				$this->_message = array('message'=>'Database successfully deleted', 'type'=>'success');
			}

			// Record project activity
			$prjAct = new ProjectActivity($this->_database);
			$msg = 'removed database "' . $title . '" from project ';
			$prjAct->recordActivity($this->_project->id, $this->_uid, str_replace("'", "\'", $msg), $id, 'databases',
				JRoute::_('index.php?option=' . $this->_option . a . 'alias=' . $this->_project->alias . a . 'active=databases'),
				'databases', 1);
		}

		$url = str_replace($_SERVER['SCRIPT_URL'], '', $_SERVER['SCRIPT_URI']) . "/projects/" . $this->_project->alias . "/databases/";
		return array('referer'=>$url, 'msg'=>$this->_message);
	}

	/**
	 * Update database
	 * 
	 * @return     array
	 */
	public function act_update()
	{
		// Incoming
		$id = JRequest::getVar('db_id', false);
		$title = JRequest::getVar('db_title', false);
		$description = JRequest::getVar('db_description', false);

		// Get project database object
		$objPD = new ProjectDatabase($this->_database);

		if ($objPD->loadRecord($id))
		{
			$dd = json_decode($objPD->data_definition, true);

			if ($title != '' && $objPD->project == $this->_project->id)
			{
				// Setting title and description
				$objPD->title = $title;
				$dd['title'] = $title;
				$objPD->description = $description;
				$objPD->data_definition = json_encode($dd);
				$objPD->store();
				
				$this->_message = array('message'=>'Database successfully updated', 'type'=>'success');
			}
		}

		$url = str_replace($_SERVER['SCRIPT_URL'], '', $_SERVER['SCRIPT_URI']) . "/projects/" . $this->_project->alias . "/databases/";
		return array('referer'=>$url, 'msg'=>$this->_message);
	}


	/**
	 * Making a copy of the database for publications
	 * 
	 * Function to be called outside of the databases plugin
	 * @param    integer  	$identifier	Database ID	or name
	 * @param    object  	$project 	Project object
	 * @param    string  	$base_path 	File-repository base path
	 * @return   integer
	 */
	public function clone_database($identifier = 0, $project = NULL, $base_path = NULL)
	{
		if (!$identifier)
		{
			$this->setError( JText::_('Error: missing database identifier') );
			return false;
		}

		if ($project == NULL)
		{
			$project = $this->_project;
		}

		$db 	= &JFactory::getDBO();
		$ds_db 	= $this->get_ds_db($project->id);

		// Load database record
		$objPD = new ProjectDatabase($db);
		if (!$objPD->loadRecord($identifier))
		{
			$this->setError( JText::_('Error: failed to load database record') );
			return false;
		}

		$dd = json_decode($objPD->data_definition, true);

		// Get last version
		$objPDV = new ProjectDatabaseVersion($db);
		$version = $objPDV->getMaxVersion($objPD->database_name) + 1;

		// Start cloning
		$orig_table = $dd['table'];
		$new_table = $orig_table . '_' . $version;

		$dd['table'] = $new_table;

		$new_cols = array();
		foreach ($dd['cols'] as $col=>$prop) 
		{
			$col = explode('.', $col);
			$new_cols[$new_table . '.' . $col[1]] = $prop;
		}

		$dd['cols'] = $new_cols;
		$dd['pk'] = $new_table . '.__ds_rec_id';

		if ($base_path != NULL)
		{
			$dd['repo_base'] = $base_path;
		}

		$dd = json_encode($dd);

		// Make new version record
		$objPDV->database_name   = $objPD->database_name;
		$objPDV->version 		 = $version;
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
	 * @param    integer  	$identifier	Database ID	or name
	 * @param    object  	$project 	Project object
	 * @param    integer  	$version 	Database version
	 * @return   bool
	 */
	public function remove_database($identifier = 0, $project = NULL, $version = NULL)
	{
		if (!$identifier)
		{
			$this->setError( JText::_('Error: missing database identifier') );
			return false;
		}

		if ($version === NULL || trim($version) == '')
		{
			$this->setError( JText::_('Error: invalid database version') );
			return false;
		}

		if ($project == NULL)
		{
			$project = $this->_project;
		}

		$db 	= &JFactory::getDBO();
		$ds_db 	= $this->get_ds_db($project->id);

		// Load database record
		$objPD = new ProjectDatabase($db);
		if (!$objPD->loadRecord($identifier))
		{
			$this->setError( JText::_('Error: failed to load database record') );
			return false;
		}

		// Remove record from database versions table
		$sql = 'DELETE FROM #__project_database_versions WHERE' 
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
	 * @param    string  $id
	 * @return   object
	 */
	public function get_ds_db($id)
	{
		$opt = array();

		// Get plugin params
		if (!isset($this->_params))
		{
			$plugin = JPluginHelper::getPlugin('projects', 'databases');
			$this->_params = new JParameter($plugin->params);
		}

		// Create database if it doesn't exist
		$sql = "CREATE DATABASE IF NOT EXISTS " . 'prj_db_' . $id;

		$opt['driver'] 		= 'mysqli';
		$opt['host'] 		= $this->_params->get('db_host');
		$opt['user'] 		= $this->_params->get('db_user');
		$opt['password'] 	= $this->_params->get('db_password');
		$opt['prefix'] 		= '';

		$db = &JDatabase::getInstance($opt);

		$db->setQuery($sql);
		$db->query();

		$opt['database'] = 'prj_db_' . $id;

		$db = &JDatabase::getInstance($opt);

		return $db;
	}
}
