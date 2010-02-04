<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------

class ToolsController
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	private $_error = NULL;

	//-----------
	
	public function __construct( $config=array() )
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';
		
		// Set the controller name
		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
		// Set the component name
		$this->_option = 'com_'.$this->_name;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}
	
	//-----------
	
	public function execute()
	{
		$default = 'browse';
		
		$task = strtolower(JRequest::getVar('task', $default, 'default'));
		
		$thisMethods = get_class_methods( get_class( $this ) );
		if (!in_array($task, $thisMethods)) {
			$task = $default;
			if (!in_array($task, $thisMethods)) {
				return JError::raiseError( 404, JText::_('Task ['.$task.'] not found') );
			}
		}
		
		$this->_task = $task;
		$this->$task();
	}

	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message );
		}
	}

	//----------------------------------------------------------
	//  Views
	//----------------------------------------------------------
	
	protected function middleware( $comm, &$fnoutput )
	{
		$retval = 1; // Assume success.
		$fnoutput = array();

		exec("/bin/sh ../components/".$this->_option."/mw $comm 2>&1 </dev/null",$output,$status);

		$outln = 0;
		if ($status != 0) {
			$retval = 0;
		}

		// Print out the applet tags or the error message, as the case may be.
		foreach ($output as $line)
		{
			// If it's a new session, catch the session number...
			if (($retval == 1) && preg_match("/^Session is ([0-9]+)/",$line,$sess)) {
				$retval = $sess[1];
			} else {
				if ($status != 0) {
					$fnoutput[$outln] = $line;
				} else {
					$fnoutput[$outln] = $line;
				}
				$outln++;
			}
		}
		
		return $retval;
	}

	//-----------

	protected function browse( ) 
	{
		$table = JRequest::getVar( 'table', '' );
		
		$html = ''.n;
		
		switch ($table) 
		{
			case 'app': 
				$html .= '<h3>The app table</h3>'.n;
				$html .= $this->app_display();
				break;
			case 'host': 
				$html .= '<h3>The host table</h3>'.n;
				$html .= $this->host_display('');
				break;
			case 'hosttype': 
				$html .= '<h3>The hosttype table</h3>'.n;
				$html .= $this->type_display();
				break;
			case 'usertype': 
				$html .= '<h3>The usertype table</h3>'.n;
				$html .= $this->type_display();
				break;
			default:
				$html .= '<h3>Select a table to administer:</h3>'.n;
				$html .= '<ul>'.n;
				
				$vars = array('table' => 'app','op' => '');
				$html .= ' <li>'.ToolsHtml::admlink('app',$vars,'Modify app table', $this->_option).'</li>'.n;
			
				$vars['table'] = 'host';
				$html .= ' <li>'.ToolsHtml::admlink('host',$vars,'Modify host table', $this->_option).'</li>'.n;
			
				$vars['table'] = 'hosttype';
				$html .= ' <li>'.ToolsHtml::admlink('hosttype',$vars,'Modify hosttype table', $this->_option).'</li>'.n;
			
				//$vars['table'] = 'usertype';
				//$html .= ' <li>'.ToolsHtml::admlink('usertype',$vars,'Modify usertype table', $this->_option).'</li>'.n;
				
				$html .= '</ul>'.n;
			break;
		}

		$html .= '<p>';
		$html .= ToolsHtml::admlink('Back to administrative menu',array(),'Back to administrative menu', $this->_option);
		$html .= '</p>'.n;
		
		echo $html;
	}

	//----------------------------------------------------------

	private function table( $rows, $headers, $middle, $trailer, &$tail_row ) 
	{
		$html  = '<table class="adminlist">'.n;
		$html .= ToolsHtml::tableHeader($headers);
		$html .= ' <tbody>'.n; 
		for($i=0; $i < count($rows); $i++) 
		{
			$html .= $this->$middle($rows[$i]); 
		}
		if ($tail_row != '') {
			$html .= $this->$trailer($tail_row); 
		}
		$html .= ' </tbody>'.n; 
		$html .= '</table>'.n;
		return $html;
	}

	//----------------------------------------------------------
	// History
	//----------------------------------------------------------

	private function history_body($row) 
	{
		$html  = '  <tr>'.n;
		$html .= ToolsHtml::td( $row->sessnum );
		$html .= ToolsHtml::td( $row->username );
		$html .= ToolsHtml::td( $row->appname );
		$html .= ToolsHtml::td( $row->start );
		$html .= ToolsHtml::td( $row->walltime );
		$html .= ToolsHtml::td( $row->viewtime );
		$html .= ToolsHtml::td( $row->cputime );
		$html .= ToolsHtml::td( $row->status );
		$html .= '  </tr>'.n;
		return $html;
	}

	//----------------------------------------------------------

	private function history()
	{
	    $juser =& JFactory::getUser();
		
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();
		
		$headers = array('ID','Username','Appname','Start','Wall time','View time','CPU time','Status');

		if ($this->_config['admin'] == 1) {
			$query = "SELECT * FROM sessionlog ORDER BY sessnum";
		} else {
			$query = "SELECT * FROM sessionlog 
					WHERE username='" . $juser->get('username') . "' 
					ORDER BY sessnum";
		}
		$mwdb->setQuery($query);
		$rows = $mwdb->loadObjectList();

		$this->table($rows,$headers,'history_body','','');
	}

	//----------------------------------------------------------
	// Host
	//----------------------------------------------------------

	private function host_body($row) 
	{
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();

		$hostname = $row->hostname;
		$provisions = $row->provisions;
		$status = $row->status;
		$uses = $row->uses;

		$vars = array('table' => 'host',
                'op' => 'edit',
                'filter_hostname' => $hostname);

		$html  = '  <tr>'.n;
		$html .= ToolsHtml::td( ToolsHtml::admlink($hostname,$vars,'Edit '.$hostname, $this->_option) );

		// The provisions bitfield.
		$query = "SELECT * FROM hosttype ORDER BY value";
		$mwdb->setQuery( $query );
		$result = $mwdb->loadObjectList();
		$list = array();
		for($i=0; $i<count($result); $i++) 
		{
			$r = $result[$i];
			$list[$r->name] = (int)$r->value & (int)$provisions;
		}
		$hidden = array('option'   => $this->_option,
                  'admin'   => '1',
                  'table'   => 'host',
                  'hostname' => $row->hostname,
                  'op'      => 'toggle_hosttype' );
		
		$html .= ToolsHtml::td( ToolsHtml::listedit($list, $hidden) );

		// The status field.
		$vars = array('option' => $this->_option,
                'admin' => '1',
                'table' => 'host',
                'hostname' => $hostname,
                'filter_hostname' => $hostname,
                'op'    => 'status');

		$html .= ToolsHtml::td( ToolsHtml::admlink($status,$vars,'Change host status', $this->_option) );
		$html .= ToolsHtml::td( $uses );

		// A button to delete the whole thing.
		$html .= ToolsHtml::td( ToolsHtml::delete_button('hostname', 'host', $hostname, $this->_option) );
		$html .= '  </tr>'.n;
		
		return $html;
	}

	//----------------------------------------------------------

	private function host_edit($row) 
	{
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();
		
		$html  = '  <tr>'.n;
		$html .= t.'<form name="insert_host" method="GET" action="index.php">'.n;
		$html .= t.ToolsHtml::hInput('option',$this->_option);
		$html .= t.ToolsHtml::hInput('admin',1);
		$html .= t.ToolsHtml::hInput('table','host');
		$html .= t.ToolsHtml::hInput('op','update');
		$html .= t.ToolsHtml::hInput('filter_hostname',$row->hostname);
		$html .= t.ToolsHtml::hInput('status','check');
		$html .= '   <td><input type="text" name="hostname" size="10" value="'.$row->hostname.'" />'.n;
		$html .= '   <td><select multiple name="hosttype[]">'.n;
		
		$query = "SELECT * FROM hosttype ORDER BY value";
		$mwdb->setQuery( $query );
		$result = $mwdb->loadObjectList();
		$list = array();
		for($i=0; $i<count($result); $i++) 
		{
			$r = $result[$i];
			if ((int)$r->value & (int)$row->provisions) {
				$html .= '    <option selected="selected" value="'.$r->name.'">'.$r->name.'</option>'.n;
			} else {
				$html .= '    <option value="'.$r->name.'">'.$r->name.'</option>'.n;
			}
		}
		
		$html .= '</select></td>'.n;
		$html .= ToolsHtml::td( $row->status );
		$html .= ToolsHtml::td( 'uses' );
		$html .= ToolsHtml::td( ToolsHtml::sInput('insert','Update') );
		$html .= t.'<form>'.n;
		$html .= '  </tr>'.n;
		return $html;
	}

	//----------------------------------------------------------

	private function host_display()
	{
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();
		
		$headers = array('Hostname','Provisions','Status','Uses','Action');
		$op = @$_GET['op'];
		$hostname = @$_GET['hostname'];
		$hosttype = @$_GET['hosttype'];
		$status = @$_GET['status'];
		$item = @$_GET['item'];
		
		$filter_hostname = @$_GET['filter_hostname'];
		$filter_hosttype = @$_GET['filter_hosttype'];
		
		switch($op)
		{
			case 'edit':
				//$this->print_post();
				$query = "SELECT * FROM host WHERE hostname='$filter_hostname'";
				$mwdb->setQuery($query);
				$result = $mwdb->query();
				//$this->check_mysql($mwdb,$result,$query);
				$rows = $mwdb->loadObjectList();
				return $this->table(array(),$headers,'','host_edit',$rows[0]);
				break;
	
			case 'update':
				if ($hostname == '') {
					echo ToolsHtml::error('You must specify a valid hostname.');
				} else {
					// Figure out the hosttype stuff.
					$harr = array();
					foreach($hosttype as $name => $value) 
					{
						$harr[$value] = 1;
					}
					$h = 0;
					$query = "SELECT name,value FROM hosttype";
					$mwdb->setQuery($query);
					$result = $mwdb->query();
					//$this->check_mysql($mwdb,$result,$query);
					$rows = $mwdb->loadObjectList();
					for($i=0; $i < count($rows); $i++) 
					{
						$row = $rows[$i];
						if (isset($harr[$row->name])) {
							$h += $row->value;
						}
					}

					if ($filter_hostname != '') {
						echo "<p>Updating $hostname</p>\n";
						$query = "UPDATE host SET hostname='$hostname',provisions=$h,status='$status' WHERE hostname='$filter_hostname'";
						$mwdb->setQuery($query);
						$result = $mwdb->query();
					} else {
						echo "<p>Inserting $hostname</p>\n";
						$query = "INSERT INTO host(hostname,provisions,status) VALUES('$hostname', $h, '$status')";
						$mwdb->setQuery($query);
						$result = $mwdb->query();
					}
					//$this->check_mysql($mwdb,$result,$query);
				}
				break;

			case 'delete':
				echo "<p>Deleting $hostname</p>\n";
				$query = "DELETE FROM host WHERE hostname='$hostname'";
				$mwdb->setQuery($query);
				$result = $mwdb->query();
				break;

			case 'status':
				$status = $this->middleware("check $hostname yes", $output);
				foreach($output as $line) 
				{
					echo "$line<br />\n";
				}
				break;

			case 'toggle_hosttype':
				$query = "SELECT @value:=value FROM hosttype WHERE name='$item'; 
						UPDATE host SET provisions = provisions ^ @value WHERE hostname = '${hostname}'";
				$mwdb->setQuery($query);
				if (defined('_JEXEC')) {
					$result = $mwdb->queryBatch();
				} else {
					$result = $mwdb->query_batch();
				}
				//$this->check_mysql($mwdb,$result,$query);
				break;
		}

		// Form the query and display the table.
		if ($filter_hosttype != '') {
			echo "<p>Filtering on hosttype $filter_hosttype</p>\n";
			$query = "SELECT host.* FROM host JOIN hosttype 
					ON host.provisions & hosttype.value != 0 
					WHERE hosttype.name = '$filter_hosttype' 
					ORDER BY hostname";
		} else {
			$query = "SELECT * FROM host ORDER BY hostname";
		}
		$mwdb->setQuery( $query );
		$result = $mwdb->query();
		//$this->check_mysql($mwdb,$result,$query);
		$rows = $mwdb->loadObjectList();

		$vars = new Host('', -1, 'offline');
		
		return $this->table($rows,$headers,'host_body','host_edit',$vars);
	}

	//----------------------------------------------------------
	// App
	//----------------------------------------------------------

	private function app_edit(&$row) 
	{
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();

		$html  = '  <tr>'.n;
		$html .= t.'<form name="insert_app" method="GET" action="index.php">'.n;
		$html .= t.ToolsHtml::hInput('option',$this->_option);
		$html .= t.ToolsHtml::hInput('admin',1);
		$html .= t.ToolsHtml::hInput('table','app');
		$html .= t.ToolsHtml::hInput('op','update');
		$html .= t.ToolsHtml::hInput('filter_appname',$row->appname);
		$html .= '   <td><input type="text" name="appname" size="10" value="'.$row->appname.'" /></td>'.n;
		$html .= '   <td><input type="text" name="geometry" size="7" value="'.$row->geometry.'" /></td>'.n;
		$html .= '   <td><input type="text" name="depth" size="3" value="'.$row->depth.'" /></td>'.n;
		$html .= '   <td><select multiple name="hostreq[]">'.n;
		$query = "SELECT * FROM hosttype ORDER BY value";
		$mwdb->setQuery( $query );
		$result = $mwdb->loadObjectList();
		$list = array();
		for($i=0; $i<count($result); $i++) 
		{
			$r = $result[$i];
			if ((int)$r->value & (int)$row->hostreq) {
				$html .= '    <option selected="selected" value="'.$r->name.'">'.$r->name.'</option>'.n;
			} else {
				$html .= '    <option value="'.$r->name.'">'.$r->name.'</option>'.n;
			}
		}
		$html .= '</select></td>'.n;
		$html .= '<td>&nbsp;</td>'.n;
		//$html .= '   <td><select multiple name="userreq[]">'.n;
		//$query = "SELECT * FROM ".$tbl.".usertype ORDER BY value";
		//$mwdb->setQuery( $query );
		//$result = $mwdb->loadObjectList();
		//$list = array();
		//for($i=0; $i<count($result); $i++) 
		//{
		//	$r = $result[$i];
		//	if ((int)$r->value & (int)$row->userreq) {
		//		$html .= '    <option selected="selected" value="'.$r->name.'">'.$r->name.'</option>'.n;
		//	} else {
		//		$html .= '    <option value="'.$r->name.'">'.$r->name.'</option>'.n;
		//	}
		//}
		//$html .= '</select></td>'.n;
		$html .= '   <td><input type="text" name="timeout" size="5" value="'.$row->timeout.'" /></td>'.n;
		$html .= '   <td><input type="text" name="command" value="'.$row->command.'" /></td>'.n;
		$html .= '   <td><input type="text" name="description" value="'.$row->description.'" /></td>'.n;
		$html .= '   <td><input type="submit" name="insert" value="Update" /></td>'.n;
		$html .= t.'<form>'.n;
		$html .= '  </tr>'.n;
		return $html;
	}

	//----------------------------------------------------------
	
	private function app_body(&$row) 
	{
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();

		$appname     = $row->appname;
		$geometry    = $row->geometry;
		$depth       = $row->depth;
		$hostreq     = $row->hostreq;
		$userreq     = $row->userreq;
		$timeout     = $row->timeout;
		$command     = $row->command;
		$description = $row->description;

		$vars = array('table' => 'app',
					  'filter_appname' => $appname,
					  'op' => 'edit');
		
		$html  = ToolsHtml::td( ToolsHtml::admlink($appname,$vars,"Edit $appname", $this->_option) );
		$html .= ToolsHtml::td( ToolsHtml::admlink($geometry,$vars,"Edit $appname", $this->_option) );
		$html .= ToolsHtml::td( ToolsHtml::admlink($depth,$vars,"Edit $appname", $this->_option) );

		// HostReq
		$query = "SELECT * FROM hosttype ORDER BY value";
		$mwdb->setQuery( $query );
		$result = $mwdb->loadObjectList();
		$list = array();
		for($ui=0; $ui<count($result); $ui++) 
		{
			$row = $result[$ui];
			$val = (int)$row->value & (int)$hostreq;
			$list[$row->name] = $val;
		}
		$hidden = array('option'  => $this->_option,
						'admin'   => '1',
						'table'   => 'app',
						'appname' => $appname,
						'op'      => 'toggle_hostreq');

		$html .= ToolsHtml::td( ToolsHtml::listedit($list, $hidden) );

		// UserReq
		$query = "SELECT * FROM usertype ORDER BY value";
		$mwdb->setQuery( $query );
		$result = $mwdb->loadObjectList();
		$list = array();
		for($ui=0; $ui<count($result); $ui++) 
		{
			$row = $result[$ui];
			$val = (int)$row->value & (int)$userreq;
			$list[$row->name] = $val;
		}
		$hidden = array('option'  => $this->_option,
						'admin'   => '1',
						'table'   => 'app',
						'appname' => $appname,
						'op'      => 'toggle_userreq' );

		$html .= ToolsHtml::td( ToolsHtml::listedit($list, $hidden) );
		$html .= ToolsHtml::td( ToolsHtml::admlink($timeout,$vars,"Edit $appname", $this->_option) );
		$html .= ToolsHtml::td( ToolsHtml::admlink($command,$vars,"Edit $appname", $this->_option) );
		$html .= ToolsHtml::td( ToolsHtml::admlink($description,$vars,"Edit $appname", $this->_option) );

		// And a button to delete the whole thing.
		$html .= ToolsHtml::td( ToolsHtml::delete_button('appname', 'app', $appname, $this->_option) );
		$html .= '  </tr>'."\n";
		
		return $html;
	}

	//----------------------------------------------------------

	private function app_display() 
	{
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();

		$op       = (isset($_GET['op'])) ? $_GET['op'] : '';
		$item     = (isset($_GET['item'])) ? $_GET['item'] : '';
		$appname  = (isset($_GET['appname'])) ? $_GET['appname'] : '';
		$geometry = (isset($_GET['geometry'])) ? $_GET['geometry'] : '';
		$depth    = (isset($_GET['depth'])) ? $_GET['depth'] : '';
		$hostreq  = (isset($_GET['hostreq'])) ? $_GET['hostreq'] : '';
		$userreq  = (isset($_GET['userreq'])) ? $_GET['userreq'] : '';
		$timeout  = (isset($_GET['timeout'])) ? $_GET['timeout'] : '';
		$command  = (isset($_GET['command'])) ? $_GET['command'] : '';
		$description = (isset($_GET['description'])) ? $_GET['description'] : '';

		$filter_appname  = (isset($_GET['filter_appname'])) ? $_GET['filter_appname'] : '';
		$filter_hosttype = (isset($_GET['filter_hosttype'])) ? $_GET['filter_hosttype'] : '';
		$filter_usertype = (isset($_GET['filter_usertype'])) ? $_GET['filter_usertype'] : '';

		$headers = array('Appname','Geometry','Depth','HostReq','UserReq','Timeout','Command','Description','Action');

		switch($op) 
		{
			case 'edit':
				$query = "SELECT * FROM app WHERE appname='$filter_appname'";
				$mwdb->setQuery($query);
				$result = $mwdb->query();
				$rows = $mwdb->loadObjectList();
				return $this->table(array(),$headers,'','app_edit',$rows[0]);
				break;

			case 'toggle_hostreq':
				echo "<p><b>Toggle hostreq $item in app $appname</b></p>\n";
				$query = "SELECT @value:=value FROM hosttype WHERE name='$item';
						  UPDATE app SET hostreq = hostreq ^ @value WHERE appname='$appname'";
				$mwdb->setQuery($query);
				if (defined('_JEXEC')) {
					$result = $mwdb->queryBatch();
				} else {
					$result = $mwdb->query_batch();
				}
				break;
			
			case 'toggle_userreq':
				echo "<p><b>Toggle userreq $item in app $appname</b></p>\n";
				$query = "SELECT @value:=value FROM usertype WHERE name='$item';
						  UPDATE app SET userreq = userreq ^ @value WHERE appname='$appname'";
				$mwdb->setQuery($query);
				if (defined('_JEXEC')) {
					$result = $mwdb->queryBatch();
				} else {
					$result = $mwdb->query_batch();
				}
				break;
			
			case 'update':
				if ($appname == '') {
					echo ToolsHtml::error('You must specify an appname.');
				} else {
					$harr = array();
					foreach($hostreq as $name => $value) 
					{
						$harr[$value] = 1;
					}
					$h = 0;
					$query = "SELECT name,value FROM hosttype";
					$mwdb->setQuery($query);
					$count = $mwdb->query();
					$rows = $mwdb->loadObjectList();

					for($i=0; $i < count($rows); $i++) 
					{
						$row = $rows[$i];
						if (isset($harr[$row->name])) {
							$h += $row->value;
						}
					}

					$uarr = array();
					foreach($userreq as $name => $value) 
					{
						$uarr[$value] = 1;
					}
					$u = 0;
					$query = "SELECT name,value FROM usertype";
					$mwdb->setQuery($query);
					$count = $mwdb->query();
					$rows = $mwdb->loadObjectList();

					for($i=0; $i < count($rows); $i++) 
					{
						$row = $rows[$i];
						if (isset($uarr[$row->name])) {
							$u += $row->value;
						}
					}

					$query = "SELECT count(*) FROM host WHERE hostname='$hostname'";
					$mwdb->setQuery($query);
					$result = $mwdb->query();
					$count = $mwdb->loadResult();
					if ($filter_appname != '') {
						$query = "UPDATE app SET appname='$appname',
                			  geometry='$geometry', depth=$depth, hostreq=$h, userreq=$u,
                			  timeout=$timeout, command='$command', description='$description'
               				   WHERE appname='$filter_appname'";
					} else {
						$query = "INSERT INTO
                			  app(appname,geometry,depth,hostreq,userreq,timeout,command,description) 
							  VALUES('$appname','$geometry','$depth',$h,$u,$timeout,'$command','$description')";
					}
					$mwdb->setQuery($query);
					$result = $mwdb->query();
					//$this->check_mysql($mwdb,$result,$query);
				}
				break;
			
			case 'delete':
				$query = "DELETE FROM app WHERE appname='$appname'";
				$mwdb->setQuery($query);
				$result = $mwdb->query();
				break;
		}

		// Form the query and display the app table.
		if ($filter_appname != '') {
			echo "<p>Filtering on appname $filter_appname</p>\n";
			
			$query = "SELECT * FROM app WHERE appname='$appname_filter'";
		} else if ($filter_hosttype != '') {
			echo "<p>Filtering on hosttype $filter_hosttype</p>\n";
			
			$query = "SELECT value FROM hosttype WHERE name='$filter_hosttype'";
			$mwdb->setQuery($query);
			if (defined('_JEXEC')) {
				$result = $mwdb->queryBatch();
			} else {
				$result = $mwdb->query_batch();
			}
			//$this->check_mysql($mwdb,$result,$query);
			$rows = $mwdb->loadObjectList();
			$row = $rows[0];
			$query = "SELECT * FROM app WHERE hostreq & $row->value != 0 ORDER BY appname";
		} else if ($filter_usertype != '') {
			echo "<p>Filtering on usertype $filter_usertype</p>\n";
			
			$query = "SELECT value FROM usertype WHERE name='$filter_usertype'";
			$mwdb->setQuery($query);
			if (defined('_JEXEC')) {
				$result = $mwdb->queryBatch();
			} else {
				$result = $mwdb->query_batch();
			}
			//$this->check_mysql($mwdb,$result,$query);
			$rows = $mwdb->loadObjectList();
			$row = $rows[0];
			$query = "SELECT * FROM app WHERE userreq & $row->value != 0 ORDER BY appname";
		} else {
			$query = "SELECT * FROM app ORDER BY appname"; 
		}
		$mwdb->setQuery($query);
		if (defined('_JEXEC')) {
			$result = $mwdb->queryBatch();
		} else {
			$result = $mwdb->query_batch();
		}
		//$this->check_mysql($mwdb,$result,$query);
		$rows = $mwdb->loadObjectList();
		/*for($i=0; $i<count($rows); $i++) 
		{
			$row = $rows[$i];
			echo "$row->name<br />\n";
		}*/
		$tail_row = new MiddlewareApp('','640x480','16',-1,-1,86400,'<command>','<edit this>');

		return $this->table( $rows, $headers, 'app_body', 'app_edit', $tail_row );
	}

	//----------------------------------------------------------
	// 'type' table entries.
	//----------------------------------------------------------
	
	private function type_display() 
	{
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();

		$op    = @$_GET['op'];
		$table = @$_GET['table'];
		$name  = @$_GET['name'];
		$value = @$_GET['value'];
		$item  = @$_GET['item'];
		$description = @$_GET['description'];
		$filter_hosttype = @$_GET['filter_hosttype'];
		$filter_usertype = @$_GET['filter_usertype'];

		$headers = array('Name','Bit#','Description','References','Action');

		if ($op == 'users') {
			if ($table == 'hosttype') {
				$this->host_display();
				$this->app_display();
			} elseif ($table == 'usertype') {
				$this->app_display();
			}
			return;
		}

		switch($op) 
		{
			case 'edit':
				$query = "SELECT * FROM $table WHERE name='$item'";
				$mwdb->setQuery($query);
				$result = $mwdb->query();
				//$this->check_mysql($mwdb,$result,$query);
				$rows = $mwdb->loadObjectList();
				if ($table == 'hosttype') {
					return $this->table(array(),$headers,'','hosttype_edit',$rows[0]);
				} else {
					return $this->table(array(),$headers,'','usertype_edit',$rows[0]);
				}
				break;

			case 'delete':
				$query = "DELETE FROM $table WHERE name='$item'";
				$mwdb->setQuery( $query );
				$result = $mwdb->query();
				//$this->check_mysql($mwdb,$result,$query);
				break;

			case 'update':
				if ($name == '') {
					echo ToolsHtml::error('You must specify a valid name.');
				} else {
					if (($table == 'hosttype' && $filter_hosttype != '') ||
						($table == 'usertype' && $filter_usertype != '')) {
						$query = "UPDATE $table SET name='$name', description='$description' WHERE name='$filter_hosttype'";
						$mwdb->setQuery( $query );
						$result = $mwdb->query();
						//$this->check_mysql($mwdb,$result,$query);
					} else {
						$query = "SELECT * FROM $table ORDER BY VALUE";
						$mwdb->setQuery( $query );
						$result = $mwdb->query();
						//$this->check_mysql($mwdb,$result,$query);
						$rows = $mwdb->loadObjectList();
					
						$value = 1;
						for($i=0; $i<count($rows); $i++) 
						{
							$row = $rows[$i];
							if ($value == $row->value) {
								$value = $value * 2;
							}
						}
						for($i=0; $i<count($rows); $i++) 
						{
							$row = $rows[$i];
							if ($row->name == $name) {
								echo ToolsHtml::error('"'.$name.'" already exists in the table.');
								$name = '';
							}
						}
						if ($name != '') {
							$query = "INSERT INTO $table(name,value,description) VALUES('$name',$value,'$description')";
							$mwdb->setQuery( $query );
							$mwdb->query();
						}
					}
				}
				break;
		}

		// Form the query and show the table.
		$query = "SELECT * FROM $table ORDER BY VALUE";
		$mwdb->setQuery( $query );
		$result = $mwdb->query();
		//$this->check_mysql($mwdb,$result,$query);
		$rows = $mwdb->loadObjectList();

		if ($table == 'hosttype') {
			return $this->table($rows,$headers,'hosttype_body','hosttype_edit',new Hosttype('',-1,''));
		} else {
			return $this->table($rows,$headers,'usertype_body','usertype_edit',new Hosttype('',-1,''));
		}
	}
	
	//----------------------------------------------------------
	// Hosttype
	//----------------------------------------------------------
	
	private function hosttype_refs($value) 
	{
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();
		
		$query = "SELECT count(*) AS count FROM host WHERE provisions & $value != 0";
		$mwdb->setQuery( $query );
		$elts = $mwdb->loadObjectList();
		$elt  = $elts[0];
		$refs = $elt->count;

		$query = "SELECT count(*) AS count FROM app WHERE hostreq & $value != 0";
		$mwdb->setQuery( $query );
		$elts = $mwdb->loadObjectList();
		$elt  = $elts[0];
		$refs = $refs + $elt->count;

		return $refs;
	}

	//----------------------------------------------------------

	private function hosttype_edit($row) 
	{
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();

		if ($row->value > 0) {
			$bit  = log($row->value)/log(2);
			$refs = $this->hosttype_refs($row->value);
		} else {
			$bit  = '';
			$refs = '';
		}

		return ToolsHtml::updateform('hosttype', $bit, $refs, &$row, $this->_option);
	}

	//----------------------------------------------------------

	private function hosttype_body( $row ) 
	{
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();

		if ($row->value > 0) {
			$bit = log($row->value)/log(2);
		} else {
			$bit = '';
		}

		$vars = array('table' => 'hosttype',
					  'op' => 'edit',
					  'item' => "$row->name",
					  'filter_hosttype' => "$row->name");

		$refs = $this->hosttype_refs($row->value);
		
		$html  = '  <tr>'."\n";
		$html .= ToolsHtml::td( ToolsHtml::admlink($row->name,$vars,"Edit $row->name", $this->_option) );
		$html .= ToolsHtml::td( $bit );
		$html .= ToolsHtml::td( ToolsHtml::admlink($row->description,$vars,"Edit $row->name", $this->_option) );
		$html .= ToolsHtml::td( $refs );
		$html .= '   <td>'.n;
		$html .= t.'<form name="'.$row->name.' users" method="get" action="index.php">'.n;
		$html .= t.ToolsHtml::hInput('option',$this->_option);
		$html .= t.ToolsHtml::hInput('admin',1);
		$html .= t.ToolsHtml::hInput('table','hosttype');
		$html .= t.ToolsHtml::hInput('item',$row->name);
		if ($refs > 0) {
			$html .= t.ToolsHtml::hInput('op','users');
			$html .= t.ToolsHtml::hInput('filter_hosttype',$row->name);
			$html .= t.ToolsHtml::sInput('users','Show Uses');
		} else {
			$html .= t.ToolsHtml::hInput('op','delete');
			$html .= t.ToolsHtml::sInput('delete','Delete');
		}
		$html .= t.'</form>'.n;
		$html .= '   </td>'.n;
		$html .= '  </tr>'.n;
		return $html;
	}

	//----------------------------------------------------------
	// Usertype
	//----------------------------------------------------------

	private function usertype_refs( $value ) 
	{
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();
		
		$query = "SELECT count(*) as count from app where userreq & $value != 0";
		$mwdb->setQuery( $query );
		$elts = $mwdb->loadObjectList();
		$elt  = $elts[0];
		$refs = $elt->count;
		return $refs;
	}

	//----------------------------------------------------------

	private function usertype_edit( $row ) 
	{
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();
		
		if ($row->value > 0) {
			$bit  = log($row->value)/log(2);
			$refs = $this->usertype_refs($row->value);
		} else {
			$bit  = '';
			$refs = '';
		}

		return ToolsHtml::updateform('usertype', $bit, $refs, &$row, $this->_option);
	}

	//----------------------------------------------------------

	private function usertype_body( $row ) 
	{
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();

		if ($row->value > 0) {
			$bit = log($row->value)/log(2);
		} else {
			$bit = '';
		}

		$vars = array('table' => 'usertype',
					  'op' => 'edit',
					  'item' => "$row->name",
					  'filter_usertype' => "$row->name");
		
		$refs = $this->usertype_refs($row->value);
		
		$html  = '  <tr>'."\n";
		$html .= ToolsHtml::td( ToolsHtml::admlink($row->name,$vars,"Edit $row->name", $this->_option) );
		$html .= ToolsHtml::td( $bit );
		$html .= ToolsHtml::td( ToolsHtml::admlink($row->description,$vars,"Edit $row->name", $this->_option) );
		$html .= ToolsHtml::td( $refs );
		$html .= '   <td>'.n;
		$html .= t.'<form name="'.$row->name.' users" method="get" action="index.php">'.n;
		$html .= t.ToolsHtml::hInput('option',$this->_option);
		$html .= t.ToolsHtml::hInput('admin',1);
		$html .= t.ToolsHtml::hInput('table','usertype');
		$html .= t.ToolsHtml::hInput('item',$row->name);
		if ($refs > 0) {
			$html .= t.ToolsHtml::hInput('op','users');
			$html .= t.ToolsHtml::hInput('filter_usertype',$row->name);
			$html .= t.ToolsHtml::sInput('users','Show Uses');
		} else {
			$html .= t.ToolsHtml::hInput('op','delete');
			$html .= t.ToolsHtml::sInput('delete','Delete');
		}
		$html .= t.'</form>'.n;
		$html .= '   </td>'.n;
		$html .= '  </tr>'.n;
		return $html;
	}
	
	//-----------

	protected function cancel()
	{
		$this->_redirect = 'index.php?option='.$this->_option;
	}
}
?>
