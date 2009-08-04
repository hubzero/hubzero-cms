<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
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

class HubController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;

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
		$task = Jrequest::getVar( 'task', '' );
		$this->_task = $task;
		return $task;
	}
	
	//-----------
	
	public function execute()
	{
		// Load the component
		$database =& JFactory::getDBO();
		$component = new JTableComponent( $database );
		$component->loadByOption( $this->_option );
		
		switch ( $this->getTask() ) 
		{
			case 'save':       $this->save();      break;
			case 'remove':     $this->delete();    break;
			case 'new':        $this->edit();      break;
			case 'add':        $this->edit();      break;
			case 'edit':       $this->edit();      break;
			case 'cancel':     $this->cancel();    break;
			case 'misc':       $this->misc();      break;
			
			case 'components':   $this->components();          break;
			case 'savecom':      $this->savecom();             break;
			
			case 'registration': $this->settings();            break;
			case 'savereg':      $this->_save('registration'); break;
			case 'databases':    $this->settings();            break;
			case 'savedb':       $this->_save('databases');    break;
			case 'site':         $this->settings();            break;
			case 'savesite':     $this->_save('site');         break;
			
			case 'addorg': $this->addorg(); break;
			case 'editorg': $this->editorg(); break;
			case 'removeorg': $this->removeorg(); break;
			case 'saveorg': $this->saveorg(); break;
			case 'cancelorg': $this->cancelorg(); break;
			case 'orgs': $this->orgs(); break;
			
			default: $this->settings(); break;
		}
		
		$database->setQuery( "SELECT COUNT(*) FROM #__components WHERE `option`='".$component->option."' AND parent=".$component->id );
		$menuitems = $database->loadResult();
		if (!$menuitems) {
			$menusite = new JTableComponent( $database );
			$menusite->name = 'Site';
			$menusite->parent = $component->id;
			$menusite->admin_menu_link = 'option='.$this->_option.'&task=site';
			$menusite->admin_menu_alt = 'Site';
			$menusite->option = $this->_option;
			$menusite->ordering = 1;
			$menusite->store();
			
			$menureg = new JTableComponent( $database );
			$menureg->name = 'Registration';
			$menureg->parent = $component->id;
			$menureg->admin_menu_link = 'option='.$this->_option.'&task=registration';
			$menureg->admin_menu_alt = 'Registration';
			$menureg->option = $this->_option;
			$menureg->ordering = 2;
			$menureg->store();
			
			$menudat = new JTableComponent( $database );
			$menudat->name = 'Databases';
			$menudat->parent = $component->id;
			$menudat->admin_menu_link = 'option='.$this->_option.'&task=databases';
			$menudat->admin_menu_alt = 'Databases';
			$menudat->option = $this->_option;
			$menudat->ordering = 3;
			$menudat->store();
			
			$menumis = new JTableComponent( $database );
			$menumis->name = 'Misc. Settings';
			$menumis->parent = $component->id;
			$menumis->admin_menu_link = 'option='.$this->_option.'&task=misc';
			$menumis->admin_menu_alt = 'Misc. Settings';
			$menumis->option = $this->_option;
			$menumis->ordering = 4;
			$menumis->store();
			
			$menucom = new JTableComponent( $database );
			$menucom->name = 'Components';
			$menucom->parent = $component->id;
			$menucom->admin_menu_link = 'option='.$this->_option.'&task=components';
			$menucom->admin_menu_alt = 'Components';
			$menucom->option = $this->_option;
			$menucom->ordering = 5;
			$menucom->store();
			
			$menucom = new JTableComponent( $database );
			$menucom->name = 'Organizations';
			$menucom->parent = $component->id;
			$menucom->admin_menu_link = 'option='.$this->_option.'&task=orgs';
			$menucom->admin_menu_alt = 'Organizations';
			$menucom->option = $this->_option;
			$menucom->ordering = 6;
			$menucom->store();
		}
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
	// Config functions
	//----------------------------------------------------------
	
	protected function &loadConfiguration()
	{
		$arr = array();
		
		if (!is_readable(JPATH_CONFIGURATION.DS.'hubconfiguration.php')) {
			return $arr;
		}
		
		require_once(JPATH_CONFIGURATION.DS.'hubconfiguration.php');
		
		$object = new HubConfig();
		
		if (is_object( $object )) {
			foreach (get_object_vars($object) as $k => $v) 
			{
				if (substr($k, 0,1) != '_' || $k == '_name') {
					$arr[$k] = $v;
				}
			}
		}
		
		return $arr;
	}
	
	//-----------
	
	protected function saveConfiguration(&$arr)
	{
		$handle = fopen(JPATH_CONFIGURATION.DS.'hubconfiguration.php', "wb");
		fwrite($handle, "<?php\nclass HubConfig {\n");
		foreach ($arr as $key => $value ) 
		{
			fwrite($handle, '    var $' . $key . " = '" . $value . "';\n");
		}
		fwrite($handle, "}\n?>\n");
		fclose($handle);
	}

	//-----------
	
	protected function settings() 
	{
		$arr =& $this->loadConfiguration();
		
		switch ($this->_task) 
		{
			case 'registration': HubConfigHTML::registration( $arr ); break;
			case 'databases': HubConfigHTML::databases( $arr ); break;
			case 'site':
			default: HubConfigHTML::site( $arr ); break;
		}
	}
	
	//-----------
	
	protected function _save( $task='' ) 
	{
		$settings = JRequest::getVar( 'settings', array(), 'post' );
		
		if (!is_array($settings) || empty($settings)) {
			$this->_redirect = 'index.php?option='.$this->_option.'&task='.$task;
			return;
		}
		
		$arr =& $this->loadConfiguration();
		
		foreach ($settings as $name=>$value) 
		{
			if ($task == 'registration') {
				$r = $value['create'].$value['proxy'].$value['update'].$value['edit'];

				$arr['registration'.$name] = $r;
			} else {
				$arr[$name] = $value;
			}
		}

		$this->saveConfiguration($arr);
		
		$this->_redirect = 'index.php?option='.$this->_option.'&task='.$task;
		$this->_message = JText::_('Configuration saved');
	}
	
	//-----------

	protected function misc()
	{
		global $mainframe;
		
		$f = array('hubShortName','hubShortURL','hubLongURL','hubSupportEmail','hubMonitorEmail','hubHomeDir');
		
		$arr =& $this->loadConfiguration();
		$arrr = array();
		foreach ($arr as $field => $value) 
		{
			if ((substr($field, 0, strlen('registration')) != 'registration')
			 && (substr($field, 0, strlen('hubLDAP')) != 'hubLDAP')
			 && (substr($field, 0, strlen('forge')) != 'forge')
			 && (substr($field, 0, strlen('mwDB')) != 'mwDB')
			 && (substr($field, 0, strlen('ipDB')) != 'ipDB')
			 && (substr($field, 0, strlen('hubFocusArea')) != 'hubFocusArea')
			 && (substr($field, 0, strlen('hubLoginReturn')) != 'hubLoginReturn')
			 && !in_array($field,$f)) {
				$arrr[$field] = $value;
			}
		}
		
		// Get Joomla configuration
		$config = JFactory::getConfig();
		
		// Get paging variables
		$limit = $mainframe->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$start = JRequest::getInt('limitstart', 0);
		
		$total = count($arrr);

		// initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $start, $limit );

		// output HTML
		HubConfigHTML::misc( $arrr, $pageNav, 'tags');
	}

	//-----------

	protected function edit()
	{
		$arr =& $this->loadConfiguration();
		
		$name = JRequest::getVar( 'name', 0 );

		if (empty($name)) {
			$ids = JRequest::getVar( 'id', array(0) );
			
			if (is_array($ids)) {
				foreach ($ids as $id) 
				{
					if (array_key_exists($id,$arr)) {
						$name = $id;
						break;
					}
				}
			}
		}
		
		if (empty($name)) {
			HubConfigHTML::edit();
		} else {
			if (!array_key_exists($name, $arr)) {
				$arr[$name] = null;
			}
			
			HubConfigHTML::edit( $name, $arr[$name] );
		}
	}

	//-----------

	protected function cancel()
	{
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	//-----------

	protected function save( $redirect=1 )
	{
		$arr =& $this->loadConfiguration();
		
		$name = JRequest::getVar( 'editname', 0, 'post' );
		
		$editsave = !empty($name);
		
		if (!$editsave) {
			$name = JRequest::getVar( 'name', 0, 'post' );
		}
		$value = JRequest::getVar( 'value', 0, 'post' );

        if (!$editsave && array_key_exists($name, $arr)) {
            $this->_redirect = 'index.php?option='.$this->_option.'&task=misc';
            $this->_message = JText::_('Variable already exists');
		} else {
			$arr[$name] = $value;
			
			$this->saveConfiguration($arr);
			
			if ($redirect) {
				$this->_redirect = 'index.php?option='.$this->_option.'&task=misc';
				$this->_message = JText::_('Configuration variable saved');
			}
		}
	}

	//-----------

	protected function delete()
	{
		$modified = false;

		$arr =& $this->loadConfiguration();

		$ids = JRequest::getVar( 'id', array(0) );

		if (is_array($ids)) {
			foreach ($ids as $id)
			{
				if (array_key_exists($id,$arr)) {
					unset( $arr[$id] );
					$modified = true;
				}
			}
			
			if ($modified) {
				$this->saveConfiguration($arr);
			}
        }

		$this->_redirect = 'index.php?option='.$this->_option.'&task=misc';
		$this->_message = JText::_('Configuration variable deleted');
	}
	
	//-----------
	
	protected function components() 
	{
		// Get the list of components
		$arr =& $this->loadConfiguration();
		
		$components = (isset($arr['hubComponentList'])) ? $arr['hubComponentList'] : '';
		$components = explode(',',$components);
		$components = array_map('trim',$components);

		sort($components);
		
		// Get the active component
		$com = JRequest::getVar( 'component', '' );
		if (!$com) {
			$com = $components[0];
		}
		
		// Load the component
		$database =& JFactory::getDBO();
		$component = new JTableComponent( $database );
		$component->loadByOption( $com );
		
		// Output HTML
		HubConfigHTML::components( $components, $this->_option, $component, $this->_message );
	}
	
	//-----------
	
	protected function savecom()
	{
		$database =& JFactory::getDBO();
		
		// Incoming component ID
		$id = JRequest::getInt( 'id', 0, 'post' );
		
		// Load the component
		$component = new JTableComponent( $database );
		$component->load( $id );
		
		// Incoming parameters
		$params = JRequest::getVar( 'params', array(), 'post' );
		if (is_array( $params )) {
			$txt = array();
			foreach ( $params as $k=>$v) 
			{
				$txt[] = "$k=$v";
			}
			
			$component->params = implode( "\n", $txt );
			
			// Save the changes
			if (!$component->store()) {
				$this->setError( $component->getError() );
			}
			
			$this->_message = JText::_('Configuration successfully saved.');
		}
		
		// Push through to the components view
		$this->components();
	}
	
	//----------------------------------------------------------
	//  Organizations
	//----------------------------------------------------------
	
	protected function orgs()
	{
		$app =& JFactory::getApplication();
		$database =& JFactory::getDBO();
		
		// Get filters
		$filters = array();
		//$filters['search'] = urldecode(JRequest::getString('search'));
		$filters['search'] = urldecode($app->getUserStateFromRequest($this->_option.'.orgsearch', 'search', ''));
		$filters['show']   = '';
		
		// Get configuration
		$config = JFactory::getConfig();
		
		// Get paging variables
		$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start'] = JRequest::getInt('limitstart', 0);

		$obj = new XOrganization( $database );

		// Get a record count
		$total = $obj->getCount( $filters );
		
		// Get records
		$rows = $obj->getRecords( $filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Output HTML
		HubConfigHTML::orgs( $rows, $pageNav, $this->_option, $filters );
	}
	
	//-----------

	protected function addorg()
	{
		$this->editorg();
	}

	//-----------

	protected function editorg() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$ids = JRequest::getVar( 'id', array() );

		// Get the single ID we're working with
		if (is_array($ids)) {
			$id = (!empty($ids)) ? $ids[0] : 0;
		} else {
			$id = 0;
		}
		
		// Initiate database class and load info
		$org = new XOrganization( $database );
		$org->load( $id );
		
		// Ouput HTML
		HubConfigHTML::editorg( $org, $this->_option );
	}
	
	//-----------
	
	protected function saveorg() 
	{
		$database =& JFactory::getDBO();
		
		// Load the tag object and bind the incoming data to it
		$row = new XOrganization( $database );
		if (!$row->bind( $_POST )) {
			echo HubConfigHTML::alert( $row->getError() );
			return;
		}

		// Check content
		if (!$row->check()) {
			echo HubConfigHTML::alert( $row->getError() );
			return;
		}

		// Store new content
		if (!$row->store()) {
			echo HubConfigHTML::alert( $row->getError() );
			return;
		}
	
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=orgs';
		$this->_message = JText::_( 'HUB_ORG_SAVED' );
	}
	
	//-----------

	protected function removeorg() 
	{
		// Incoming
		$ids = JRequest::getVar( 'ids', array() );

		// Get the single ID we're working with
		if (!is_array($ids)) {
			$ids = array();
		}
		
		// Do we have any IDs?
		if (!empty($ids)) {
			$database =& JFactory::getDBO();
			
			$org = new XOrganization( $database );
			
			// Loop through each ID and delete the necessary items
			foreach ($ids as $id) 
			{
				// Remove the organization
				$org->delete( $id );
			}
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=orgs';
		$this->_message = JText::_('HUB_ORG_REMOVED');
	}
	
	//-----------

	protected function cancelorg()
	{
		$this->_redirect = 'index.php?option='.$this->_option.'&task=orgs';
	}
}
?>
