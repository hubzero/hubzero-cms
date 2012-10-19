<?php
JLoader::import('Hubzero.Api.Controller');

class MembersApiController extends Hubzero_Api_Controller
{
	function execute()
	{
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		switch($this->segments[0]) 
		{
			case 'myprofile':		$this->myprofile();					break;
			case 'mygroups':		$this->mygroups();					break;
			case 'mysessions':		$this->mysessions();				break;
			case 'recenttools':		$this->recenttools();				break;
			default:				$this->not_found();
		}
	}
	
	/**
	 * Short description for 'not_found'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	private function not_found()
	{
		$response = $this->getResponse();
	
		$response->setErrorMessage(404,'Not Found');
	}
	
	function myprofile()
	{
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = Hubzero_User_Profile::getInstance($userid);

		if ($result === false)	return $this->not_found();

		$profile = array();
		$public_keys = array("uidNumber","name","picture","givenName","middleName","surname","registerDate");
		$private_keys = array("username","bio","email","phone","url","homeDirectory","orgtype","organization","countryresident","countryorigin","gender");

		ximport("Hubzero_User_Profile_Helper");

		$member_pic = Hubzero_User_Profile_Helper::getMemberPhoto( $result, 0, false );
		$member_pic_thumb = Hubzero_User_Profile_Helper::getMemberPhoto( $result, 0, true );
		
		foreach($public_keys as $pub) 
		{
			switch( $pub )
			{
				case "picture":
					$profile['picture']['thumb'] = $member_pic_thumb;
					$profile['picture']['full'] = $member_pic;
					break;
				default:
					$profile[$pub] = ($result->get($pub) != "") ? $result->get($pub) : "";
			}
		}
		
		foreach($private_keys as $pri)
		{
			$profile[$pri] = ($result->get($pri) != "") ? $result->get($pri) : "";
		}
		
		//sleep(5);
		
		//encode and return result
		$obj = new stdClass();
		$obj->profile = $profile;
		$this->setMessageType("application/json");
		$this->setMessage($obj);
	}
	
	private function mygroups()
	{
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = Hubzero_User_Profile::getInstance($userid);

		if ($result === false)	return $this->not_found();
		
		$groups = Hubzero_User_Helper::getGroups( $result->get('uidNumber'), 'members', 0);
		
		$g = array();
		foreach($groups as $k => $group)
		{
			$g[$k]['gidNumber'] 	= $group->gidNumber;
			$g[$k]['cn'] 			= $group->cn;
			$g[$k]['description'] 	= $group->description;
		}
		
		//encode and return result
		$obj = new stdClass();
		$obj->groups = $g;
		$this->setMessageType("application/json");
		$this->setMessage($obj);
	}
	
	private function mysessions()
	{
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = Hubzero_User_Profile::getInstance($userid);

		if ($result === false)
			return $this->not_found();

		//include middleware utilities               
		JLoader::import("joomla.database.table");
		include_once( JPATH_ROOT.DS.'components'.DS.'com_tools'.DS.'models'.DS.'mw.utils.php' );
		include_once( JPATH_ROOT.DS.'components'.DS.'com_tools'.DS.'models'.DS.'mw.class.php' ); 

		//get db connection
		$db =& JFactory::getDBO();

		//get Middleware DB connection
		$mwdb =& MwUtils::getMWDBO();

		//get com_tools params
		$mconfig = JComponentHelper::getParams( 'com_tools' );

		//check to make sure we have a connection to the middleware and its on
		if(!$mwdb || !$mconfig->get('mw_on') || $mconfig->get('mw_on') > 1)
		{
			return false;
		}

		//get my sessions
		$ms = new MwSession( $mwdb );
		$sessions = $ms->getRecords( $result->get("username"), '', false );
		
		//
		$result = array();
		foreach($sessions as $session)
		{
			//get the resource
			//$shots = $this->getSessionScreenshots($session->appname);
			$shots = "";
			
			$r = array(
				'id' => $session->sessnum,
				'app' => $session->appname,
				'name' => $session->sessname,
				'started' => $session->start,
				'accessed' => $session->accesstime,
				'screenshots' => $shots
			);

			$result[] = $r;
		}

		//sleep(10);

		//encode sessions for return
		$object = new stdClass();
		$object->sessions = $result;

		//set format and content
		$this->setMessageType( "json" );
		$this->setMessage( $object );
	}
	
	
	//------
	
	private function recenttools()
	{
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = Hubzero_User_Profile::getInstance($userid);

		if ($result === false)	return $this->not_found();
		
		//load database object
		JLoader::import("joomla.database.table");
		$database =& JFactory::getDBO();
		
		//load users recent tools
		$sql = "SELECT r.id, r.title, r.alias, r.introtext as description, r.fulltxt as abstract FROM jos_recent_tools as rt, jos_resources as r WHERE rt.uid={$result->get("uidNumber")} AND rt.tool=r.alias AND r.published=1 ORDER BY rt.created";
		$database->setQuery($sql);
		$recent = $database->loadObjectList();
		
		//remove slashes and html code from abstract
		for($i=0, $n=count($recent); $i < $n; $i++)
		{
			$recent[$i]->abstract = stripslashes(strip_tags($recent[$i]->abstract));
		}
		
		//encode sessions for return
		$object = new stdClass();
		$object->recenttools = $recent;
		$this->setMessageType( "json" );
		$this->setMessage( $object );
	}
	
	//------

	private function getSessionScreenshots( $appname )
	{
		//return array

		$shots = array();

		//check to make sure we have an app to check

		if($appname == '')
		{
			return $shots;
		}

		//database connection

		$database =& JFactory::getDBO();

		//include required libs

		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'helper.php' ); 
		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'html.php' );

		//get base upload path for resources

		$rparams = JComponentHelper::getParams('com_resources');

		$base_path = $rparams->get("uploadpath", "/site/resources");

		$base_path = (substr($base_path,0,1) != DS) ? DS . $base_path : $base_path;
		$base_path = rtrim($base_path, DS);

		//get the resource info based on application name

		$resource = $this->getResourceFromAppname( $appname, $database );

		//build path to resource uploads

		$resource_path  = ResourcesHtml::build_path($resource->created, $resource->id, '');
		$resource_path .= DS . $resource->revisionid;

		//final path to screenshots

		$path = $base_path . $resource_path;

		//get the resource screenshots

		$rs = new ResourceScreenshot($database);

		$screenshots = $rs->getScreenshots($resource->id, $resource->revisionid);

		//loop through each screenshot found and add to array to be returned

		foreach($screenshots as $screenshot)
		{
			//make sure the file exits before adding to return array

			if(file_exists(JPATH_ROOT . $path . DS . $screenshot->filename))
			{
				$shots[] = $path . DS . $screenshot->filename;
			}
		}

		return $shots;
	}

	//------

	private function getResourceFromAppname( $appname, $database )
	{
		$sql = "SELECT r.*, tv.id as revisionid FROM jos_resources as r, jos_tool_version as tv WHERE tv.toolname=r.alias and tv.instance='".$appname."'";

		$database->setQuery($sql);

		return $database->loadObject();
	}
}