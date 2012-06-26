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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
ximport('Hubzero_Tool');
ximport('Hubzero_Tool_Version');

/**
 * Short description for 'ContribtoolController'
 * 
 * Long description (if any) ...
 */
class ContribtoolController extends JObject
{

	/**
	 * Description for '_name'
	 * 
	 * @var string
	 */
	private $_name  = NULL;

	/**
	 * Description for '_data'
	 * 
	 * @var array
	 */
	private $_data  = array();

	/**
	 * Description for '_task'
	 * 
	 * @var unknown
	 */
	private $_task  = NULL;

	/**
	 * Description for 'error'
	 * 
	 * @var unknown
	 */
	private $error  = NULL;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $config Parameter description (if any) ...
	 * @return     void
	 */
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

	/**
	 * Short description for '__set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Short description for '__get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}
	//-----------

	/**
	 * Short description for 'getTask'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	private function getTask()
	{
		$task = JRequest::getVar( 'task', 'view' );
		$this->_task = $task;
		return $task;
	}

	/**
	 * Short description for 'execute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$database =& JFactory::getDBO();

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

		switch ( $this->getTask() )
		{
		    case  'edit':
				$this->edit();
				break;
			case  'apply':
				$this->apply();
				break;
			case  'save':
				$this->save();
				break;
			case  'cancel':
				$this->cancel();
				break;
			case  'editToolVersion':
				$this->editToolVersion();
				break;
			case  'view':
				$this->view();
				break;
			case  'editTool':
				$this->editTool();
				break;
			case  'batch_doi':
				$this->_batchDoi();
				break;
			case  'setup_doi':
				$this->_setupDoi();
				break;
			default:
				$this->view();
				break;
		}
	}

	//----------------------------------------------------------
	// Setup component
	//----------------------------------------------------------

	/**
	 * Short description for 'defaultParams'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
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
					usedoi=0
					exec_pu=0
					screenshot_edit=1';

		return $params;

	}
	//-------------

	/**
	 * Short description for 'cancel'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function cancel()
	{
		    $toolid = JRequest::getInt( 'toolid', null );

		    $ids = JRequest::getVar( 'id', array() );

		    if (is_array($ids))
			   	$id = (!empty($ids)) ? $ids[0] : null;
		    else
			   	$id = $ids;

		    if (is_numeric($toolid) && !is_numeric($id))
			   $this->view(0);

		    if (is_numeric($toolid) && is_numeric($id))
			   $this->view($toolid,0);
	}

	/**
	 * Short description for 'apply'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function apply()
	{
	    $this->save(0);
	}

	/**
	 * Short description for 'saveToolVersion'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      boolean $redirect Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	protected function saveToolVersion($redirect = true)
	{
		// Incoming instance ID
          $id = JRequest::getInt( 'id', 0, 'post' );

          // Do we have an ID?
          if (!$id) {
			die('invalid tool instance id');
               return;
          }

	   	$hztv = Hubzero_Tool_Version::getInstance( $id );

		if (!$hztv)
			die('tool instance not found');

		$vnc_command = JRequest::getVar('command', null, 'post');

		if (is_null($vnc_command))
			die('no command value returned by form');

		$vnc_geometry = JRequest::getString('geometry', null, 'post');

		if (is_null($vnc_geometry))
			die('no geometry value returned by form');

		$vnc_hostreq = JRequest::getString('hostreq', null, 'post');

		if (is_null($vnc_hostreq))
			die('no hostreq value returned by form');

		$vnc_timeout = JRequest::getString('timeout', null, 'post');

		if (is_null($vnc_timeout))
			die('no timeout value returned by form');

		$params = JRequest::getString('params', null, 'post');

		if (is_null($params))
			die('no params value returned by form');

		if ($vnc_timeout == "0")
		    $vnc_timeout = '0';
		else if (!is_numeric($vnc_timeout))
		    $vnc_timeout = null;
		else
		    $vnc_timeout = intval($vnc_timeout);

		$vnc_hostreq = explode(',',$vnc_hostreq);

		$hostreq = array();
		foreach((array)$vnc_hostreq as $req)
		{
		    	if (!empty($req))
			{
			    	$hostreq[] = $req;
			}
		}

		$hztv->hostreq = $hostreq;
		$hztv->vnc_command = $vnc_command;
		$hztv->vnc_timeout = $vnc_timeout;
		$hztv->vnc_geometry = $vnc_geometry;
		$hztv->params = $params;
		$hztv->update();

		if ($redirect) {
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option . "&task=view&toolid=" . $hztv->toolid);
			$this->_message = JText::_('TOOL_VERSION_SAVED');
		}
		else
		{
			$this->editToolVersion();
		}
	}

	/**
	 * Short description for 'saveTool'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      boolean $redirect Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	protected function saveTool($redirect = true)
	{
		// Incoming instance ID
          $toolid = JRequest::getInt( 'toolid', null, 'post' );

          // Do we have an ID?
          if (!$toolid) {
			die('invalid tool id');
               return;
          }

	   	$hzt = Hubzero_Tool::getInstance( $toolid );

		if (!$hzt)
			die('tool not found');

		$tooltitle = JRequest::getString('tooltitle',null,'post');

		if (is_null($tooltitle))
			die('no tooltitle returned by form');

		$hzt->title = $tooltitle;
		$hzt->update();

		if ($redirect) {
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option);
			$this->_message = JText::_('TOOL_SAVED');
		}
		else
		{
			$this->editTool();
		}
	}

	/**
	 * Short description for 'save'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      boolean $redirect Parameter description (if any) ...
	 * @return     void
	 */
	protected function save($redirect = true)
	{
		JRequest::checkToken() or die( 'Invalid Token' );
		
          $type = JRequest::getString( 'type', '' );

		if ($type == "toolversion")
		{
			$this->saveToolVersion($redirect);
		}
		else if ($type == "tool")
		{
		    	$this->saveTool($redirect);
		}
	}

	/**
	 * Short description for 'edit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function edit()
	{
		    $toolid = JRequest::getInt( 'toolid', null );

		    $ids = JRequest::getVar( 'id', array() );

		    if (is_array($ids))
			   	$id = (!empty($ids)) ? $ids[0] : null;
		    else
			   	$id = $ids;

		    if (is_numeric($toolid) && !is_numeric($id))
			   $this->editTool($toolid);

		    if (is_numeric($toolid) && is_numeric($id))
			   $this->editToolVersion($toolid,$id);
	}

	/**
	 * Short description for 'editTool'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $toolid Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	protected function editTool($toolid = null)
	{
		// Incoming instance ID
          if (empty($toolid))
		{
		    $toolid = JRequest::getInt( 'toolid', null );
		}

          // Do we have an ID?
          if (!$toolid) {
			die('invalid tool id');
               return;
          }

	   	$hzt = Hubzero_Tool::getInstance( $toolid );

		$data['toolid'] = $hzt->id;
		$data['toolname'] = $hzt->toolname;
		$data['title'] = $hzt->title;

		ContribtoolHtml::editTool($data, $this->_option);
	}

	/**
	 * Short description for 'editToolVersion'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $toolid Parameter description (if any) ...
	 * @param      unknown $id Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	protected function editToolVersion($toolid  = null,$id = null)
	{
		// Incoming instance ID
          if (empty($toolid))
		{
		    $toolid = JRequest::getInt( 'toolid', null );
		}

          // Do we have an ID?
          if (!$toolid) {
			die('invalid tool id');
               return;
          }

		if (empty($id))
		{
		    	$id = JRequest::getInt('id',0);
		}

		if (!$id) {
		    die('invalid tool version id');
		    return;
		}

          $app =& JFactory::getApplication();
          $database =& JFactory::getDBO();

	   	$hzt = Hubzero_Tool_Version::getInstance( $id );

		$data['toolid'] = $hzt->toolid;
		$data['id'] = $hzt->id;
		$data['instance'] = $hzt->instance;
		$data['vnc_geometry'] = $hzt->vnc_geometry;
		$data['vnc_command'] = $hzt->vnc_command;
		$data['vnc_timeout'] = $hzt->vnc_timeout;
		$data['hostreq'] = $hzt->hostreq;
		$data['params'] = $hzt->params;

		ContribtoolHtml::editToolVersion($data, $this->_option);
	}

	/**
	 * Short description for 'view'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $toolid Parameter description (if any) ...
	 * @param      unknown $id Parameter description (if any) ...
	 * @return     void
	 */
     protected function view($toolid = null, $id = null)
     {
          $app =& JFactory::getApplication();
          $database =& JFactory::getDBO();

		if (is_null($toolid))
			   $toolid = JRequest::getInt( 'toolid', null );

		if (is_null($id))
		{
			$ids = JRequest::getVar( 'id', array() );

			if (is_array($ids))
		 		$id = (!empty($ids)) ? $ids[0] : null;
			else
		   		$id = $ids;
		}

          // Get configuration
          $config = JFactory::getConfig();

          jimport('joomla.html.pagination');

		if (empty($toolid))
		{
          	// Get filters
          	$filters = array();
          	$filters['search'] = urldecode($app->getUserStateFromRequest($this->_option.'.search', 'search', ''));
          	$filters['search_field'] = urldecode($app->getUserStateFromRequest($this->_option.'.search_field', 'search_field', 'toolname'));
          	$filters['sortby'] = $app->getUserStateFromRequest($this->_option.'.sortby', 'sortby', 'toolname');
          	// Get paging variables
          	$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
          	$filters['start'] = JRequest::getInt('limitstart', 0);

			// Get a record count
			$total = Hubzero_Tool::getToolCount($filters, true);

			// Get records
			$rows = Hubzero_Tool::getToolSummaries($filters, true);

          	// Initiate paging
          	$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

			if (empty($filters['search_field']))
			    	$filters['search_field'] = 'all';

			if (empty($filters['sortby']))
			    	$filters['sortby'] = 'state_changed DESC';

          	// Output HTML
          	ContribtoolHtml::browseTools( $rows, $pageNav, $this->_option, $filters );
		}
		else
		{
	    		$hzt = Hubzero_Tool::getInstance( $toolid );

          	// Get a record count
			$total = count($hzt->version);

			$data['toolname'] = $hzt->toolname;
			$data['id'] = $hzt->id;
			$data['title'] = $hzt->title;

          	// Get filters
          	$filters = array();
          	$filters['search'] = urldecode($app->getUserStateFromRequest($this->_option.'.search2', 'search', ''));
          	$filters['search_field'] = urldecode($app->getUserStateFromRequest($this->_option.'.search_field2', 'search_field', 'toolname'));
          	$filters['sortby'] = $app->getUserStateFromRequest($this->_option.'.sortby2', 'sortby', 'toolname');
          	// Get paging variables
          	$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit2', 'limit', $config->getValue('config.list_limit'), 'int');
          	$filters['start'] = JRequest::getInt('limitstart', 0);

			$data['version'] = $hzt->getToolVersionSummaries($filters, true);

          	// Initiate paging
			$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

          	// Output HTML
			ContribtoolHtml::browseToolVersions($data, $pageNav, $this->_option, $filters );
		}
	}

	/**
	 * Short description for 'createResPage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $toolid Parameter description (if any) ...
	 * @param      array $tool Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
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

	/**
	 * Short description for 'createTicket'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $toolid Parameter description (if any) ...
	 * @param      array $tool Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	protected function createTicket($toolid, $tool)
	{
		ximport('Hubzero_Tool');
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
				Hubzero_Tool::saveTicketId($toolid, $row->id);

				// make a record
				$this->updateTicket($toolid, '', '', JText::_('Tool ticket was previously missing. The ticket has been created.'), $access=0, $email=1);
			}

		}

		return $row->id;
	}

	/**
	 * Short description for 'updateTicket'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $toolid Parameter description (if any) ...
	 * @param      array $oldstuff Parameter description (if any) ...
	 * @param      array $newstuff Parameter description (if any) ...
	 * @param      unknown $comment Parameter description (if any) ...
	 * @param      integer $access Parameter description (if any) ...
	 * @param      integer $email Parameter description (if any) ...
	 * @param      array $changelog Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	protected function updateTicket($toolid, $oldstuff, $newstuff, $comment, $access=0, $email=0, $changelog=array())
	{
		ximport('Hubzero_Tool');
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();

		$ticketid = Hubzero_Tool::getTicketId($toolid);
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

	//-----------
	// Temp function to issue new service DOIs for tool versions published previously

	/**
	 * Short description for '_batchDoi'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	private function _batchDoi()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		//  Limit one-time batch size
		$limit = JRequest::getInt( 'limit', 2 );

		// Store output	
		$created = array();
		$failed = array();

		// Initiate extended database classes
		$resource = new ResourcesResource( $database );
		$objDOI = new ResourcesDoi ($database);
		$objV = new ToolVersion( $database );
		$objA = new ToolAuthor( $database);

		// Get hub config
		$xhub =& Hubzero_Factory::getHub();
		$jconfig =& JFactory::getConfig();
		$live_site = rtrim(JURI::base(),'/');
		$hubShortName 	= $xhub->getCfg('hubShortName');
		
		// Get config
		$config =& JComponentHelper::getParams( $this->_option );

		// Get all tool publications without new DOI
		$query = "SELECT * FROM #__doi_mapping WHERE doi='' OR doi IS NULL ";
		$database->setQuery( $query );
		$rows = $database->loadObjectList();

		if($rows) {
			$i = 0;
			foreach($rows as $row) {
				if($limit && $i == $limit) {
					// Output status message
					if($created) {
						foreach ($created as $cr) {
							echo '<p>'.$cr.'</p>';
						}
					}
					echo '<p>Registered '.count($created).' dois, failed '.count($failed).'</p>';
					return;
				}

				// Skip entries with no resource information loaded / non-tool resources
				if(!$resource->load($row->rid) || !$row->alias) {
					continue;
				}

				// Get version info
				$query = "SELECT * FROM #__tool_version WHERE toolname='".$row->alias."' ";
				$query.= "AND revision='".$row->local_revision."' AND state!=3 LIMIT 1";
				$database->setQuery( $query );
				$results = $database->loadObjectList();

				if($results) {
					$title = $results[0]->title ? $results[0]->title : $resource->title;
					$pubyear = $results[0]->released ? trim(JHTML::_('date', $results[0]->released, '%Y')) : date( 'Y' );
				}
				else {
					// Skip if version not found
					continue;
				}

				// Collect metadata
				$metadata = array();
				$metadata['targetURL'] = $live_site . '/resources/' . $row->rid . '/?rev='.$row->local_revision;
				$metadata['title'] = htmlspecialchars($title);
				$metadata['pubYear'] = $pubyear;
				
				// Get authors
				$objA = new ToolAuthor( $database);
				$authors = $objA->getAuthorsDOI($row->rid);

				// Register DOI			
				$doiSuccess = $objDOI->registerDOI( $authors, $config, $metadata, $doierr);
				if($doiSuccess) {
					$query = "UPDATE #__doi_mapping SET doi='$doiSuccess' ";
					$query.= "WHERE rid=$row->rid AND local_revision=$row->local_revision";
					$database->setQuery( $query );
					if (!$database->query()) {
						$failed[] = $doiSuccess;
					}
					else {
						$created[] = $doiSuccess;
					}
				}
				else {
					print_r($doierr);
					echo '<br />';
					print_r($metadata);
					echo '<br />';
				}

				$i++;
			}
		}

		// Output status message
		if($created) {
			foreach ($created as $cr) {
				echo '<p>'.$cr.'</p>';
			}
		}
		echo '<p>Registered '.count($created).' dois, failed '.count($failed).'</p>';
		return;
	}

	//-----------
	// Temp function to ensure jos_doi_mapping table is updated

	/**
	 * Short description for '_setupDoi'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	private function _setupDoi()
	{
		$database =& JFactory::getDBO();
		$fields = $database->getTableFields('jos_doi_mapping');
		print_r($fields);

		if(!array_key_exists('versionid', $fields['jos_doi_mapping'] )) {
			$database->setQuery( "ALTER TABLE `jos_doi_mapping` ADD `versionid` int(11) default '0'" );
			if (!$database->query()) {
				echo $database->getErrorMsg();
				return false;
			}
		}
		if(!array_key_exists('doi', $fields['jos_doi_mapping'] )) {
				$database->setQuery( "ALTER TABLE `jos_doi_mapping` ADD `doi` varchar(50) default NULL" );
				if (!$database->query()) {
					echo $database->getErrorMsg();
					return false;
				}
		}
		return;
	}
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	/**
	 * Short description for 'pipeline'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function pipeline()
	{
		// Output HTML
		ContribtoolHtml::summary( $this->error, $this->_option, $this->config,  0);
	}

	/**
	 * Short description for 'redirect'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}
}
?>
