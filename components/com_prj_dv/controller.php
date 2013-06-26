<?php
/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2010-2013 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2010-2013 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class Controller
{
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	private $_error = NULL;

	public function __construct($config=array())
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';

		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower($r[1]);
			}
		}
		$this->_option = 'com_'.$this->_name;
	}

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}

	public function execute()
	{
		$task = strtolower(JRequest::getVar('task'));
		global $dv_conf;

		if (!isset($task) || $task == '') {
			$task = 'view';
		}

		switch($task) {
			case 'data':
				$this->data();
				break;

			case 'view':
				$u =& JFactory::getURI();
				$path = explode('/', $u->_path);
				$view = (isset($path[2]) && $path[2] != '')? $path[2]: 'spreadsheet';
				$name = (isset($path[3]) && $path[3] != '')? $path[3]: '';
				if (isset($path[4]) && $path[4] == 'admin') {
					$dv_conf['settings']['admin_mode'] = true;
				} else {
					$dv_conf['settings']['admin_mode'] = false;
				}
				$this->view($view, $name);

				break;

			case 'file':
				$hash = JRequest::getVar('hash');
				stream_file($hash);
				break;

			case 'files':
				$path = JRequest::getVar('path');
				stream_files($path);
				break;

			case 'zip':
				$hash_list = JRequest::getVar('hash_list');
				zip_files($hash_list);
				break;
		}
	}

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$mainframe =& $this->mainframe;
			$mainframe->redirect($this->_redirect, $this->_message, $this->_messageType);
		}
	}

	protected function start() {
		// Redirect to databases page
		$url = "/database";
		header('Location: ' . $url);
	}

	public function data()
	{
		global $dv_conf;

		$name = $_GET['obj'];
		$version = isset($_GET['v']) ? $_GET['v'] : false;

		$dd = get_dd($name, $version);

		$filter = strtolower(JRequest::getVar('format', 'json'));
		require_once(JPATH_COMPONENT.DS."filter/$filter.php");

		if (!$this->_authorize($dd)) {
			print ('Sorry, you are not authorized to view this page.');
			return;
		}

		if ($dd) {
			$id = isset($_REQUEST['id'])? mysql_real_escape_string($_REQUEST['id']): false;

			if ($id) {
				$dd['where'][] = array('field'=>$dd['pk'], 'value'=>$id);
				$dd['single'] = true;
			}

			// Data for Custom Views
			$custom_view = isset($_REQUEST['custom_view'])? explode(',', $_REQUEST['custom_view']): array();
			if (count($custom_view) > 0) {
				unset($dd['customizer']);

				// Custom Title
				if (isset($_REQUEST['custom_title']) && trim($_REQUEST['custom_title']) != '') {
					$dd['title'] = htmlspecialchars(strip_tags($_REQUEST['custom_title']));
				}

				// Custom Group by
				if (isset($_REQUEST['group_by']) && trim($_REQUEST['group_by']) != '') {
					$dd['group_by'] = htmlspecialchars(strip_tags($_REQUEST['group_by']));
				}

				// Ordering
				$order_cols = $dd['cols'];
				$dd['cols'] = array();
				foreach($custom_view as $cv_col) {
					$dd['cols'][$cv_col] = $order_cols[$cv_col];
				}

				// Hiding
				foreach($order_cols as $id=>$prop) {
					if (!in_array($id, $custom_view)) {
						$dd['cols'][$id] = $prop;

						if (!isset($dd['cols'][$id]['hide'])) {
							$dd['cols'][$id]['hide'] = 'custom';
						}

					}
				}
			}

			$sql = query_gen($dd);

			$res = get_results($sql, $dd);

			print filter($res, $dd);
			exit(0);
		} else {
			print "<h3>Error: Not Implemented</h3>";
			exit(1);
		}
	}

	public function view($view, $name)
	{
		global $dv_conf;

		$version = isset($_GET['v']) ? $_GET['v'] : false;
		$dd = get_dd($name, $version);

		if (!$this->_authorize($dd)) {
			print ('Sorry, you are not authorized to view this page.');
			return;
		}

		$filter = strtolower(JRequest::getVar( 'format', 'json' ));
		$file = (JPATH_COMPONENT.DS."filter/$filter.php");
		if (file_exists($file)) {
			require_once ($file);
		}

		$file = (JPATH_COMPONENT.DS."view".DS."$view.php");
		if (file_exists($file)) {
			require_once ($file);
			view($name, $dd);
		}
	}

	private function _authorize($dd)
	{
		global $dv_conf;
		$juser =& JFactory::getUser();
		ximport('Hubzero_Group');
		ximport('Hubzero_User_Helper');

		// Publication state
		if(isset($dd['publication_state']) && $dd['publication_state'] == 1) {
			return true;
		}

		// Until publishing is added, no public views
		if ($juser->get('guest')) {
			$redir_url = '?return=' . base64_encode($_SERVER['REQUEST_URI']);
			$login_url = '/login';
			$url = $login_url . $redir_url;
			header('Location: ' . $url);
			return;
		}

		if (isset($dd['project']) && !$juser->get('guest')) {
			$db = &JFactory::getDBO();
			$db->setQuery("SELECT userid FROM #__project_owners WHERE projectid=" . $dd['project']);
			$prj_owners = $db->loadResultArray();

			if (in_array($juser->get('id'), $prj_owners)) {
				return true;
			}
		}

		return false;
	}
}
?>
