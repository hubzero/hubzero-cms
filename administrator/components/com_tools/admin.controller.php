<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
			case 'host': 
				$html .= '<h3>The host table</h3>'.n;
				$html .= $this->host_display('');
				break;
			case 'hosttype': 
				$html .= '<h3>The hosttype table</h3>'.n;
				$html .= $this->type_display();
				break;
			default:
				$html .= '<h3>Select a table to administer:</h3>'.n;
				$html .= '<ul>'.n;
				
				$vars['table'] = 'host';
				$html .= ' <li>'.ToolsHtml::admlink('host',$vars,'Modify host table', $this->_option).'</li>'.n;
			
				$vars['table'] = 'hosttype';
				$html .= ' <li>'.ToolsHtml::admlink('hosttype',$vars,'Modify hosttype table', $this->_option).'</li>'.n;
			
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
		$html .= t.'<form name="insert_host" method="get" action="index.php">'.n;
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
		$op = JRequest::getVar( 'op', '', 'get' );
		$hostname = JRequest::getVar( 'hostname', '', 'get' );
		$hosttype = JRequest::getVar( 'hosttype', '', 'get' );
		$status = JRequest::getVar( 'status', '', 'get' );
		$item = JRequest::getVar( 'item', '', 'get' );
		
		$filter_hostname = JRequest::getVar( 'filter_hostname', '', 'get' );
		$filter_hosttype = JRequest::getVar( 'filter_hosttype', '', 'get' );
		
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

	private function type_display() 
	{
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();

		$op    = JRequest::getVar( 'op', '', 'get' );
		$table = JRequest::getVar( 'table', '', 'get' );
		$name  = JRequest::getVar( 'name', '', 'get' );
		$value = JRequest::getVar( 'value', '', 'get' );
		$item  = JRequest::getVar( 'item', '', 'get' );
		$description = JRequest::getVar( 'description', '', 'get' );
		$filter_hosttype = JRequest::getVar( 'filter_hosttype', '', 'get' );
		$filter_usertype = JRequest::getVar( 'filter_usertype', '', 'get' );

		$headers = array('Name','Bit#','Description','References','Action');

		if ($op == 'users') {
			if ($table == 'hosttype') {
				$this->host_display();
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
					return;
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
					if (($table == 'hosttype' && $filter_hosttype != '') 
						) {
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
			return;
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

	//-----------

	protected function cancel()
	{
		$this->_redirect = 'index.php?option='.$this->_option;
	}
}
?>