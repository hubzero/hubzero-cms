<?php
JLoader::import('Hubzero.Api.Controller');

class MembersControllerApi extends \Hubzero\Component\ApiController
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
			case 'checkpass':		$this->checkpass();					break;
			case 'diskusage':		$this->diskusage();					break;
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

	private function not_authorized()
	{
		$response = $this->getResponse();
		$response->setErrorMessage(401,'Not Authorized');
	}

	private function error( $code, $message )
	{
		if($code != '' && $message != '')
		{
			$response = $this->getResponse();
			$response->setErrorMessage( $code, $message );
		}
	}

	function myprofile()
	{
		//get the userid from authentication token
		//load user profile from userid
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		//check to make sure we have a profile
		if ($result === false)	return $this->not_found();

		//get any request vars
		$format = JRequest::getVar('format', 'json');

		//
		$profile = array(
			'id' => $result->get('uidNumber'),
			'username' => $result->get('username'),
			'name' => $result->get('name'),
			'first_name' => $result->get('givenName'),
			'middle_name' => $result->get('middleName'),
			'last_name' => $result->get('surname'),
			'bio' => $result->getBio('clean'),
			'email' => $result->get('email'),
			'phone' => $result->get('phone'),
			'url' => $result->get('url'),
			'gender' => $result->get('gender'),
			'organization' => $result->get('organization'),
			'organization_type' => $result->get('orgtype'),
			'country_resident' => $result->get('countryresident'),
			'country_origin' => $result->get('countryorigin'),
			'member_since' => $result->get('registerDate'),
			'orcid' => $result->get('orcid'),
			'picture' => array(
				'thumb' => \Hubzero\User\Profile\Helper::getMemberPhoto( $result, 0, true ),
				'full' => \Hubzero\User\Profile\Helper::getMemberPhoto( $result, 0, false )
			)
		);

		//encode and return result
		$object = new stdClass();
		$object->profile = $profile;
		$this->setMessageType( $format );
		$this->setMessage( $object );
	}

	private function mygroups()
	{
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		if ($result === false)	return $this->not_found();

		$groups = \Hubzero\User\Helper::getGroups( $result->get('uidNumber'), 'members', 0);

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
		//get user from authentication and load their profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		//make sure we have a user
		if ($result === false)
		{
			return $this->not_authorized();
		}

		//include middleware utilities
		JLoader::import("joomla.database.table");
		include_once( JPATH_ROOT.DS.'components'.DS.'com_tools'.DS.'models'.DS.'mw.utils.php' );
		include_once( JPATH_ROOT.DS.'components'.DS.'com_tools'.DS.'models'.DS.'mw.class.php' );

		//get db connection
		$db = JFactory::getDBO();

		//get Middleware DB connection
		$mwdb = MwUtils::getMWDBO();

		//get com_tools params
		$mconfig = JComponentHelper::getParams( 'com_tools' );

		//check to make sure we have a connection to the middleware and its on
		if(!$mwdb || !$mconfig->get('mw_on') || $mconfig->get('mw_on') > 1)
		{
			return $this->error( 503, 'Middleware Service Unavailable' );
		}

		//get request vars
		$format = JRequest::getVar('format', 'json');
		$order = JRequest::getVar('order', 'id_asc' );

		//get my sessions
		$ms = new MwSession( $mwdb );
		$sessions = $ms->getRecords( $result->get("username"), '', false );

		//run middleware command to create screenshots
		$cmd = "/bin/sh ". JPATH_SITE . "/components/com_tools/scripts/mw screenshot " . $result->get('username') . " 2>&1 </dev/null";
		exec($cmd, $results, $status);

		//
		$results = array();
		foreach($sessions as $session)
		{
			$r = array(
				'id' => $session->sessnum,
				'app' => $session->appname,
				'name' => $session->sessname,
				'started' => $session->start,
				'accessed' => $session->accesstime,
				'owner' => ($result->get('username') == $session->username) ? 1 : 0,
				'ready-only' => ($session->readonly == 'No') ? 0 : 1
			);
			$results[] = $r;
		}

		//make sure we have an acceptable ordering
		$accepted_ordering = array('id_asc', 'id_desc', 'started_asc', 'started_desc', 'accessed_asc', 'accessed_desc');
		if(in_array($order, $accepted_ordering))
		{
			switch( $order )
			{
				case 'id_asc':
					break;
				case 'id_desc':
					usort($results, array("MembersControllerApi", "id_sort_desc"));
					break;
				case 'started_asc':
					break;
				case 'started_desc':
					usort($results, array("MembersControllerApi", "started_date_sort_desc"));
					break;
				case 'accessed_asc':
					usort($results, array("MembersControllerApi", "accessed_date_sort_asc"));
					break;
				case 'accessed_desc':
					usort($results, array("MembersControllerApi", "accessed_date_sort_desc"));
					break;
			}
		}

		//encode sessions for return
		$object = new stdClass();
		$object->sessions = $results;

		//set format and content
		$this->setMessageType( $format );
		$this->setMessage( $object );
	}

	private function id_sort_desc($a, $b)
	{
		return $a['id'] < $b['id'] ? 1 : -1;
	}

	private function started_date_sort_desc($a, $b)
	{
		return (strtotime($a['started']) < strtotime($b['started'])) ? 1 : -1;
	}

	private function accessed_date_sort_asc($a, $b)
	{
		return (strtotime($a['accessed']) < strtotime($b['accessed'])) ? -1 : 1;
	}

	private function accessed_date_sort_desc($a, $b)
	{
		return (strtotime($a['accessed']) < strtotime($b['accessed'])) ? 1 : -1;
	}

	//------

	private function recenttools()
	{
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		if ($result === false)	return $this->not_found();

		//load database object
		$database = JFactory::getDBO();

		//get the supported tag
		$rconfig = JComponentHelper::getParams('com_resources');
		$supportedtag = $rconfig->get('supportedtag', '');

		//get supportedtag usage
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'tags.php');
		$this->rt = new ResourcesTags($database);
		$supportedtagusage = $this->rt->getTagUsage($supportedtag, 'alias');

		//load users recent tools
		$sql = "SELECT r.alias, tv.toolname, tv.title, tv.description, tv.toolaccess as access, tv.mw, tv.instance, tv.revision
				FROM #__resources as r, #__recent_tools as rt, #__tool_version as tv
				WHERE r.published=1
				AND r.type=7
				AND r.standalone=1
				AND r.access!=4
				AND r.alias=tv.toolname
				AND tv.state=1
				AND rt.uid={$result->get("uidNumber")}
				AND rt.tool=r.alias
				GROUP BY r.alias
				ORDER BY rt.created DESC";

		$database->setQuery($sql);
		$recent_tools = $database->loadObjectList();

		$r = array();
		foreach($recent_tools as $k => $recent)
		{
			$r[$k]['alias'] = $recent->alias;
			$r[$k]['title'] = $recent->title;
			$r[$k]['description'] = $recent->description;
			$r[$k]['version'] = $recent->revision;
			$r[$k]['supported'] = (in_array($recent->alias, $supportedtagusage)) ? 1 : 0;
		}

		//encode sessions for return
		$object = new stdClass();
		$object->recenttools = $r;
		$this->setMessageType( "json" );
		$this->setMessage( $object );
	}

	//------

	private function checkpass()
	{
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		if ($result === false)	return $this->not_found();

		// Get the password rules
		$password_rules = \Hubzero\Password\Rule::getRules();

		$pw_rules = array();

		// Get the password rule descriptions
		foreach($password_rules as $rule)
		{
			if (!empty($rule['description']))
			{
				$pw_rules[] = $rule['description'];
			}
		}

		// Get the password
		$pw = JRequest::getCmd('password1', null, 'post');

		// Validate the password
		if (!empty($pw))
		{
			$msg = \Hubzero\Password\Rule::validate($pw, $password_rules, $result);
		}
		else
		{
			$msg = array();
		}

		$html = '';

		// Iterate through the rules and add the appropriate classes (passed/error)
		if (count($pw_rules) > 0) {
			foreach ($pw_rules as $rule)
			{
				if (!empty($rule))
				{
					if (!empty($msg) && is_array($msg)) {
						$err = in_array($rule, $msg);
					} else {
						$err = '';
					}
					$mclass = ($err)  ? ' class="error"' : 'class="passed"';
					$html .= "<li $mclass>".$rule."</li>";
				}
			}
			if (!empty($msg) && is_array($msg)) {
				foreach ($msg as $message)
				{
					if (!in_array($message, $pw_rules)) {
						$html .= '<li class="error">'.$message."</li>";
					}
				}
			}
		}

		// Encode sessions for return
		$object = new stdClass();
		$object->html = $html;
		$this->setMessageType("json");
		$this->setMessage($object);
	}

	private function diskusage()
	{
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		if ($result === false)
		{
			return $this->not_found();
		}

		require_once JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'helpers' . DS . 'utils.php';
		$du = ToolsHelperUtils::getDiskUsage($result->get('username'));
		if (count($du) <=1)
		{
			// error
			$percent = 0;
		}
		else
		{
			bcscale(6);
			$val = (isset($du['softspace']) && $du['softspace'] != 0) ? bcdiv($du['space'], $du['softspace']) : 0;
			$percent = round($val * 100);
		}

		$amt = ($percent > 100) ? '100' : $percent;
		$total = (isset($du['softspace'])) ? $du['softspace'] / 1024000000 : 0;

		//encode sessions for return
		$object = new stdClass();
		$object->amount = $amt;
		$object->total  = $total;
		$this->setMessageType( "json" );
		$this->setMessage( $object );
	}

	//------

	private function getResourceFromAppname( $appname, $database )
	{
		$sql = "SELECT r.*, tv.id as revisionid FROM jos_resources as r, jos_tool_version as tv WHERE tv.toolname=r.alias and tv.instance='".$appname."'";

		$database->setQuery($sql);

		return $database->loadObject();
	}
}