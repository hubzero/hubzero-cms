<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

class ContribtoolController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	private $error  = NULL;

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
	
	private function getTask()
	{
		$task = JRequest::getVar( 'task', '' );
		$this->_task = $task;
		return $task;
	}
	
	//-----------

	public function execute()
	{
		
		$database =& JFactory::getDBO();
		$obj = new ContribtoolSetup ($database);
		
		// Check if component entry is there
		$database->setQuery( "SELECT c.id FROM #__components as c WHERE c.option='".$this->_option."'" );
		$found = $database->loadResult();
		
		if(!$found) {
			// Make component entry
			$params = $this->defaultParams();
			$obj->createComponentEntry($this->_option, $this->_name, $params);
		}
		
		// Load the component config
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;
		
		// Check if all necessary tables are there		
		$tables = $database->getTableList();
		$table_tool_version = $database->_table_prefix.'tool_version';
		$table_tool_groups = $database->_table_prefix.'tool_groups';
		$setup = 0;
		
		if (!in_array($table_tool_version,$tables)) {
			$setup = 1;
		}
		if (!in_array($table_tool_groups,$tables)) {
			$setup = 2;
		}
		
		if($setup && $this->getTask()!='setup') {
			$this->startSetup ($setup);
			return;
		}
		
		switch ( $this->getTask() ) 
		{	
			case  'setup':  $this->setup();       			break; 
			case  'start':  $this->startSetup();       		break; 
		
			default: 		$this->pipeline(); 				break;
		}
	}
	
	//----------------------------------------------------------
	// Setup component
	//----------------------------------------------------------
	
	public function startSetup($setup=0)
	{
		
		// Output HTML
		ContribtoolHtml::setup( $this->_option, $setup);
	}
	//-------------
	
	public function defaultParams()
	{
		$params = 'contribtool_on=0
					admingroup=apps
					default_mw=narwhal
					default_vnc=780x600
					developer_url=https://developer.nanohub.org
					developer_site=nanoFORGE
					developer_email=support@nanohub.org
					project_path=/projects/app-
					invokescript_dir=/apps
					adminscript_dir=/apps/bin
					dev_suffix=_dev
					group_prefix=app-
					demo_url=
					doi_service=http://dir1.lib.purdue.edu:8080/axis/services/CreateHandleService?wsdl
					ldap_save=1
					ldap_read=0
					usedoi=0
					exec_pu=0
					screenshot_edit=1';
	
		return $params;
	
	}
	//-------------
	
	public function setup()
	{
		$database =& JFactory::getDBO();		
		$tables = $database->getTableList();
		$juser =& JFactory::getUser();
		
		$update = JRequest::getInt( 'update', 0);
		
		$table_tool 			= $database->_table_prefix.'tool';
		$table_tool_version 	= $database->_table_prefix.'tool_version';
		$table_tool_groups 		= $database->_table_prefix.'tool_groups';
		$table_tool_authors 	= $database->_table_prefix.'tool_authors';
		$table_tool_licenses 	= $database->_table_prefix.'tool_licenses';
		$table_tool_statusviews = $database->_table_prefix.'tool_statusviews';
		$table_screenshots 		= $database->_table_prefix.'screenshots';
		$table_doi_mapping		= $database->_table_prefix.'doi_mapping';
		
		$obj = new ContribtoolSetup ($database);
		
		if($update) {
	
			// update component parameters (set defaults)
			$params = $this->defaultParams();
			$obj->updateComponentEntry($this->_option, $this->_name, $params);
			// display summary
			ContribtoolHtml:: summary($this->error, $this->_option, $this->config, $update);
			return;
		}
		
		// add tool table if not already present
		if (!in_array($table_tool,$tables)) {
		
			if(!$obj->createToolTb()) {
				$this->error .= '<br />'.$obj->getError();
			}
		}
		
		// add versions table if not already present
		if (!in_array($table_tool_version,$tables)) {
			
			if(!$obj->createVersionTb()) {
				$this->error .= '<br />'.$obj->getError();
			}
		}
		else {
		 	// check if vnc_command column is there
			$database->setQuery( 'SHOW COLUMNS FROM `' .$table_tool_version. '` ' );
    		$tableFields = $database->loadObjectList();
			$vnc_command_found = 0;
			foreach ($tableFields as $tf) {
				if($tf->Field=='vnc_command') {
					$vnc_command_found = 1;
				}
			}
			if(!$vnc_command_found) {
				// add field
				$database->setQuery( 'ALTER TABLE '.$table_tool_version.' ADD vnc_command VARCHAR(100) AFTER vnc_geometry' );
				$database->query();
			}
		}
			
		// add authors table if not already present
		if (!in_array($table_tool_authors,$tables)) {
		
			if(!$obj->createAuthorsTb()) {
				$this->error .= '<br />'.$obj->getError();
			}
		}
		else {
		 	// check if version_id column is there
			$database->setQuery( 'SHOW COLUMNS FROM `' .$table_tool_authors. '` ' );
    		$tableFields = $database->loadObjectList();
			$vid_found = 0;
			foreach ($tableFields as $tf) {
				if($tf->Field=='version_id') {
					$vid_found = 1;
				}
			}
			if(!$vid_found) {
				// add field
				$database->setQuery( 'ALTER TABLE '.$table_tool_authors.' ADD version_id INTEGER(11) NOT NULL AFTER ordering' );
				$database->query();
			}
		}
			
		// add licenses table with default data if not already present
		if (!in_array($table_tool_licenses,$tables)) {
		
			if(!$obj->createLicensesTb()) {
				$this->error .= '<br />'.$obj->getError();
			}
		}
		
		// add statusviews table if not already present
		if (!in_array($table_tool_statusviews,$tables)) {
		
			if(!$obj->createStatusViewsTb()) {
				$this->error .= '<br />'.$obj->getError();
			}
		}
		
		// add doi_mapping table if not already present
		if (!in_array($table_doi_mapping,$tables)) {
		
			if(!$obj->createDOITb()) {
				$this->error .= '<br />'.$obj->getError();
			}
		}
		
		// add tool_groups table if not already present
		if (!in_array($table_tool_groups,$tables)) {
		
			if(!$obj->createGroupsTb()) {
				$this->error .= '<br />'.$obj->getError();
			}
		}
		
		// add screenshots table if not already present
		if (!in_array($table_screenshots,$tables)) {
		
			if(!$obj->createSSTb()) {
				$this->error .= '<br />'.$obj->getError();		}
		}
		
		if($this->error) {
			ContribtoolHtml:: summary($this->error, $this->_option, $this->config, $update);
			return;
		}
		
		
		// create some objects we'll need
		$toolObj 	= new Tool( $database );
		$objV	 	= new ToolVersion( $database );
		$objA 	 	= new ToolAuthor( $database);
		$resource 	= new ResourcesResource( $database);
		$objG 		= new ToolGroup( $database );	
		$st 		= new SupportTags( $database );
		//include_once( JPATH_ROOT.DS.'components'.DS.'com_support'.DS.'support.tags.php' );
		
		// get tools data
		$tools = $toolObj->getToolsOldScheme();
		
		// all tables/columns are there, now convert to new scheme
		if(count($tools) > 0 ) {
			// loop through tools
			foreach ($tools as $tool) {
				$uids = array();
				
				if(!$objV->getDevVersionProperty ($tool->toolname, 'id')) {
					// create entry for dev version
					$obj->createVersion($tool, $this->config, 'dev');
				}
				if($tool->published==1 && $tool->state!=8 && $tool->state!=9 &&  !$objV->getCurrentVersionProperty ($tool->toolname, 'id')) {
					// create entry for published version if not already present
					$obj->createVersion($tool, $this->config, 'current');
				}
				
				// convert team to an array
				if($tool->team) {
					$developers = ContribtoolHelper::makeArray($tool->team);
					foreach ($developers as $developer) {
						$muser =& JUser::getInstance( $developer );
						if (is_object($muser)) {
							$uids[] = $muser->get('id');
						} 
					}
				}
				
				
				// create/update developers group
				$group_prefix  = isset($this->config->parameters['group_prefix']) ? $this->config->parameters['group_prefix'] : 'app-';
				$groupexists = $toolObj->getToolDevGroup($tool->id);
				if(!$groupexists) {
					$objG->saveGroup($tool->id, $group_prefix.$tool->toolname, $uids, $groupexists);
				}
				
				// get tool status
				$toolObj->getToolStatus( $tool->id, $this->_option, $status, 'dev', 0 );

				
				// add tool:tag to ticket
				if($tool->ticketid) {
					$st->tag_object( $juser->get('id'), $tool->ticketid, 'tool:'.$tool->toolname, 0, 0 );
				}
				else {
					// create ticket
					if($status) {
					$this->createTicket($tool->id, $status);
					}
				}	
	
				// create resource page
				$rid = $toolObj->getResourceId($tool->id);
				if(!$rid && $status) {
					$this->createResPage($tool->id, $status);
				}		
				
			}
		}
		

		// fix up authors
		$versions = $objV->getAll(0);
		if(count($versions) > 0) {
			foreach ($versions as $v) {
		
				$database->setQuery( "UPDATE #__tool_authors SET version_id='$v->id' WHERE toolname='$v->toolname' AND revision='$v->revision'" );
				if (!$database->query()) {
					echo $database->getErrorMsg();
					exit;
				}
				
			}
		
		}
			
		ContribtoolHtml:: summary($this->error, $this->_option, $this->config, $update);	
	
	}
	//-----------
	
	protected function createResPage($toolid, $tool)
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();

		$params = 'pageclass_sfx=
					show_title=1
					show_authors=1
					show_assocs=1
					show_type=1
					show_logicaltype=1
					show_rating=1
					show_date=1
					show_parents=1
					series_banner=
					show_banner=1
					show_footer=3
					show_stats=0
					st_appname='.strtolower($tool['toolname']).'
					st_appcaption='.$tool['title'].$tool['version'].'
					st_method=com_narwhal';

		// Initiate extended database class
		$row = new ResourcesResource( $database );
		$row->created_by = $juser->get('id');
		$row->created = date( 'Y-m-d H:i:s' );
		$row->published = '2';  // draft state
		$row->params = $params;
		$row->attribs = 'marknew=0';
		$row->standalone = '1';
		$row->type = '7';
		
		$binditems = array('title'=>$tool['title'], 'introtext'=>$tool['description'],  'alias'=>strtolower($tool['toolname']) );

		if (!$row->bind($binditems)) {
			$this->setError( $row->getError() );
			return false;
		}
		if (!$row->store()) {
			$this->setError( $row->getError() );
			return false;
		}

		// Checkin resource
		$row->checkin();

		return $row->id;
	}
	//-----------

	protected function createTicket($toolid, $tool)
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();

	
		$st = new SupportTags( $database );
		$row = new SupportTicket( $database );
		$row->status = 0;
		$row->created =  date( "Y-m-d H:i:s" );
		$row->login = $juser->get('username');
		$row->severity = 'normal';
		$row->summary = JText::_('Tool').': '.$tool['toolname'];
		$row->report = $tool['toolname'];
		$row->section = 2;
		$row->type = 1;
		$row->email = $juser->get('email');
		$row->name = $juser->get('name');

		if (!$row->store()) {
			$this->_error = $row->getError();
			return false;
		}
		else {
			// Checkin ticket
			$row->checkin();

			if($row->id) {
				// save tag
				$st->tag_object( $juser->get('id'), $row->id, 'tool:'.$tool['toolname'], 0, 0 );

				// store ticket id
				$obj = new Tool( $database);
				$obj->saveTicketId($toolid, $row->id);

				// make a record
				$this->updateTicket($toolid, '', '', JText::_('Tool ticket was previously missing. The ticket has been created.'), $access=0, $email=1);
			}

		}

		return $row->id;
	}
	
	//-----------
	
	protected function updateTicket($toolid, $oldstuff, $newstuff, $comment, $access=0, $email=0, $changelog=array())
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();

		$obj = new Tool( $database);
		$ticketid = $obj->getTicketId($toolid);
		$summary = '';

		// see what changed
		if($oldstuff != $newstuff) {
			if ($oldstuff['toolname'] != $newstuff['toolname']) {
				$changelog[] = '<li><strong>'.JText::_('TOOLNAME').'</strong> '.JText::_('TICKET_CHANGED_FROM')
				.' <em>'.$oldstuff['toolname'].'</em> '.JText::_('TO').' <em>'.$newstuff['toolname'].'</em></li>';
			}
			if ($oldstuff['title'] != $newstuff['title']) {
				$changelog[] = '<li><strong>'.JText::_('TOOL').' '.strtolower(JText::_('TITLE')).'</strong> '.JText::_('TICKET_CHANGED_FROM')
				.' <em>'.$oldstuff['title'].'</em> '.JText::_('TO').' <em>'.$newstuff['title'].'</em></li>';
				$summary .= strtolower(JText::_('TITLE'));
			}
			if ($oldstuff['version']!='' && $oldstuff['version'] != $newstuff['version'] ) {
				$changelog[] = '<li><strong>'.strtolower(JText::_('DEV_VERSION_LABEL')).'</strong> '.JText::_('TICKET_CHANGED_FROM')
				.' <em>'.$oldstuff['version'].'</em> '.JText::_('TO').' <em>'.$newstuff['version'].'</em></li>';
				$summary .= ', '.strtolower(JText::_('VERSION'));
			}
			else if($oldstuff['version']=='' && $newstuff['version']!='') {
				$changelog[] = '<li><strong>'.strtolower(JText::_('DEV_VERSION_LABEL')).'</strong> '.JText::_('TICKET_SET_TO')
				.' <em>'.$newstuff['version'].'</em>';
			}
			if ($oldstuff['description'] != $newstuff['description']) {
				$changelog[] = '<li><strong>'.JText::_('TOOL').' '.strtolower(JText::_('DESCRIPTION')).'</strong> '.JText::_('TICKET_CHANGED_FROM')
				.' <em>'.$oldstuff['description'].'</em> '.JText::_('TO').' <em>'.$newstuff['description'].'</em></li>';
				$summary .= ', '.strtolower(JText::_('DESCRIPTION'));
			}
			if ($oldstuff['exec'] != $newstuff['exec']) {
				$changelog[] = '<li><strong>'.JText::_('TOOL_ACCESS').'</strong> '.JText::_('TICKET_CHANGED_FROM')
				.' <em>'.$oldstuff['exec'].'</em> '.JText::_('TO').' <em>'.$newstuff['exec'].'</em></li>';
				if($newstuff['exec']=='@GROUP') {
				$changelog[] = '<li><strong>'.JText::_('ALLOWED_GROUPS').'</strong> '.JText::_('TICKET_SET_TO')
				.' to <em>'.ContribtoolHtml::getGroups($newstuff['membergroups']).'</em></li>';
				}
				$summary .= ', '.strtolower(JText::_('TOOL_ACCESS'));
			}
			if ($oldstuff['code'] != $newstuff['code']) {
				$changelog[] = '<li><strong>'.JText::_('CODE_ACCESS').'</strong> '.JText::_('TICKET_CHANGED_FROM')
				.' <em>'.$oldstuff['code'].'</em> '.JText::_('TO').' <em>'.$newstuff['code'].'</em></li>';
				$summary .= ', '.strtolower(JText::_('CODE_ACCESS'));
			}
			if ($oldstuff['wiki'] != $newstuff['wiki']) {
				$changelog[] = '<li><strong>'.JText::_('WIKI_ACCESS').'</strong> '.JText::_('TICKET_CHANGED_FROM')
				.' <em>'.$oldstuff['wiki'].'</em> '.JText::_('TO').' <em>'.$newstuff['wiki'].'</em></li>';
				$summary .= ', '.strtolower(JText::_('WIKI_ACCESS'));
			}
			if ($oldstuff['vncGeometry'] != $newstuff['vncGeometry']) {
				$changelog[] = '<li><strong>'.JText::_('VNC_GEOMETRY').'</strong> '.JText::_('TICKET_CHANGED_FROM')
				.' <em>'.$oldstuff['vncGeometry'].'</em> to <em>'.$newstuff['vncGeometry'].'</em></li>';
				$summary .= ', '.strtolower(JText::_('VNC_GEOMETRY'));
			}
			if ($oldstuff['developers'] != $newstuff['developers']) {
				$changelog[] = '<li><strong>'.JText::_('DEVELOPMENT_TEAM').'</strong> '.JText::_('TICKET_CHANGED_FROM')
				.' <em>'.ContribtoolHtml::getDevTeam($oldstuff['developers']).'</em> '.JText::_('TO').' <em>'.ContribtoolHtml::getDevTeam($newstuff['developers']).'</em></li>';
				$summary .= ', '.strtolower(JText::_('DEVELOPMENT_TEAM'));
			}			
			if ($oldstuff['vncGeometry'] != $newstuff['vncGeometry']) {
				$changelog[] = '<li><strong>'.JText::_('VNC_GEOMETRY').'</strong> '.JText::_('TICKET_CHANGED_FROM')
				.' <em>'.$oldstuff['vncGeometry'].'</em> '.JText::_('TO').' <em>'.$newstuff['vncGeometry'].'</em></li>';
				$summary .= ', '.strtolower(JText::_('VNC_GEOMETRY'));
			}
			// end of tool information changes
			if($summary) {
				$summary .= ' '.JText::_('INFO_CHANGED');
			}
			
			// tool status/priority changes
			if ($oldstuff['priority'] != $newstuff['priority']) {
				$changelog[] = '<li><strong>'.JText::_('PRIORITY').'</strong> '.JText::_('TICKET_CHANGED_FROM')
				.' <em>'.ContribtoolHtml::getPriority($oldstuff['priority']).'</em> '.JText::_('TO').' <em>'.ContribtoolHtml::getPriority($newstuff['priority']).'</em></li>';
				$email = 0; // do not send email about priority changes
			}
			if ($oldstuff['state'] != $newstuff['state']) {
				$changelog[] = '<li><strong>'.JText::_('STATUS').'</strong> '.JText::_('TICKET_CHANGED_FROM')
				.' <em>'.ContribtoolHtml::getStatusName($oldstuff['state'], $oldstate).'</em> '.JText::_('TO').' <em>'.ContribtoolHtml::getStatusName($newstuff['state'], $newstate).'</em></li>';
				$summary = JText::_('STATUS').' '.JText::_('TICKET_CHANGED_FROM').' '.$oldstate.' '.JText::_('TO').' '.$newstate;
				$email = 1; // send email about status changes
			}
		}

		// Were there any changes?
		$log = implode(n,$changelog);
		if ($log != '') {
			$log = '<ul class="changelog">'.n.$log.'</ul>'.n;
		}

		$rowc = new SupportComment( $database );
		$rowc->ticket     = $ticketid;
		if($comment) {
			$rowc->comment    = nl2br($comment);
			$rowc->comment    = str_replace( '<br>', '<br />', $rowc->comment );
		}
		$rowc->created    = date( 'Y-m-d H:i:s', time() );
		$rowc->created_by = $juser->get('username');
		$rowc->changelog  = $log;
		$rowc->access     = $access;

		if (!$rowc->store()) {
			$this->_error = $rowc->getError();
			return false;
		}
		

		return true;

	}
	

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function pipeline()
	{
		// Output HTML
		ContribtoolHtml::summary( $this->error, $this->_option, $this->config,  0);
	}

	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}
}
?>