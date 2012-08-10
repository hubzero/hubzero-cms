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
			case 'test':		$this->test();						break;
			case 'groups':		$this->groups();					break;
			case 'profile':		$this->profile();					break;
			case 'myprofile':	$this->myprofile();					break;
			case 'sessions':	$this->sessions();					break;
			default:
				$this->not_found();
				break;
					
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
	
	
	function test()
	{
		$this->setMessageType('application/json');
		$this->setMessage( array("who" => 'me', "what" => 'this', "where" => 'here') );
	}

	function groups()
	{
		$userid = JRequest::getInt("userid");

		$result = Hubzero_User_Helper::getGroups( $userid, 'members', 0);

		$a = array();

		foreach($result as $k => $r)
		{
			$a[$k]['gidNumber'] = $r->gidNumber;
			$a[$k]['cn'] = $r->cn;
		}

		$obj = new stdClass();

		$obj->groups = $a;

		$this->setMessageType('application/json');
		$this->setMessage($obj);
	}

	function profile()
	{
		$userid = JRequest::getInt("userid");
		
		$result = new Hubzero_User_Profile( $userid );

		if ($result === false)
			return $this->not_found();

		$profile = array();

		$public_keys = array("uidNumber","name","picture","givenName","middleName","surname","registerDate");
		$private_keys = array("username","bio","email","phone","url","homeDirectory","orgtype","organization","countryresident","countryorigin","gender");

		ximport("Hubzero_User_Profile_Helper");

		$member_pic = Hubzero_User_Profile_Helper::getMemberPhoto( $result );
		//$member_pic = "https://" . $_SERVER['HTTP_HOST'] . $member_pic;

		foreach($public_keys as $pub) 
		{
			switch( $pub )
			{
				case "picture":		$profile['picture'] = $member_pic;										break;
				default:			$profile[$pub] = ($result->get($pub) != "") ? $result->get($pub) : "";
			}
		}

		$obj = new stdClass();

		$obj->profile = $profile;

		$this->setMessageType("application/json");
		$this->setMessage($obj);
	}

	function myprofile()
	{
		$data = $this->_provider->getTokenData();

		$userid = $data->user_id;
		
		$result = Hubzero_User_Profile::getInstance($userid);

		if ($result === false)
			return $this->not_found();

		$profile = array();

		$public_keys = array("uidNumber","name","picture","givenName","middleName","surname","registerDate");
		$private_keys = array("username","bio","email","phone","url","homeDirectory","orgtype","organization","countryresident","countryorigin","gender");

		ximport("Hubzero_User_Profile_Helper");

		$member_pic = Hubzero_User_Profile_Helper::getMemberPhoto( $result );
		//$member_pic = "https://" . $_SERVER['HTTP_HOST'] . $member_pic;

		foreach($public_keys as $pub) 
		{
			switch( $pub )
			{
				case "picture":		$profile['picture'] = $member_pic;										break;
				default:			$profile[$pub] = ($result->get($pub) != "") ? $result->get($pub) : "";
			}
		}

		$obj = new stdClass();

		$obj->profile = $profile;

		$this->setMessageType("application/json");
		$this->setMessage($obj);
	}
	
	private function sessions()
	{
		//get request vars

		$userid = JRequest::getInt("userid", 0);
		$format = JRequest::getVar("format", "json");

		//get the user

		$user =  Hubzero_User_Profile::getInstance( $userid );

		if(!$user)
		{
			return;
		}

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

		$sessions = $ms->getRecords( $user->get("username"), '', false );

		//echo "<pre>";
		//print_r($sessions);
		//echo "</pre>";

		//

		$result = array();

		foreach($sessions as $session)
		{
			//get the resource

			$shots = $this->getSessionScreenshots($session->appname);

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

		//sleep(5);

		//encode sessions for return

		$object = new stdClass();

		$object->sessions = $result;

		//set format and content

		$this->setMessageType( $format );
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